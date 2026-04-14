<?php

namespace App\Libraries;
/**
 * Morocco B2B Service
 *
 * Handles Morocco-specific B2B invoicing calculations with CashPlus processor fees
 *
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class MoroccoB2BService {

    // Morocco B2B constants
    const COUNTRY_CODE = 'MA';
    const CUSTOMER_TYPE = 'B2B';
    const VAT_RATE = 0.20; // 20%
    const CASHPLUS_FEE_RATE = 0.03; // 3%
    const LEGAL_ENTITY = 'Decloedt SARL';

    // Rounding precision
    const ROUNDING_PRECISION = 2;

    public function __construct($params = null)
    {
    }

    /**
     * Check if Morocco B2B rules should apply
     *
     * @param array $context Context data (country, customer_type, payment_type)
     * @return bool
     */
    public function shouldApplyMoroccoB2BRules($context)
    {
        return isset($context['country']) &&
               isset($context['customer_type']) &&
               isset($context['payment_type']) &&
               $context['country'] === self::COUNTRY_CODE &&
               $context['customer_type'] === self::CUSTOMER_TYPE &&
               $context['payment_type'] === 'subscription_admin';
    }

    /**
     * Calculate Morocco B2B invoice amounts with CashPlus fees
     *
     * @param float $total_ttc Total TTC amount (790 MAD)
     * @param float $vat_rate VAT rate (0.20 for Morocco)
     * @param float $cashplus_rate CashPlus fee rate (0.03)
     * @return array Calculation results
     */
    public function calculateMoroccoB2BAmounts($total_ttc, $vat_rate = null, $cashplus_rate = null)
    {
        // Use defaults if not provided
        $vat_rate = $vat_rate ?? self::VAT_RATE;
        $cashplus_rate = $cashplus_rate ?? self::CASHPLUS_FEE_RATE;

        // Ensure consistent rounding
        $total_ttc = round($total_ttc, self::ROUNDING_PRECISION);

        // Step 1: Calculate sale HT (reverse calculation from TTC)
        $sale_ht = $total_ttc / (1 + $vat_rate);
        $sale_ht = round($sale_ht, self::ROUNDING_PRECISION);

        // Step 2: Calculate sale VAT
        $sale_vat = $total_ttc - $sale_ht;
        $sale_vat = round($sale_vat, self::ROUNDING_PRECISION);

        // Step 3: Calculate CashPlus fee HT (3% of sale HT)
        $fee_ht = $sale_ht * $cashplus_rate;
        $fee_ht = round($fee_ht, self::ROUNDING_PRECISION);

        // Step 4: Calculate CashPlus fee VAT (20% of fee HT)
        $fee_vat = $fee_ht * $vat_rate;
        $fee_vat = round($fee_vat, self::ROUNDING_PRECISION);

        // Step 5: Calculate CashPlus fee TTC
        $fee_ttc = $fee_ht + $fee_vat;
        $fee_ttc = round($fee_ttc, self::ROUNDING_PRECISION);

        // Step 6: Calculate net cash received (TTC - sale VAT - fee TTC)
        $net_cash = $total_ttc - $sale_vat - $fee_ttc;
        $net_cash = round($net_cash, self::ROUNDING_PRECISION);

        // Step 7: Calculate net economic (HT - fee HT) - B2B deductible
        $net_economic = $sale_ht - $fee_ht;
        $net_economic = round($net_economic, self::ROUNDING_PRECISION);

        return [
            'sale_ht' => $sale_ht,
            'sale_vat' => $sale_vat,
            'fee_ht' => $fee_ht,
            'fee_vat' => $fee_vat,
            'fee_ttc' => $fee_ttc,
            'net_cash' => $net_cash,
            'net_economic' => $net_economic,
            'total_ttc' => $total_ttc,
            'vat_rate' => $vat_rate,
            'cashplus_rate' => $cashplus_rate,
            'legal_entity' => self::LEGAL_ENTITY,
            'applied_rules' => 'Morocco B2B'
        ];
    }

    /**
     * Apply Morocco B2B calculations to invoice data
     *
     * @param array $invoice_data Raw invoice data
     * @return array Updated invoice data with Morocco calculations
     */
    public function applyMoroccoB2BToInvoice($invoice_data)
    {
        $total_ttc = $invoice_data['total_amount'];

        // Calculate Morocco B2B amounts
        $calculations = $this->calculateMoroccoB2BAmounts($total_ttc);

        // Update invoice data with calculated values (only if columns exist)
        $invoice_data['sub_total'] = $calculations['sale_ht'];
        $invoice_data['vat_rate'] = $calculations['vat_rate'] * 100; // Store as percentage
        $invoice_data['vat_amount'] = $calculations['sale_vat'];

        // Add Morocco B2B specific fields (only if they exist in the table)
        $morocco_fields = [
            'processor_fee_ht' => $calculations['fee_ht'],
            'processor_fee_vat' => $calculations['fee_vat'],
            'processor_fee_ttc' => $calculations['fee_ttc'],
            'net_cash' => $calculations['net_cash'],
            'net_economic' => $calculations['net_economic']
        ];

        foreach ($morocco_fields as $field => $value) {
            $invoice_data[$field] = $value;
        }

        // Store legal entity in session or as metadata (not in DB column)
        // $invoice_data['legal_entity'] = $calculations['legal_entity'];

        return $invoice_data;
    }

    /**
     * Apply Morocco B2B calculations to payment data
     *
     * @param array $payment_data Raw payment data
     * @param array $invoice_data Related invoice data
     * @return array Updated payment data with Morocco calculations
     */
    public function applyMoroccoB2BToPayment($payment_data, $invoice_data)
    {
        if (!$this->shouldApplyMoroccoB2BRules([
            'country' => $invoice_data['country'] ?? null,
            'customer_type' => $invoice_data['customer_type'] ?? null,
            'payment_type' => $invoice_data['payment_type'] ?? null
        ])) {
            return $payment_data; // No changes if rules don't apply
        }

        // For Morocco B2B, use configured PSP (Stripe by default, CashPlus disabled)
        $payment_data['processor_name'] = 'Stripe';
        $payment_data['processor_fee_ht'] = $invoice_data['processor_fee_ht'] ?? null;
        $payment_data['processor_fee_vat'] = $invoice_data['processor_fee_vat'] ?? null;
        $payment_data['processor_fee_ttc'] = $invoice_data['processor_fee_ttc'] ?? null;

        return $payment_data;
    }

    /**
     * Get Morocco B2B display data for UI
     *
     * @param array $invoice_data Invoice data with Morocco calculations
     * @param array $context Optional context override (for subscription detection)
     * @return array Display data for frontend
     */
    public function getMoroccoB2BDisplayData($invoice_data, $context = null)
    {
        // Use provided context or extract from invoice data
        $check_context = $context ?? [
            'country' => $invoice_data['country'] ?? null,
            'customer_type' => $invoice_data['customer_type'] ?? null,
            'payment_type' => $invoice_data['payment_type'] ?? null
        ];

        if (!$this->shouldApplyMoroccoB2BRules($check_context)) {
            return []; // No display data if rules don't apply
        }

        return [
            'morocco_b2b_badge' => 'Morocco (B2B) rule applied',
            'legal_entity' => $invoice_data['legal_entity'] ?? self::LEGAL_ENTITY,
            'breakdown' => [
                'total_ttc' => number_format($invoice_data['total_amount'], 2, '.', ' ') . ' MAD',
                'sale_ht' => number_format($invoice_data['sub_total'], 2, '.', ' ') . ' MAD',
                'sale_vat' => number_format($invoice_data['vat_amount'], 2, '.', ' ') . ' MAD (' . $invoice_data['vat_rate'] . '%)',
                'cashplus_fee_ht' => number_format($invoice_data['processor_fee_ht'], 2, '.', ' ') . ' MAD',
                'cashplus_fee_vat' => number_format($invoice_data['processor_fee_vat'], 2, '.', ' ') . ' MAD',
                'cashplus_fee_ttc' => number_format($invoice_data['processor_fee_ttc'], 2, '.', ' ') . ' MAD',
                'net_cash' => number_format($invoice_data['net_cash'], 2, '.', ' ') . ' MAD',
                'net_economic' => number_format($invoice_data['net_economic'], 2, '.', ' ') . ' MAD'
            ]
        ];
    }

    /**
     * Validate Morocco B2B calculation results
     *
     * @param array $calculations Calculation results to validate
     * @return array Validation result with success/error messages
     */
    public function validateMoroccoB2BCalculations($calculations)
    {
        $errors = [];

        // Expected values for total_ttc=790
        $expected = [
            'sale_ht' => 658.33,
            'sale_vat' => 131.67,
            'fee_ht' => 19.75,
            'fee_vat' => 3.95,
            'fee_ttc' => 23.70,
            'net_cash' => 634.63,
            'net_economic' => 638.58
        ];

        foreach ($expected as $field => $expected_value) {
            if (!isset($calculations[$field])) {
                $errors[] = "Missing field: {$field}";
                continue;
            }

            $actual = round($calculations[$field], self::ROUNDING_PRECISION);
            if (abs($actual - $expected_value) > 0.01) { // Allow small rounding differences
                $errors[] = "Field {$field}: expected {$expected_value}, got {$actual}";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}

