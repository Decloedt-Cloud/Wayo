<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Worker Mode Configuration
 *
 * Worker Mode is a performance optimization feature that allows CodeIgniter
 * to handle multiple HTTP requests within the same PHP process.
 *
 * @see https://codeigniter.com/user_guide/installation/worker_mode.html
 */
class WorkerMode extends BaseConfig
{
    /**
     * Persistent Services
     *
     * Services listed here will persist across requests in Worker Mode.
     * Use with caution - services must be stateless or properly reset.
     *
     * @var list<string>
     */
    public array $persistentServices = [];

    /**
     * Reset Event Listeners
     *
     * Event listener names that should be reset between requests.
     * Add custom event names if you have listeners that hold request-specific state.
     *
     * @var list<string>
     */
    public array $resetEventListeners = [];
}
