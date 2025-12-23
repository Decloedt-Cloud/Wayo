<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron Controller
 * 
 * Handles scheduled tasks via CLI or protected HTTP endpoints
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class Cron extends CI_Controller {

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load config
        $this->config->load('fxrates');
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
        if ($this->input->is_cli_request()) {
            return true;
        }

        // For HTTP requests, verify cron token
        $token = $this->input->get('cron_token', true);
        
        if (empty($token)) {
            $token = $this->input->get_request_header('X-Cron-Token', true);
        }

        $expected_token = $this->config->item('fxrates_cron_token');
        
        if (empty($expected_token) || $expected_token === 'change-this-cron-token') {
            log_message('warning', 'Cron: Using default cron token - please configure FXRATES_CRON_TOKEN');
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
        if ($this->input->is_cli_request()) {
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
            $this->output->set_status_header($status);
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode($data, JSON_PRETTY_PRINT));
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
            $this->load->library('FxRatesService', null, 'fxService');
            
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
            $this->load->library('FxRatesService', null, 'fxService');
            
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
            $days_to_keep = $this->input->get('days', true);
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
            $this->load->model('FxRates_model', 'fxrates_model');
            
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
            $this->load->library('FxRatesService', null, 'fxService');
            
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
            $this->load->library('FxRatesService', null, 'fxService');
            
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
            'message' => 'Available FX Rates cron jobs',
            'details' => [
                'fx_fetch_daily' => 'Fetch and store daily FX rates',
                'fx_health' => 'Check FX rates service health',
                'fx_cleanup' => 'Delete old FX rate records',
                'fx_clear_cache' => 'Clear FX rates cache',
                'fx_test_api' => 'Test API connection'
            ]
        ]);
    }
}

