<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Services Configuration
 * 
 * CI4 Migration Template - Based on CI3 autoload.php
 * Copy to app/Config/Services.php when migrating to CI4
 * 
 * Note: CI4 uses service providers instead of autoload
 */
class Services extends BaseConfig
{
    /**
     * CI3-style library loading compatibility
     * 
     * In CI4, libraries are loaded via service containers
     * This method provides backward compatibility for CI3-style loading
     */
    public static function library(string $name, $params = null, $getShared = true)
    {
        // Load CI3-style libraries
        $instance = &get_instance();
        
        if (!isset($instance->$name)) {
            $instance->load->library($name, $params);
        }
        
        return $instance->$name;
    }

    /**
     * Load database (CI3 style)
     */
    public static function database($params = '', $queryBuilderOverride = null)
    {
        $db = \Config\Database::connect();
        return $db;
    }

    /**
     * Load helper (CI3 style)
     */
    public static function helper(string $helper, $is_core = false)
    {
        helper($helper);
    }

    /**
     * Load model (CI3 style)
     */
    public static function model(string $name, $params = '', $queryBuilder = false)
    {
        $instance = &get_instance();
        
        if (!isset($instance->$name)) {
            $instance->load->model($name, $params, $queryBuilder);
        }
        
        return $instance->$name;
    }

    /**
     * Load view (CI3 style)
     */
    public static function view(string $view, $data = [], $return = false)
    {
        $instance = &get_instance();
        return $instance->load->view($view, $data, $return);
    }
}
