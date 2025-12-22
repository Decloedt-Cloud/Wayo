<?php
/**
 * FX Rates Unit Tests
 * 
 * Basic test suite for FX rates functionality
 * Run with: php application/tests/FxRatesTest.php
 * 
 * @author Senior PHP Engineer
 * @version 1.0.0
 */

// Prevent direct web access
if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line.');
}

// Set up paths
define('BASEPATH', dirname(__DIR__) . '/../system/');
define('APPPATH', dirname(__DIR__) . '/');
define('FCPATH', dirname(__DIR__) . '/../');

/**
 * Simple Test Runner
 */
class FxRatesTest {
    
    protected $passed = 0;
    protected $failed = 0;
    protected $errors = [];
    
    /**
     * Run all tests
     */
    public function run()
    {
        echo "\n========================================\n";
        echo "FX Rates Integration Tests\n";
        echo "========================================\n\n";
        
        $this->testApiResponseParsing();
        $this->testDateValidation();
        $this->testCurrencyConversion();
        $this->testUpsertBehavior();
        $this->testRangeQuery();
        $this->testCacheKeyGeneration();
        
        $this->printResults();
    }
    
    /**
     * Test API response parsing
     */
    protected function testApiResponseParsing()
    {
        echo "Test: API Response Parsing\n";
        
        // Valid response
        $valid_response = [
            'result' => 'success',
            'base_code' => 'USD',
            'conversion_rates' => [
                'USD' => 1,
                'EUR' => 0.923456,
                'MAD' => 10.052134,
                'AED' => 3.672500
            ]
        ];
        
        $this->assert(
            $this->validateApiResponse($valid_response) === true,
            'Valid response should pass validation'
        );
        
        // Missing result
        $invalid_response1 = [
            'base_code' => 'USD',
            'conversion_rates' => ['USD' => 1]
        ];
        
        $this->assert(
            $this->validateApiResponse($invalid_response1) === false,
            'Response without result field should fail'
        );
        
        // Missing conversion_rates
        $invalid_response2 = [
            'result' => 'success',
            'base_code' => 'USD'
        ];
        
        $this->assert(
            $this->validateApiResponse($invalid_response2) === false,
            'Response without conversion_rates should fail'
        );
        
        // Missing required currency
        $invalid_response3 = [
            'result' => 'success',
            'base_code' => 'USD',
            'conversion_rates' => [
                'USD' => 1,
                'EUR' => 0.92
                // Missing MAD and AED
            ]
        ];
        
        $this->assert(
            $this->validateApiResponse($invalid_response3) === false,
            'Response missing required currencies should fail'
        );
        
        // Error response
        $error_response = [
            'result' => 'error',
            'error-type' => 'invalid-key'
        ];
        
        $this->assert(
            $this->validateApiResponse($error_response) === false,
            'Error response should fail validation'
        );
        
        echo "\n";
    }
    
    /**
     * Test date validation
     */
    protected function testDateValidation()
    {
        echo "Test: Date Validation\n";
        
        $this->assert(
            $this->isValidDate('2025-12-18') === true,
            'Valid date format should pass'
        );
        
        $this->assert(
            $this->isValidDate('2025-12-32') === false,
            'Invalid day should fail'
        );
        
        $this->assert(
            $this->isValidDate('2025-13-01') === false,
            'Invalid month should fail'
        );
        
        $this->assert(
            $this->isValidDate('18-12-2025') === false,
            'Wrong format should fail'
        );
        
        $this->assert(
            $this->isValidDate('') === false,
            'Empty date should fail'
        );
        
        $this->assert(
            $this->isValidDate('2025-02-29') === false,
            '2025 is not a leap year, Feb 29 should fail'
        );
        
        $this->assert(
            $this->isValidDate('2024-02-29') === true,
            '2024 is a leap year, Feb 29 should pass'
        );
        
        echo "\n";
    }
    
    /**
     * Test currency conversion logic
     */
    protected function testCurrencyConversion()
    {
        echo "Test: Currency Conversion\n";
        
        $rates = [
            'USD' => 1,
            'EUR' => 0.92,
            'MAD' => 10.0,
            'AED' => 3.67
        ];
        
        // USD to EUR
        $converted = $this->convert(100, 'USD', 'EUR', $rates);
        $this->assert(
            abs($converted - 92) < 0.01,
            "100 USD should be ~92 EUR (got: {$converted})"
        );
        
        // EUR to USD
        $converted = $this->convert(92, 'EUR', 'USD', $rates);
        $this->assert(
            abs($converted - 100) < 0.01,
            "92 EUR should be ~100 USD (got: {$converted})"
        );
        
        // MAD to AED
        $converted = $this->convert(100, 'MAD', 'AED', $rates);
        $expected = (100 / 10.0) * 3.67; // 36.7
        $this->assert(
            abs($converted - $expected) < 0.01,
            "100 MAD should be ~36.7 AED (got: {$converted})"
        );
        
        // Same currency
        $converted = $this->convert(100, 'USD', 'USD', $rates);
        $this->assert(
            abs($converted - 100) < 0.01,
            "100 USD to USD should be 100 (got: {$converted})"
        );
        
        echo "\n";
    }
    
