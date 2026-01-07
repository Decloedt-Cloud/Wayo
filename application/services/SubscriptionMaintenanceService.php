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

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('Crud_model', 'crud_model');
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
     * Clean duplicate unpaid subscription invoices
     * Keeps the most recent unpaid invoice, cancels others
     */
    private function cleanDuplicateUnpaidInvoices()
    {
        $result = [
            'duplicates_found' => 0,
            'duplicates_cancelled' => 0,
            'schools_affected' => 0
        ];

        // Find schools with multiple unpaid subscription invoices
        $this->CI->db->select('school_id, COUNT(*) as count');
        $this->CI->db->from('invoices');
        $this->CI->db->where('payment_type', 'subscription_admin');
        $this->CI->db->where('status', 'unpaid');
        $this->CI->db->group_by('school_id');
        $this->CI->db->having('count > 1');
        $duplicates = $this->CI->db->get()->result_array();

        foreach ($duplicates as $duplicate) {
            $school_id = $duplicate['school_id'];
            $result['duplicates_found'] += ($duplicate['count'] - 1); // All except one
            $result['schools_affected']++;

            // Get all unpaid invoices for this school, ordered by creation date (newest first)
            $this->CI->db->select('id');
            $this->CI->db->from('invoices');
            $this->CI->db->where('school_id', $school_id);
            $this->CI->db->where('payment_type', 'subscription_admin');
            $this->CI->db->where('status', 'unpaid');
            $this->CI->db->order_by('created_at', 'DESC');
            $invoices = $this->CI->db->get()->result_array();

            // Keep the most recent (first), cancel the others
            $keep_id = $invoices[0]['id'];
            $cancel_ids = array_column(array_slice($invoices, 1), 'id');

            if (!empty($cancel_ids)) {
                $this->CI->db->where_in('id', $cancel_ids);
                $this->CI->db->update('invoices', [
                    'status' => 'cancelled',
                    'updated_at' => time()
                ]);

                $result['duplicates_cancelled'] += count($cancel_ids);
                log_message('info', "SubscriptionMaintenanceService: Cancelled " . count($cancel_ids) .
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
     * Get maintenance health status
     */
    public function getHealthStatus()
    {
        $health = [
            'last_run' => null,
            'next_run' => null,
            'issues_detected' => 0,
            'recommendations' => []
        ];

        // Check for duplicate unpaid invoices
        $this->CI->db->select('school_id, COUNT(*) as count');
        $this->CI->db->from('invoices');
        $this->CI->db->where('payment_type', 'subscription_admin');
        $this->CI->db->where('status', 'unpaid');
        $this->CI->db->group_by('school_id');
        $this->CI->db->having('count > 1');
        $duplicates = $this->CI->db->count_all_results();

        if ($duplicates > 0) {
            $health['issues_detected'] += $duplicates;
            $health['recommendations'][] = "Found {$duplicates} schools with duplicate unpaid invoices";
        }

        // Check for schools with incorrect status
        $schools = $this->CI->crud_model->get_schools()->result_array();
        $status_issues = 0;

        foreach ($schools as $school) {
            $current_status = $this->CI->subscriptionService->getSubscriptionStatus($school['id']);
            if ($school['subscription_status'] !== $current_status['status']) {
                $status_issues++;
            }
        }

        if ($status_issues > 0) {
            $health['issues_detected'] += $status_issues;
            $health['recommendations'][] = "Found {$status_issues} schools with incorrect subscription status";
        }

        return $health;
    }
}






