<?php

namespace App\Libraries;
/**
 * VAT Resolver Service
 *
 * Handles VAT calculation based on community tax residence country
 *
 * @author Senior Backend Engineer
 * @version 1.0.0
 */
class VatResolver {

    // Supported countries and their VAT rates
    const SUPPORTED_COUNTRIES = [
        'MA' => [
            'name' => 'Morocco',
            'vat_rate' => 0.20, // 20%
            'legal_entity' => 'Decloedt SARL',
            'legal_entity_country' => 'Morocco'
        ],
        'AE' => [
            'name' => 'United Arab Emirates',
            'vat_rate' => 0.05, // 5%
            'legal_entity' => 'Bouhouti',
            'legal_entity_country' => 'United Arab Emirates'
        ]
    ];

    // Rounding precision for all calculations
    const ROUNDING_PRECISION = 2;

    public function __construct($params = null)
    {
    }

    /**
     * Resolve VAT information for a community
     *
     * @param string $country_code Two-letter country code (MA, AE, UAE)
     * @return array VAT information or throws exception
     * @throws Exception If country not supported
     */
    public function resolveVatForCountry($country_code)
    {
        $country_code = strtoupper(trim($country_code));

        // Handle country code aliases
        $country_aliases = [
            'UAE' => 'AE', // United Arab Emirates can be UAE or AE
        ];

        if (isset($country_aliases[$country_code])) {
            $country_code = $country_aliases[$country_code];
        }

        if (!isset(self::SUPPORTED_COUNTRIES[$country_code])) {
            throw new Exception("Unsupported country for VAT logic: '{$country_code}'. Supported countries: " . implode(', ', array_keys(self::SUPPORTED_COUNTRIES)));
        }

        return self::SUPPORTED_COUNTRIES[$country_code];
    }

    /**
     * Calculate VAT breakdown for a TTC amount
     *
     * @param float $total_ttc Total amount including VAT
     * @param string $country_code Two-letter country code
     * @return array Calculation breakdown
     * @throws Exception If country not supported
     */
    public function calculateVatBreakdown($total_ttc, $country_code)
    {
        $vat_info = $this->resolveVatForCountry($country_code);

        // Round input to ensure consistency
        $total_ttc = round($total_ttc, self::ROUNDING_PRECISION);

        // Calculate HT amount: TTC / (1 + VAT_RATE)
        $sub_total = $total_ttc / (1 + $vat_info['vat_rate']);
        $sub_total = round($sub_total, self::ROUNDING_PRECISION);

        // Calculate VAT amount: TTC - HT
        $vat_amount = $total_ttc - $sub_total;
        $vat_amount = round($vat_amount, self::ROUNDING_PRECISION);

        // Verification: HT + VAT should equal TTC
        $verification = round($sub_total + $vat_amount, self::ROUNDING_PRECISION);
        if (abs($verification - $total_ttc) > 0.01) {
            throw new Exception("VAT calculation error: HT + VAT ({$verification}) != TTC ({$total_ttc})");
        }

        return [
            'country_code' => $country_code,
            'country_name' => $vat_info['name'],
            'vat_rate' => $vat_info['vat_rate'],
            'vat_rate_percentage' => ($vat_info['vat_rate'] * 100) . '%',
            'total_ttc' => $total_ttc,
            'sub_total' => $sub_total,
            'vat_amount' => $vat_amount,
            'legal_entity_name' => $vat_info['legal_entity'],
            'legal_entity_country' => $vat_info['legal_entity_country'],
            'tax_residence_country' => $vat_info['name']
        ];
    }

    /**
     * Get supported countries list
     *
     * @return array List of supported countries with their info
     */
    public function getSupportedCountries()
    {
        return self::SUPPORTED_COUNTRIES;
    }

    /**
     * Check if a country is supported
     *
     * @param string $country_code Two-letter country code
     * @return bool
     */
    public function isCountrySupported($country_code)
    {
        $country_code = strtoupper(trim($country_code));

        // Handle country code aliases
        $country_aliases = [
            'UAE' => 'AE',
        ];

        if (isset($country_aliases[$country_code])) {
            $country_code = $country_aliases[$country_code];
        }

        return isset(self::SUPPORTED_COUNTRIES[$country_code]);
    }

    /**
     * Get VAT rate for a country
     *
     * @param string $country_code Two-letter country code
     * @return float VAT rate (0.20 for 20%, etc.)
     * @throws Exception If country not supported
     */
    public function getVatRateForCountry($country_code)
    {
        $vat_info = $this->resolveVatForCountry($country_code);
        return $vat_info['vat_rate'];
    }

    /**
     * Validate VAT calculation results
     *
     * @param array $calculation Results from calculateVatBreakdown
     * @return array Validation result
     */
    public function validateVatCalculation($calculation)
    {
        $errors = [];

        // Check that HT + VAT = TTC
        $calculated_ttc = round($calculation['sub_total'] + $calculation['vat_amount'], self::ROUNDING_PRECISION);
        if (abs($calculated_ttc - $calculation['total_ttc']) > 0.01) {
            $errors[] = "HT + VAT ({$calculated_ttc}) != TTC ({$calculation['total_ttc']})";
        }

        // Check VAT amount consistency
        $expected_vat = round($calculation['sub_total'] * $calculation['vat_rate'], self::ROUNDING_PRECISION);
        if (abs($expected_vat - $calculation['vat_amount']) > 0.01) {
            $errors[] = "VAT amount ({$calculation['vat_amount']}) != HT × rate ({$expected_vat})";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
