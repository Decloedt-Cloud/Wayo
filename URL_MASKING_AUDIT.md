# URL Masking Audit Report

**Date:** April 2, 2026  
**Scope:** Full codebase audit of URL masking implementation  
**Project:** School-Management-De_migration (CodeIgniter 4)

---

## Executive Summary

The application uses a URL masking strategy to hide internal role-based URL structure (e.g., `admin/`, `teacher/`, `student/`, `superadmin/`) behind generic `app/*` URLs. The masking system has **two core filters** (`AppAccessFilter` and `AppRewriterFilter`) — both are currently **DISABLED** in `Filters.php`. Additionally, there are **significant URL leakage points** across controllers, views, and JavaScript code that expose the real internal URL structure even when the filters are enabled.

### Risk Level: 🔴 HIGH

---

## 1. Masking Architecture Overview

### 1.1 Filters (Both DISABLED)

| Filter | Purpose | Status | Location |
|--------|---------|--------|----------|
| `AppAccessFilter` | Intercepts incoming GET/HEAD requests and 301-redirects unmasked URLs to masked `app/*` equivalents | ❌ DISABLED | `Filters.php` line 59 |
| `AppRewriterFilter` | Post-processes HTML output to rewrite unmasked URLs in `href`, `src`, `action`, JS strings, and JSON redirects | ❌ DISABLED | `Filters.php` line 65 |

**File:** `app/Config/Filters.php`
```php
// 'appaccess',  // CI3-style URL masking redirects (GET/HEAD only) - DISABLED
// 'apprewriter', // CI3-style HTML link masking and asset versioning - DISABLED
```

### 1.2 Route-Level Masking (Partially Active)

Routes.php defines 301 redirect routes for some legacy URLs (lines 28-75), but these only cover **GET requests** for a limited set of paths:
- `admin/dashboard` → `app/dashboard`
- `admin/teacher/*` → `app/mentor/*`
- `admin/student/*` → `app/member/*`
- `admin/exam/*` → `app/certifications/*`
- `admin/event_calendar/*` → `app/announcements/*`
- `admin/school_settings/*` → `app/community_settings/*`
- `admin/school/*` → `app/community_list/*`
- `superadmin/*` → `app/dashboard/*`
- `teacher/*` → `app/mentor/*`
- `student/*` → `app/member/*`

---

## 2. Critical Findings

### 🔴 CRITICAL-1: Both Masking Filters Are Disabled

The `appaccess` and `apprewriter` filters are commented out in `Filters.php`. This means:
- **No incoming URL redirect protection** — users can freely access `admin/dashboard`, `superadmin/system_settings`, etc.
- **No output rewriting** — all HTML output contains raw role-based URLs visible in browser source/network tab.

**Impact:** Full internal URL structure is exposed to all users.

**Fix:** Uncomment both filters in `Filters.php`:
```php
public array $required = [
    'before' => [
        'forcehttps',
        'appaccess',   // ← ENABLE
    ],
    'after' => [
        'performance',
        'apprewriter', // ← ENABLE
    ],
];
```

---

### 🔴 CRITICAL-2: 49+ Controller Redirects Use Unmasked URLs

Controllers generate `redirect()->to()` calls with raw role-based paths that bypass masking entirely (these are HTTP `Location` headers, not HTML output):