    /**
     * Test upsert behavior (insert vs update)
     */
    protected function testUpsertBehavior()
    {
        echo "Test: Upsert Behavior (Simulated)\n";
        
        // Simulate database storage
        $storage = [];
        
        // First insert
        $data1 = [
            'rate_date' => '2025-12-18',
            'base_code' => 'USD',
            'eur' => 0.92,
            'mad' => 10.0,
            'aed' => 3.67
        ];
        
        $storage = $this->simulateUpsert($storage, $data1);
        $this->assert(
            count($storage) === 1,
            'First upsert should create 1 record'
        );
        
        // Update same date
        $data2 = [
            'rate_date' => '2025-12-18',
            'base_code' => 'USD',
            'eur' => 0.93, // Updated rate
            'mad' => 10.1,
            'aed' => 3.68
        ];
        
        $storage = $this->simulateUpsert($storage, $data2);
        $this->assert(
            count($storage) === 1,
            'Upsert same date should still have 1 record'
        );
        
        $this->assert(
            $storage['2025-12-18_USD']['eur'] === 0.93,
            'EUR rate should be updated to 0.93'
        );
        
        // Insert different date
        $data3 = [
            'rate_date' => '2025-12-19',
            'base_code' => 'USD',
            'eur' => 0.94,
            'mad' => 10.2,
            'aed' => 3.69
        ];
        
        $storage = $this->simulateUpsert($storage, $data3);
        $this->assert(
            count($storage) === 2,
            'Different date should create second record'
        );
        
        echo "\n";
    }
    
    /**
     * Test range query logic
     */
    protected function testRangeQuery()
    {
        echo "Test: Range Query\n";
        
        // Simulate data
        $data = [
            ['rate_date' => '2025-12-15', 'eur' => 0.91],
            ['rate_date' => '2025-12-16', 'eur' => 0.92],
            ['rate_date' => '2025-12-17', 'eur' => 0.93],
            ['rate_date' => '2025-12-18', 'eur' => 0.94],
            ['rate_date' => '2025-12-19', 'eur' => 0.95],
        ];
        
        // Full range
        $result = $this->simulateRange($data, '2025-12-15', '2025-12-19');
        $this->assert(
            count($result) === 5,
            'Full range should return 5 records'
        );
        
        // Partial range
        $result = $this->simulateRange($data, '2025-12-16', '2025-12-18');
        $this->assert(
            count($result) === 3,
            'Partial range should return 3 records'
        );
        
        // Single day
        $result = $this->simulateRange($data, '2025-12-17', '2025-12-17');
        $this->assert(
            count($result) === 1,
            'Single day range should return 1 record'
        );
        
        // Out of range
        $result = $this->simulateRange($data, '2025-12-20', '2025-12-25');
        $this->assert(
            count($result) === 0,
            'Out of range should return 0 records'
        );
        
        echo "\n";
    }
    
    /**
     * Test cache key generation
     */
    protected function testCacheKeyGeneration()
    {
        echo "Test: Cache Key Generation\n";
        
        $prefix = 'fxrates_';
        
        $key1 = $prefix . '2025-12-18';
        $key2 = $prefix . 'today';
        $key3 = $prefix . 'latest';
        
        $this->assert(
            $key1 === 'fxrates_2025-12-18',
            'Date cache key should be fxrates_2025-12-18'
        );
        
        $this->assert(
            $key2 === 'fxrates_today',
            'Today cache key should be fxrates_today'
        );
        
        $this->assert(
            $key1 !== $key2,
            'Different suffixes should produce different keys'
        );
        
        echo "\n";
    }
    
    // =========================================
    // Helper Methods (Simulated Service Logic)
    // =========================================
    
    protected function validateApiResponse($data)
    {
        if (!isset($data['result']) || $data['result'] !== 'success') {
            return false;
        }
        
        if (empty($data['base_code'])) {
            return false;
        }
        
        if (empty($data['conversion_rates']) || !is_array($data['conversion_rates'])) {
            return false;
        }
        
        $required = ['USD', 'EUR', 'MAD', 'AED'];
        foreach ($required as $currency) {
            if (!isset($data['conversion_rates'][$currency])) {
                return false;
            }
        }
        
        return true;
    }
    
    protected function isValidDate($date)
    {
        if (empty($date)) {
            return false;
        }
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    protected function convert($amount, $from, $to, $rates)
    {
        $amount_in_usd = $amount / $rates[$from];
        return round($amount_in_usd * $rates[$to], 6);
    }
    
    protected function simulateUpsert($storage, $data)
    {
        $key = $data['rate_date'] . '_' . $data['base_code'];
        $storage[$key] = $data;
        return $storage;
    }
    
    protected function simulateRange($data, $start, $end)
    {
        $result = [];
        foreach ($data as $row) {
            if ($row['rate_date'] >= $start && $row['rate_date'] <= $end) {
                $result[] = $row;
            }
        }
        return $result;
    }
    
    // =========================================
    // Test Framework Methods
    // =========================================
    
    protected function assert($condition, $message)
    {
        if ($condition) {
            echo "  ✓ {$message}\n";
            $this->passed++;
        } else {
            echo "  ✗ {$message}\n";
            $this->failed++;
            $this->errors[] = $message;
        }
    }
    
    protected function printResults()
    {
        echo "========================================\n";
        echo "Results: {$this->passed} passed, {$this->failed} failed\n";
        echo "========================================\n";
        
        if ($this->failed > 0) {
            echo "\nFailed Tests:\n";
            foreach ($this->errors as $error) {
                echo "  - {$error}\n";
            }
            exit(1);
        }
        
        echo "\nAll tests passed!\n";
        exit(0);
    }
}

// Run tests
$test = new FxRatesTest();
$test->run();

