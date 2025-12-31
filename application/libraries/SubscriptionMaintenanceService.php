<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Subscription Maintenance Service
 *
 * Automatically detects and fixes subscription/payment issues
 *
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class SubscriptionMaintenanceService {

    protected $CI;
    protected $stats_cache = [];

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Crud_model', 'crud_model');
        $this->CI->load->model('Invoice_model', 'invoice_model');
        $this->CI->load->library('SubscriptionService', null, 'subscriptionService');
    }

    /**
     * Run complete maintenance check and fix
     *
     * @return array Results
     */
    public function runMaintenance()
    {
        $results = [
            'start_time' => time(),
            'issues_found' => 0,
            'issues_fixed' => 0,
            'schools_processed' => 0,
            'errors' => [],
            'details' => []
        ];

        try {
            log_message('info', 'SubscriptionMaintenanceService: Starting maintenance run');

            // 1. Clean duplicate unpaid invoices
            $duplicate_results = $this->cleanDuplicateUnpaidInvoices();
            $results['details']['duplicates'] = $duplicate_results;
            $results['issues_found'] += $duplicate_results['duplicates_found'];
            $results['issues_fixed'] += $duplicate_results['duplicates_cancelled'];

            // 2. Fix subscription statuses
            $status_results = $this->fixSubscriptionStatuses();
            $results['details']['statuses'] = $status_results;
            $results['issues_found'] += $status_results['schools_updated'];

            // 3. Validate invoice periods
            $period_results = $this->validateInvoicePeriods();
            $results['details']['periods'] = $period_results;
            $results['issues_found'] += $period_results['periods_fixed'];

            // 4. Clean expired cancelled invoices
            $cleanup_results = $this->cleanExpiredCancelledInvoices();
            $results['details']['cleanup'] = $cleanup_results;
            $results['issues_fixed'] += $cleanup_results['cleaned'];

            // 5. Update school statistics
            $this->updateSchoolStatistics();

            $results['schools_processed'] = $this->getTotalSchools();
            $results['end_time'] = time();
            $results['duration'] = $results['end_time'] - $results['start_time'];

            log_message('info', 'SubscriptionMaintenanceService: Maintenance completed - Found: ' .
                       $results['issues_found'] . ', Fixed: ' . $results['issues_fixed']);

        } catch (Exception $e) {
            $results['errors'][] = $e->getMessage();
            log_message('error', 'SubscriptionMaintenanceService: Exception - ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Clean duplicate unpaid subscription invoices (optimized)
     * Uses efficient batch processing instead of individual queries
     */
    private function cleanDuplicateUnpaidInvoices()
    {
        $result = [
            'duplicates_found' => 0,
            'duplicates_cancelled' => 0,
            'schools_affected' => 0
        ];

        // Use optimized query to find and process duplicates in batches
        $sql = "
            SELECT school_id,
                   GROUP_CONCAT(id ORDER BY created_at DESC) as invoice_ids,
                   COUNT(*) as count
            FROM invoices
            WHERE payment_type = 'subscription_admin'
            AND status = 'unpaid'
            GROUP BY school_id
            HAVING count > 1
        ";

        $duplicates = $this->CI->db->query($sql)->result_array();

        foreach ($duplicates as $duplicate) {
            $school_id = $duplicate['school_id'];
            $invoice_ids = explode(',', $duplicate['invoice_ids']);
            $total_count = $duplicate['count'];

            $result['duplicates_found'] += ($total_count - 1); // All except one
            $result['schools_affected']++;

            // Keep the most recent (first in DESC order), cancel the others
            $keep_id = $invoice_ids[0];
            $cancel_ids = array_slice($invoice_ids, 1);

            if (!empty($cancel_ids)) {
                // Batch update for better performance
                $this->CI->db->where_in('id', $cancel_ids);
                $this->CI->db->update('invoices', [
                    'status' => 'cancelled',
                    'updated_at' => time()
                ]);

                $result['duplicates_cancelled'] += count($cancel_ids);
                log_message('info', "SubscriptionMaintenanceService: Batch cancelled " . count($cancel_ids) .
                           " duplicate invoices for school {$school_id}, kept invoice {$keep_id}");
            }
        }

        return $result;
    }

    /**
     * Fix subscription statuses that don't match reality
     */
    private function fixSubscriptionStatuses()
    {
        $result = [
            'schools_checked' => 0,
            'schools_updated' => 0
        ];

        // Get all schools
        $schools = $this->CI->crud_model->get_schools()->result_array();
        $result['schools_checked'] = count($schools);

        foreach ($schools as $school) {
            $current_status = $this->CI->subscriptionService->getSubscriptionStatus($school['id']);
            $correct_status = $current_status['status'];

            // Check if database status needs updating
            if ($school['subscription_status'] !== $correct_status) {
                $this->CI->db->where('id', $school['id']);
                $this->CI->db->update('schools', [
                    'subscription_status' => $correct_status,
                    'updated_at' => time()
                ]);

                $result['schools_updated']++;
                log_message('info', "SubscriptionMaintenanceService: Updated school {$school['id']} " .
                           "status from '{$school['subscription_status']}' to '{$correct_status}'");
            }
        }

        return $result;
    }

    /**
     * Validate and fix invoice periods
     */
    private function validateInvoicePeriods()
    {
        $result = [
            'periods_checked' => 0,
            'periods_fixed' => 0
        ];

        // Get all subscription invoices
        $this->CI->db->where('payment_type', 'subscription_admin');
        $invoices = $this->CI->db->get('invoices')->result_array();
        $result['periods_checked'] = count($invoices);

        foreach ($invoices as $invoice) {
            // Check if period is properly set
            if (empty($invoice['period_start']) || empty($invoice['period_end'])) {
                // Calculate period based on creation date + 1 month
                $period_start = $invoice['created_at'];
                $period_end = strtotime('+1 month', $period_start);

                $this->CI->db->where('id', $invoice['id']);
                $this->CI->db->update('invoices', [
                    'period_start' => $period_start,
                    'period_end' => $period_end,
                    'updated_at' => time()
                ]);

                $result['periods_fixed']++;
                log_message('info', "SubscriptionMaintenanceService: Fixed period for invoice {$invoice['id']}");
            }
        }

        return $result;
    }

    /**
     * Clean expired cancelled invoices (older than 30 days)
     */
    private function cleanExpiredCancelledInvoices()
    {
        $result = ['cleaned' => 0];

        // Delete cancelled invoices older than 30 days
        $thirty_days_ago = time() - (30 * 24 * 60 * 60);

        $this->CI->db->where('status', 'cancelled');
        $this->CI->db->where('updated_at <', $thirty_days_ago);
        $this->CI->db->delete('invoices');

        $result['cleaned'] = $this->CI->db->affected_rows();

        if ($result['cleaned'] > 0) {
            log_message('info', "SubscriptionMaintenanceService: Cleaned {$result['cleaned']} expired cancelled invoices");
        }

        return $result;
    }

    /**
     * Update school statistics
     */
    private function updateSchoolStatistics()
    {
        // This could be used to cache statistics or update counters
        // For now, just ensure all schools have proper status
        return true;
    }

    /**
     * Get total number of schools
     */
    private function getTotalSchools()
    {
        return $this->CI->db->count_all('schools');
    }

    /**
     * Get maintenance health status (optimized with caching)
     */
    public function getHealthStatus()
    {
        $cache_key = 'maintenance_health_status';
        $cached = $this->CI->cache->get($cache_key);

        // Return cached result if less than 5 minutes old
        if ($cached !== false && (time() - $cached['timestamp']) < 300) {
            return $cached['data'];
        }

        $health = [
            'last_run' => null,
            'next_run' => null,
            'issues_detected' => 0,
            'recommendations' => []
        ];

        // Optimized duplicate check
        $duplicate_result = $this->CI->db->query("
            SELECT COUNT(*) as duplicate_schools
            FROM (
                SELECT school_id
                FROM invoices
                WHERE payment_type = 'subscription_admin'
                AND status = 'unpaid'
                GROUP BY school_id
                HAVING COUNT(*) > 1
            ) duplicates
        ")->row();

        $duplicates = $duplicate_result->duplicate_schools ?? 0;

        if ($duplicates > 0) {
            $health['issues_detected'] += $duplicates;
            $health['recommendations'][] = "Found {$duplicates} schools with duplicate unpaid invoices";
        }

        // Optimized status check - sample only 10% of schools for performance
        $schools_sample = $this->CI->db->query("
            SELECT id, subscription_status
            FROM schools
            WHERE RAND() < 0.1
            LIMIT 50
        ")->result_array();

        $status_issues = 0;
        foreach ($schools_sample as $school) {
            $current_status = $this->CI->subscriptionService->getSubscriptionStatus($school['id']);
            if ($school['subscription_status'] !== $current_status['status']) {
                $status_issues++;
            }
        }

        // Extrapolate to full population (rough estimate)
        $estimated_total_issues = intval($status_issues * 10);
        if ($estimated_total_issues > 0) {
            $health['issues_detected'] += $estimated_total_issues;
            $health['recommendations'][] = "Estimated {$estimated_total_issues} schools with incorrect subscription status (sampled)";
        }

        // Cache result for 5 minutes
        $this->CI->cache->save($cache_key, [
            'timestamp' => time(),
            'data' => $health
        ], 300);

        return $health;
    }
}
