<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AppModules extends BaseConfig
{
    public $enabled = true;
    public $discover = [
        'root'   => [APPPATH . 'Modules'],
        'suffix' => '',
    ];
    public $composerPath = null;
    public $defaultNamespace = 'App';
    public $controller = 'Controller';
}
