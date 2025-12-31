<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FX Rates Service
 * 
 * Service layer for fetching, caching, and managing FX rates
 * Uses ExchangeRate-API v6
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class FxRatesService {

    /**
     * CodeIgniter instance
     * @var CI_Controller
     */
    protected $CI;

    /**
     * Configuration
     * @var array
     */
    protected $config = [];

    /**
     * Cache instance
     * @var CI_Cache
     */
    protected $cache;

    /**
     * Last error message
     * @var string
     */
    protected $last_error = '';

    /**
     * Last HTTP response code
     * @var int
     */
    protected $last_http_code = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        
        // Load configuration (sans namespace pour accès direct)
        $this->CI->config->load('fxrates');
        
        // Charger chaque config item directement
        $this->config = [
            'fxrates_api_key' => $this->CI->config->item('fxrates_api_key') ?: '',
            'fxrates_api_url' => $this->CI->config->item('fxrates_api_url') ?: 'https://v6.exchangerate-api.com/v6/',
            'fxrates_base_currency' => $this->CI->config->item('fxrates_base_currency') ?: 'USD',
            'fxrates_currencies' => $this->CI->config->item('fxrates_currencies') ?: ['USD', 'EUR', 'MAD', 'AED'],
            'fxrates_cache_duration' => $this->CI->config->item('fxrates_cache_duration') ?: 600,
            'fxrates_cache_prefix' => $this->CI->config->item('fxrates_cache_prefix') ?: 'fxrates_',
            'fxrates_api_timeout' => $this->CI->config->item('fxrates_api_timeout') ?: 30,
            'fxrates_api_connect_timeout' => $this->CI->config->item('fxrates_api_connect_timeout') ?: 10,
            'fxrates_store_raw_json' => $this->CI->config->item('fxrates_store_raw_json') !== null ? $this->CI->config->item('fxrates_store_raw_json') : true,
            'fxrates_timezone' => $this->CI->config->item('fxrates_timezone') ?: 'UTC',
            'fxrates_api_token' => $this->CI->config->item('fxrates_api_token') ?: '',
            'fxrates_cron_token' => $this->CI->config->item('fxrates_cron_token') ?: ''
        ];
        
        // Load model
        $this->CI->load->model('FxRates_model', 'fxrates_model');
        
        // Load cache driver
        $this->CI->load->driver('cache', ['adapter' => 'file', 'backup' => 'file']);
        $this->cache = $this->CI->cache;
        
        // Set timezone
        date_default_timezone_set($this->config['fxrates_timezone']);
    }

    /**
     * Fetch rates from ExchangeRate-API provider
     * 
     * @return array|false Rates array or false on failure
     */
    public function fetchFromProvider()
    {
        $api_key = $this->getConfig('fxrates_api_key');
        
        if (empty($api_key)) {
            $this->last_error = 'API key not configured';
            log_message('error', 'FxRatesService::fetchFromProvider - ' . $this->last_error);
            return false;
        }

        $base_currency = $this->getConfig('fxrates_base_currency');
        $url = $this->getConfig('fxrates_api_url') . $api_key . '/latest/' . $base_currency;

        log_message('debug', 'FxRatesService::fetchFromProvider - Fetching from: ' . preg_replace('/[a-f0-9]{20,}/', '***API_KEY***', $url));

        // Initialize cURL
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->getConfig('fxrates_api_timeout'),
            CURLOPT_CONNECTTIMEOUT => $this->getConfig('fxrates_api_connect_timeout'),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: SchoolManagement-FxRates/1.0'
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ]);

        $response = curl_exec($ch);
        $this->last_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        curl_close($ch);

        // Handle cURL errors
        if ($curl_errno !== 0) {
            $this->last_error = "cURL error ({$curl_errno}): {$curl_error}";
            log_message('error', 'FxRatesService::fetchFromProvider - ' . $this->last_error);
            return false;
        }

        // Check HTTP status
        if ($this->last_http_code !== 200) {
            $this->last_error = "HTTP error: {$this->last_http_code}";
            log_message('error', 'FxRatesService::fetchFromProvider - ' . $this->last_error);
            return false;
        }

        // Parse JSON
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->last_error = 'Invalid JSON response: ' . json_last_error_msg();
            log_message('error', 'FxRatesService::fetchFromProvider - ' . $this->last_error);
            return false;
        }

        // Validate response structure
        if (!$this->validateApiResponse($data)) {
            return false;
        }

        log_message('info', 'FxRatesService::fetchFromProvider - Successfully fetched rates for ' . $data['base_code']);

        return [
            'base_code' => $data['base_code'],
            'time_last_update_utc' => $data['time_last_update_utc'] ?? null,
            'time_next_update_utc' => $data['time_next_update_utc'] ?? null,
            'conversion_rates' => $data['conversion_rates'],
            'raw_json' => $response
        ];
    }

    /**
     * Validate API response structure
     * 
     * @param array $data Decoded API response
     * @return bool True if valid
     */
    protected function validateApiResponse($data)
    {
        // Check result status
        if (!isset($data['result']) || $data['result'] !== 'success') {
            $error_type = $data['error-type'] ?? 'unknown';
            $this->last_error = "API error: {$error_type}";
            log_message('error', 'FxRatesService::validateApiResponse - ' . $this->last_error);
            return false;
        }

        // Check base code
        if (empty($data['base_code'])) {
            $this->last_error = 'Missing base_code in response';
            log_message('error', 'FxRatesService::validateApiResponse - ' . $this->last_error);
            return false;
        }

        // Check conversion_rates exists
        if (empty($data['conversion_rates']) || !is_array($data['conversion_rates'])) {
            $this->last_error = 'Missing or invalid conversion_rates in response';
            log_message('error', 'FxRatesService::validateApiResponse - ' . $this->last_error);
            return false;
        }

        // Check required currencies
        $required_currencies = $this->getConfig('fxrates_currencies');
        foreach ($required_currencies as $currency) {
            if (!isset($data['conversion_rates'][$currency])) {
                $this->last_error = "Missing required currency: {$currency}";
                log_message('error', 'FxRatesService::validateApiResponse - ' . $this->last_error);
                return false;
            }
        }

        return true;
    }

    /**
     * Upsert daily rates into database
     * 
     * @param string $date Date in Y-m-d format
     * @param array $rates Rates array from fetchFromProvider()
     * @param string|null $raw_json Raw JSON response
     * @return bool Success status
     */
    public function upsertDailyRates($date, $rates, $raw_json = null)
    {
        if (empty($rates) || empty($rates['conversion_rates'])) {
            log_message('error', 'FxRatesService::upsertDailyRates - Invalid rates data');
            return false;
        }

        $data = [
            'rate_date' => $date,
            'base_code' => $rates['base_code'] ?? 'USD',
            'usd' => $rates['conversion_rates']['USD'] ?? 1.000000,
            'eur' => $rates['conversion_rates']['EUR'] ?? 0,
            'mad' => $rates['conversion_rates']['MAD'] ?? 0,
            'aed' => $rates['conversion_rates']['AED'] ?? 0,
            'gbp' => $rates['conversion_rates']['GBP'] ?? 0,
            'source' => 'exchange-rate-api',
            'fetched_at' => date('Y-m-d H:i:s'),
            'status' => 'ok'
        ];

        // Optionally store raw JSON
        if ($this->getConfig('fxrates_store_raw_json') && $raw_json) {
            $data['raw_json'] = $raw_json;
        }

        $result = $this->CI->fxrates_model->upsert($data);

        if ($result !== false) {
            // Clear cache for this date
            $this->clearCache($date);
            $this->clearCache('latest');
            $this->clearCache('today');
            
            log_message('info', "FxRatesService::upsertDailyRates - Successfully upserted rates for {$date}");
            return true;
        }

        log_message('error', "FxRatesService::upsertDailyRates - Failed to upsert rates for {$date}");
        return false;
    }

    /**
     * Get rates by date with caching
     * 
     * @param string $date Date in Y-m-d format
     * @return array|null Formatted rates or null
     */
    public function getRatesByDate($date)
    {
        $cache_key = $this->getCacheKey($date);
        
        // Try cache first
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) {
            log_message('debug', "FxRatesService::getRatesByDate - Cache hit for {$date}");
            return $cached;
        }

        // Get from database
        $record = $this->CI->fxrates_model->get_by_date($date);
        
        if ($record) {
            $formatted = $this->CI->fxrates_model->format_for_response($record);
            
            // Cache the result
            $this->cache->save($cache_key, $formatted, $this->getConfig('fxrates_cache_duration'));
            
            return $formatted;
        }

        return null;
    }

    /**
     * Get latest available rates with caching
     * 
     * @return array|null Formatted rates or null
     */
    public function getLatestRates()
    {
        $cache_key = $this->getCacheKey('latest');
        
        // Try cache first
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) {
            log_message('debug', 'FxRatesService::getLatestRates - Cache hit');
            return $cached;
        }

        // Get from database
        $record = $this->CI->fxrates_model->get_latest();
        
        if ($record) {
            $formatted = $this->CI->fxrates_model->format_for_response($record);
            
            // Cache the result
            $this->cache->save($cache_key, $formatted, $this->getConfig('fxrates_cache_duration'));
            
            return $formatted;
        }

        return null;
    }

    /**
     * Get today's rates with fallback to latest if not available
     * 
     * @return array Formatted rates with stale flag if using fallback
     */
    public function getTodayRates()
    {
        $today = date('Y-m-d');
        $cache_key = $this->getCacheKey('today');
        
        // Try cache first
        $cached = $this->cache->get($cache_key);
        if ($cached !== false) {
            log_message('debug', 'FxRatesService::getTodayRates - Cache hit');
            return $cached;
        }

        // Try to get today's rates
        $rates = $this->getRatesByDate($today);
        
        if ($rates && !$rates['stale']) {
            // Cache today's rates
            $this->cache->save($cache_key, $rates, $this->getConfig('fxrates_cache_duration'));
            return $rates;
        }

        // Fallback to latest available rates
        $latest = $this->getLatestRates();
        
        if ($latest) {
            // Mark as stale since it's not today's rates
            $latest['stale'] = true;
            $latest['warning'] = 'Using last known rates - today\'s rates not yet available';
            
            // Cache with shorter duration for stale data
            $this->cache->save($cache_key, $latest, 60); // 1 minute for stale
            
            return $latest;
        }

        // No rates available at all
        return [
            'base' => 'USD',
            'date' => $today,
            'rates' => null,
            'stale' => true,
            'source' => 'none',
            'error' => 'No rates available'
        ];
    }

    /**
     * Get rates for a date range
     * 
     * @param string $start_date Start date in Y-m-d format
     * @param string $end_date End date in Y-m-d format
     * @return array Array of formatted rate records
     */
    public function getRange($start_date, $end_date)
    {
        // Validate dates
        if (!$this->isValidDate($start_date) || !$this->isValidDate($end_date)) {
            log_message('error', 'FxRatesService::getRange - Invalid date format');
            return [];
        }

        if ($start_date > $end_date) {
            log_message('error', 'FxRatesService::getRange - Start date is after end date');
            return [];
        }

        $records = $this->CI->fxrates_model->get_range($start_date, $end_date);
        
        $result = [];
        foreach ($records as $record) {
            $result[] = $this->CI->fxrates_model->format_for_response($record);
        }

        return $result;
    }

    /**
     * Fetch and store today's rates (for cron job)
     * 
     * @return array Result with status and message
     */
    public function fetchAndStoreTodayRates()
    {
        $today = date('Y-m-d');
        
        log_message('info', "FxRatesService::fetchAndStoreTodayRates - Starting fetch for {$today}");

        // Fetch from provider
        $rates = $this->fetchFromProvider();

        if ($rates === false) {
            // Mark existing rates as stale if fetch failed
            $this->CI->fxrates_model->update_status($today, 'stale');
            
            return [
                'success' => false,
                'date' => $today,
                'message' => $this->last_error,
                'http_code' => $this->last_http_code
            ];
        }

        // Store rates
        $raw_json = $rates['raw_json'] ?? null;
        $success = $this->upsertDailyRates($today, $rates, $raw_json);

        if ($success) {
            return [
                'success' => true,
                'date' => $today,
                'message' => 'Rates fetched and stored successfully',
                'rates' => [
                    'EUR' => $rates['conversion_rates']['EUR'],
                    'MAD' => $rates['conversion_rates']['MAD'],
                    'AED' => $rates['conversion_rates']['AED'],
                    'GBP' => $rates['conversion_rates']['GBP']
                ]
            ];
        }

        return [
            'success' => false,
            'date' => $today,
            'message' => 'Failed to store rates in database'
        ];
    }

    /**
     * Convert amount between currencies
     * 
     * @param float $amount Amount to convert
     * @param string $from Source currency code
     * @param string $to Target currency code
     * @param string|null $date Optional date for historical rate
     * @return float|false Converted amount or false on failure
     */
    public function convert($amount, $from, $to, $date = null)
    {
        // Get rates
        if ($date) {
            $rates_data = $this->getRatesByDate($date);
        } else {
            $rates_data = $this->getTodayRates();
        }

        if (empty($rates_data['rates'])) {
            log_message('error', 'FxRatesService::convert - No rates available');
            return false;
        }

        $rates = $rates_data['rates'];
        $from = strtoupper($from);
        $to = strtoupper($to);

        if (!isset($rates[$from]) || !isset($rates[$to]) || $rates[$from] === null || $rates[$to] === null) {
            log_message('error', "FxRatesService::convert - Currency not available or zero rate: {$from} ({$rates[$from]}) or {$to} ({$rates[$to]})");
            return false;
        }

        // Convert via base currency (USD)
        // amount_in_usd = amount / from_rate
        // amount_in_target = amount_in_usd * to_rate
        $amount_in_usd = $amount / $rates[$from];
        $converted = $amount_in_usd * $rates[$to];

        return round($converted, 6);
    }

    /**
     * Get configuration value
     * 
     * @param string $key Config key (without prefix)
     * @return mixed Config value
     */
    public function getConfig($key)
    {
        // Try with full key first
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        
        // Try CI config
        $value = $this->CI->config->item($key, 'fxrates');
        if ($value !== null) {
            return $value;
        }

        return null;
    }

    /**
     * Get cache key with prefix
     * 
     * @param string $suffix Key suffix
     * @return string Full cache key
     */
    protected function getCacheKey($suffix)
    {
        return $this->getConfig('fxrates_cache_prefix') . $suffix;
    }

    /**
     * Clear cache for specific key
     * 
     * @param string $suffix Key suffix
     * @return bool Success status
     */
    public function clearCache($suffix = null)
    {
        if ($suffix) {
            return $this->cache->delete($this->getCacheKey($suffix));
        }
        
        // Clear all fx rates cache
        $this->cache->delete($this->getCacheKey('today'));
        $this->cache->delete($this->getCacheKey('latest'));
        
        return true;
    }

    /**
     * Validate date format
     * 
     * @param string $date Date string
     * @return bool True if valid Y-m-d format
     */
    protected function isValidDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Get last error message
     * 
     * @return string Last error
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * Get last HTTP response code
     * 
     * @return int HTTP code
     */
    public function getLastHttpCode()
    {
        return $this->last_http_code;
    }

    /**
     * Get service health status
     * 
     * @return array Health status information
     */
    public function getHealthStatus()
    {
        $has_api_key = !empty($this->getConfig('fxrates_api_key'));
        $has_today_rates = $this->CI->fxrates_model->has_today_rates();
        $latest = $this->CI->fxrates_model->get_latest();
        $total_records = $this->CI->fxrates_model->count_all();

        return [
            'api_key_configured' => $has_api_key,
            'has_today_rates' => $has_today_rates,
            'latest_rate_date' => $latest ? $latest['rate_date'] : null,
            'latest_status' => $latest ? $latest['status'] : null,
            'total_records' => $total_records,
            'cache_duration' => $this->getConfig('fxrates_cache_duration'),
            'timezone' => $this->getConfig('fxrates_timezone')
        ];
    }
}

