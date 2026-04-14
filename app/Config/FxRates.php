<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class FxRates extends BaseConfig
{
    public $fxrates_api_key = '';
    public $fxrates_api_url = 'https://v6.exchangerate-api.com/v6/';
    public $fxrates_base_currency = 'USD';
    public $fxrates_currencies = ['USD', 'EUR', 'MAD', 'AED'];
    public $fxrates_cache_duration = 600;
    public $fxrates_cache_prefix = 'fxrates_';
    public $fxrates_api_timeout = 30;
    public $fxrates_api_connect_timeout = 10;
    public $fxrates_store_raw_json = true;
    public $fxrates_timezone = 'UTC';
    public $fxrates_api_token = '';
    public $fxrates_cron_token = '';
}
