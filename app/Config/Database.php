<?php

namespace Config;

use CodeIgniter\Database\Config;

class Database extends Config
{
    public string $defaultGroup = 'default';

    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'wayodb',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->default['hostname'] = (string) env('database.default.hostname', $this->default['hostname']);
        $this->default['username'] = (string) env('database.default.username', $this->default['username']);
        $this->default['password'] = (string) env('database.default.password', $this->default['password']);
        $this->default['database'] = (string) env('database.default.database', $this->default['database']);
        $this->default['DBDriver'] = (string) env('database.default.DBDriver', $this->default['DBDriver']);
        $this->default['DBDebug']  = (ENVIRONMENT === 'development');
    }
}
