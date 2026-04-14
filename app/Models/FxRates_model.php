<?php

namespace App\Models;

use CodeIgniter\Model;

class FxRates_model extends Model {
    protected $table            = 'fx_rates';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $DBGroup = 'default';

    /**
     * Table name
     */
    const TABLE = 'fx_rates_daily';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get rates by specific date
     * 
     * @param string $date Date in Y-m-d format
     * @param string $base_code Base currency code (default: USD)
     * @return array|null Rate record or null if not found
     */
    public function get_by_date($date, $base_code = 'USD')
    {
        return \db()->table(self::TABLE)
            ->where('rate_date', $date)
            ->where('base_code', $base_code)
            ->get()
            ->getRowArray();
    }

    /**
     * Get the latest available rates
     * 
     * @param string $base_code Base currency code (default: USD)
     * @return array|null Latest rate record or null if none exist
     */
    public function get_latest($base_code = 'USD')
    {
        return \db()->table(self::TABLE)
            ->where('base_code', $base_code)
            ->orderBy('rate_date', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
    }

    /**
     * Get rates for a date range
     * 
     * @param string $start_date Start date in Y-m-d format
     * @param string $end_date End date in Y-m-d format
     * @param string $base_code Base currency code (default: USD)
     * @return array Array of rate records
     */
    public function get_range($start_date, $end_date, $base_code = 'USD')
    {
        return \db()->table(self::TABLE)
            ->where('base_code', $base_code)
            ->where('rate_date >=', $start_date)
            ->where('rate_date <=', $end_date)
            ->orderBy('rate_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Insert or update daily rates (upsert)
     * 
     * @param array $data Rate data with keys: rate_date, base_code, eur, mad, aed, etc.
     * @return bool|int Insert ID on success, true on update, false on failure
     */
    public function upsert($data)
    {
        // Validate required fields
        if (empty($data['rate_date']) || empty($data['base_code'])) {
            log_message('error', 'FxRates_model::upsert - Missing required fields: rate_date or base_code');
            return false;
        }

        // Check if record exists
        $existing = $this->get_by_date($data['rate_date'], $data['base_code']);

        if ($existing) {
            // Update existing record
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            if (\db()->table(self::TABLE)->where('id', $existing['id'])->update($data)) {
                log_message('info', 'FxRates_model::upsert - Updated rates for date: ' . $data['rate_date']);
                return true;
            } else {
                log_message('error', 'FxRates_model::upsert - Failed to update rates for date: ' . $data['rate_date']);
                return false;
            }
        } else {
            // Insert new record
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            if (\db()->table(self::TABLE)->insert($data)) {
                $insert_id = \db()->insertID();
                log_message('info', 'FxRates_model::upsert - Inserted new rates for date: ' . $data['rate_date'] . ', ID: ' . $insert_id);
                return $insert_id;
            } else {
                log_message('error', 'FxRates_model::upsert - Failed to insert rates for date: ' . $data['rate_date']);
                return false;
            }
        }
    }

    /**
     * Update status for a specific date
     * 
     * @param string $date Date in Y-m-d format
     * @param string $status New status (ok|stale|error)
     * @param string $base_code Base currency code (default: USD)
     * @return bool Success status
     */
    public function update_status($date, $status, $base_code = 'USD')
    {
        return \db()->table(self::TABLE)
            ->where('rate_date', $date)
            ->where('base_code', $base_code)
            ->update([
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get count of records for statistics
     * 
     * @param string $base_code Base currency code (default: USD)
     * @return int Record count
     */
    public function count_all($base_code = 'USD')
    {
        return \db()->table(self::TABLE)
            ->where('base_code', $base_code)
            ->countAllResults();
    }

    /**
     * Delete old records (cleanup)
     * 
     * @param int $days_to_keep Number of days to keep (default: 365)
     * @return int Number of deleted rows
     */
    public function cleanup_old_records($days_to_keep = 365)
    {
        $cutoff_date = date('Y-m-d', strtotime("-{$days_to_keep} days"));
        
        \db()->table(self::TABLE)->where('rate_date <', $cutoff_date)->delete();
        
        $affected = \db()->affectedRows();
        
        if ($affected > 0) {
            log_message('info', "FxRates_model::cleanup_old_records - Deleted {$affected} records older than {$cutoff_date}");
        }
        
        return $affected;
    }

    /**
     * Check if rates exist for today
     * 
     * @param string $base_code Base currency code (default: USD)
     * @return bool True if today's rates exist
     */
    public function has_today_rates($base_code = 'USD')
    {
        $today = date('Y-m-d');
        $record = $this->get_by_date($today, $base_code);
        
        return !empty($record) && $record['status'] === 'ok';
    }

    /**
     * Get rates with stale status
     * 
     * @param int $limit Maximum records to return
     * @return array Array of stale rate records
     */
    public function get_stale_rates($limit = 10)
    {
        return \db()->table(self::TABLE)
            ->where('status', 'stale')
            ->orderBy('rate_date', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get conversion rate between two currencies
     * 
     * @param string $from Source currency code (e.g., 'MAD')
     * @param string $to Target currency code (e.g., 'AED')
     * @return float Conversion rate, or fallback rate if not available
     */
    public function get_conversion_rate($from, $to)
    {
        $from = strtoupper($from);
        $to = strtoupper($to);
        
        // If same currency, no conversion needed
        if ($from === $to) {
            return 1.0;
        }
        
        // Get latest rates
        $latest = $this->get_latest('USD');
        
        if ($latest) {
            $from_col = strtolower($from);
            $to_col = strtolower($to);
            
            // Check if both currencies exist in the record
            if (isset($latest[$from_col]) && isset($latest[$to_col]) && 
                (float)$latest[$from_col] > 0 && (float)$latest[$to_col] > 0) {
                
                // Convert via USD base: rate = to_rate / from_rate
                $rate = (float)$latest[$to_col] / (float)$latest[$from_col];
                log_message('debug', "FxRates_model::get_conversion_rate: {$from} -> {$to} = {$rate}");
                return round($rate, 6);
            }
        }
        
        // Fallback rates if database rates not available
        $fallback_rates = [
            'MAD_AED' => 0.37,   // 1 MAD ≈ 0.37 AED
            'AED_MAD' => 2.70,   // 1 AED ≈ 2.70 MAD
            'MAD_USD' => 0.10,   // 1 MAD ≈ 0.10 USD
            'USD_MAD' => 10.0,   // 1 USD ≈ 10 MAD
            'MAD_EUR' => 0.09,   // 1 MAD ≈ 0.09 EUR
            'EUR_MAD' => 11.0,   // 1 EUR ≈ 11 MAD
            'USD_AED' => 3.67,   // 1 USD ≈ 3.67 AED
            'AED_USD' => 0.27,   // 1 AED ≈ 0.27 USD
        ];
        
        $key = "{$from}_{$to}";
        if (isset($fallback_rates[$key])) {
            log_message('debug', "FxRates_model::get_conversion_rate: Using fallback for {$key}: " . $fallback_rates[$key]);
            return $fallback_rates[$key];
        }
        
        log_message('debug', "FxRates_model::get_conversion_rate: No rate found for {$from} -> {$to}");
        return 1.0;
    }

    /**
     * Format rate record for API response
     * 
     * @param array $record Database record
     * @param bool $include_metadata Include additional metadata
     * @return array Formatted response
     */
    public function format_for_response($record, $include_metadata = false)
    {
        if (empty($record)) {
            return null;
        }

        // Fonction helper pour éviter les valeurs nulles/zéro
        $safe_float = function($value) {
            $float_val = (float) $value;
            return $float_val > 0 ? $float_val : null; // Retourner null si <= 0
        };

        $response = [
            'base' => $record['base_code'],
            'date' => $record['rate_date'],
            'rates' => [
                'USD' => $safe_float($record['usd']),
                'EUR' => $safe_float($record['eur']),
                'MAD' => $safe_float($record['mad']),
                'AED' => $safe_float($record['aed']),
                'GBP' => $safe_float($record['gbp'] ?? 0)
            ],
            'stale' => ($record['status'] !== 'ok'),
            'source' => $record['source']
        ];

        if ($include_metadata) {
            $response['fetched_at'] = $record['fetched_at'];
            $response['status'] = $record['status'];
        }

        return $response;
    }
}

