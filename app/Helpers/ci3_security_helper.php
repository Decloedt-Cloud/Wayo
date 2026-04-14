<?php

if (!function_exists('get_csrf_token_name')) {
    function get_csrf_token_name(): string
    {
        return \CodeIgniter\Config\Services::security()->getTokenName();
    }
}

if (!function_exists('get_csrf_hash')) {
    function get_csrf_hash(): string
    {
        return \CodeIgniter\Config\Services::security()->getHash();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \CodeIgniter\Config\Services::security()->getHash();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $security = \CodeIgniter\Config\Services::security();
        return '<input type="hidden" name="' . $security->getTokenName() . '" value="' . $security->getHash() . '">';
    }
}

if (!function_exists('csrf_meta')) {
    function csrf_meta(): string
    {
        $security = \CodeIgniter\Config\Services::security();
        return '<meta name="csrf_token_name" content="' . $security->getTokenName() . '"><meta name="csrf_token_hash" content="' . $security->getHash() . '">';
    }
}
