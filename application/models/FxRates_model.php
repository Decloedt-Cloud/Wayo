<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FX Rates Model
 * 
 * Handles database operations for daily FX rates storage
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class FxRates_model extends CI_Model {

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
        $this->load->database();
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
        $this->db->where('rate_date', $date);
        $this->db->where('base_code', $base_code);
        $query = $this->db->get(self::TABLE);
        
        return $query->row_array();
    }

    /**
     * Get the latest available rates
     * 
     * @param string $base_code Base currency code (default: USD)
     * @return array|null Latest rate record or null if none exist
     */
    public function get_latest($base_code = 'USD')
    {
        $this->db->where('base_code', $base_code);
        $this->db->order_by('rate_date', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get(self::TABLE);
        
        return $query->row_array();
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
        $this->db->where('base_code', $base_code);
        $this->db->where('rate_date >=', $start_date);
        $this->db->where('rate_date <=', $end_date);
        $this->db->order_by('rate_date', 'ASC');
        $query = $this->db->get(self::TABLE);
        
        return $query->result_array();
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
            $this->db->where('id', $existing['id']);
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            if ($this->db->update(self::TABLE, $data)) {
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
            
            if ($this->db->insert(self::TABLE, $data)) {
                $insert_id = $this->db->insert_id();
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
        $this->db->where('rate_date', $date);
        $this->db->where('base_code', $base_code);
        
        return $this->db->update(self::TABLE, [
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
        $this->db->where('base_code', $base_code);
        return $this->db->count_all_results(self::TABLE);
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
        
        $this->db->where('rate_date <', $cutoff_date);
        $this->db->delete(self::TABLE);
        
        $affected = $this->db->affected_rows();
        
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
        $this->db->where('status', 'stale');
        $this->db->order_by('rate_date', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get(self::TABLE);
        
        return $query->result_array();
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

        $response = [
            'base' => $record['base_code'],
            'date' => $record['rate_date'],
            'rates' => [
                'USD' => (float) $record['usd'],
                'EUR' => (float) $record['eur'],
                'MAD' => (float) $record['mad'],
                'AED' => (float) $record['aed'],
                'GBP' => (float) ($record['gbp'] ?? 0)
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

