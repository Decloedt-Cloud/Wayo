<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public function __construct()
    {
        parent::__construct();

        $configuredBaseURL = (string) env('app.baseURL', $this->baseURL);
        $this->baseURL = rtrim($configuredBaseURL, '/') . '/';
        $this->forceGlobalSecureRequests = (bool) env('app.forceGlobalSecureRequests', $this->forceGlobalSecureRequests);

        $proxyIPs = (string) env('app.proxyIPs', '');
        if ($proxyIPs !== '') {
            $this->proxyIPs = array_fill_keys(
                array_filter(array_map('trim', explode(',', $proxyIPs))),
                ''
            );
        }
    }

    /**
     * Base Site URL
     */
    public string $baseURL = 'http://localhost/wayo/';

    /**
     * Allowed Hostnames in the Site URL other than the hostname in the baseURL.
     *
     * @var list<string>
     */
    public array $allowedHostnames = [];

    /**
     * Index File
     */
    public string $indexPage = '';

    /**
     * URI Protocol - which server global should be used to retrieve the URI string.
     */
    public string $uriProtocol = 'REQUEST_URI';

    /**
     * Allowed URL Characters
     */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-@';

    /**
     * Default Locale
     */
    public string $defaultLocale = 'en';

    /**
     * Negotiate Locale
     */
    public bool $negotiateLocale = false;

    /**
     * Supported Locales
     *
     * @var list<string>
     */
    public array $supportedLocales = ['en', 'fr', 'ar', 'es', 'nl'];

    /**
     * Application Timezone
     */
    public string $appTimezone = 'Africa/Casablanca';

    /**
     * Default Character Set
     */
    public string $charset = 'UTF-8';

    /**
     * Force Global Secure Requests
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * Reverse Proxy IPs
     *
     * @var array<string, string>
     */
    public array $proxyIPs = [];

    /**
     * Content Security Policy
     */
    public bool $CSPEnabled = false;
}
