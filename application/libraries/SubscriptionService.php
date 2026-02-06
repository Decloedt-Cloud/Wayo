<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Subscription Service
 *
 * Handles subscription lifecycle, invoice generation, and state management
 *
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class SubscriptionService {

    protected $CI;

    // Subscription statuses
    const STATUS_TRIALING = 'trialing';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_CANCELED = 'canceled';

    // Grace period in days
    const GRACE_PERIOD_DAYS = 3;

    public function __construct($params = null)
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Crud_model', 'crud_model');
        $this->CI->load->model('Payment_model', 'payment_model');
    }

    /**
     * Get subscription status for a school
     *
     * @param int $school_id
     * @return array Status information
     */
    public function getSubscriptionStatus($school_id)
    {
        $school = $this->CI->crud_model->get_school_details_by_id($school_id);

        if (!$school) {
            return [
                'status' => self::STATUS_CANCELED,
                'is_access_allowed' => false,
                'message' => 'School not found'
            ];
        }

        $now = time();

        // Trialing period
        if ($school['subscription_status'] === self::STATUS_TRIALING ||
            ($school['is_trial'] == 1 && (!isset($school['trial_end']) || $school['trial_end'] > $now))) {
            return [
                'status' => self::STATUS_TRIALING,
                'is_access_allowed' => true,
                'trial_remaining_days' => isset($school['trial_end']) ? ceil(($school['trial_end'] - $now) / 86400) : 14,
                'message' => 'Trial period active'
            ];
        }

        // Active subscription
        if ($school['subscription_status'] === self::STATUS_ACTIVE ||
            ($school['is_paid'] == 1 && isset($school['subscription_end']) && $school['subscription_end'] > $now)) {
            return [
                'status' => self::STATUS_ACTIVE,
                'is_access_allowed' => true,
                'subscription_days_remaining' => ceil(($school['subscription_end'] - $now) / 86400),
                'message' => 'Active subscription'
            ];
        }

        // Past due - within grace period
        $grace_period_end = $school['subscription_end'] + (self::GRACE_PERIOD_DAYS * 86400);
        if ($now <= $grace_period_end) {
            return [
                'status' => self::STATUS_PAST_DUE,
                'is_access_allowed' => true, // Limited access during grace period
                'grace_days_remaining' => ceil(($grace_period_end - $now) / 86400),
                'message' => 'Payment overdue - grace period active'
            ];
        }

        // Suspended - grace period expired
        return [
            'status' => self::STATUS_SUSPENDED,
            'is_access_allowed' => false,
            'message' => 'Subscription suspended - payment required'
        ];
    }

    /**
     * Ensure renewal invoice exists for a school
     * DEPRECATED: Use Invoice_model::ensureSubscriptionInvoice() instead
     *
     * @param int $school_id
     * @return array Result with invoice_id or error
     */
    public function ensureRenewalInvoice($school_id)
    {
        // Delegate to the new idempotent invoice model
        $this->CI->load->model('Invoice_model', 'invoice_model');
        return $this->CI->invoice_model->ensureSubscriptionInvoice($school_id);
    }

    /**
     * Process successful subscription payment
     * DEPRECATED: Use Invoice_model::markInvoicePaid() instead
     *
     * @param array $invoice Invoice data
     * @param string $stripe_payment_intent_id Stripe payment intent ID
     * @return array Result
     */
    public function processPaymentSuccess($invoice, $stripe_payment_intent_id = null)
    {
        // Delegate to the new transactional invoice model
        $this->CI->load->model('Invoice_model', 'invoice_model');
        return $this->CI->invoice_model->markInvoicePaid($invoice['id'], $stripe_payment_intent_id);
    }

    /**
     * Get default subscription plan
     *
     * @param string $country Country code (e.g. 'MA', 'AE')
     * @return array|null Plan data
     */
    public function getDefaultPlan($country = 'MA')
    {
        // Default to MA if country is empty or null
        $country = empty($country) ? 'MA' : $country;
        
        // Try to find a default plan for the specific country
        $plan = $this->CI->db->get_where('subscription_plans', [
            'is_default' => 1, 
            'active' => 1,
            'country' => $country
        ])->row_array();
        
        // Fallback: if no plan found for country (and country is not MA), try MA/default
        if (!$plan && $country !== 'MA') {
            $plan = $this->CI->db->get_where('subscription_plans', [
                'is_default' => 1, 
                'active' => 1,
                'country' => 'MA'
            ])->row_array();
        }
        
        // Ultimate fallback: any default active plan
        if (!$plan) {
            $plan = $this->CI->db->get_where('subscription_plans', [
                'is_default' => 1, 
                'active' => 1
            ])->row_array();
        }
        
        return $plan;
    }

    /**
     * Get all active plans
     *
     * @param string|null $country Country code to filter by
     * @return array Plans
     */
    public function getActivePlans($country = null)
    {
        $where = ['active' => 1];
        if ($country) {
            $where['country'] = $country;
        }
        return $this->CI->db->get_where('subscription_plans', $where)->result_array();
    }

    /**
     * Check if school needs renewal invoice
     *
     * @param int $school_id
     * @return bool
     */
    public function needsRenewalInvoice($school_id)
    {
        $status = $this->getSubscriptionStatus($school_id);
        return in_array($status['status'], [self::STATUS_PAST_DUE, self::STATUS_SUSPENDED]);
    }

    /**
     * Process Stripe webhook for subscription payment
     *
     * @param array $event Stripe webhook event
     * @return array Result
     */
    public function processStripeWebhook($event)
    {
        $event_type = $event['type'];
        $event_data = $event['data']['object'];

        log_message('info', "SubscriptionService: Processing Stripe webhook: {$event_type}");

        switch ($event_type) {
            case 'payment_intent.succeeded':
                return $this->handlePaymentIntentSucceeded($event_data);

            case 'invoice.payment_succeeded':
                return $this->handleInvoicePaymentSucceeded($event_data);

            default:
                return ['success' => true, 'message' => 'Event type not handled'];
        }
    }

    /**
     * Handle payment_intent.succeeded webhook
     */
    private function handlePaymentIntentSucceeded($payment_intent)
    {
        // Find invoice by payment intent ID
        $invoice = $this->CI->db->get_where('invoices', [
            'stripe_payment_intent_id' => $payment_intent['id']
        ])->row_array();

        if (!$invoice) {
            log_message('error', "SubscriptionService: Invoice not found for payment_intent {$payment_intent['id']}");
            return ['success' => false, 'message' => 'Invoice not found for payment intent'];
        }

        // Check if already processed
        if ($invoice['status'] === 'paid') {
            return ['success' => true, 'message' => 'Payment already processed'];
        }

        return $this->processPaymentSuccess($invoice, $payment_intent['id']);
    }

    /**
     * Handle invoice.payment_succeeded webhook
     */
    private function handleInvoicePaymentSucceeded($stripe_invoice)
    {
        // Find invoice by Stripe invoice ID
        $invoice = $this->CI->db->get_where('invoices', [
            'stripe_invoice_id' => $stripe_invoice['id']
        ])->row_array();

        if (!$invoice) {
            return ['success' => true, 'message' => 'Invoice not found for Stripe invoice ID'];
        }

        if ($invoice['status'] === 'paid') {
            return ['success' => true, 'message' => 'Payment already processed'];
        }

        return $this->processPaymentSuccess($invoice);
    }
}
