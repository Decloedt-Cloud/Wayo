<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Morocco B2B Service Unit Tests
 *
 * Tests for MoroccoB2BService calculations and logic
 *
 * @author Senior PHP Engineer
 * @version 1.0.0
 */
class MoroccoB2BServiceTest extends CI_Controller {

    private $moroccoService;

    public function __construct()
    {
        parent::__construct();

        // Load the service to test
        $this->load->library('MoroccoB2BService', null, 'moroccoService');
        $this->moroccoService = $this->moroccoService;
    }

    /**
     * Main test runner
     */
    public function index()
    {
        header('Content-Type: text/plain');

        echo "=== Morocco B2B Service Tests ===\n\n";

        $tests = [
            'testMoroccoB2BRulesApplication' => 'Test Morocco B2B rules application',
            'testMoroccoB2BCalculations' => 'Test Morocco B2B calculations',
            'testMoroccoB2BCalculationValidation' => 'Test Morocco B2B calculation validation',
            'testMoroccoB2BInvoiceApplication' => 'Test Morocco B2B invoice application',
            'testMoroccoB2BPaymentApplication' => 'Test Morocco B2B payment application',
            'testMoroccoB2BDisplayData' => 'Test Morocco B2B display data'
        ];

        $passed = 0;
        $total = count($tests);

        foreach ($tests as $method => $description) {
            echo "Running: $description\n";
            try {
                $result = $this->$method();
                if ($result === true) {
                    echo "✓ PASSED\n";
                    $passed++;
                } else {
                    echo "✗ FAILED: $result\n";
                }
            } catch (Exception $e) {
                echo "✗ ERROR: " . $e->getMessage() . "\n";
            }
            echo "\n";
        }

        echo "=== Results: $passed/$total tests passed ===\n";

        if ($passed === $total) {
            echo "🎉 All tests passed! Morocco B2B service is working correctly.\n";
        } else {
            echo "❌ Some tests failed. Please check the implementation.\n";
        }
    }

    /**
     * Test Morocco B2B rules application
     */
    private function testMoroccoB2BRulesApplication()
    {
        // Test cases for shouldApplyMoroccoB2BRules
        $testCases = [
            // Should apply
            [
                'context' => [
                    'country' => 'MA',
                    'customer_type' => 'B2B',
                    'payment_type' => 'subscription_admin'
                ],
                'expected' => true,
                'description' => 'Valid Morocco B2B subscription'
            ],
            // Should not apply - wrong country
            [
                'context' => [
                    'country' => 'FR',
                    'customer_type' => 'B2B',
                    'payment_type' => 'subscription_admin'
                ],
                'expected' => false,
                'description' => 'Wrong country'
            ],
            // Should not apply - wrong customer type
            [
                'context' => [
                    'country' => 'MA',
                    'customer_type' => 'B2C',
                    'payment_type' => 'subscription_admin'
                ],
                'expected' => false,
                'description' => 'Wrong customer type'
            ],
            // Should not apply - wrong payment type
            [
                'context' => [
                    'country' => 'MA',
                    'customer_type' => 'B2B',
                    'payment_type' => 'class_enrol'
                ],
                'expected' => false,
                'description' => 'Wrong payment type'
            ]
        ];

        foreach ($testCases as $testCase) {
            $result = $this->moroccoService->shouldApplyMoroccoB2BRules($testCase['context']);
            if ($result !== $testCase['expected']) {
                return "Failed: {$testCase['description']} - Expected: {$testCase['expected']}, Got: $result";
            }
        }

        return true;
    }

    /**
     * Test Morocco B2B calculations
     */
    private function testMoroccoB2BCalculations()
    {
        // Test the exact expected values
        $calculations = $this->moroccoService->calculateMoroccoB2BAmounts(790.00);

        $expected = [
            'sale_ht' => 658.33,
            'sale_vat' => 131.67,
            'fee_ht' => 19.75,
            'fee_vat' => 3.95,
            'fee_ttc' => 23.70,
            'net_cash' => 634.63,
            'net_economic' => 638.58,
            'total_ttc' => 790.00,
            'vat_rate' => 0.20,
            'cashplus_rate' => 0.03,
            'legal_entity' => 'Decloedt SARL',
            'applied_rules' => 'Morocco B2B CashPlus'
        ];

        foreach ($expected as $field => $expected_value) {
            if (!isset($calculations[$field])) {
                return "Missing field: $field";
            }

            // Allow small rounding differences
            $actual = round($calculations[$field], 2);
            if ($field === 'vat_rate' || $field === 'cashplus_rate') {
                // For rates, compare exactly
                if (abs($actual - $expected_value) > 0.001) {
                    return "Field $field: expected $expected_value, got $actual";
                }
            } else {
                // For monetary values, allow 0.01 difference
                if (abs($actual - $expected_value) > 0.01) {
                    return "Field $field: expected $expected_value, got $actual";
                }
            }
        }

        return true;
    }

