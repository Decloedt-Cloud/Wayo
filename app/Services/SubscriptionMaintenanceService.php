<?php
namespace App\Services;

use CodeIgniter\Model;
use Config\Database;

/**
 * Subscription Maintenance Service
 *
 * Automatically detects and fixes subscription/payment issues
 *
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class SubscriptionMaintenanceService {

    private $db;
    private $crudModel;
    private $subscriptionService;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->crudModel = model('CrudModel');
        $this->subscriptionService = service('subscriptionService');
    }

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

            $duplicate_results = $this->cleanDuplicateUnpaidInvoices();
            $results['details']['duplicates'] = $duplicate_results;
            $results['issues_found'] += $duplicate_results['duplicates_found'];
            $results['issues_fixed'] += $duplicate_results['duplicates_cancelled'];

            $status_results = $this->fixSubscriptionStatuses();
            $results['details']['statuses'] = $status_results;
            $results['issues_found'] += $status_results['schools_updated'];

            $period_results = $this->validateInvoicePeriods();
            $results['details']['periods'] = $period_results;
            $results['issues_found'] += $period_results['periods_fixed'];

            $cleanup_results = $this->cleanExpiredCancelledInvoices();
            $results['details']['cleanup'] = $cleanup_results;
            $results['issues_fixed'] += $cleanup_results['cleaned'];

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

    private function cleanDuplicateUnpaidInvoices()
    {
        $result = [
            'duplicates_found' => 0,
            'duplicates_cancelled' => 0,
            'schools_affected' => 0
        ];

        $builder = db()->table('invoices');
        $duplicates = $builder->select('school_id, COUNT(*) as count')
            ->where('payment_type', 'subscription_admin')
            ->where('status', 'unpaid')
            ->groupBy('school_id')
            ->having('count > 1')
            ->get()
            ->getResultArray();

        foreach ($duplicates as $duplicate) {
            $school_id = $duplicate['school_id'];
            $result['duplicates_found'] += ($duplicate['count'] - 1);
            $result['schools_affected']++;

            $invoices = $builder->select('id')
                ->where('school_id', $school_id)
                ->where('payment_type', 'subscription_admin')
                ->where('status', 'unpaid')
                ->orderBy('created_at', 'DESC')
                ->get()
                ->getResultArray();

            $keep_id = $invoices[0]['id'];
            $cancel_ids = array_column(array_slice($invoices, 1), 'id');

            if (!empty($cancel_ids)) {
                $builder->whereIn('id', $cancel_ids);
                $builder->update([
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

    private function fixSubscriptionStatuses()
    {
        $result = [
            'schools_checked' => 0,
            'schools_updated' => 0
        ];

        $schools = $this->crudModel->get_schools()->getResultArray();
        $result['schools_checked'] = count($schools);

        foreach ($schools as $school) {
            $current_status = $this->subscriptionService->getSubscriptionStatus($school['id']);
            $correct_status = $current_status['status'];

            if ($school['subscription_status'] !== $correct_status) {
                $builder = db()->table('schools');
                $builder->where('id', $school['id']);
                $builder->update([
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

    private function validateInvoicePeriods()
    {
        $result = [
            'periods_checked' => 0,
            'periods_fixed' => 0
        ];

        $builder = db()->table('invoices');
        $invoices = $builder->where('payment_type', 'subscription_admin')
            ->get()
            ->getResultArray();
        $result['periods_checked'] = count($invoices);

        foreach ($invoices as $invoice) {
            if (empty($invoice['period_start']) || empty($invoice['period_end'])) {
                $period_start = $invoice['created_at'];
                $period_end = strtotime('+1 month', $period_start);

                $builder->where('id', $invoice['id']);
                $builder->update([
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

    private function cleanExpiredCancelledInvoices()
    {
        $result = ['cleaned' => 0];

        $thirty_days_ago = time() - (30 * 24 * 60 * 60);

        $builder = db()->table('invoices');
        $builder->where('status', 'cancelled');
        $builder->where('updated_at <', $thirty_days_ago);
        $builder->delete();

        $result['cleaned'] = db()->affectedRows();

        if ($result['cleaned'] > 0) {
            log_message('info', "SubscriptionMaintenanceService: Cleaned {$result['cleaned']} expired cancelled invoices");
        }

        return $result;
    }

    private function updateSchoolStatistics()
    {
        return true;
    }

    private function getTotalSchools()
    {
        return db()->table('schools')->countAll();
    }

    public function getHealthStatus()
    {
        $health = [
            'last_run' => null,
            'next_run' => null,
            'issues_detected' => 0,
            'recommendations' => []
        ];

        $builder = db()->table('invoices');
        $duplicates = $builder->select('school_id, COUNT(*) as count')
            ->where('payment_type', 'subscription_admin')
            ->where('status', 'unpaid')
            ->groupBy('school_id')
            ->having('count > 1')
            ->countAllResults();

        if ($duplicates > 0) {
            $health['issues_detected'] += $duplicates;
            $health['recommendations'][] = "Found {$duplicates} schools with duplicate unpaid invoices";
        }

        $schools = $this->crudModel->get_schools()->getResultArray();
        $status_issues = 0;

        foreach ($schools as $school) {
            $current_status = $this->subscriptionService->getSubscriptionStatus($school['id']);
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
