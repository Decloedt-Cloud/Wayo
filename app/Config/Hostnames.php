<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Allowed Hostnames
 *
 * You can specify allowed hostnames for your site.
 * Requests from hostnames not in this list will be filtered out.
 *
 * @see https://codeigniter.com/user_guide/general/configuration.html
 */
class Hostnames extends BaseConfig
{
    /**
     * Allowed hostnames
     *
     * @var list<string>
     */
    public array $allowed = [];

    /**
     * Whether to filter requests by HTTP Host header
     *
     * @var bool
     */
    public bool $filterEnabled = false;
}
