<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AppAccessFilter implements FilterInterface
{
    /**
     * Redirect legacy/non-masked URLs to CI3-style masked app URLs.
     * For safety, only GET/HEAD navigation requests are redirected.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $method = strtoupper($request->getMethod());
        if (!in_array($method, ['GET', 'HEAD'], true)) {
            return null;
        }

        if ($request->isAJAX()) {
            return null;
        }

        // Internal one-shot bypass used by app/* alias routes to avoid masking loops.
        $session = session();
        if ($session && $session->getTempdata('__app_unmask_once') === '1') {
            $session->removeTempdata('__app_unmask_once');
            return null;
        }

        $path = trim($request->getUri()->getPath(), '/');
        if ($path === '') {
            return null;
        }

        if ($path === 'index.php') {
            return null;
        }
        if (str_starts_with($path, 'index.php/')) {
            $path = substr($path, strlen('index.php/'));
        }

        // Do not interfere with API/assets/static paths.
        $skipPrefixes = ['api/', 'assets/', 'uploads/', 'vendor/', 'writable/', 'socketio-server/', 'update/', 'app/'];
        foreach ($skipPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return null;
            }
        }

        $segments = explode('/', $path);
        $supportedLocales = ['en', 'fr', 'ar', 'es', 'nl'];
        $localePrefix = '';
        if (!empty($segments[0]) && in_array($segments[0], $supportedLocales, true)) {
            $localePrefix = array_shift($segments);
            $path = implode('/', $segments);
        }

        if ($path === '') {
            return null;
        }

        $targetPath = $this->rewritePath($path);
        if ($targetPath === null || $targetPath === $path) {
            return null;
        }

        if ($localePrefix !== '' && !str_starts_with($targetPath, 'app/')) {
            $targetPath = $localePrefix . '/' . $targetPath;
        }

        $query = $request->getUri()->getQuery();
        $url = site_url($targetPath . ($query ? ('?' . $query) : ''));

        return redirect()->to($url, 301);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function rewritePath(string $path): ?string
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
            $rewritten = $this->replacePrefix($path, $from, $to);
            if ($rewritten !== null) {
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
                $rewritten = $this->replacePrefix($path, $role . '/' . $suffix, $replacement);
                if ($rewritten !== null) {
                    return $rewritten;
                }
            }
        }

        $blockedSegments = ['admin', 'teacher', 'student', 'superadmin', 'addons'];
        foreach ($blockedSegments as $segment) {
            $rewritten = $this->replacePrefix($path, $segment, 'app');
            if ($rewritten !== null) {
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
            $rewritten = $this->replacePrefix($path, $from, $to);
            if ($rewritten !== null) {
                return $rewritten;
            }
        }

        foreach (['communities', 'about', 'privacy_policy', 'community_details'] as $segment) {
            $rewritten = $this->replacePrefix($path, 'home/' . $segment, $segment);
            if ($rewritten !== null) {
                return $rewritten;
            }
        }

        return null;
    }

    private function replacePrefix(string $path, string $from, string $to): ?string
    {
        if ($path === $from) {
            return $to;
        }

        $fromWithSlash = $from . '/';
        if (str_starts_with($path, $fromWithSlash)) {
            return $to . substr($path, strlen($from));
        }

        return null;
    }
}

