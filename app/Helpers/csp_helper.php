<?php

declare(strict_types=1);

if (!function_exists('csp_nonce')) {
    /**
     * Current request CSP nonce (empty if filter did not run).
     */
    function csp_nonce(): string
    {
        $csp = config('Csp');

        return $csp instanceof \Config\Csp ? $csp->nonce : '';
    }
}

if (!function_exists('csp_nonce_attr')) {
    /**
     * Safe HTML attribute fragment: nonce="..." for manual use in views.
     */
    function csp_nonce_attr(): string
    {
        $n = csp_nonce();

        return $n === '' ? '' : ' nonce="' . esc($n, 'attr') . '"';
    }
}