| Controller | Unmasked Redirect Count | Examples |
|------------|------------------------|----------|
| `Student.php` | 16 | `student/invoice`, `student/courses/`, `student/exam`, `student/syllabus` |
| `Admin.php` | 15 | `superadmin/Recording`, `superadmin/dashboard`, `student/invoice`, `superadmin/online_admission` |
| `Superadmin.php` | 9 | `superadmin/Recording`, `superadmin/online_admission`, `superadmin/exam` |
| `Teacher.php` | 4 | (various teacher/* paths) |
| `Login.php` | 1 | `student/dashboard` (line 44) |
| `Updater.php` | 2 | (role-based paths) |
| Others | 2 | `addons/Courses.php`, `addons/Lessons.php` |

**Impact:** Even if `AppRewriterFilter` is enabled, it only rewrites **response body** (HTML). HTTP `Location` redirect headers are **NOT processed** by the `after` filter, so the browser address bar will show the unmasked URL.

**Fix:** Replace all controller redirects to use masked URLs:
```php
// BEFORE (leaks role):
return redirect()->to(site_url('superadmin/dashboard'));

// AFTER (masked):
return redirect()->to(site_url('app/dashboard'));
```

---

### 🔴 CRITICAL-3: Login Controller Exposes Role in Redirect (Line 42-44)

```php
// app/Controllers/Login.php line 40-44
$redirect_role = $this->session->get('role');
if ($redirect_role && $redirect_role !== 'student') {
    return redirect()->to(site_url($redirect_role . '/dashboard'))->send();
} else {
    return redirect()->to(site_url('student/dashboard'))->send();
}
```

This dynamically constructs a URL using the raw role name, directly exposing the user's role in the browser URL bar.

**Fix:**
```php
return redirect()->to(site_url('app/dashboard'))->send();
```

---

### 🟠 HIGH-4: Navigation Views Build URLs with Raw `$controller` Variable

`backend/navigation.php` (11 instances) and `backend/navigation-another.php` (4+ instances) construct sidebar URLs using `$controller` which equals the raw role name:

```php
// navigation.php line 1-7
$controller = "";
if ($user_type == 'parent') {
    $controller = 'parents';
} else {
    $controller = $user_type;  // e.g., "admin", "teacher", "student"
}
```

Then used as:
```php
$main_route = $controller . '/calendar';          // → "admin/calendar"
$route = $controller . '/' . $menu['route_name']; // → "admin/invoice"
```

These generate URLs like `site_url('admin/calendar')` in every sidebar link.

**Impact:** Every navigation link in the backend sidebar exposes the real role-based URL structure.

**Fix:** Change `$controller` to always use `'app'` and map route names through the alias system:
```php
$controller = 'app';
```

---

### 🟠 HIGH-5: 120+ View Files Contain Hardcoded Unmasked URLs

Across `app/Views/backend/`, there are extensive hardcoded references to unmasked URLs:

| Pattern | Files Affected | Total Instances |
|---------|---------------|-----------------|
| `site_url('admin/...')` | 30+ files | ~70 instances |
| `site_url('student/...')` | 15+ files | ~30 instances |
| `site_url('teacher/...')` | 10+ files | ~20 instances |
| `site_url('superadmin/...')` | 20+ files | ~40 instances |
| `base_url('admin/...')` | 18+ files | ~50 instances |
| `href="...admin/..."` | 44+ files | ~60 instances |
| AJAX `url:` with role paths | 65+ files | ~100+ instances |
| `action="...admin/..."` | 40+ files | ~50 instances |

**Top offending files:**
- `backend/admin/calendar/list.php` — 12 AJAX URLs with `admin/`
- `backend/teacher/calendar/list.php` — 16 AJAX URLs with role paths
- `backend/superadmin/calendar/list.php` — 17 AJAX URLs with role paths
- `backend/payment_gateway/index.php` — 10+ role-based URLs

---

### 🟠 HIGH-6: POST Routes Not Covered by URL Masking Redirects

There are **123 POST routes** using unmasked role paths (e.g., `admin/teacher/(:any)`, `student/attendance/(:any)`), but:
- `AppAccessFilter` only handles GET/HEAD requests (line 18)
- Route-level 301 redirects only cover GET
- The generic `$routes->post('app/(.*)')` catch-all exists but POST form actions in views still point to unmasked URLs

**Impact:** All form submissions expose the real URL structure in browser network tab and address bar.

---

### 🟡 MEDIUM-7: JavaScript Exposes Role in Global Variable

`backend/header.php` line 365:
```javascript
let CURRENT_USER_ROLE = '<?php echo strtolower(session()->get('role') ?? ''); ?>';
```

**Impact:** Any user can see their exact role name in the page source. While this is needed for client-side logic, it defeats the purpose of URL masking.

---

### 🟡 MEDIUM-8: Error Page Leaks File Path

`backend/index.php` line 149:
```php
echo '<div class="alert alert-danger">Page not found: ' . htmlspecialchars($include_path) . '</div>';
```

This exposes the full server filesystem path when a view file is missing, which reveals the internal directory structure including role names.

**Fix:**
```php
echo '<div class="alert alert-danger">Page not found.</div>';
log_message('error', 'View not found: ' . $include_path);
```

---

### 🟡 MEDIUM-9: `AppRewriterFilter` Uses Broad Regex That Can Cause False Positives

`AppRewriterFilter::rewriteInlineJsUrlStrings()` line 422:
```php
$pattern = '/(["\'])(\/?(?:admin|teacher|student|superadmin|addons|app|home|admission|tutorial|faq|contact)[^"\']*)\1/i';
```

This regex matches ANY quoted string starting with these keywords, which can corrupt:
- Data content (e.g., article text mentioning "student")
- CSS class names
- JavaScript variable values
- Database content displayed on page

---

### 🟡 MEDIUM-10: Route Conflict — Duplicate Route Definitions

Several routes are defined twice, causing potential masking bypass:
- `admin/dashboard` defined as both a redirect (line 32) AND a direct controller route (line 486-507)
- `admin/teacher` defined as redirect (line 36) AND direct route (line 518)
- `admin/student` defined as redirect (line 40) AND direct route (line 533)

The **last matching route wins** in CI4, so the direct controller routes override the masking redirects.

---

### 🟢 LOW-11: `AppAccessFilter` Session Bypass Could Be Exploited

`AppAccessFilter` line 28:
```php
if ($session && $session->getTempdata('__app_unmask_once') === '1') {
    $session->removeTempdata('__app_unmask_once');
    return null;
}
```

If an attacker can set this session tempdata (e.g., through another vulnerability), they can bypass URL masking for one request.

---

### 🟢 LOW-12: Frontend Views Also Leak Internal URLs

`frontend/ultimate/community_details.php` contains `site_url()` and `base_url()` calls with role-based paths (4+ instances), and `href` attributes pointing to `admin/` and `student/` paths.

---

## 3. Summary of Leakage Points

| Layer | Leakage Type | Count | Severity |
|-------|-------------|-------|----------|
| Filters | Both masking filters disabled | 2 | 🔴 CRITICAL |
| Controllers | `redirect()->to()` with unmasked URLs | 49+ | 🔴 CRITICAL |
| Login | Dynamic role-based URL construction | 1 | 🔴 CRITICAL |
| Navigation | `$controller` = raw role name in sidebar | 15+ | 🟠 HIGH |
| Views (site_url) | Hardcoded role-based `site_url()` | 120+ | 🟠 HIGH |
| Views (base_url) | Hardcoded role-based `base_url()` | 50+ | 🟠 HIGH |
| Views (href) | Hardcoded role-based `href=` | 60+ | 🟠 HIGH |
| Views (AJAX url) | JavaScript AJAX with role paths | 100+ | 🟠 HIGH |
| Views (action) | Form actions with role paths | 50+ | 🟠 HIGH |
| POST routes | No masking for POST requests | 123 | 🟠 HIGH |
| JS globals | Role exposed in `CURRENT_USER_ROLE` | 1 | 🟡 MEDIUM |
| Error pages | File path leaked in error message | 1 | 🟡 MEDIUM |
| Route conflicts | Duplicate routes override redirects | 3+ | 🟡 MEDIUM |
| Regex | False positive risk in output rewriting | 1 | 🟡 MEDIUM |
| Frontend | Role URLs in public-facing pages | 4+ | 🟢 LOW |

---

## 4. Recommended Action Plan

### Phase 1 — Immediate (Enable Filters)
1. ✅ Enable `appaccess` filter in `$required['before']`
2. ✅ Enable `apprewriter` filter in `$required['after']`
3. ✅ Fix route conflicts (remove duplicate direct routes that override redirects)

### Phase 2 — Controller Fixes (49+ redirects)
4. Replace all `redirect()->to(site_url('superadmin/...'))` → `site_url('app/...')`
5. Replace all `redirect()->to(site_url('admin/...'))` → `site_url('app/...')`
6. Replace all `redirect()->to(site_url('student/...'))` → `site_url('app/...')`
7. Replace all `redirect()->to(site_url('teacher/...'))` → `site_url('app/...')`
8. Fix Login.php dynamic role URL construction

### Phase 3 — Navigation Refactor
9. Change `$controller` in `navigation.php` to use `'app'` prefix
10. Update route name mapping to use masked aliases (member, mentor, etc.)

### Phase 4 — View Cleanup (400+ instances)
11. Systematically replace all hardcoded role-based URLs in views
12. Create a helper function `masked_url($path)` that auto-maps role paths
13. Update all AJAX URLs and form actions

### Phase 5 — Hardening
14. Remove file path from error messages
15. Tighten `rewriteInlineJsUrlStrings` regex to avoid false positives
16. Add POST request masking support to `AppAccessFilter`
17. Consider obfuscating `CURRENT_USER_ROLE` JS variable

---

## 5. Quick Win: Helper Function

Create a helper to centralize URL masking:

```php
// app/Helpers/url_masking_helper.php
function masked_url(string $path): string
{
    $roleMap = [
        'admin' => 'app', 'superadmin' => 'app', 'teacher' => 'app',
        'student' => 'app', 'accountant' => 'app', 'librarian' => 'app',
        'driver' => 'app', 'parent' => 'app', 'parents' => 'app',
    ];
    $segmentMap = [
        'student' => 'member', 'teacher' => 'mentor', 'exam' => 'certifications',
        'event_calendar' => 'announcements', 'school_settings' => 'community_settings',
        'school' => 'community_list',
    ];

    $parts = explode('/', trim($path, '/'));
    $first = strtolower($parts[0] ?? '');

    if (isset($roleMap[$first])) {
        $parts[0] = $roleMap[$first];
        if (isset($parts[1]) && isset($segmentMap[$parts[1]])) {
            $parts[1] = $segmentMap[$parts[1]];
        }
    }

    return site_url(implode('/', $parts));
}
```

Then replace: `site_url('admin/dashboard')` → `masked_url('admin/dashboard')` → outputs `/app/dashboard`

---

*End of Audit Report*

Recommended Action Plan
Phase 1 (Immediate): Enable both filters in Filters.php + fix route conflicts
Phase 2: Fix 49+ controller redirects to use app/* paths
Phase 3: Refactor navigation to use 'app' prefix instead of raw role
Phase 4: Clean up 400+ hardcoded URLs across views (use a masked_url() helper)
Phase 5: Harden error pages, tighten regex, add POST masking

---

## Re-Audit (2026-04-02)

### Scope
- Rechecked URL masking posture after Phase 2 and Phase 3 implementation.
- Focused on `Filters.php`, controller redirects, backend navigation, routes, and remaining view leaks.

### What Is Fixed
- **Controller redirects to role-prefixed URLs:** fixed.
  - Current scan result: no matches for `redirect()->to(site_url('admin/...|teacher/...|student/...|superadmin/...'))` in `app/Controllers`.
- **Backend navigation role prefix:** fixed.
  - `app/Views/backend/navigation.php` and `app/Views/backend/navigation-another.php` now build non-addon links with `app/*`.
  - No remaining `$controller` route-concatenation logic in these two files.

### What Is Still Open (High Priority)
- **Masking filters are still disabled** in `app/Config/Filters.php`.
  - `appaccess` remains commented in `$required['before']`.
  - `apprewriter` remains commented in `$required['after']`.
- **Route conflicts still exist** in `app/Config/Routes.php`.
  - Example: `admin/dashboard` is declared multiple times (redirect + direct controller routes), which can override masking intent.
- **Large view-layer leakage still present**.
  - Role-prefixed `site_url('admin/...|teacher/...|student/...|superadmin/...')` usage remains across many backend/frontend view files.
  - Role-prefixed `base_url(...)` usage also remains in multiple views.

### Notable Residuals
- `navigation.php` still contains a JS selector targeting `a[href*="admin/dashboard"]` for disabled-menu behavior.
  - This does not build navigation routes, but it is a leftover role-specific reference.

### Re-Audit Conclusion
- **Phases completed:** 2 and 3.
- **Current risk:** still elevated until Phase 1 and Phase 4 are completed.
- **Next critical step:** enable `appaccess` and `apprewriter` filters, then run focused Phase 4 cleanup on high-traffic views (calendar, student/invoice, recording flows).