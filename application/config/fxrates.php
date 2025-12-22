<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| FX Rates Configuration
|--------------------------------------------------------------------------
|
| Configuration for ExchangeRate-API integration
| API Documentation: https://www.exchangerate-api.com/docs/overview
|
*/

/*
|--------------------------------------------------------------------------
| API Key
|--------------------------------------------------------------------------
|
| Get your free API key from: https://www.exchangerate-api.com/
|
*/
$config['fxrates_api_key'] = 'be04c41a7f57bbb46fdfe5eb';

/*
|--------------------------------------------------------------------------
| API Base URL
|--------------------------------------------------------------------------
|
| ExchangeRate-API v6 endpoint
|
*/
$config['fxrates_api_url'] = 'https://v6.exchangerate-api.com/v6/';

/*
|--------------------------------------------------------------------------
| Base Currency
|--------------------------------------------------------------------------
|
| The base currency for all conversions (USD = 1)
|
*/
$config['fxrates_base_currency'] = 'USD';

/*
|--------------------------------------------------------------------------
| Target Currencies
|--------------------------------------------------------------------------
|
| List of currencies to track and store
|
*/
$config['fxrates_currencies'] = ['USD', 'EUR', 'MAD', 'AED', 'GBP'];

/*
|--------------------------------------------------------------------------
| Cache Settings
|--------------------------------------------------------------------------
|
| Cache duration in seconds (default: 10 minutes = 600 seconds)
| Set to 0 to disable caching
|
*/
$config['fxrates_cache_duration'] = 600;

/*
|--------------------------------------------------------------------------
| Cache Key Prefix
|--------------------------------------------------------------------------
|
| Prefix for cache keys to avoid collisions
|
*/
$config['fxrates_cache_prefix'] = 'fxrates_';

/*
|--------------------------------------------------------------------------
| API Timeout
|--------------------------------------------------------------------------
|
| cURL timeout in seconds for API calls
|
*/
$config['fxrates_api_timeout'] = 30;

/*
|--------------------------------------------------------------------------
| API Connect Timeout
|--------------------------------------------------------------------------
|
| cURL connection timeout in seconds
|
*/
$config['fxrates_api_connect_timeout'] = 10;

/*
|--------------------------------------------------------------------------
| Internal API Token
|--------------------------------------------------------------------------
|
| Token for protecting internal API endpoints
| Generate a secure random string: openssl rand -hex 32
|
*/
$config['fxrates_api_token'] = 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6';

/*
|--------------------------------------------------------------------------
| Cron Token
|--------------------------------------------------------------------------
|
| Token for protecting cron endpoint
| Generate a secure random string: openssl rand -hex 32
|
*/
$config['fxrates_cron_token'] = 'cron_z9y8x7w6v5u4t3s2r1q0p9o8n7m6l5k4j3i2h1g0f9e8d7c6b5a4';

/*
|--------------------------------------------------------------------------
| Store Raw JSON
|--------------------------------------------------------------------------
|
| Whether to store the raw API response JSON in database
| Useful for debugging but increases storage
|
*/
$config['fxrates_store_raw_json'] = true;

/*
|--------------------------------------------------------------------------
| Timezone
|--------------------------------------------------------------------------
|
| Timezone for date calculations (use UTC for consistency)
|
*/
$config['fxrates_timezone'] = 'UTC';
