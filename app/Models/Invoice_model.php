<?php

namespace App\Models;

use CodeIgniter\Model;

class Invoice_model extends Model {
    protected $table            = 'invoices';
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

    private $school_cache = [];
    private $plan_cache = [];

    function __construct()
    {
        parent::__construct();
        $this->cache = \Config\Services::cache();
    }

    /**
     * Ensure a subscription invoice exists for a school (idempotent)
     *
     * Creates an invoice ONLY if no existing unpaid invoice exists for the school
     * Uses DB transaction to prevent race conditions
     * Optimized with caching and early validation
     *
     * @param int $school_id
     * @param array $plan_data Optional plan data
     * @return array Result with invoice_id or error
     */
    public function ensureSubscriptionInvoice($school_id, $plan_data = null)
    {
        // DEBUG
        log_message('error', 'ensureSubscriptionInvoice: school_id=' . $school_id . ', type=' . gettype($school_id));
        
        // Early validation
        if (!is_numeric($school_id) || $school_id <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid school_id: must be positive integer'
            ];
        }

        // Start transaction for atomicity
        \db()->transBegin();

        try {
            // 1. Check for existing unpaid invoice (optimized query)
            // DEBUG
            log_message('error', 'ensureSubscriptionInvoice: checking for existing invoice');
            
            $existing_invoice = \db()->query("
                SELECT id, status, period_start, period_end
                FROM invoices
                WHERE school_id = ?
                AND payment_type = 'subscription_admin'
                AND status = 'unpaid'
                LIMIT 1
                FOR UPDATE
            ", [$school_id])->getRowArray();
            
            // DEBUG
            log_message('error', 'ensureSubscriptionInvoice: existing_invoice=' . ($existing_invoice ? json_encode($existing_invoice) : 'none'));
            
            if ($existing_invoice) {
                \db()->transCommit();
                return [
                    'success' => true,
                    'invoice_id' => $existing_invoice['id'],
                    'message' => 'Existing unpaid invoice found',
                    'existing' => true
                ];
            }

            // 2. Get school details (with caching)
            $school = $this->getCachedSchool($school_id);
            if (!$school) {
                \db()->transRollback();
                return ['success' => false, 'message' => 'School not found'];
            }

            // 3. Get plan data (with caching)
            if (!$plan_data) {
                // Get country from school data, default to MA if missing
                $school_country = !empty($school['country']) ? $school['country'] : 'MA';
                
                $plan_data = $this->getCachedDefaultPlan($school_country);
                if (!$plan_data) {
                    \db()->transRollback();
                    return ['success' => false, 'message' => 'No active subscription plan found for country: ' . $school_country];
                }
            }
            
            // 4. Calculate period (max(old_end, now) to avoid losing days)
            $now = time();
            $period_start = max($school['subscription_end'] ?? 0, $now);
            $period_end = strtotime("+{$plan_data['interval_count']} {$plan_data['interval_type']}", $period_start);
            
            // 5. Create invoice data
            $invoice_data = [
                'title' => $school['name'] . ' - ' . $plan_data['name'],
                'total_amount' => $plan_data['amount'],
                'currency' => $plan_data['currency'],
                'payment_type' => 'subscription_admin',
                'status' => 'unpaid',
                'school_id' => $school_id,
                'session' => active_session(),
                'created_at' => $now,
                'period_start' => $period_start,
                'period_end' => $period_end
            ];
            
            // 7. Insert invoice
            \db()->table('invoices')->insert($invoice_data);
            $invoice_id = \db()->insertID();
            
            // 8. Complete transaction
            \db()->transCommit();
            
            log_message('info', "Invoice_model: Created subscription invoice #{$invoice_id} for school #{$school_id}, period_start: " . date('Y-m-d H:i:s', $period_start));
            
            return [
                'success' => true,
                'invoice_id' => $invoice_id,
                'message' => 'Subscription invoice created',
                'existing' => false
            ];
            
        } catch (Exception $e) {
            \db()->transRollback();
            log_message('error', "Invoice_model ensureSubscriptionInvoice error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get default subscription plan
     * 
     * @param string $country
     */
    private function getDefaultSubscriptionPlan($country = 'MA')
    {
        // Default to MA if country is empty or null
        $country = empty($country) ? 'MA' : $country;
        
        // Normalize UAE -> AE
        if (strtoupper($country) === 'UAE') {
            $country = 'AE';
        }
        
        // 1. Try to find a default plan for the specific country
        $result = \db()->table('subscription_plans')
            ->where('is_default', 1)
            ->where('active', 1)
            ->where('country', $country)
            ->get();
        $plan = $result ? $result->getRowArray() : null;
        
        // 2. Fallback: if no default plan found for country, try ANY active plan for country (cheapest first)
        if (!$plan) {
            $result = \db()->table('subscription_plans')
                ->where('active', 1)
                ->where('country', $country)
                ->orderBy('amount', 'ASC')
                ->get();
            $plan = $result ? $result->getRowArray() : null;
        }
        
        // 3. Fallback: if still no plan found (and country is not MA), try MA/default
        if (!$plan && $country !== 'MA') {
            $result = \db()->table('subscription_plans')
                ->where('is_default', 1)
                ->where('active', 1)
                ->where('country', 'MA')
                ->get();
            $plan = $result ? $result->getRowArray() : null;
        }
        
        // 4. Ultimate fallback: any default active plan
        if (!$plan) {
            $result = \db()->table('subscription_plans')
                ->where('is_default', 1)
                ->where('active', 1)
                ->get();
            $plan = $result ? $result->getRowArray() : null;
        }
        
        return $plan;
    }
    
    /**
     * Mark invoice as paid and update school subscription
     * Uses transaction to ensure consistency
     * 
     * @param int $invoice_id
     * @param string|null $payment_reference
     * @param string|null $payment_method
     * @param float|null $amount_paid
     * @param array|null $fx_data Currency conversion data (payment_currency, payment_amount_converted, fx_rate, fx_rate_date)
     * @return array
     */
    public function markInvoicePaid($invoice_id, $payment_reference = null, $payment_method = null, $amount_paid = null, $fx_data = null)
    {
        \db()->transBegin();
        
        try {
            // 1. Get invoice with lock (raw SQL for CodeIgniter compatibility)
            $invoice = \db()->query("
                SELECT * FROM invoices WHERE id = ? FOR UPDATE
            ", [$invoice_id])->getRowArray();
            
            if (!$invoice) {
                \db()->transRollback();
                return ['success' => false, 'message' => 'Invoice not found'];
            }
            
            // 2. Check if already paid
            if ($invoice['status'] === 'paid') {
                \db()->transCommit();
                return ['success' => true, 'message' => 'Invoice already paid'];
            }
            
            // 3. Update invoice avec les détails du paiement
            $invoice_update = [
                'status' => 'paid',
                'paid_at' => time(),
                'updated_at' => time()
            ];
            
            // Ajouter les détails spécifiques au type de paiement
            if ($payment_method) {
                $invoice_update['payment_method'] = $payment_method;
            }
            
            if ($amount_paid !== null) {
                $invoice_update['paid_amount'] = $amount_paid;
            }
            
            // Stocker la référence selon le type de paiement
            if ($payment_reference) {
                if ($payment_method === 'stripe') {
                    $invoice_update['stripe_payment_intent_id'] = $payment_reference;
                } elseif ($payment_method === 'paystack') {
                    $invoice_update['payment_reference'] = $payment_reference;
                }
                // Pour PayPal, on pourrait stocker dans un champ dédié si nécessaire
            }
            
            // ========== FX CONVERSION DATA ==========
            // Store currency conversion information if payment was made in different currency
            if (!empty($fx_data) && is_array($fx_data)) {
                // Conversion applied flag
                if (isset($fx_data['conversion_applied']) && $fx_data['conversion_applied']) {
                    $invoice_update['conversion_applied'] = 1;
                    
                    // Payment currency (the currency actually charged to customer)
                    if (!empty($fx_data['payment_currency'])) {
                        $invoice_update['payment_currency'] = $fx_data['payment_currency'];
                    }
                    
                    // Amount in payment currency (converted amount charged)
                    if (isset($fx_data['payment_amount_converted'])) {
                        $invoice_update['payment_amount_converted'] = $fx_data['payment_amount_converted'];
                    }
                    
                    // Exchange rate used
                    if (isset($fx_data['fx_rate'])) {
                        $invoice_update['fx_rate'] = $fx_data['fx_rate'];
                    }
                    
                    // Date of exchange rate
                    if (!empty($fx_data['fx_rate_date'])) {
                        $invoice_update['fx_rate_date'] = $fx_data['fx_rate_date'];
                    }
                    
                    log_message('info', "Invoice_model: FX conversion stored for invoice #{$invoice_id} - " .
                        "Payment: {$fx_data['payment_amount_converted']} {$fx_data['payment_currency']}, " .
                        "Rate: {$fx_data['fx_rate']}, Date: {$fx_data['fx_rate_date']}");
                }
            }
            
            \db()->table('invoices')->where('id', $invoice_id)->update($invoice_update);
            
            // 4. Cancel other unpaid invoices for this school
            \db()->table('invoices')
                ->where('school_id', $invoice['school_id'])
                ->where('payment_type', 'subscription_admin')
                ->where('status', 'unpaid')
                ->where('id !=', $invoice_id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => time()
                ]);
            
            // 5. Update school subscription
            $school_update = [
                'is_paid' => 1,
                'is_trial' => 0,
                'subscription_status' => 'active',
                'subscription_end' => $invoice['period_end'], // Use invoice period_end
                'updated_at' => time()
            ];
            
            \db()->table('schools')->where('id', $invoice['school_id'])->update($school_update);
            
            // 6. Complete transaction
            \db()->transCommit();
            
            log_message('info', "Invoice_model: Marked invoice #{$invoice_id} as paid, school #{$invoice['school_id']} active until " . date('Y-m-d H:i:s', $invoice['period_end']));
            
            return [
                'success' => true,
                'message' => 'Invoice marked as paid and school updated'
            ];
            
        } catch (Exception $e) {
            \db()->transRollback();
            log_message('error', "Invoice_model markInvoicePaid error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if school has unpaid subscription invoice (optimized)
     */
    public function hasUnpaidSubscriptionInvoice($school_id)
    {
        if (!is_numeric($school_id) || $school_id <= 0) {
            return false;
        }

        // Use COUNT(*) for better performance than num_rows()
        $result = \db()->query("
            SELECT COUNT(*) as count
            FROM invoices
            WHERE school_id = ?
            AND payment_type = 'subscription_admin'
            AND status = 'unpaid'
        ", [$school_id])->getRow();

        return $result->count > 0;
    }

    /**
     * Get cached school data to reduce DB queries
     */
    private function getCachedSchool($school_id)
    {
        static $memory_cache = [];
        
        $cache_key = "school_{$school_id}";

        // DEBUG
        log_message('error', 'getCachedSchool START: school_id=' . $school_id);

        // Check memory cache first
        if (isset($memory_cache[$school_id])) {
            log_message('error', 'getCachedSchool: returning from memory cache');
            return $memory_cache[$school_id];
        }

        // Check file cache
        $cached = $this->cache->get($cache_key);
        if ($cached !== false && $cached !== null) {
            log_message('error', 'getCachedSchool: returning from file cache, cached=' . json_encode($cached));
            $memory_cache[$school_id] = $cached;
            return $cached;
        }
        
        // If cached value is null, delete it and query DB
        if ($cached === null) {
            log_message('error', 'getCachedSchool: found null in cache, deleting it');
            $this->cache->delete($cache_key);
        }

        // DEBUG
        log_message('error', 'getCachedSchool: no cache hit, querying DB for school_id=' . $school_id);
        
        // Load from database
        $school = \db()->table('schools')->where('id', $school_id)->get()->getRowArray();

        // DEBUG
        log_message('error', 'getCachedSchool: school found=' . ($school ? 'yes' : 'no'));
        if ($school) {
            log_message('error', 'getCachedSchool: school data=' . json_encode($school));
        }

        if ($school) {
            // Cache for 5 minutes
            $this->cache->save($cache_key, $school, 300);
            $memory_cache[$school_id] = $school;
        }

        log_message('error', 'getCachedSchool END: returning ' . ($school ? 'school' : 'null'));
        return $school;
    }

    /**
     * Get cached default subscription plan
     * 
     * @param string $country
     */
    private function getCachedDefaultPlan($country = 'MA')
    {
        static $plan_memory_cache = [];
        
        $country = empty($country) ? 'MA' : $country;
        $cache_key = 'default_subscription_plan_' . $country;

        // DEBUG
        log_message('error', 'getCachedDefaultPlan START: country=' . $country);

        // Check memory cache first
        if (!empty($plan_memory_cache[$country])) {
            log_message('error', 'getCachedDefaultPlan: returning from memory cache');
            return $plan_memory_cache[$country];
        }

        // Check file cache
        $cached = $this->cache->get($cache_key);
        if ($cached !== false && $cached !== null) {
            log_message('error', 'getCachedDefaultPlan: returning from file cache');
            $plan_memory_cache[$country] = $cached;
            return $cached;
        }
        
        // If cached value is null, delete it and query DB
        if ($cached === null) {
            log_message('error', 'getCachedDefaultPlan: found null in cache, deleting it');
            $this->cache->delete($cache_key);
        }

        // Load from database using the new method
        log_message('error', 'getCachedDefaultPlan: querying DB for country=' . $country);
        $plan = $this->getDefaultSubscriptionPlan($country);

        // DEBUG
        log_message('error', 'getCachedDefaultPlan: plan found=' . ($plan ? 'yes' : 'no'));
        if ($plan) {
            log_message('error', 'getCachedDefaultPlan: plan data=' . json_encode($plan));
        }

        if ($plan) {
            // Cache for 10 minutes (plans change infrequently)
            $this->cache->save($cache_key, $plan, 600);
            $plan_memory_cache[$country] = $plan;
        }

        log_message('error', 'getCachedDefaultPlan END: returning ' . ($plan ? 'plan' : 'null'));
        return $plan;
    }

    /**
     * Clear cache for a specific school (useful after updates)
     */
    public function clearSchoolCache($school_id)
    {
        $cache_key = "school_{$school_id}";
        $this->cache->delete($cache_key);
        unset($this->school_cache[$school_id]);
    }

    /**
     * Clear plan cache (useful after plan updates)
     */
    public function clearPlanCache()
    {
        $this->cache->delete('default_subscription_plan');
        $this->plan_cache = [];
    }
}
?>
