<?php

namespace App\Controllers\api;


/**
 * FX Rates API Controller
 * 
 * Provides REST API endpoints for FX rate data
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class FxRates extends BaseController {

    /**
     * FxRatesService instance
     * @var FxRatesService
     */
    protected $fxService;
    protected $fxratesConfig;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->fxratesConfig = config('FxRates');
        
        // CORS headers (adjust as needed for your security requirements)
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-API-Token');
        
        // Handle preflight OPTIONS request
        if ($this->request->getMethod() === 'options') {
            $this->response->setStatusCode(200);
            return;
        }
    }

    /**
     * Verify API token for protected endpoints
     * 
     * @return bool True if authorized
     */
    protected function verifyApiToken()
    {
        // Get token from header or query parameter
        $token = $this->request->getHeaderLine('X-API-Token', true);
        
        if (empty($token)) {
            $token = $this->request->getGet('api_token');
        }

        $expected_token = $this->fxratesConfig->fxrates_api_token ?? '';
        
        if (empty($expected_token) || $expected_token === 'change-this-token-in-production') {
            log_message('debug', 'FxRates API: Using default token - please configure FXRATES_API_TOKEN');
        }

        if (empty($token) || $token !== $expected_token) {
            $this->sendError('Unauthorized - Invalid or missing API token', 401);
            return false;
        }

        return true;
    }

    /**
     * Send JSON success response
     * 
     * @param mixed $data Response data
     * @param int $status HTTP status code
     */
    protected function sendSuccess($data, $status = 200)
    {
        $this->response
            ->setStatusCode($status)
            ->setContentType('application/json')
            ->setBody(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * Send JSON error response
     * 
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param array $extra Additional data
     */
    protected function sendError($message, $status = 400, $extra = [])
    {
        $response = array_merge([
            'error' => true,
            'message' => $message,
            'status' => $status
        ], $extra);

        $this->response
            ->setStatusCode($status)
            ->setContentType('application/json')
            ->setBody(json_encode($response, JSON_PRETTY_PRINT));
    }

    /**
     * GET /api/fx/today
     * 
     * Get today's exchange rates
     * Returns latest stored rates if today's not available (with stale flag)
     */
    public function today()
    {
        // Verify token
        if (!$this->verifyApiToken()) {
            return;
        }

        try {
            $rates = $this->fxService->getTodayRates();
            
            if (empty($rates['rates'])) {
                $this->sendError('No rates available', 503, [
                    'stale' => true,
                    'retry_after' => 3600
                ]);
                return;
            }

            $this->sendSuccess($rates);
            
        } catch (Exception $e) {
            log_message('error', 'FxRates::today - Exception: ' . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }

    /**
     * GET /api/fx/latest
     * 
     * Get the most recent available rates
     */
    public function latest()
    {
        // Verify token
        if (!$this->verifyApiToken()) {
            return;
        }

        try {
            $rates = $this->fxService->getLatestRates();
            
            if (empty($rates)) {
                $this->sendError('No rates available', 404);
                return;
            }

            $this->sendSuccess($rates);
            
        } catch (Exception $e) {
            log_message('error', 'FxRates::latest - Exception: ' . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }

    /**
     * GET /api/fx/date/{date}
     * 
     * Get rates for a specific date
     * 
     * @param string $date Date in Y-m-d format
     */
    public function date($date = null)
    {
        // Verify token
        if (!$this->verifyApiToken()) {
            return;
        }

        // Validate date parameter
        if (empty($date)) {
            $this->sendError('Date parameter is required', 400);
            return;
        }

        // Validate date format
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            $this->sendError('Invalid date format. Use YYYY-MM-DD', 400);
            return;
        }

        // Check date is not in future
        if ($date > date('Y-m-d')) {
            $this->sendError('Cannot get rates for future dates', 400);
            return;
        }

        try {
            $rates = $this->fxService->getRatesByDate($date);
            
            if (empty($rates)) {
                $this->sendError('No rates available for ' . $date, 404);
                return;
            }

            $this->sendSuccess($rates);
            
        } catch (Exception $e) {
            log_message('error', 'FxRates::date - Exception: ' . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }

    /**
     * GET /api/fx/range
     * 
     * Get rates for a date range
     * Query params: start=YYYY-MM-DD, end=YYYY-MM-DD
     */
    public function range()
    {
        // Verify token
        if (!$this->verifyApiToken()) {
            return;
        }

        // Get query parameters
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');

        // Validate parameters
        if (empty($start) || empty($end)) {
            $this->sendError('Both start and end date parameters are required', 400);
            return;
        }

        // Validate date formats
        $start_d = DateTime::createFromFormat('Y-m-d', $start);
        $end_d = DateTime::createFromFormat('Y-m-d', $end);
        
        if (!$start_d || $start_d->format('Y-m-d') !== $start) {
            $this->sendError('Invalid start date format. Use YYYY-MM-DD', 400);
            return;
        }
        
        if (!$end_d || $end_d->format('Y-m-d') !== $end) {
            $this->sendError('Invalid end date format. Use YYYY-MM-DD', 400);
            return;
        }

        // Validate date range
        if ($start > $end) {
            $this->sendError('Start date must be before or equal to end date', 400);
            return;
        }

        // Limit range to prevent abuse (max 365 days)
        $diff = $start_d->diff($end_d)->days;
        if ($diff > 365) {
            $this->sendError('Date range cannot exceed 365 days', 400);
            return;
        }

        try {
            $rates = $this->fxService->getRange($start, $end);
            
            $this->sendSuccess([
                'base' => 'USD',
                'start_date' => $start,
                'end_date' => $end,
                'count' => count($rates),
                'rates' => $rates
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'FxRates::range - Exception: ' . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }

    /**
     * GET /api/fx/convert
     * 
     * Convert amount between currencies
     * Query params: amount, from, to, date (optional)
     */
    public function convert()
    {
        // Verify token
        if (!$this->verifyApiToken()) {
            return;
        }

        // Get parameters
        $amount = $this->request->getGet('amount');
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');
        $date = $this->request->getGet('date');

        // Validate required parameters
        if (!is_numeric($amount) || $amount <= 0) {
            $this->sendError('Invalid or missing amount parameter', 400);
            return;
        }

        if (empty($from) || strlen($from) !== 3) {
            $this->sendError('Invalid or missing from currency code', 400);
            return;
        }

        if (empty($to) || strlen($to) !== 3) {
            $this->sendError('Invalid or missing to currency code', 400);
            return;
        }

        // Validate optional date
        if (!empty($date)) {
            $d = DateTime::createFromFormat('Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) {
                $this->sendError('Invalid date format. Use YYYY-MM-DD', 400);
                return;
            }
        }

        try {
            $converted = $this->fxService->convert((float)$amount, $from, $to, $date);
            
            if ($converted === false) {
                $this->sendError('Currency not supported or rates not available', 404);
                return;
            }

            $this->sendSuccess([
                'amount' => (float)$amount,
                'from' => strtoupper($from),
                'to' => strtoupper($to),
                'converted' => $converted,
                'date' => $date ?: date('Y-m-d'),
                'rate' => $converted / (float)$amount
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'FxRates::convert - Exception: ' . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }

    /**
     * GET /api/fx/health
     * 
     * Get service health status (for monitoring)
     */
    public function health()
    {
        // Verify token
        if (!$this->verifyApiToken()) {
            return;
        }

        try {
            $health = $this->fxService->getHealthStatus();
            
            // Determine overall status
            $status = 'healthy';
            if (!$health['api_key_configured']) {
                $status = 'degraded';
            }
            if (!$health['has_today_rates'] && !$health['latest_rate_date']) {
                $status = 'unhealthy';
            }

            $this->sendSuccess([
                'status' => $status,
                'timestamp' => date('c'),
                'details' => $health
            ]);
            
        } catch (Exception $e) {
            log_message('error', 'FxRates::health - Exception: ' . $e->getMessage());
            $this->sendError('Internal server error', 500);
        }
    }

    /**
     * Default index - show available endpoints
     */
    public function index()
    {
        $this->sendSuccess([
            'service' => 'FX Rates API',
            'version' => '1.0.0',
            'endpoints' => [
                'GET /api/fx/today' => 'Get today\'s exchange rates',
                'GET /api/fx/latest' => 'Get most recent available rates',
                'GET /api/fx/date/{date}' => 'Get rates for specific date (YYYY-MM-DD)',
                'GET /api/fx/range?start=YYYY-MM-DD&end=YYYY-MM-DD' => 'Get rates for date range',
                'GET /api/fx/convert?amount=X&from=USD&to=EUR' => 'Convert between currencies',
                'GET /api/fx/health' => 'Service health status'
            ],
            'authentication' => 'X-API-Token header or api_token query parameter required'
        ]);
    }
}

