<?php

namespace App\View;

/**
 * CI3-compatible wrapper around CI4's Security service.
 *
 * Maps the old CI3 method names (get_csrf_token_name / get_csrf_hash)
 * to their CI4 equivalents (getTokenName / getHash) and passes every
 * other call straight through to the real Security object.
 */
class SecurityCompat
{
    private $security;
    
    public function __construct($security)
    {
        $this->security = $security;
    }
    
    public function get_csrf_token_name()
    {
        return $this->security->getTokenName();
    }
    
    public function get_csrf_hash()
    {
        return $this->security->getHash();
    }
    
    public function __call($method, $args)
    {
        return $this->security->$method(...$args);
    }
}
