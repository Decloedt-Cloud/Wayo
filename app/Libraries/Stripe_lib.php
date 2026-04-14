<?php

namespace App\Libraries;
use Config\Database;

$vendorAutoload = ROOTPATH . 'vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    require_once ROOTPATH . 'app/Libraries/Stripe/init.php';
}

class Stripe_lib
{
    private $stripe;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize(?string $apiKey = null)
    {
        if ($apiKey === null) {
            $stripe_keys = $this->get_stripe_keys();
            $apiKey = $stripe_keys['secret_key'];
        }

        if (class_exists('\Stripe\Stripe')) {
            \Stripe\Stripe::setApiKey($apiKey);
            \Stripe\Stripe::setApiVersion('2023-10-16');
        }
        
        return $this;
    }

    public function get_stripe_keys()
    {
        $db = Database::connect();
        $school_id = school_id();
        
        if ($school_id > 0) {
            $stripe_settings = $db->get_where('payment_gateways', ['school_id' => $school_id, 'gateway' => 'stripe'])->getRowArray();
        } else {
            $stripe_settings = $db->get_where('payment_gateways', ['school_id' => 0, 'gateway' => 'stripe'])->getRowArray();
        }

        $stripe['mode'] = $stripe_settings['mode'] ?? 'test';
        
        if ($stripe['mode'] == 'test') {
            $stripe['secret_key'] = $stripe_settings['test_secret_key'] ?? '';
            $stripe['public_key'] = $stripe_settings['test_public_key'] ?? '';
        } else {
            $stripe['secret_key'] = $stripe_settings['secret_key'] ?? '';
            $stripe['public_key'] = $stripe_settings['public_key'] ?? '';
        }

        return $stripe;
    }

    public function setApiKey(string $apiKey)
    {
        if (class_exists('\Stripe\Stripe')) {
            \Stripe\Stripe::setApiKey($apiKey);
        }
        return $this;
    }

    public function createCustomer(array $params)
    {
        if (class_exists('\Stripe\Customer')) {
            return \Stripe\Customer::create($params);
        }
        return null;
    }

    public function createCharge(array $params)
    {
        if (class_exists('\Stripe\Charge')) {
            return \Stripe\Charge::create($params);
        }
        return null;
    }

    public function createPaymentIntent(array $params)
    {
        if (class_exists('\Stripe\PaymentIntent')) {
            return \Stripe\PaymentIntent::create($params);
        }
        return null;
    }

    public function retrievePaymentIntent(string $id)
    {
        if (class_exists('\Stripe\PaymentIntent')) {
            return \Stripe\PaymentIntent::retrieve($id);
        }
        return null;
    }

    public function createSubscription(array $params)
    {
        if (class_exists('\Stripe\Subscription')) {
            return \Stripe\Subscription::create($params);
        }
        return null;
    }

    public function cancelSubscription(string $id)
    {
        if (class_exists('\Stripe\Subscription')) {
            return \Stripe\Subscription::cancel($id);
        }
        return null;
    }

    public function createRefund(array $params)
    {
        if (class_exists('\Stripe\Refund')) {
            return \Stripe\Refund::create($params);
        }
        return null;
    }

    public function constructWebhookEvent(string $payload, string $signature, string $webhookSecret)
    {
        if (class_exists('\Stripe\Webhook')) {
            return \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
        }
        return null;
    }

    public function getPublishableKey()
    {
        $keys = $this->get_stripe_keys();
        return $keys['public_key'];
    }

    public function getMode()
    {
        $keys = $this->get_stripe_keys();
        return $keys['mode'];
    }
}
