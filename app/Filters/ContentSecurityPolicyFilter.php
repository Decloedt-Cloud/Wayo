<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Csp;

class ContentSecurityPolicyFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $csp = config('Csp');
        if ($csp instanceof Csp) {
            $csp->nonce = base64_encode(random_bytes(16));
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $csp = config('Csp');
        $nonce = ($csp instanceof Csp) ? $csp->nonce : '';

        $config = config('App');
        $baseURL = rtrim($config->baseURL ?? '', '/');

        $noncePart = $nonce !== '' ? "'nonce-" . $this->escapeCspToken($nonce) . "' " : '';

        $unsafeEval = ($csp instanceof Csp && $csp->allowUnsafeEval) ? "'unsafe-eval' " : '';

        $cspHeader = "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
            'http://cdn.jsdelivr.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://stackpath.bootstrapcdn.com https://ajax.googleapis.com https://js.stripe.com https://www.paypalobjects.com https://t.paypal.com https://www.paypal.com; ' .
            "style-src 'self' 'unsafe-inline' 'unsafe-hashes' " .
            'http://cdn.jsdelivr.net https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com https://stackpath.bootstrapcdn.com; ' .
            "img-src 'self' data: blob: http: https: https://cdn.plyr.io; " .
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com; " .
            'connect-src \'self\' ' . $baseURL . ' https://cdn.jsdelivr.net https://cdn.plyr.io https://api.country.is https://unpkg.com https://js.stripe.com https://www.paypal.com https://www.paypalobjects.com; ' .
            "media-src 'self' blob: https:; " .
            "object-src 'none'; " .
            "frame-src 'self' https://js.stripe.com https://www.paypal.com https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com; " .
            "base-uri 'self'; " .
            "form-action 'self'; " .
            "frame-ancestors 'self'";

        $response->setHeader('Content-Security-Policy', $cspHeader);
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        if ($nonce === '') {
            return $response;
        }

        if (!$csp instanceof Csp || !$csp->injectNonceIntoHtml) {
            return $response;
        }

        $contentType = $response->getHeaderLine('Content-Type');
        $body = $response->getBody();
        if ($body === null || $body === '') {
            return $response;
        }

        $html = is_string($body) ? $body : (string) $body;
        if ($html === '') {
            return $response;
        }

        // Fragments may start with <div>, <script>, etc. Avoid <?xml ...> and JSON.
        $looksLikeHtml = (bool) preg_match('/^\s*</', $html)
            && !preg_match('/^\s*<\?xml\b/i', $html);
        $isHtmlResponse = stripos($contentType, 'text/html') !== false
            || ($contentType === '' && $looksLikeHtml);
        if (!$isHtmlResponse) {
            return $response;
        }

        $newHtml = $this->injectNonceIntoHtml($html, $nonce);
        if ($newHtml !== $html) {
            $response->setBody($newHtml);
        }

        return $response;
    }

    /**
     * CSP source tokens: use only in header after encoding.
     */
    private function escapeCspToken(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9+\/_\-=]/', '', $value) ?? '';
    }

    private function injectNonceIntoHtml(string $html, string $nonce): string
    {
        $nonceEsc = htmlspecialchars($nonce, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $html = preg_replace_callback('/<script\b([^>]*)>/i', static function (array $m) use ($nonceEsc): string {
            $attrs = $m[1];
            if (preg_match('/\bsrc\s*=/i', $attrs)) {
                return $m[0];
            }
            if (preg_match('/\bnonce\s*=/i', $attrs)) {
                return $m[0];
            }

            return '<script nonce="' . $nonceEsc . '"' . $attrs . '>';
        }, $html) ?? '';

        $html = preg_replace_callback('/<style\b([^>]*)>/i', static function (array $m) use ($nonceEsc): string {
            $attrs = $m[1];
            if (preg_match('/\bnonce\s*=/i', $attrs)) {
                return $m[0];
            }

            return '<style nonce="' . $nonceEsc . '"' . $attrs . '>';
        }, $html) ?? '';

        return $html;
    }
}
