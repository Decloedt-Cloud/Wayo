<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Application Configuration
 * 
 * CI4 Migration Template - Based on CI3 config.php
 * Copy to app/Config/App.php when migrating to CI4
 */
class App extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Base Site URL
     * --------------------------------------------------------------------------
     */
    public string $baseURL = 'http://localhost/';

    /**
     * --------------------------------------------------------------------------
     * Index File
     * --------------------------------------------------------------------------
     */
    public string $indexPage = '';

    /**
     * --------------------------------------------------------------------------
     * URI PROTOCOL
     * --------------------------------------------------------------------------
     */
    public string $uriProtocol = 'REQUEST_URI';

    /**
     * --------------------------------------------------------------------------
     * URL suffix
     * --------------------------------------------------------------------------
     */
    public string $urlSuffix = '';

    /**
     * --------------------------------------------------------------------------
     * Default Language
     * --------------------------------------------------------------------------
     */
    public string $defaultLocale = 'en';

    /**
     * --------------------------------------------------------------------------
     * Supported Locales
     * --------------------------------------------------------------------------
     */
    public array $supportedLocales = ['en', 'fr', 'ar', 'es'];

    /**
     * --------------------------------------------------------------------------
     * Application Timezone
     * --------------------------------------------------------------------------
     */
    public string $appTimezone = 'Africa/Casablanca';

    /**
     * --------------------------------------------------------------------------
     * CSRF Protection
     * --------------------------------------------------------------------------
     */
    public bool $CSRFProtection = true;
    public string $CSRFTokenName = 'csrf_token_name';
    public string $CSRFCookieName = 'csrf_cookie_name';
    public int $CSRFExpire = 7200;
    public bool $CSRFRegenerate = true;
    public bool $CSRFRedirect = true;

    /**
     * --------------------------------------------------------------------------
     * Session Configuration
     * --------------------------------------------------------------------------
     */
    public string $sessionDriver = 'CodeIgniter\Session\Handlers\DatabaseHandler';
    public string $sessionCookieName = 'ci_session';
    public int $sessionExpiration = 7200;
    public string $sessionSavePath = 'ci_sessions';
    public bool $sessionMatchIP = false;
    public int $sessionTimeToUpdate = 300;
    public bool $sessionRegenerateDestroy = false;

    /**
     * --------------------------------------------------------------------------
     * Cookie Configuration
     * --------------------------------------------------------------------------
     */
    public string $cookiePrefix = '';
    public string $cookieDomain = '';
    public string $cookiePath = '/';
    public bool $cookieSecure = false;
    public string $cookieHTTPOnly = '1';
    public string $cookieSameSite = 'Lax';

    /**
     * --------------------------------------------------------------------------
     * Reverse Proxy IPs
     * --------------------------------------------------------------------------
     */
    public array $proxyIPs = [];

    /**
     * --------------------------------------------------------------------------
     * Allow Dynamic Properties (for BC with CI3)
     * --------------------------------------------------------------------------
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->baseURL = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $this->baseURL .= "://".$_SERVER['HTTP_HOST'];
        $this->baseURL .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
    }
}
