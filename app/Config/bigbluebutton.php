<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Bigbluebutton extends BaseConfig
{
    public $bbb_url = 'https://visio.wayo.site/bigbluebutton/api/';
    public $bbb_url_play = 'https://visio.wayo.site';
    public $bbb_url_old = 'https://visio.wayo.site:80/';
    public $bbb_secret = 'AnvikgYDf1o0eJwO5qzNhWYGAoatXoZfgxMfotilc';

    public $bbb_hook_token = 'y7Xy-ULTRA-SECRET';
    public $socket_emit_url = 'https://preprod.wayo.site/emit';
    public $socket_emit_key = 'S3cr3t-Emit-Key';
    public $app_base_url = 'https://votre-domaine.tld';
}
