<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AppRewriterFilter implements FilterInterface
{
    /**
     * Patterns to ignore for asset versioning (external/CDN).
     *
     * @var string[]
     */
    protected array $ignorePatterns = [
        'cdn.jsdelivr.net',
        'cdnjs.cloudflare.com',
        'fonts.googleapis.com',
        'unpkg.com',
        'code.jquery.com',
        'maxcdn.bootstrapcdn.com',
        'stackpath.bootstrapcdn.com',
        'ajax.googleapis.com',
        '//fonts.',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // Nothing before controller for output rewriting.
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $contentType = strtolower((string) $response->getHeaderLine('Content-Type'));
        $output = (string) $response->getBody();
        if ($output === '') {
            return;
        }

        if (str_contains($contentType, 'application/json') || str_contains($contentType, 'json')) {
            $json = json_decode($output, true);
            if (is_array($json)) {
                $json = $this->rewriteJsonRedirects($json);
                $response->setBody((string) json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
            return;
        }

        if ($contentType !== '' && !str_contains($contentType, 'html') && !str_contains($contentType, 'text')) {
            return;
        }

        $output = $this->addAssetVersioning($output);
        $output = $this->rewriteMaskedUrls($output);

        $response->setBody($output);
    }

    private function rewriteJsonRedirects(array $payload): array
    {
        $redirectKeys = ['redirect', 'redirect_url', 'next_url', 'url'];

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->rewriteJsonRedirects($value);
                continue;
            }

            if (!is_string($value)) {
                continue;
            }

            if (in_array((string) $key, $redirectKeys, true)) {
                $payload[$key] = $this->rewriteOneUrlToMasked($value);
            }
        }

        return $payload;
    }

    private function rewriteOneUrlToMasked(string $url): string
    {
        $trimmed = trim($url);
        if ($trimmed === '' || str_starts_with($trimmed, '#') || str_starts_with(strtolower($trimmed), 'javascript:')) {
            return $url;
        }

        $baseUrl = rtrim((string) config('App')->baseURL, '/');
        $baseHost = (string) parse_url($baseUrl, PHP_URL_HOST);
        $baseScheme = (string) parse_url($baseUrl, PHP_URL_SCHEME);

        $parts = parse_url($trimmed);
        if ($parts === false) {
            return $url;
        }

        $isAbsolute = isset($parts['scheme']) || isset($parts['host']);
        if ($isAbsolute) {
            $urlHost = (string) ($parts['host'] ?? '');
            $urlScheme = (string) ($parts['scheme'] ?? '');
            if (($urlHost !== '' && strcasecmp($urlHost, $baseHost) !== 0) || ($urlScheme !== '' && strcasecmp($urlScheme, $baseScheme) !== 0)) {
                return $url;
            }
        }

        $path = (string) ($parts['path'] ?? '');
        if ($path === '') {
            return $url;
        }

        $relativePath = ltrim($path, '/');
        $basePath = trim((string) parse_url($baseUrl, PHP_URL_PATH), '/');
        if ($basePath !== '' && str_starts_with($relativePath, $basePath . '/')) {
            $relativePath = substr($relativePath, strlen($basePath) + 1);
        } elseif ($basePath !== '' && $relativePath === $basePath) {
            $relativePath = '';
        }

        if ($relativePath === '') {
            return $url;
        }

        $maskedPath = $this->rewritePathLikeAccess($relativePath);
        if ($maskedPath === $relativePath) {
            return $url;
        }

        $query = isset($parts['query']) ? ('?' . $parts['query']) : '';
        $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';

        if ($isAbsolute) {
            return $baseUrl . '/' . ltrim($maskedPath, '/') . $query . $fragment;
        }

        if (str_starts_with($trimmed, '/')) {
            return '/' . ltrim($maskedPath, '/') . $query . $fragment;
        }

        return ltrim($maskedPath, '/') . $query . $fragment;
    }

    private function rewritePathLikeAccess(string $path): string
    {
        $specialMaps = [
            'app/student' => 'app/member',
            'app/teacher' => 'app/mentor',
            'app/exam' => 'app/certifications',
            'app/event_calendar' => 'app/announcements',
            'app/school_settings' => 'app/community_settings',
            'app/school' => 'app/community_list',
        ];

        foreach ($specialMaps as $from => $to) {
            $rewritten = $this->replacePrefixPath($path, $from, $to);
            if ($rewritten !== $path) {
                return $rewritten;
            }
        }

        $managerRoles = ['superadmin', 'admin', 'teacher', 'student', 'parent', 'parents', 'accountant', 'librarian', 'driver'];
        $roleSuffixMap = [
            'student' => 'app/member',
            'teacher' => 'app/mentor',
            'exam' => 'app/certifications',
            'event_calendar' => 'app/announcements',
            'school_settings' => 'app/community_settings',
            'school' => 'app/community_list',
        ];
        foreach ($managerRoles as $role) {
            foreach ($roleSuffixMap as $suffix => $replacement) {
                $rewritten = $this->replacePrefixPath($path, $role . '/' . $suffix, $replacement);
                if ($rewritten !== $path) {
                    return $rewritten;
                }
            }
        }

        foreach (['admin', 'teacher', 'student', 'superadmin', 'addons'] as $segment) {
            $rewritten = $this->replacePrefixPath($path, $segment, 'app');
            if ($rewritten !== $path) {
                return $rewritten;
            }
        }

        $specialFrontendMap = [
            'admission/online_admission' => 'join/community',
            'admission/online_admission_student' => 'join/member',
            'home/tutorial' => 'getting_started',
            'tutorial' => 'getting_started',
            'home/faq' => 'help-center',
            'faq' => 'help-center',
            'home/terms_conditions' => 'terms',
            'terms_conditions' => 'terms',
            'home/contact' => 'support',
            'contact' => 'support',
        ];
        foreach ($specialFrontendMap as $from => $to) {
            $rewritten = $this->replacePrefixPath($path, $from, $to);
            if ($rewritten !== $path) {
                return $rewritten;
            }
        }

        foreach (['communities', 'about', 'privacy_policy', 'community_details'] as $segment) {
            $rewritten = $this->replacePrefixPath($path, 'home/' . $segment, $segment);
            if ($rewritten !== $path) {
                return $rewritten;
            }
        }

        return $path;
    }

    private function replacePrefixPath(string $path, string $from, string $to): string
    {
        if ($path === $from) {
            return $to;
        }
        if (str_starts_with($path, $from . '/')) {
            return $to . substr($path, strlen($from));
        }
        return $path;
    }

    private function rewriteMaskedUrls(string $output): string
    {
        // Rewrite URL-bearing attributes first (safer than broad global replacement).
        $output = $this->rewriteHtmlAttributes($output);
        // Rewrite common JS URL strings like window.location = "student/..."
        $output = $this->rewriteInlineJsUrlStrings($output);

        $roles = ['superadmin', 'admin', 'teacher', 'student', 'accountant', 'librarian', 'driver', 'parent', 'parents'];
        $managerRoles = ['superadmin', 'admin', 'teacher', 'student', 'parent', 'parents', 'accountant', 'librarian', 'driver'];
        $segmentMap = [
            'student' => 'member',
            'teacher' => 'mentor',
            'exam' => 'certifications',
            'event_calendar' => 'announcements',
            'school_settings' => 'community_settings',
            'school' => 'community_list',
        ];

        $baseUrl = rtrim((string) config('App')->baseURL, '/');

        foreach ($managerRoles as $role) {
            foreach ($segmentMap as $originalSegment => $mappedSegment) {
                $target = $role . '/' . $originalSegment;
                $replacement = 'app/' . $mappedSegment;
                $output = $this->replaceUrlForms($output, $baseUrl, $target, $replacement);
            }
        }

        foreach ($roles as $role) {
            $output = $this->replaceUrlForms($output, $baseUrl, $role, 'app');
        }

        $addonsMap = [
            'addons/courses' => 'app/courses',
            'addons/lessons' => 'app/lessons',
            'chat' => 'app/chat',
        ];
        foreach ($addonsMap as $search => $replace) {
            $output = $this->replaceUrlForms($output, $baseUrl, $search, $replace);
        }

        $homeSegments = ['communities', 'about', 'privacy_policy', 'community_details'];
        foreach ($homeSegments as $segment) {
            $output = $this->replaceUrlForms($output, $baseUrl, 'home/' . $segment, $segment);
        }

        $specialMap = [
            'admission/online_admission' => 'join/community',
            'admission/online_admission_student' => 'join/member',
            'home/tutorial' => 'getting_started',
            'tutorial' => 'getting_started',
            'home/faq' => 'help-center',
            'faq' => 'help-center',
            'home/terms_conditions' => 'terms',
            'terms_conditions' => 'terms',
            'home/contact' => 'support',
            'contact' => 'support',
        ];
        foreach ($specialMap as $search => $replace) {
            $output = $this->replaceUrlForms($output, $baseUrl, $search, $replace);
        }

        return $output;
    }

    private function replaceUrlForms(string $output, string $baseUrl, string $searchSegment, string $replacementSegment): string
    {
        $pairs = [
            // Absolute URLs
            [$baseUrl . '/' . $searchSegment . '/', $baseUrl . '/' . $replacementSegment . '/'],
            [$baseUrl . '/index.php/' . $searchSegment . '/', $baseUrl . '/index.php/' . $replacementSegment . '/'],
            [$baseUrl . '/' . $searchSegment . '"', $baseUrl . '/' . $replacementSegment . '"'],
            [$baseUrl . '/' . $searchSegment . '\'', $baseUrl . '/' . $replacementSegment . '\''],
            // Root-relative URLs
            ['/index.php/' . $searchSegment . '/', '/index.php/' . $replacementSegment . '/'],
            ['/' . $searchSegment . '/', '/' . $replacementSegment . '/'],
            ['/' . $searchSegment . '"', '/' . $replacementSegment . '"'],
            ['/' . $searchSegment . '\'', '/' . $replacementSegment . '\''],
        ];

        foreach ($pairs as [$search, $replace]) {
            $output = str_replace($search, $replace, $output);
        }

        return $output;
    }

    private function addAssetVersioning(string $html): string
    {
        $html = $this->versionCss($html);
        return $this->versionJs($html);
    }

    private function versionCss(string $html): string
    {
        $pattern = '/<link([^>]*?)href=["\']([^"\']+\.css)(\?[^"\']*)?["\']([^>]*?)>/i';
        return (string) preg_replace_callback($pattern, function (array $m): string {
            $beforeHref = $m[1];
            $url = $m[2];
            $existingQuery = $m[3] ?? '';
            $afterHref = $m[4];

            if ($this->shouldIgnoreUrl($url) || str_contains($existingQuery, 'v=')) {
                return $m[0];
            }

            $version = $this->getFileVersion($url);
            $separator = $existingQuery === '' ? '?' : '&';
            $newUrl = $url . $existingQuery . $separator . 'v=' . $version;

            return '<link' . $beforeHref . 'href="' . $newUrl . '"' . $afterHref . '>';
        }, $html);
    }

    private function versionJs(string $html): string
    {
        $pattern = '/<script([^>]*?)src=["\']([^"\']+\.js)(\?[^"\']*)?["\']([^>]*?)>/i';
        return (string) preg_replace_callback($pattern, function (array $m): string {
            $beforeSrc = $m[1];
            $url = $m[2];
            $existingQuery = $m[3] ?? '';
            $afterSrc = $m[4];

            if ($this->shouldIgnoreUrl($url) || str_contains($existingQuery, 'v=')) {
                return $m[0];
            }

            $version = $this->getFileVersion($url);
            $separator = $existingQuery === '' ? '?' : '&';
            $newUrl = $url . $existingQuery . $separator . 'v=' . $version;

            return '<script' . $beforeSrc . 'src="' . $newUrl . '"' . $afterSrc . '>';
        }, $html);
    }

    private function shouldIgnoreUrl(string $url): bool
    {
        if (preg_match('#^(https?:)?//#i', $url)) {
            $base = rtrim((string) config('App')->baseURL, '/');
            if (str_starts_with($url, $base)) {
                return false;
            }
            return true;
        }

        foreach ($this->ignorePatterns as $pattern) {
            if (stripos($url, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getFileVersion(string $url): string
    {
        $baseUrl = rtrim((string) config('App')->baseURL, '/');
        $basePath = (string) parse_url($baseUrl, PHP_URL_PATH);
        $basePath = trim($basePath, '/');

        $path = $url;
        if (preg_match('#^(https?:)?//#i', $path)) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = is_string($parsed) ? $parsed : '';
        }

        $path = preg_replace('/\?.*$/', '', $path);
        $path = ltrim((string) $path, '/');

        if ($basePath !== '' && str_starts_with($path, $basePath . '/')) {
            $path = substr($path, strlen($basePath) + 1);
        }

        $filePath = rtrim((string) FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        if ($path !== '' && file_exists($filePath)) {
            return (string) filemtime($filePath);
        }

        return date('Ymd');
    }

    private function rewriteHtmlAttributes(string $html): string
    {
        $pattern = '/\b(href|src|action|formaction|data-url)\s*=\s*(["\'])(.*?)\2/i';
        return (string) preg_replace_callback($pattern, function (array $m): string {
            $attr = $m[1];
            $quote = $m[2];
            $value = $m[3];

            $newValue = $this->rewriteOneUrlToMasked($value);
            return $attr . '=' . $quote . $newValue . $quote;
        }, $html);
    }

    private function rewriteInlineJsUrlStrings(string $html): string
    {
        $pattern = '/(["\'])(\/?(?:admin|teacher|student|superadmin|addons|app|home|admission|tutorial|faq|contact)[^"\']*)\1/i';
        return (string) preg_replace_callback($pattern, function (array $m): string {
            $quote = $m[1];
            $value = $m[2];
            $newValue = $this->rewriteOneUrlToMasked($value);
            return $quote . $newValue . $quote;
        }, $html);
    }
}

