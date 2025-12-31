<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stripe Webhook Controller
 *
 * Handles Stripe webhook events for subscription payments
 *
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class StripeWebhook extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        // Load required libraries and services
        $this->load->library('SubscriptionService', null, 'subscriptionService');
        $this->load->model('Invoice_model', 'invoice_model');

        // Disable CSRF for webhook endpoint
        $this->config->set_item('csrf_protection', false);

        // Set JSON content type for responses
        $this->output->set_content_type('application/json');

        // Enable caching for webhook processing
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    /**
     * Main webhook endpoint
     *
     * POST /stripe_webhook
     */
    public function index()
    {
        $input = file_get_contents('php://input');
        $event = null;

        try {
            // Parse JSON first to get event data
            $event_data = json_decode($input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'StripeWebhook: Invalid JSON payload');
                $this->output->set_status_header(400);
                echo json_encode(['error' => 'Invalid JSON']);
                return;
            }

            // Get Stripe signature from headers
            $signature = $this->input->get_request_header('Stripe-Signature');
            $endpoint_secret = $this->config->item('stripe_webhook_secret');

            if (empty($endpoint_secret)) {
                log_message('error', 'StripeWebhook: Webhook secret not configured');
                $this->output->set_status_header(500);
                echo json_encode(['error' => 'Webhook secret not configured']);
                return;
            }

            // Check if webhook already processed (idempotency)
            $event_id = $event_data['id'] ?? null;
            if ($event_id && $this->isWebhookProcessed($event_id)) {
                log_message('info', "StripeWebhook: Event {$event_id} already processed, skipping");
                echo json_encode(['status' => 'success', 'message' => 'Event already processed']);
                return;
            }

            // Verify webhook signature
            $tolerance = 300; // 5 minutes tolerance
            $event = $this->verifyWebhookSignature($input, $signature, $endpoint_secret, $tolerance);

            if (!$event) {
                log_message('error', 'StripeWebhook: Invalid signature');
                $this->output->set_status_header(400);
                echo json_encode(['error' => 'Invalid signature']);
                return;
            }

            log_message('info', 'StripeWebhook: Processing event: ' . $event['type'] . ' (ID: ' . $event['id'] . ')');

            // Process the event
            $result = $this->subscriptionService->processStripeWebhook($event);

            // Mark as processed if successful
            if ($result['success'] && $event_id) {
                $this->markWebhookProcessed($event_id);
            }

            if ($result['success']) {
                log_message('info', 'StripeWebhook: Successfully processed event: ' . $result['message']);
                echo json_encode(['status' => 'success', 'message' => $result['message']]);
            } else {
                log_message('error', 'StripeWebhook: Failed to process event: ' . $result['message']);
                $this->output->set_status_header(500);
                echo json_encode(['error' => $result['message']]);
            }

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: Exception - ' . $e->getMessage());
            $this->output->set_status_header(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }

    /**
     * Verify Stripe webhook signature
     *
     * @param string $payload Raw payload
     * @param string $signature Stripe signature header
     * @param string $secret Webhook endpoint secret
     * @param int $tolerance Timestamp tolerance in seconds
     * @return array|null Event data or null if invalid
     */
    private function verifyWebhookSignature($payload, $signature, $secret, $tolerance = 300)
    {
        if (empty($signature) || empty($secret)) {
            return null;
        }

        // Parse signature header
        $signature_parts = explode(',', $signature);
        $timestamp = null;
        $signatures = [];

        foreach ($signature_parts as $part) {
            $pair = explode('=', $part, 2);
            if (count($pair) !== 2) {
                continue;
            }

            list($key, $value) = $pair;

            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v1') {
                $signatures[] = $value;
            }
        }

        if (empty($timestamp) || empty($signatures)) {
            return null;
        }

        // Check timestamp tolerance
        $current_timestamp = time();
        if (($current_timestamp - $timestamp) > $tolerance) {
            log_message('error', 'StripeWebhook: Timestamp outside tolerance window');
            return null;
        }

        // Create signed payload
        $signed_payload = $timestamp . '.' . $payload;

        // Verify signature
        $expected_signature = hash_hmac('sha256', $signed_payload, $secret);

        foreach ($signatures as $sig) {
            if (hash_equals($expected_signature, $sig)) {
                // Signature valid, construct event
                $event_data = json_decode($payload, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    log_message('error', 'StripeWebhook: Invalid JSON payload');
                    return null;
                }
                return $event_data;
            }
        }

        return null;
    }

    /**
     * Test endpoint for webhook verification
     *
     * GET /stripe_webhook/test
     */
    public function test()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development') {
            show_404();
        }

        // Mock event data for testing
        $mock_event = [
            'id' => 'evt_test_' . time(),
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_' . time(),
                    'amount' => 79000, // 790 EUR in cents
                    'currency' => 'eur',
                    'status' => 'succeeded'
                ]
            ],
            'created' => time()
        ];

        echo "<h1>Stripe Webhook Test</h1>";
        echo "<p>Endpoint is accessible</p>";
        echo "<p>Mock event would be:</p>";
        echo "<pre>" . json_encode($mock_event, JSON_PRETTY_PRINT) . "</pre>";

        // Test service availability
        if (isset($this->subscriptionService)) {
            echo "<p>✓ SubscriptionService loaded successfully</p>";
        } else {
            echo "<p>✗ SubscriptionService not loaded</p>";
        }
    }

    /**
     * Check if webhook event has already been processed
     */
    private function isWebhookProcessed($event_id)
    {
        $cache_key = "webhook_processed_{$event_id}";
        return $this->cache->get($cache_key) !== false;
    }

    /**
     * Mark webhook event as processed
     */
    private function markWebhookProcessed($event_id)
    {
        $cache_key = "webhook_processed_{$event_id}";
        // Cache for 24 hours to prevent duplicate processing
        $this->cache->save($cache_key, true, 86400);
    }
}
