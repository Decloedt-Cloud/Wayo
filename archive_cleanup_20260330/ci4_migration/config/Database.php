<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Database Configuration
 * 
 * CI4 Migration Template - Based on CI3 database.php
 * Copy to app/Config/Database.php when migrating to CI4
 */
class Database extends BaseConfig
{
    /**
     * The default database connection.
     */
    public array $default = [];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     */
    public array $tests = [];

    /**
     * Constructor - Load CI3 database config
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load CI3 database config
        $db_config_file = APPPATH . 'config/database.php';
        if (file_exists($db_config_file)) {
            include($db_config_file);
            
            if (isset($db) && isset($db['default'])) {
                $this->default = $db['default'];
                
                // CI4 requires 'DBDriver' instead of 'dbdriver'
                if (isset($this->default['dbdriver'])) {
                    $this->default['DBDriver'] = $this->default['dbdriver'];
                }
                
                // Set default values for CI4 if not present
                $this->default['DBDebug'] = $this->default['db_debug'] ?? true;
                $this->default['charset'] = $this->default['char_set'] ?? 'utf8';
                $this->default['DBCollation'] = $this->default['dbcollat'] ?? 'utf8_general_ci';
                $this->default['swapPre'] = $this->default['swap_pre'] ?? '';
                $this->default['encrypt'] = $this->default['encrypt'] ?? false;
                $this->default['compress'] = $this->default['compress'] ?? false;
                $this->default['strictOn'] = $this->default['stricton'] ?? false;
                $this->default['failover'] = $this->default['failover'] ?? [];
                $this->default['saveQueries'] = $this->default['save_queries'] ?? true;
            }
        }
    }

    /**
     * CI3-style getter for backward compatibility
     */
    public function getDefaultConnection(): array
    {
        return $this->default;
    }

    /**
     * Get database config by group name
     */
    public function getConnection(string $group = 'default'): array
    {
        if ($group === 'default') {
            return $this->default;
        }
        
        // Load additional database groups from CI3 config
        $db_config_file = APPPATH . 'config/database.php';
        if (file_exists($db_config_file)) {
            include($db_config_file);
            
            if (isset($db) && isset($db[$group])) {
                return $db[$group];
            }
        }
        
        return [];
    }
}
