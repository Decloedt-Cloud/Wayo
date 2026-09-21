<?php

namespace Config;

class Modules
{
    public $enabled = true;
    public $discoverInComposer = true;
    public $composerPackages = [];

    /**
     * Auto-discovery rules. Without `'services'` here, `service('moroccoB2BService')`
     * and other custom factories in Config\Services are never resolved (only core
     * CodeIgniter\Config\Services methods are scanned).
     *
     * @var list<string>
     */
    public $aliases = [
        'events',
        'filters',
        'registrars',
        'routes',
        'services',
    ];

    public function shouldDiscover(string $alias): bool
    {
        if (! $this->enabled) {
            return false;
        }

        return in_array(strtolower($alias), array_map('strtolower', $this->aliases), true);
    }
}
