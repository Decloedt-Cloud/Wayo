<?php

namespace Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\Router\RouteCollection;

class Services extends BaseService
{
    public static function request($config = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('request', $config);
        }

        $config ??= config('App');

        $uri = \CodeIgniter\Config\Services::uri(null, false);

        return new \CodeIgniter\HTTP\IncomingRequest(
            $config,
            $uri,
            'php://input',
            new \CodeIgniter\HTTP\UserAgent()
        );
    }

    public static function response($config = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('response', $config);
        }

        $config ??= config('App');

        return new \CodeIgniter\HTTP\Response($config);
    }

    public static function routes($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('routes');
        }

        return new RouteCollection(
            static::locator(),
            new \Config\Modules(),
            new \Config\Routing()
        );
    }

    public static function renderer(?string $viewPath = null, ?\CodeIgniter\Config\ViewConfig $config = null, bool $getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('renderer', $viewPath, $config);
        }

        $viewPath = $viewPath !== null && $viewPath !== '' && $viewPath !== '0' ? $viewPath : (new \Config\Paths())->viewDirectory;
        $config ??= config(\Config\View::class);

        return new \App\View\View($config, $viewPath, static::locator(), CI_DEBUG, static::logger());
    }

    /**
     * Subscription Service - Handles subscription status and billing
     */
    public static function subscriptionService($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('subscriptionService');
        }

        return new \App\Libraries\SubscriptionService();
    }

    /**
     * Morocco B2B Service - Handles Morocco-specific B2B invoicing
     */
    public static function moroccoB2BService($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('moroccoB2BService');
        }

        return new \App\Libraries\MoroccoB2BService();
    }

    /**
     * FX Rates Service - Foreign exchange rate management
     */
    public static function fxRatesService($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('fxRatesService');
        }

        return new \App\Libraries\FxRatesService();
    }

    /**
     * Stripe Payment Library
     */
    public static function stripe($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('stripe');
        }

        return new \App\Libraries\Stripe_lib();
    }

    /**
     * PDF Generation Service
     */
    public static function pdf(array $config = [], $getShared = false)
    {
        return new \App\Libraries\Pdf($config);
    }

    /**
     * BigBlueButton Library
     */
    public static function bigBlueButton($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('bigBlueButton');
        }

        return new \App\Libraries\BigBlueButtonLibrary();
    }

    /**
     * Email Service
     */
    public static function emailService($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('emailService');
        }

        return new \App\Libraries\Phpmailer_lib();
    }

    /**
     * VAT Resolver Service
     */
    public static function vatResolver($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('vatResolver');
        }

        return new \App\Libraries\VatResolver();
    }

    /**
     * Billing Entity Service
     */
    public static function billingEntityService($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('billingEntityService');
        }

        return new \App\Libraries\BillingEntityService();
    }

    /**
     * Subscription Maintenance Service
     */
    public static function subscriptionMaintenanceService($getShared = true)
    {
        if ($getShared) {
            return self::getSharedInstance('subscriptionMaintenanceService');
        }

        return new \App\Libraries\SubscriptionMaintenanceService();
    }
}