    /**
     * Test Morocco B2B calculation validation
     */
    private function testMoroccoB2BCalculationValidation()
    {
        // Test with correct calculations
        $calculations = $this->moroccoService->calculateMoroccoB2BAmounts(790.00);
        $validation = $this->moroccoService->validateMoroccoB2BCalculations($calculations);

        if (!$validation['valid']) {
            return "Valid calculations failed validation: " . implode(', ', $validation['errors']);
        }

        // Test with incorrect calculations
        $wrongCalculations = $calculations;
        $wrongCalculations['sale_ht'] = 600.00; // Wrong value

        $wrongValidation = $this->moroccoService->validateMoroccoB2BCalculations($wrongCalculations);
        if ($wrongValidation['valid']) {
            return "Invalid calculations passed validation";
        }

        return true;
    }

    /**
     * Test Morocco B2B invoice application
     */
    private function testMoroccoB2BInvoiceApplication()
    {
        // Test invoice that should have Morocco rules applied
        $invoice_data = [
            'total_amount' => 790.00,
            'country' => 'MA',
            'customer_type' => 'B2B',
            'payment_type' => 'subscription_admin'
        ];

        $result = $this->moroccoService->applyMoroccoB2BToInvoice($invoice_data);

        // Should have applied calculations
        if (!isset($result['processor_fee_ht']) || $result['processor_fee_ht'] != 19.75) {
            return "Morocco calculations not applied to invoice";
        }

        // Test invoice that should NOT have Morocco rules applied
        $non_morocco_invoice = [
            'total_amount' => 790.00,
            'country' => 'FR',
            'customer_type' => 'B2B',
            'payment_type' => 'subscription_admin'
        ];

        $non_morocco_result = $this->moroccoService->applyMoroccoB2BToInvoice($non_morocco_invoice);

        // Should not have applied calculations
        if (isset($non_morocco_result['processor_fee_ht'])) {
            return "Morocco calculations applied to non-Morocco invoice";
        }

        return true;
    }

    /**
     * Test Morocco B2B payment application
     */
    private function testMoroccoB2BPaymentApplication()
    {
        $payment_data = [
            'method' => 'stripe',
            'amount_paid' => 790.00
        ];

        $invoice_data = [
            'country' => 'MA',
            'customer_type' => 'B2B',
            'payment_type' => 'subscription_admin',
            'processor_fee_ht' => 19.75,
            'processor_fee_vat' => 3.95,
            'processor_fee_ttc' => 23.70
        ];

        $result = $this->moroccoService->applyMoroccoB2BToPayment($payment_data, $invoice_data);

        // Should have CashPlus processor info
        if ($result['processor_name'] !== 'CashPlus') {
            return "Payment processor not set to CashPlus";
        }

        if ($result['processor_fee_ht'] != 19.75) {
            return "Payment processor fees not applied";
        }

        return true;
    }

    /**
     * Test Morocco B2B display data
     */
    private function testMoroccoB2BDisplayData()
    {
        // Test with Morocco invoice
        $invoice_data = [
            'total_amount' => 790.00,
            'sub_total' => 658.33,
            'vat_amount' => 131.67,
            'processor_fee_ht' => 19.75,
            'processor_fee_vat' => 3.95,
            'processor_fee_ttc' => 23.70,
            'net_cash' => 634.63,
            'net_economic' => 638.58,
            'country' => 'MA',
            'customer_type' => 'B2B',
            'payment_type' => 'subscription_admin'
        ];

        $display_data = $this->moroccoService->getMoroccoB2BDisplayData($invoice_data);

        if (empty($display_data)) {
            return "Display data not generated for Morocco invoice";
        }

        if (!isset($display_data['morocco_b2b_badge'])) {
            return "Morocco badge not in display data";
        }

        if (!isset($display_data['breakdown'])) {
            return "Breakdown not in display data";
        }

        // Test with non-Morocco invoice
        $non_morocco_invoice = [
            'total_amount' => 790.00,
            'country' => 'FR',
            'customer_type' => 'B2B',
            'payment_type' => 'subscription_admin'
        ];

        $non_morocco_display = $this->moroccoService->getMoroccoB2BDisplayData($non_morocco_invoice);

        if (!empty($non_morocco_display)) {
            return "Display data generated for non-Morocco invoice";
        }

        return true;
    }
}


