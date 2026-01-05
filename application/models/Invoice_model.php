<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model {

    // Cache for frequently accessed data
    private $plan_cache = [];
    private $school_cache = [];

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    /**
     * Ensure a subscription invoice exists for a school (idempotent)
     *
     * Creates an invoice ONLY if no existing unpaid invoice exists for the school
     * Uses DB transaction to prevent race conditions
     * Optimized with caching and early validation
     *
     * @param int $school_id
     * @param array $plan_data Optional plan data
     * @return array Result with invoice_id or error
     */
    public function ensureSubscriptionInvoice($school_id, $plan_data = null)
    {
        // Early validation
        if (!is_numeric($school_id) || $school_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid school_id: must be positive integer'
            ];
        }

        // Start transaction for atomicity
        $this->db->trans_start();

        try {
            // 1. Check for existing unpaid invoice (optimized query)
            $existing_invoice = $this->db->query("
                SELECT id, status, period_start, period_end
                FROM invoices
                WHERE school_id = ?
                AND payment_type = 'subscription_admin'
                AND status = 'unpaid'
                LIMIT 1
                FOR UPDATE
            ", [$school_id])->row_array();
            
            if ($existing_invoice) {
                $this->db->trans_complete();
                return [
                    'success' => true,
                    'invoice_id' => $existing_invoice['id'],
                    'message' => 'Existing unpaid invoice found',
                    'existing' => true
                ];
            }

            // 2. Get school details (with caching)
            $school = $this->getCachedSchool($school_id);
            if (!$school) {
                $this->db->trans_rollback();
                return ['success' => false, 'message' => 'School not found'];
            }

            // 3. Get plan data (with caching)
            if (!$plan_data) {
                $plan_data = $this->getCachedDefaultPlan();
                if (!$plan_data) {
                    $this->db->trans_rollback();
                    return ['success' => false, 'message' => 'No active subscription plan found'];
                }
            }
            
            // 4. Calculate period (max(old_end, now) to avoid losing days)
            $now = time();
            $period_start = max($school['subscription_end'] ?? 0, $now);
            $period_end = strtotime("+{$plan_data['interval_count']} {$plan_data['interval_type']}", $period_start);
            
            // 5. Create invoice data
            $invoice_data = [
                'title' => $school['name'] . ' - ' . $plan_data['name'],
                'total_amount' => $plan_data['amount'],
                'currency' => $plan_data['currency'],
                'payment_type' => 'subscription_admin',
                'status' => 'unpaid',
                'school_id' => $school_id,
                'session' => active_session(),
                'created_at' => $now,
                'period_start' => $period_start,
                'period_end' => $period_end
            ];
            
            // 7. Insert invoice
            $this->db->insert('invoices', $invoice_data);
            $invoice_id = $this->db->insert_id();
            
            // 8. Complete transaction
            $this->db->trans_complete();
            
            log_message('info', "Invoice_model: Created subscription invoice #{$invoice_id} for school #{$school_id}, period_start: " . date('Y-m-d H:i:s', $period_start));
            
            return [
                'success' => true,
                'invoice_id' => $invoice_id,
                'message' => 'Subscription invoice created',
                'existing' => false
            ];
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', "Invoice_model ensureSubscriptionInvoice error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get default subscription plan
     */
    private function getDefaultSubscriptionPlan()
    {
        return $this->db->get_where('subscription_plans', ['is_default' => 1, 'active' => 1])->row_array();
    }
    
    /**
     * Mark invoice as paid and update school subscription
     * Uses transaction to ensure consistency
     * 
     * @param int $invoice_id
     * @param string|null $payment_reference
     * @param string|null $payment_method
     * @param float|null $amount_paid
     * @param array|null $fx_data Currency conversion data (payment_currency, payment_amount_converted, fx_rate, fx_rate_date)
     * @return array
     */
    public function markInvoicePaid($invoice_id, $payment_reference = null, $payment_method = null, $amount_paid = null, $fx_data = null)
    {
        $this->db->trans_start();
        
        try {
            // 1. Get invoice with lock (raw SQL for CodeIgniter compatibility)
            $invoice = $this->db->query("
                SELECT * FROM invoices WHERE id = ? FOR UPDATE
            ", [$invoice_id])->row_array();
            
            if (!$invoice) {
                $this->db->trans_rollback();
                return ['success' => false, 'message' => 'Invoice not found'];
            }
            
            // 2. Check if already paid
            if ($invoice['status'] === 'paid') {
                $this->db->trans_complete();
                return ['success' => true, 'message' => 'Invoice already paid'];
            }
            
            // 3. Update invoice avec les détails du paiement
            $invoice_update = [
                'status' => 'paid',
                'paid_at' => time(),
                'updated_at' => time()
            ];
            
            // Ajouter les détails spécifiques au type de paiement
            if ($payment_method) {
                $invoice_update['payment_method'] = $payment_method;
            }
            
            if ($amount_paid !== null) {
                $invoice_update['paid_amount'] = $amount_paid;
            }
            
            // Stocker la référence selon le type de paiement
            if ($payment_reference) {
                if ($payment_method === 'stripe') {
                    $invoice_update['stripe_payment_intent_id'] = $payment_reference;
                } elseif ($payment_method === 'paystack') {
                    $invoice_update['payment_reference'] = $payment_reference;
                }
                // Pour PayPal, on pourrait stocker dans un champ dédié si nécessaire
            }
            
            // ========== FX CONVERSION DATA ==========
            // Store currency conversion information if payment was made in different currency
            if (!empty($fx_data) && is_array($fx_data)) {
                // Conversion applied flag
                if (isset($fx_data['conversion_applied']) && $fx_data['conversion_applied']) {
                    $invoice_update['conversion_applied'] = 1;
                    
                    // Payment currency (the currency actually charged to customer)
                    if (!empty($fx_data['payment_currency'])) {
                        $invoice_update['payment_currency'] = $fx_data['payment_currency'];
                    }
                    
                    // Amount in payment currency (converted amount charged)
                    if (isset($fx_data['payment_amount_converted'])) {
                        $invoice_update['payment_amount_converted'] = $fx_data['payment_amount_converted'];
                    }
                    
                    // Exchange rate used
                    if (isset($fx_data['fx_rate'])) {
                        $invoice_update['fx_rate'] = $fx_data['fx_rate'];
                    }
                    
                    // Date of exchange rate
                    if (!empty($fx_data['fx_rate_date'])) {
                        $invoice_update['fx_rate_date'] = $fx_data['fx_rate_date'];
                    }
                    
                    log_message('info', "Invoice_model: FX conversion stored for invoice #{$invoice_id} - " .
                        "Payment: {$fx_data['payment_amount_converted']} {$fx_data['payment_currency']}, " .
                        "Rate: {$fx_data['fx_rate']}, Date: {$fx_data['fx_rate_date']}");
                }
            }
            
            $this->db->where('id', $invoice_id);
            $this->db->update('invoices', $invoice_update);
            
            // 4. Cancel other unpaid invoices for this school
            $this->db->where('school_id', $invoice['school_id']);
            $this->db->where('payment_type', 'subscription_admin');
            $this->db->where('status', 'unpaid');
            $this->db->where('id !=', $invoice_id);
            $this->db->update('invoices', [
                'status' => 'cancelled',
                'updated_at' => time()
            ]);
            
            // 5. Update school subscription
            $school_update = [
                'is_paid' => 1,
                'is_trial' => 0,
                'subscription_status' => 'active',
                'subscription_end' => $invoice['period_end'], // Use invoice period_end
                'updated_at' => time()
            ];
            
            $this->db->where('id', $invoice['school_id']);
            $this->db->update('schools', $school_update);
            
            // 6. Complete transaction
            $this->db->trans_complete();
            
            log_message('info', "Invoice_model: Marked invoice #{$invoice_id} as paid, school #{$invoice['school_id']} active until " . date('Y-m-d H:i:s', $invoice['period_end']));
            
            return [
                'success' => true,
                'message' => 'Invoice marked as paid and school updated'
            ];
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', "Invoice_model markInvoicePaid error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if school has unpaid subscription invoice (optimized)
     */
    public function hasUnpaidSubscriptionInvoice($school_id)
    {
        if (!is_numeric($school_id) || $school_id <= 0) {
            return false;
        }

        // Use COUNT(*) for better performance than num_rows()
        $result = $this->db->query("
            SELECT COUNT(*) as count
            FROM invoices
            WHERE school_id = ?
            AND payment_type = 'subscription_admin'
            AND status = 'unpaid'
        ", [$school_id])->row();

        return $result->count > 0;
    }

    /**
     * Get cached school data to reduce DB queries
     */
    private function getCachedSchool($school_id)
    {
        $cache_key = "school_{$school_id}";

        // Check memory cache first
        if (isset($this->school_cache[$school_id])) {
            return $this->school_cache[$school_id];
        }

        // Check file cache
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) {
            $this->school_cache[$school_id] = $cached;
            return $cached;
        }

        // Load from database
        $school = $this->db->get_where('schools', ['id' => $school_id])->row_array();

        if ($school) {
            // Cache for 5 minutes
            $this->cache->save($cache_key, $school, 300);
            $this->school_cache[$school_id] = $school;
        }

        return $school;
    }

    /**
     * Get cached default subscription plan
     */
    private function getCachedDefaultPlan()
    {
        $cache_key = 'default_subscription_plan';

        // Check memory cache first
        if (!empty($this->plan_cache)) {
            return $this->plan_cache;
        }

        // Check file cache
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) {
            $this->plan_cache = $cached;
            return $cached;
        }

        // Load from database
        $plan = $this->db->get_where('subscription_plans', [
            'is_default' => 1,
            'active' => 1
        ])->row_array();

        if ($plan) {
            // Cache for 10 minutes (plans change infrequently)
            $this->cache->save($cache_key, $plan, 600);
            $this->plan_cache = $plan;
        }

        return $plan;
    }

    /**
     * Clear cache for a specific school (useful after updates)
     */
    public function clearSchoolCache($school_id)
    {
        $cache_key = "school_{$school_id}";
        $this->cache->delete($cache_key);
        unset($this->school_cache[$school_id]);
    }

    /**
     * Clear plan cache (useful after plan updates)
     */
    public function clearPlanCache()
    {
        $this->cache->delete('default_subscription_plan');
        $this->plan_cache = [];
    }
}
?>
