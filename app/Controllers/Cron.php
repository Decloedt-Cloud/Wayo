<?php

namespace App\Controllers;


/**
 * Cron Controller
 * 
 * Handles scheduled tasks via CLI or protected HTTP endpoints
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class Cron extends BaseController {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Verify cron access
     * Allows CLI access or HTTP with valid token
     * 
     * @return bool True if authorized
     */
    protected function verifyCronAccess()
    {
        // Always allow CLI access
        if ($this->request->isCLI()) {
            return true;
        }

        // For HTTP requests, verify cron token
        $token = $this->request->getGet('cron_token');
        
        if (empty($token)) {
            $token = $this->request->getHeaderLine('X-Cron-Token', true);
        }

        $expected_token = config('FxRates')->fxrates_cron_token;
        
        if (empty($expected_token) || $expected_token === 'change-this-cron-token') {
            log_message('debug', 'Cron: Using default cron token - please configure FXRATES_CRON_TOKEN');
        }

        if (empty($token) || $token !== $expected_token) {
            $this->outputResult([
                'success' => false,
                'message' => 'Unauthorized - Invalid or missing cron token'
            ], 401);
            return false;
        }

        return true;
    }

    /**
     * Output result in appropriate format
     * 
     * @param array $data Result data
     * @param int $status HTTP status code
     */
    protected function outputResult($data, $status = 200)
    {
        if ($this->request->isCLI()) {
            // CLI output
            $prefix = $data['success'] ?? false ? '[OK]' : '[ERROR]';
            echo $prefix . ' ' . ($data['message'] ?? 'Unknown') . PHP_EOL;
            
            if (!empty($data['rates'])) {
                echo 'Rates:' . PHP_EOL;
                foreach ($data['rates'] as $currency => $rate) {
                    echo "  {$currency}: {$rate}" . PHP_EOL;
                }
            }
            
            if (!empty($data['details'])) {
                echo 'Details:' . PHP_EOL;
                foreach ($data['details'] as $key => $value) {
                    echo "  {$key}: " . (is_array($value) ? json_encode($value) : $value) . PHP_EOL;
                }
            }
        } else {
            // HTTP JSON output
            $this->response
                ->setStatusCode($status)
                ->setContentType('application/json')
                ->setBody(json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Fetch daily FX rates
     * 
     * CLI: php index.php cron fx_fetch_daily
     * HTTP: GET /cron/fx_fetch_daily?cron_token=xxx
     * 
     * Cron example: 5 2 * * * cd /path/to/app && php index.php cron fx_fetch_daily >> /var/log/fxrates.log 2>&1
     */
    public function fx_fetch_daily()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        log_message('info', 'Cron::fx_fetch_daily - Starting FX rates fetch');
        
        $start_time = microtime(true);

        try {
            // Load service
            // TODO: Replace with $this->fxService = Config\Services::FxRatesService();
            
            // Fetch and store rates
            $result = $this->fxService->fetchAndStoreTodayRates();
            
            $execution_time = round(microtime(true) - $start_time, 3);
            $result['execution_time'] = $execution_time . 's';
            
            if ($result['success']) {
                log_message('info', 'Cron::fx_fetch_daily - Completed successfully in ' . $execution_time . 's');
                $this->outputResult($result);
            } else {
                log_message('error', 'Cron::fx_fetch_daily - Failed: ' . $result['message']);
                $this->outputResult($result, 500);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Cron::fx_fetch_daily - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'date' => date('Y-m-d')
            ], 500);
        }
    }

    /**
     * FX rates health check
     * 
     * CLI: php index.php cron fx_health
     * HTTP: GET /cron/fx_health?cron_token=xxx
     */
    public function fx_health()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        try {
            // TODO: Replace with $this->fxService = Config\Services::FxRatesService();
            
            $health = $this->fxService->getHealthStatus();
            
            // Determine status
            $status = 'healthy';
            if (!$health['api_key_configured']) {
                $status = 'degraded';
            }
            if (!$health['has_today_rates'] && !$health['latest_rate_date']) {
                $status = 'unhealthy';
            }

            $this->outputResult([
                'success' => ($status !== 'unhealthy'),
                'message' => 'FX Rates service status: ' . $status,
                'details' => $health
            ], $status === 'unhealthy' ? 503 : 200);
            
        } catch (Exception $e) {
            log_message('error', 'Cron::fx_health - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup old FX rates records
     * 
     * CLI: php index.php cron fx_cleanup [days_to_keep]
     * HTTP: GET /cron/fx_cleanup?cron_token=xxx&days=365
     * 
     * @param int $days_to_keep Number of days to keep (default: 365)
     */
    public function fx_cleanup($days_to_keep = null)
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        // Get days parameter
        if ($days_to_keep === null) {
            $days_to_keep = $this->request->getGet('days');
        }
        
        $days_to_keep = (int)($days_to_keep ?: 365);
        
        if ($days_to_keep < 30) {
            $this->outputResult([
                'success' => false,
                'message' => 'days_to_keep must be at least 30'
            ], 400);
            return;
        }

        try {
            $this->loadModel('FxRates_model', 'fxrates_model');
            
            $deleted = $this->fxrates_model->cleanup_old_records($days_to_keep);
            
            $this->outputResult([
                'success' => true,
                'message' => "Cleanup completed. Deleted {$deleted} records older than {$days_to_keep} days.",
                'deleted_count' => $deleted
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Cron::fx_cleanup - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear FX rates cache
     * 
     * CLI: php index.php cron fx_clear_cache
     * HTTP: GET /cron/fx_clear_cache?cron_token=xxx
     */
    public function fx_clear_cache()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        try {
            // TODO: Replace with $this->fxService = Config\Services::FxRatesService();
            
            $this->fxService->clearCache();
            
            $this->outputResult([
                'success' => true,
                'message' => 'FX rates cache cleared successfully'
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'Cron::fx_clear_cache - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test FX rates API connection
     * 
     * CLI: php index.php cron fx_test_api
     * HTTP: GET /cron/fx_test_api?cron_token=xxx
     */
    public function fx_test_api()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        try {
            // TODO: Replace with $this->fxService = Config\Services::FxRatesService();
            
            // Test API fetch without storing
            $rates = $this->fxService->fetchFromProvider();
            
            if ($rates !== false) {
                $this->outputResult([
                    'success' => true,
                    'message' => 'API connection successful',
                    'rates' => [
                        'EUR' => $rates['conversion_rates']['EUR'],
                        'MAD' => $rates['conversion_rates']['MAD'],
                        'AED' => $rates['conversion_rates']['AED']
                    ]
                ]);
            } else {
                $this->outputResult([
                    'success' => false,
                    'message' => 'API connection failed: ' . $this->fxService->getLastError(),
                    'http_code' => $this->fxService->getLastHttpCode()
                ], 500);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Cron::fx_test_api - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate renewal invoices for expired subscriptions
     *
     * CLI: php index.php cron subscription_generate_invoices
     * HTTP: GET /cron/subscription_generate_invoices?cron_token=xxx
     */
    public function subscription_generate_invoices()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        log_message('info', 'Cron::subscription_generate_invoices - Starting renewal invoice generation');

        $start_time = microtime(true);

        try {
            // TODO: Replace with $this->subscriptionService = Config\Services::SubscriptionService();

            // Get all schools that need renewal invoices
            $schools_needing_invoices = $this->get_schools_needing_invoices();

            $processed = 0;
            $errors = 0;

            foreach ($schools_needing_invoices as $school) {
                $result = $this->subscriptionService->ensureRenewalInvoice($school['id']);

                if ($result['success']) {
                    $processed++;
                    log_message('info', "Generated renewal invoice for school #{$school['id']}: " . $result['message']);
                } else {
                    $errors++;
                    log_message('error', "Failed to generate renewal invoice for school #{$school['id']}: " . $result['message']);
                }
            }

            $execution_time = round(microtime(true) - $start_time, 3);

            $this->outputResult([
                'success' => true,
                'message' => "Invoice generation completed in {$execution_time}s. Processed: {$processed}, Errors: {$errors}",
                'processed' => $processed,
                'errors' => $errors,
                'execution_time' => $execution_time . 's'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Cron::subscription_generate_invoices - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check subscription health and status
     *
     * CLI: php index.php cron subscription_health
     * HTTP: GET /cron/subscription_health?cron_token=xxx
     */
    public function subscription_health()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        try {
            // TODO: Replace with $this->subscriptionService = Config\Services::SubscriptionService();
            $this->loadModel('Crud_model', 'crud_model');

            // Get all schools
            $schools = $this->crud_model->get_schools();

            $stats = [
                'total_schools' => count($schools),
                'trialing' => 0,
                'active' => 0,
                'past_due' => 0,
                'suspended' => 0,
                'canceled' => 0,
                'needs_invoice' => 0
            ];

            foreach ($schools as $school) {
                $status = $this->subscriptionService->getSubscriptionStatus($school['id']);
                $stats[$status['status']]++;

                if ($this->subscriptionService->needsRenewalInvoice($school['id'])) {
                    $stats['needs_invoice']++;
                }
            }

            $health_status = 'healthy';
            if ($stats['suspended'] > 0) {
                $health_status = 'warning';
            }
            if ($stats['needs_invoice'] > $stats['total_schools'] * 0.1) { // More than 10% need invoices
                $health_status = 'critical';
            }

            $this->outputResult([
                'success' => true,
                'message' => "Subscription health: {$health_status}",
                'details' => $stats
            ], $health_status === 'critical' ? 500 : 200);

        } catch (Exception $e) {
            log_message('error', 'Cron::subscription_health - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schools that need renewal invoices
     */
    private function get_schools_needing_invoices()
    {
        $this->subscriptionService = new \App\Libraries\SubscriptionService();
        $this->loadModel('Crud_model', 'crud_model');

        $schools = $this->crud_model->get_schools();
        $schools_needing_invoices = [];

        foreach ($schools as $school) {
            if ($this->subscriptionService->needsRenewalInvoice($school['id'])) {
                $schools_needing_invoices[] = $school;
            }
        }

        return $schools_needing_invoices;
    }

    /**
     * Run subscription system maintenance
     *
     * CLI: php index.php cron subscription_maintenance
     * HTTP: GET /cron/subscription_maintenance?cron_token=xxx
     */
    public function subscription_maintenance()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        log_message('info', 'Cron::subscription_maintenance - Starting maintenance');

        $start_time = microtime(true);

        try {
            // TODO: Replace with $this->maintenanceService = Config\Services::SubscriptionMaintenanceService();

            // Run maintenance
            $results = $this->maintenanceService->runMaintenance();

            $execution_time = round(microtime(true) - $start_time, 3);

            $message = "Maintenance completed in {$execution_time}s. " .
                      "Found: {$results['issues_found']}, Fixed: {$results['issues_fixed']}, " .
                      "Schools: {$results['schools_processed']}";

            if (!empty($results['errors'])) {
                $message .= ". Errors: " . implode(', ', $results['errors']);
                log_message('error', 'Cron::subscription_maintenance - ' . $message);
                $this->outputResult([
                    'success' => false,
                    'message' => $message,
                    'details' => $results
                ], 500);
            } else {
                log_message('info', 'Cron::subscription_maintenance - ' . $message);
                $this->outputResult([
                    'success' => true,
                    'message' => $message,
                    'details' => $results
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Cron::subscription_maintenance - Exception: ' . $e->getMessage());
            $this->outputResult([
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Default index - show available cron jobs
     */
    public function index()
    {
        // Verify access
        if (!$this->verifyCronAccess()) {
            return;
        }

        $this->outputResult([
            'success' => true,
            'message' => 'Available cron jobs',
            'details' => [
                // FX Rates jobs
                'fx_fetch_daily' => 'Fetch and store daily FX rates',
                'fx_health' => 'Check FX rates service health',
                'fx_cleanup' => 'Delete old FX rate records',
                'fx_clear_cache' => 'Clear FX rates cache',
                'fx_test_api' => 'Test API connection',
                // Subscription jobs
                'subscription_generate_invoices' => 'Generate renewal invoices for expired subscriptions',
                'subscription_health' => 'Check subscription system health',
                'subscription_maintenance' => 'Run automatic subscription system maintenance'
            ]
        ]);
    }
}

