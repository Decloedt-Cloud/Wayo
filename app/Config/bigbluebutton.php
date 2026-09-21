<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Bigbluebutton extends BaseConfig
{
    public $bbb_url = 'https://visio.wayo.site/bigbluebutton/api/';
    public $bbb_url_play = 'https://visio.wayo.site';
    public $bbb_url_old = 'https://visio.wayo.site:80/';
    public $bbb_secret = '';

    public $bbb_hook_token = '';
    public $socket_emit_url = 'https://preprod.wayo.site/emit';
    public $socket_emit_key = '';
    public $app_base_url = 'https://votre-domaine.tld';

    public function __construct()
    {
        parent::__construct();

        $this->bbb_url = (string) env('bbb.url', $this->bbb_url);
        $this->bbb_url_play = (string) env('bbb.urlPlay', $this->bbb_url_play);
        $this->bbb_url_old = (string) env('bbb.urlOld', $this->bbb_url_old);
        $this->bbb_secret = (string) env('bbb.secret', $this->bbb_secret);
        $this->bbb_hook_token = (string) env('bbb.hookToken', $this->bbb_hook_token);
        $this->socket_emit_url = (string) env('bbb.socketEmitUrl', $this->socket_emit_url);
        $this->socket_emit_key = (string) env('bbb.socketEmitKey', $this->socket_emit_key);
        $this->app_base_url = (string) env('bbb.appBaseUrl', $this->app_base_url);
    }
}
