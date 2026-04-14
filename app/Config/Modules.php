<?php

namespace Config;

class Modules
{
    public $enabled = true;
    public $discoverInComposer = true;
    public $composerPackages = [];
    public $aliases = [];

    public function shouldDiscover(string $alias): bool
    {
        if (! $this->enabled) {
            return false;
        }

        return in_array(strtolower($alias), array_map('strtolower', $this->aliases), true);
    }
}
