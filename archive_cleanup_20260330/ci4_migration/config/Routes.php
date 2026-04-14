<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Routes Configuration
 * 
 * CI4 Migration Template - Based on CI3 routes.php
 * Copy to app/Config/Routes.php when migrating to CI4
 */
class Routes extends BaseConfig
{
    /**
     * Default Controller
     */
    public string $defaultController = 'Home';

    /**
     * 404 Override
     */
    public string $override404 = '';

    /**
     * Translate URI dashes
     */
    public bool $translateURIDashes = false;

    /**
     * Enable query strings
     */
    public bool $enableQueryStrings = false;

    /**
     * Controller namespace
     */
    public string $defaultNamespace = 'App\Controllers';

    /**
     * Supported languages
     */
    public array $supportedLocales = ['en', 'fr', 'ar', 'es', 'nl'];

    /**
     * Locale detection
     */
    public string $negotiateLocale = 'en';

    /**
     * --------------------------------------------------------------------------
     * Route Definitions
     * --------------------------------------------------------------------------
     */
    public function __construct()
    {
        parent::__construct();
        
        // CI3-style routes will be auto-loaded from application/config/routes.php
        // For CI4, we need to convert them to array format
        
        $this->loadCIRoutes();
    }

    /**
     * Load and convert CI3 routes to CI4 format
     */
    protected function loadCIRoutes()
    {
        $routes_file = APPPATH . 'config/routes.php';
        
        if (!file_exists($routes_file)) {
            return;
        }

        // Extract route definitions from CI3 routes.php
        // This is a simplified approach - may need manual adjustment
        
        // Frontend routes (without admin prefix)
        $this->collection->add('home', 'Home::index', ['filter' => 'language']);
        
        // Language-prefixed routes will be handled by CI4's Locale filtering
        // Admin routes
        $this->collection->add('admin', 'Admin::index', ['filter' => 'auth']);
        
        // Student routes
        $this->collection->add('student', 'Student::index', ['filter' => 'auth']);
        
        // Teacher routes
        $this->collection->add('teacher', 'Teacher::index', ['filter' => 'auth']);
        
        // API routes
        $this->collection->add('api/(.*)', 'Api/$1', ['filter' => 'api']);
    }

    /**
     * CI3 route compatibility getter
     * 
     * Usage in CI4: $routes = \Config\Services::routes();
     * Then access CI3 routes via: include APPPATH . 'config/routes.php';
     */
    public function getCIRoutes(): array
    {
        $routes_file = APPPATH . 'config/routes.php';
        
        if (!file_exists($routes_file)) {
            return [];
        }

        // Return the raw CI3 routes array
        // This allows CI3-style routing to work in CI4
        $routes = [];
        include $routes_file;
        
        return $routes ?? [];
    }
}
