<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Subscription System Configuration
 *
 * Configuration for the school subscription payment system
 */

$config['subscription'] = [
    // Subscription statuses
    'statuses' => [
        'trialing'  => 'trialing',
        'active'    => 'active',
        'past_due'  => 'past_due',
        'suspended' => 'suspended',
        'canceled'  => 'canceled'
    ],

    // Trial configuration
    'trial' => [
        'default_days' => 14,
        'enabled' => true
    ],

    // Grace period configuration
    'grace_period' => [
        'days' => 3,
        'allow_limited_access' => true
    ],

    // Billing configuration
    'billing' => [
        'default_interval' => 'month',
        'default_interval_count' => 1,
        'currency' => 'EUR'
    ],

    // Access control
    'access_control' => [
        'allowed_methods_when_blocked' => [
            'dashboard',
            'logout',
            'language',
            'subscription',
            'payment',
            'payment_success'
        ],
        'auto_generate_invoice_on' => [
            'dashboard',
            'payment',
            'subscription'
        ]
    ],

    // Stripe configuration
    'stripe' => [
        'webhook_events' => [
            'payment_intent.succeeded',
            'invoice.payment_succeeded'
        ],
        'webhook_tolerance' => 300 // 5 minutes
    ],

    // Cron configuration
    'cron' => [
        'invoice_generation' => [
            'enabled' => true,
            'schedule' => '0 2 * * *', // Daily at 2 AM
        ],
        'health_check' => [
            'enabled' => true,
            'schedule' => '0 */6 * * *', // Every 6 hours
        ]
    ]
];




