<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
$routes->setDefaultNamespace('App\\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');

$routes->get('Student(/.*)?', function() {
    return redirect()->to(str_replace('Student', 'student', current_url(true)->getPath()));
});
$routes->get('Admin(/.*)?', function() {
    return redirect()->to(str_replace('Admin', 'admin', current_url(true)->getPath()));
});
$routes->get('Teacher(/.*)?', function() {
    return redirect()->to(str_replace('Teacher', 'teacher', current_url(true)->getPath()));
});
$routes->get('Superadmin(/.*)?', function() {
    return redirect()->to(str_replace('Superadmin', 'superadmin', current_url(true)->getPath()));
});

// ============================================================
// URL MASKING - Legacy URLs redirects to app/* (301 redirects)
// ============================================================
// These preserve SEO by redirecting old URLs to new app/* URLs
$routes->get('admin/dashboard', static function() {
    return redirect()->to('/app/dashboard', 301);
});

$routes->get('admin/teacher(/.*)?', static function($path = '') {
    return redirect()->to('/app/mentor' . $path, 301);
});

$routes->get('admin/student(/.*)?', static function($path = '') {
    return redirect()->to('/app/member' . $path, 301);
});

$routes->get('admin/exam(/.*)?', static function($path = '') {
    return redirect()->to('/app/certifications' . $path, 301);
});

$routes->get('admin/event_calendar(/.*)?', static function($path = '') {
    return redirect()->to('/app/announcements' . $path, 301);
});

$routes->get('admin/school_settings(/.*)?', static function($path = '') {
    return redirect()->to('/app/community_settings' . $path, 301);
});

$routes->get('admin/school(/.*)?', static function($path = '') {
    return redirect()->to('/app/community_list' . $path, 301);
});

// Legacy role-based admin URLs
$routes->get('superadmin(/.*)?', static function($path = '') {
    return redirect()->to('/app/dashboard' . $path, 301);
});

$routes->get('teacher(/.*)?', static function($path = '') {
    return redirect()->to('/app/mentor' . $path, 301);
});

$routes->get('student(/.*)?', static function($path = '') {
    return redirect()->to('/app/member' . $path, 301);
});

// ============================================================
// END URL MASKING
// ============================================================

$routes->get('home/dropdown_guest_lang', 'Home::dropdown_guest_lang');
$routes->post('home/set_guest_language', 'Home::set_guest_language');
$routes->get('home/get_user_roles', 'Home::get_user_roles');
$routes->get('home/get_user_communities', 'Home::get_user_communities');
$routes->get('home/get_student_communities', 'Home::get_student_communities');
$routes->post('home/switch_community_role', 'Home::switch_community_role');
$routes->post('home/switch_to_member_account', 'Home::switch_to_member_account');
$routes->post('home/check_community_name_exists', 'Home::check_community_name_exists');
$routes->post('home/get_communities_by_role', 'Home::get_communities_by_role');
$routes->post('home/switch_community_role_front', 'Home::switch_community_role_front');
$routes->post('home/switch_to_member_account_front', 'Home::switch_to_member_account_front');
$routes->get('home/check_student_status_ajax/(:num)', 'Home::check_student_status_ajax/$1');
$routes->get('home/active_school_id_for_frontend/(:num)', 'Home::active_school_id_for_frontend/$1');
$routes->get('home/communities_search', 'Home::communities_search');
$routes->get('home/communities_search/(:any)', 'Home::communities_search/$1');
$routes->get('home/dropdown_guest', 'Home::dropdown_guest');
$routes->get('home/alumni_event', 'Home::alumni_event');
$routes->get('home/alumni_gallery', 'Home::alumni_gallery');
$routes->get('home/alumni_gallery_view/(:num)', 'Home::alumni_gallery_view/$1');
$routes->get('en', 'Home::index');
$routes->get('fr', 'Home::index');
$routes->get('ar', 'Home::index');
$routes->get('es', 'Home::index');
$routes->get('nl', 'Home::index');
$routes->get('en/join/community', function() {
    return redirect()->to('/admission/online_admission');
});
$routes->get('fr/join/community', function() {
    return redirect()->to('/fr/admission/online_admission');
});
$routes->get('ar/join/community', function() {
    return redirect()->to('/ar/admission/online_admission');
});
$routes->get('es/join/community', function() {
    return redirect()->to('/es/admission/online_admission');
});
$routes->get('nl/join/community', function() {
    return redirect()->to('/nl/admission/online_admission');
});
$routes->get('en/admission/online_admission', 'Home::online_admission_school');
$routes->get('fr/admission/online_admission', 'Home::online_admission_school');
$routes->get('ar/admission/online_admission', 'Home::online_admission_school');
$routes->get('es/admission/online_admission', 'Home::online_admission_school');
$routes->get('nl/admission/online_admission', 'Home::online_admission_school');
$routes->get('en/communities', 'Home::communities');
$routes->get('fr/communities', 'Home::communities');
$routes->get('ar/communities', 'Home::communities');
$routes->get('es/communities', 'Home::communities');
$routes->get('nl/communities', 'Home::communities');
$routes->get('en/communities/(:any)', 'Home::communities');
$routes->get('fr/communities/(:any)', 'Home::communities');
$routes->get('ar/communities/(:any)', 'Home::communities');
$routes->get('es/communities/(:any)', 'Home::communities');
$routes->get('nl/communities/(:any)', 'Home::communities');
$routes->get('home/communities', 'Home::communities');
if (ENVIRONMENT === 'development') {
    $routes->get('test-ci4', static function () {
        echo 'Test route - CI4 anonymous function works!';
        exit;
    });
    $routes->get('test-ctrl', 'Test::index');
}

$routes->get('login', 'Login::index');
$routes->get('login/logout', 'Login::logout', ['as' => 'logout']);
$routes->get('verify-email/(:any)', 'Home::verify_email/$1');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');
$routes->get('teachers', 'Home::teachers');
$routes->get('events', 'Home::events');
$routes->get('gallery', 'Home::gallery');
$routes->get('gallery_view/(:num)', 'Home::gallery_view/$1');
$routes->get('noticeboard', 'Home::noticeboard');
$routes->get('notice_details/(:num)', 'Home::notice_details/$1');
$routes->get('faq', 'Home::faq');
$routes->get('tutorial', 'Home::tutorial');
$routes->get('webinaire', 'Home::webinaire');
$routes->get('affiliation', 'Home::affiliation');
$routes->get('privacy_policy', 'Home::privacy_policy');
$routes->get('terms_conditions', 'Home::terms_conditions');
$routes->get('communities', 'Home::communities');
$routes->get('community_details/(:num)', 'Home::community_details/$1');
$routes->get('online_admission', 'Home::online_admission_school');

$routes->get('en/about', 'Home::about');
$routes->get('fr/about', 'Home::about');
$routes->get('ar/about', 'Home::about');
$routes->get('es/about', 'Home::about');
$routes->get('nl/about', 'Home::about');

$routes->get('en/contact', 'Home::contact');
$routes->get('fr/contact', 'Home::contact');
$routes->get('ar/contact', 'Home::contact');
$routes->get('es/contact', 'Home::contact');
$routes->get('nl/contact', 'Home::contact');

$routes->get('en/teachers', 'Home::teachers');
$routes->get('fr/teachers', 'Home::teachers');
$routes->get('ar/teachers', 'Home::teachers');
$routes->get('es/teachers', 'Home::teachers');
$routes->get('nl/teachers', 'Home::teachers');

$routes->get('en/events', 'Home::events');
$routes->get('fr/events', 'Home::events');
$routes->get('ar/events', 'Home::events');
$routes->get('es/events', 'Home::events');
$routes->get('nl/events', 'Home::events');

$routes->get('en/gallery', 'Home::gallery');
$routes->get('fr/gallery', 'Home::gallery');
$routes->get('ar/gallery', 'Home::gallery');
$routes->get('es/gallery', 'Home::gallery');
$routes->get('nl/gallery', 'Home::gallery');

$routes->get('en/gallery_view/(:num)', 'Home::gallery_view/$1');
$routes->get('fr/gallery_view/(:num)', 'Home::gallery_view/$1');
$routes->get('ar/gallery_view/(:num)', 'Home::gallery_view/$1');
$routes->get('es/gallery_view/(:num)', 'Home::gallery_view/$1');
$routes->get('nl/gallery_view/(:num)', 'Home::gallery_view/$1');

$routes->get('en/noticeboard', 'Home::noticeboard');
$routes->get('fr/noticeboard', 'Home::noticeboard');
$routes->get('ar/noticeboard', 'Home::noticeboard');
$routes->get('es/noticeboard', 'Home::noticeboard');
$routes->get('nl/noticeboard', 'Home::noticeboard');

$routes->get('en/notice_details/(:num)', 'Home::notice_details/$1');
$routes->get('fr/notice_details/(:num)', 'Home::notice_details/$1');
$routes->get('ar/notice_details/(:num)', 'Home::notice_details/$1');
$routes->get('es/notice_details/(:num)', 'Home::notice_details/$1');
$routes->get('nl/notice_details/(:num)', 'Home::notice_details/$1');

$routes->get('en/faq', 'Home::faq');
$routes->get('fr/faq', 'Home::faq');
$routes->get('ar/faq', 'Home::faq');
$routes->get('es/faq', 'Home::faq');
$routes->get('nl/faq', 'Home::faq');

$routes->get('en/tutorial', 'Home::tutorial');
$routes->get('fr/tutorial', 'Home::tutorial');
$routes->get('ar/tutorial', 'Home::tutorial');
$routes->get('es/tutorial', 'Home::tutorial');
$routes->get('nl/tutorial', 'Home::tutorial');

$routes->get('en/webinaire', 'Home::webinaire');
$routes->get('fr/webinaire', 'Home::webinaire');
$routes->get('ar/webinaire', 'Home::webinaire');
$routes->get('es/webinaire', 'Home::webinaire');
$routes->get('nl/webinaire', 'Home::webinaire');

$routes->get('en/affiliation', 'Home::affiliation');
$routes->get('fr/affiliation', 'Home::affiliation');
$routes->get('ar/affiliation', 'Home::affiliation');
$routes->get('es/affiliation', 'Home::affiliation');
$routes->get('nl/affiliation', 'Home::affiliation');

$routes->get('en/privacy_policy', 'Home::privacy_policy');
$routes->get('fr/privacy_policy', 'Home::privacy_policy');
$routes->get('ar/privacy_policy', 'Home::privacy_policy');
$routes->get('es/privacy_policy', 'Home::privacy_policy');
$routes->get('nl/privacy_policy', 'Home::privacy_policy');

$routes->get('terms', 'Home::terms_conditions');

$routes->get('en/terms_conditions', 'Home::terms_conditions');
$routes->get('en/terms', 'Home::terms_conditions');
$routes->get('fr/terms_conditions', 'Home::terms_conditions');
$routes->get('fr/terms', 'Home::terms_conditions');
$routes->get('ar/terms_conditions', 'Home::terms_conditions');
$routes->get('ar/terms', 'Home::terms_conditions');
$routes->get('es/terms_conditions', 'Home::terms_conditions');
$routes->get('es/terms', 'Home::terms_conditions');
$routes->get('nl/terms_conditions', 'Home::terms_conditions');
$routes->get('nl/terms', 'Home::terms_conditions');

$routes->get('en/community_details/(:num)', 'Home::community_details/$1');
$routes->get('fr/community_details/(:num)', 'Home::community_details/$1');
$routes->get('ar/community_details/(:num)', 'Home::community_details/$1');
$routes->get('es/community_details/(:num)', 'Home::community_details/$1');
$routes->get('nl/community_details/(:num)', 'Home::community_details/$1');
$routes->get('en/online_admission', 'Home::online_admission_school');
$routes->get('fr/online_admission', 'Home::online_admission_school');
$routes->get('ar/online_admission', 'Home::online_admission_school');
$routes->get('es/online_admission', 'Home::online_admission_school');
$routes->get('nl/online_admission', 'Home::online_admission_school');
$routes->get('online-admission', 'Home::onlineadmission');
$routes->get('admission/online_admission', 'Home::online_admission_school');

$routes->get('help-center', 'Home::help_center');
$routes->get('en/help-center', 'Home::help_center');
$routes->get('fr/help-center', 'Home::help_center');
$routes->get('ar/help-center', 'Home::help_center');
$routes->get('es/help-center', 'Home::help_center');
$routes->get('nl/help-center', 'Home::help_center');

$routes->post('login/validate_login', 'Login::validate_login');
$routes->post('login/validate_login_frontend', 'Login::validate_login_frontend', ['filter' => 'empty']);
$routes->post('login/validate_credentials', 'Login::validate_credentials');
$routes->post('login/send_reset_link', 'Login::send_reset_link');
$routes->post('login/set_student_just_registered', 'Login::set_student_just_registered');
$routes->post('login/check_email_exists', 'Login::check_email_exists');
$routes->post('login/check_school_name_exists', 'Login::check_school_name_exists');
$routes->get('login/get_csrf_token', 'Login::get_csrf_token');
$routes->get('login/retrieve_password', 'Login::retrieve_password');

$routes->get('register', 'Register::index');
$routes->get('register/communities', 'Register::communities');
$routes->get('register/community/(:num)', 'Register::community/$1');
$routes->get('register/community', 'Admission::online_admission');
$routes->post('register/create', 'Register::create');
$routes->post('register/community', 'Admission::register_community');
$routes->post('register/member', 'Admission::register_member');

$resolveAppRole = static function (): string {
    $session = service('session');

    if ($session->get('superadmin_login')) {
        return 'superadmin';
    }
    if ($session->get('admin_login')) {
        return 'admin';
    }
    if ($session->get('teacher_login')) {
        return 'teacher';
    }
    if ($session->get('student_login')) {
        return 'student';
    }
    if ($session->get('parent_login')) {
        return 'parent';
    }
    if ($session->get('accountant_login')) {
        return 'accountant';
    }
    if ($session->get('librarian_login')) {
        return 'librarian';
    }
    if ($session->get('driver_login')) {
        return 'driver';
    }

    $fallbackRole = strtolower((string) ($session->get('user_type') ?: $session->get('role')));
    if (in_array($fallbackRole, ['superadmin', 'admin', 'teacher', 'student', 'parent', 'accountant', 'librarian', 'driver'], true)) {
        return $fallbackRole;
    }

    return '';
};

$routes->get('app/dashboard', static function () use ($resolveAppRole) {
    $role = $resolveAppRole();
    $controllerMap = [
        'superadmin' => [\App\Controllers\Superadmin::class, 'dashboard'],
        'admin' => [\App\Controllers\Admin::class, 'dashboard'],
        'teacher' => [\App\Controllers\Teacher::class, 'dashboard'],
        'student' => [\App\Controllers\Student::class, 'dashboard'],
        'accountant' => [\App\Controllers\Accountant::class, 'dashboard'],
        'librarian' => [\App\Controllers\Librarian::class, 'dashboard'],
        'driver' => [\App\Controllers\Driver::class, 'dashboard'],
        'parent' => [\App\Controllers\Parents::class, 'index'],
    ];

    if (!isset($controllerMap[$role])) {
        return redirect()->to(site_url('login'));
    }

    [$controllerClass, $method] = $controllerMap[$role];
    $controller = new $controllerClass();
    $controller->initController(service('request'), service('response'), service('logger'));

    return $controller->{$method}();
});

$dispatchAppAlias = static function (string $targetMethod, string $tail = '') use ($resolveAppRole) {
    $role = $resolveAppRole();

    $controllerMap = [
        'superadmin' => \App\Controllers\Superadmin::class,
        'admin' => \App\Controllers\Admin::class,
        'teacher' => \App\Controllers\Teacher::class,
        'student' => \App\Controllers\Student::class,
        'accountant' => \App\Controllers\Accountant::class,
        'librarian' => \App\Controllers\Librarian::class,
        'driver' => \App\Controllers\Driver::class,
        'parent' => \App\Controllers\Parents::class,
    ];

    if (!isset($controllerMap[$role])) {
        return redirect()->to(site_url('login'));
    }

    $controllerClass = $controllerMap[$role];
    $controller = new $controllerClass();
    $controller->initController(service('request'), service('response'), service('logger'));

    if (!method_exists($controller, $targetMethod)) {
        return redirect()->to(site_url('login'));
    }

    $params = [];
    if ($tail !== '') {
        $params = array_values(array_filter(explode('/', $tail), static fn($s) => $s !== ''));
    }

    return $controller->{$targetMethod}(...$params);
};

$routes->get('app/member', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('student');
});
$routes->get('app/member/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('student', (string) $tail);
});
$routes->get('app/mentor', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('teacher');
});
// Keep student sub-routes explicit so /app/mentor/student/* resolves to Teacher::student().
$routes->get('app/mentor/student', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('student');
});
$routes->get('app/mentor/student/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('student', (string) $tail);
});
$routes->post('app/mentor/student', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('student');
});
$routes->post('app/mentor/student/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('student', (string) $tail);
});
$routes->get('app/mentor/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('teacher', (string) $tail);
});
$routes->get('app/certifications', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('exam');
});
$routes->get('app/certifications/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('exam', (string) $tail);
});
$routes->get('app/announcements', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('event_calendar');
});
// Specific routes for AJAX endpoints - MUST be before the wildcard route
$routes->get('app/announcements/get_events', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_events');
});
$routes->get('app/announcements/create_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('create_event');
});
$routes->post('app/announcements/create_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('create_event');
});
$routes->get('app/announcements/update_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('update_event');
});
$routes->post('app/announcements/update_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('update_event');
});
$routes->get('app/announcements/delete_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('delete_event');
});
$routes->post('app/announcements/delete_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('delete_event');
});
$routes->post('app/announcements/start_meeting', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('start_meeting');
});
// Wildcard route - MUST be after specific routes
$routes->get('app/announcements/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('event_calendar', (string) $tail);
});
$routes->get('app/community_settings', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('school_settings');
});
$routes->get('app/community_settings/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('school_settings', (string) $tail);
});
$routes->get('app/community_list', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('school');
});
$routes->get('app/community_list/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('school', (string) $tail);
});
$routes->get('app/courses', 'addons\\Courses::index');
$routes->get('app/courses/(:num)', 'Student::manage_class/courses/$1');
$routes->get('app/courses/(.*)', 'addons\\Courses::$1');
$routes->post('app/courses', 'addons\\Courses::index');
$routes->post('app/courses/(.*)', 'addons\\Courses::$1');
$routes->get('app/lessons', 'addons\\Lessons::index');
$routes->get('app/lessons/(.*)', 'addons\\Lessons::$1');
$routes->post('app/lessons', 'addons\\Lessons::index');
$routes->post('app/lessons/(.*)', 'addons\\Lessons::$1');
$routes->get('app/chat', 'Chat::index');
$routes->post('app/chat', 'Chat::index');
$routes->get('app/calendar', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('calendar');
});
$routes->get('app/calendar/(.*)', static function ($tail) use ($dispatchAppAlias) {
    return $dispatchAppAlias('calendar', (string) $tail);
});
// Keep wall routes explicit so they don't fall into app/(.*) generic dispatch
// (which would try role-controller methods and can bounce to dashboard/login).
$routes->get('app/class_wall', 'Wall::class');
$routes->get('app/class_wall/(:segment)', 'Wall::class/$1');
$routes->get('app/wall', 'Wall::community');
$routes->get('app/wall/(:segment)', 'Wall::community/$1');
$routes->get('app/community_wall', 'Wall::community');
$routes->get('app/community_wall/(:segment)', 'Wall::community/$1');
// Missing app aliases for shared calendar/school endpoints used by views/AJAX.
$routes->get('app/get_events', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_events');
});
$routes->get('app/get_user_school', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_user_school');
});
$routes->get('app/school/get_user_school', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_user_school');
});
$routes->get('app/get_school_data', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_school_data');
});
$routes->post('app/get_school_data', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_school_data');
});
$routes->get('app/school/get_school_data', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_school_data');
});
$routes->post('app/school/get_school_data', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_school_data');
});
$routes->get('app/create_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('create_event');
});
$routes->post('app/create_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('create_event');
});
$routes->get('app/update_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('update_event');
});
$routes->post('app/update_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('update_event');
});
$routes->get('app/delete_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('delete_event');
});
$routes->post('app/delete_event', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('delete_event');
});
$routes->post('app/start_meeting', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('start_meeting');
});
$routes->get('app/get_exams_paginated', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_exams_paginated');
});
$routes->get('app/certifications/get_exams_paginated', static function () use ($dispatchAppAlias) {
    return $dispatchAppAlias('get_exams_paginated');
});
$dispatchGenericApp = static function (string $appPath = '') use ($resolveAppRole) {
    $role = $resolveAppRole();
    $controllerMap = [
        'superadmin' => \App\Controllers\Superadmin::class,
        'admin' => \App\Controllers\Admin::class,
        'teacher' => \App\Controllers\Teacher::class,
        'student' => \App\Controllers\Student::class,
        'accountant' => \App\Controllers\Accountant::class,
        'librarian' => \App\Controllers\Librarian::class,
        'driver' => \App\Controllers\Driver::class,
        'parent' => \App\Controllers\Parents::class,
    ];

    if (!isset($controllerMap[$role])) {
        return redirect()->to(site_url('login'));
    }

    $controllerClass = $controllerMap[$role];
    $controller = new $controllerClass();
    $controller->initController(service('request'), service('response'), service('logger'));

    $appPath = trim($appPath, '/');
    if ($appPath === '') {
        $defaultMethod = $role === 'parent' ? 'index' : 'dashboard';
        if (!method_exists($controller, $defaultMethod)) {
            return redirect()->to(site_url('login'));
        }
        return $controller->{$defaultMethod}();
    }

    $segments = array_values(array_filter(explode('/', $appPath), static fn($s) => $s !== ''));
    $first = (string) ($segments[0] ?? '');
    $rest = array_slice($segments, 1);

    $aliasToMethod = [
        'member' => 'student',
        'mentor' => 'teacher',
        'certifications' => 'exam',
        'announcements' => 'event_calendar',
        'community_settings' => 'school_settings',
        'community_list' => 'school',
    ];
    $method = $aliasToMethod[$first] ?? $first;

    if (!method_exists($controller, $method)) {
        return redirect()->to(site_url('login'));
    }

    return $controller->{$method}(...$rest);
};
$routes->get('app', static function () use ($dispatchGenericApp) {
    return $dispatchGenericApp('dashboard');
});
$routes->get('app/', static function () use ($dispatchGenericApp) {
    return $dispatchGenericApp('dashboard');
});
$routes->get('app/(.*)', static function ($path) use ($dispatchGenericApp) {
    return $dispatchGenericApp((string) $path);
});
$routes->post('app/(.*)', static function ($path) use ($dispatchGenericApp) {
    return $dispatchGenericApp((string) $path);
});

$routes->get('admin', 'Admin::index');
$routes->get('admin/dashboard', 'Admin::dashboard', ['as' => 'dashboard']);
$routes->get('admin/invoice', 'Admin::invoice', ['as' => 'invoice']);
if (ENVIRONMENT === 'development') {
    $routes->get('test-route', static function () {
        return 'Test route works!';
    });
}

$routes->get('admin/language', 'Admin::language', ['as' => 'language']);
$routes->get('admin/language/dropdown', 'Admin::language/dropdown', ['as' => 'language/dropdown']);
$routes->get('admin/class_wall', 'Wall::class');
$routes->get('admin/class_wall/(:segment)', 'Wall::class/$1');
$routes->get('admin/wall', 'Wall::community');
$routes->get('admin/wall/(:segment)', 'Wall::community/$1');
$routes->get('community_wall', 'Wall::community');
$routes->get('community_wall/(:segment)', 'Wall::community/$1');
$routes->get('(:segment)/community_wall', 'Wall::community');
$routes->get('(:segment)/community_wall/(:segment)', 'Wall::community/$2');
$routes->get('join/community', function() {
    return redirect()->to('/admission/online_admission');
}, ['as' => 'join/community']);

$routes->get('admin/school', 'Admin::school');
$routes->get('admin/dashboard', 'Admin::dashboard');
$routes->get('admin/manage_class', 'Admin::manage_class');
$routes->get('admin/manage_class/(:any)', 'Admin::manage_class/$1');
$routes->post('admin/manage_class', 'Admin::manage_class');
$routes->post('admin/manage_class/(:any)', 'Admin::manage_class/$1');
$routes->get('admin/section', 'Admin::section');
$routes->get('admin/section/(:any)', 'Admin::section/$1');
$routes->get('admin/class_room', 'Admin::class_room');
$routes->get('admin/class_room/(:any)', 'Admin::class_room/$1');
$routes->get('admin/subject', 'Admin::subject');
$routes->get('admin/subject/(:any)', 'Admin::subject/$1');
$routes->get('admin/teacher', 'Admin::teacher');
$routes->get('admin/teacher/(:any)', 'Admin::teacher/$1');
$routes->post('admin/teacher', 'Admin::teacher');
$routes->post('admin/teacher/(:any)', 'Admin::teacher/$1');
$routes->post('admin/teacher/(:any)/(:any)', 'Admin::teacher/$1/$2');
$routes->post('admin/teacher/(:any)/(:any)/(:any)', 'Admin::teacher/$1/$2/$3');
$routes->get('admin/permission', 'Admin::permission');
$routes->get('admin/permission/(:any)', 'Admin::permission/$1');
$routes->get('admin/permission/(:any)/(:any)', 'Admin::permission/$1/$2');
$routes->post('admin/permission', 'Admin::permission');
$routes->post('admin/permission/(:any)', 'Admin::permission/$1');
$routes->post('admin/permission/(:any)/(:any)', 'Admin::permission/$1/$2');
$routes->post('admin/get_permission_history', 'Admin::get_permission_history');
$routes->get('admin/accountant', 'Admin::accountant');
$routes->get('admin/accountant/(:any)', 'Admin::accountant/$1');
$routes->get('admin/librarian', 'Admin::librarian');
$routes->get('admin/librarian/(:any)', 'Admin::librarian/$1');
$routes->get('admin/student', 'Admin::student');
$routes->get('admin/student/(:any)', 'Admin::student/$1');
$routes->get('admin/student/(:any)/(:any)', 'Admin::student/$1/$2');
$routes->post('admin/student', 'Admin::student');
$routes->post('admin/student/(:any)', 'Admin::student/$1');
$routes->post('admin/student/(:any)/(:any)', 'Admin::student/$1/$2');
$routes->post('admin/student/(:any)/(:any)/(:any)', 'Admin::student/$1/$2/$3');
$routes->post('admin/student/(:any)/(:any)/(:any)/(:any)', 'Admin::student/$1/$2/$3/$4');
$routes->post('admin/student/(:any)/(:any)/(:any)/(:any)/(:any)', 'Admin::student/$1/$2/$3/$4/$5');
$routes->get('admin/class_wise_subject', 'Admin::class_wise_subject');
$routes->get('admin/syllabus', 'Admin::syllabus');
$routes->get('admin/syllabus/(:any)', 'Admin::syllabus/$1');
$routes->post('admin/syllabus', 'Admin::syllabus');
$routes->post('admin/syllabus/(:any)', 'Admin::syllabus/$1');
$routes->get('admin/routine', 'Admin::routine');
$routes->get('admin/routine/(:any)', 'Admin::routine/$1');
$routes->get('admin/attendance', 'Admin::attendance');
$routes->get('admin/attendance/(:any)', 'Admin::attendance/$1');
$routes->get('admin/attendance/(:any)/(:any)', 'Admin::attendance/$1/$2');
$routes->get('admin/attendance/(:any)/(:any)/(:any)', 'Admin::attendance/$1/$2/$3');
$routes->post('admin/attendance', 'Admin::attendance');
$routes->post('admin/attendance/(:any)', 'Admin::attendance/$1');
$routes->post('admin/attendance/(:any)/(:any)', 'Admin::attendance/$1/$2');
$routes->post('admin/attendance/(:any)/(:any)/(:any)', 'Admin::attendance/$1/$2/$3');
$routes->get('admin/exam', 'Admin::exam');
$routes->get('admin/exam/(:any)', 'Admin::exam/$1');
$routes->post('admin/exam', 'Admin::exam');
$routes->post('admin/exam/(:any)', 'Admin::exam/$1');
$routes->get('admin/mark', 'Admin::mark');
$routes->get('admin/mark/(:any)', 'Admin::mark/$1');
$routes->get('admin/quiz', 'Admin::quiz');
$routes->get('admin/quiz/(:any)', 'Admin::quiz/$1');
$routes->post('admin/quiz', 'Admin::quiz');
$routes->post('admin/quiz/(:any)', 'Admin::quiz/$1');
$routes->get('admin/grade', 'Admin::grade');
$routes->get('admin/grade/(:any)', 'Admin::grade/$1');
$routes->post('admin/grade', 'Admin::grade');
$routes->post('admin/grade/(:any)', 'Admin::grade/$1');
$routes->get('admin/promotion', 'Admin::promotion');
$routes->get('admin/promotion/(:any)', 'Admin::promotion/$1');
$routes->post('admin/promotion', 'Admin::promotion');
$routes->post('admin/promotion/(:any)', 'Admin::promotion/$1');
$routes->get('admin/invoice', 'Admin::invoice');
$routes->get('admin/invoice/(:any)', 'Admin::invoice/$1');
$routes->post('admin/invoice', 'Admin::invoice');
$routes->post('admin/invoice/(:any)', 'Admin::invoice/$1');
$routes->get('admin/payment/(:any)', 'Admin::payment/$1');
$routes->post('admin/payment/(:any)', 'Admin::payment/$1');
$routes->get('admin/payment_success/(:any)', 'Admin::payment_success/$1', ['as' => 'payment_success']);
$routes->post('admin/payment_success/(:any)', 'Admin::payment_success/$1');
$routes->get('admin/export', 'Admin::export');
$routes->get('admin/export/(:any)', 'Admin::export/$1');
$routes->get('admin/expense', 'Admin::expense');
$routes->get('admin/expense/(:any)', 'Admin::expense/$1');
$routes->post('admin/expense', 'Admin::expense');
$routes->post('admin/expense/(:any)', 'Admin::expense/$1');
$routes->post('admin/expense/(:any)/(:any)', 'Admin::expense/$1/$2');
$routes->get('admin/expense_category', 'Admin::expense_category');
$routes->get('admin/expense_category/(:any)', 'Admin::expense_category/$1');
$routes->post('admin/expense_category', 'Admin::expense_category');
$routes->post('admin/expense_category/(:any)', 'Admin::expense_category/$1');
$routes->post('admin/expense_category/(:any)/(:any)', 'Admin::expense_category/$1/$2');
$routes->get('admin/book', 'Admin::book');
$routes->get('admin/book/(:any)', 'Admin::book/$1');
$routes->get('admin/book_issue', 'Admin::book_issue');
$routes->get('admin/book_issue/(:any)', 'Admin::book_issue/$1');
$routes->get('admin/noticeboard', 'Admin::noticeboard');
$routes->get('admin/noticeboard/(:any)', 'Admin::noticeboard/$1');
$routes->get('admin/event', 'Admin::events');
$routes->get('admin/event/(:any)', 'Admin::events/$1');
$routes->get('admin/event_calendar', 'Admin::event_calendar');
$routes->get('admin/event_calendar/(:any)', 'Admin::event_calendar/$1');
$routes->post('admin/event_calendar', 'Admin::event_calendar');
$routes->post('admin/event_calendar/(:any)', 'Admin::event_calendar/$1');
$routes->get('admin/calendar', 'Admin::calendar');
$routes->get('admin/calendar/(:any)', 'Admin::calendar/$1');
$routes->get('admin/get_events', 'Admin::get_events');
$routes->get('admin/get_exams_paginated', 'Admin::get_exams_paginated');
$routes->get('admin/get_school_data', 'Admin::get_school_data');
$routes->post('admin/get_school_data', 'Admin::get_school_data');
$routes->get('admin/check_teacher_email', 'Admin::check_teacher_email');
$routes->post('admin/check_teacher_email', 'Admin::check_teacher_email');
$routes->get('admin/get_user_school', 'Admin::get_user_school');
$routes->post('admin/start_meeting', 'Admin::start_meeting');
$routes->get('admin/create_event', 'Admin::create_event');
$routes->post('admin/create_event', 'Admin::create_event');
$routes->get('admin/update_event', 'Admin::update_event');
$routes->post('admin/update_event', 'Admin::update_event');
$routes->get('admin/delete_event', 'Admin::delete_event');
$routes->post('admin/delete_event', 'Admin::delete_event');
$routes->get('admin/system_settings', 'Admin::system_settings');
$routes->get('admin/system_settings/(:any)', 'Admin::system_settings/$1');
$routes->get('admin/website_settings', 'Admin::website_settings');
$routes->get('admin/website_settings/(:any)', 'Admin::website_settings/$1');
$routes->get('admin/language', 'Admin::language');
$routes->get('admin/language/(:any)', 'Admin::language/$1');
$routes->get('admin/profile', 'Admin::profile');
$routes->get('admin/profile/(:any)', 'Admin::profile/$1');
$routes->get('admin/online_admission', 'Admin::online_admission');
$routes->get('admin/online_admission/(:any)', 'Admin::online_admission/$1');
$routes->get('admin/addon', 'Admin::addon_manager');
$routes->get('admin/addon/(:any)', 'Admin::addon_manager/$1');
$routes->get('admin/session', 'Admin::session_manager');
$routes->get('admin/session/(:any)', 'Admin::session_manager/$1');
$routes->get('admin/live_class', 'Admin::Liveclasse');
$routes->get('admin/live_class/(:any)', 'Admin::Liveclasse/$1');
$routes->get('admin/recording', 'Admin::recording');
$routes->get('admin/recording/(:any)', 'Admin::recording/$1');
$routes->get('admin/billing', 'Admin::billing_entities');
$routes->get('admin/billing/(:any)', 'Admin::billing_entities/$1');
$routes->get('admin/payment_methods', 'Admin::payment_methods');
$routes->get('admin/payment_methods/(:any)', 'Admin::payment_methods/$1');
$routes->get('admin/school_settings', 'Admin::school_settings');
$routes->get('admin/school_settings/(:any)', 'Admin::school_settings/$1');
$routes->post('admin/school_settings', 'Admin::school_settings');
$routes->post('admin/school_settings/(:any)', 'Admin::school_settings/$1');
$routes->get('admin/payment_settings', 'Admin::payment_settings');
$routes->get('admin/payment_settings/(:any)', 'Admin::payment_settings/$1');
$routes->post('admin/payment_settings', 'Admin::payment_settings');
$routes->post('admin/payment_settings/(:any)', 'Admin::payment_settings/$1');
$routes->get('admin/(:segment)', 'Admin::$1');

$routes->get('payment/community/(:num)', 'Student::payment/community/$1');
$routes->get('app/payment', 'Student::payment');
$routes->get('app/payment/(:any)', 'Admin::payment/$1');

$routes->get('student', 'Student::index');
$routes->get('student/dashboard', 'Student::dashboard');
$routes->get('student/courses', 'Student::courses');
$routes->get('student/courses/(:any)', 'Student::courses/$1');
$routes->get('student/my_courses', 'Student::courses');
$routes->get('student/recording', 'Student::recording');
$routes->get('student/recording/(:any)', 'Student::recording/$1');
$routes->get('student/syllabus', 'Student::syllabus');
$routes->get('student/syllabus/(:any)', 'Student::syllabus/$1');
$routes->get('student/routine', 'Student::routine');
$routes->get('student/routine/(:any)', 'Student::routine/$1');
$routes->get('student/attendance', 'Student::attendance');
$routes->get('student/attendance/(:any)', 'Student::attendance/$1');
$routes->post('student/attendance', 'Student::attendance');
$routes->post('student/attendance/(:any)', 'Student::attendance/$1');
$routes->post('student/attendance/(:any)/(:any)', 'Student::attendance/$1/$2');
$routes->get('student/exam', 'Student::exam');
$routes->get('student/exam/(:any)', 'Student::exam/$1');
$routes->get('student/class_wall', 'Wall::class');
$routes->get('student/class_wall/(:segment)', 'Wall::class/$1');
$routes->get('student/exam_class', 'Student::exam_class');
$routes->get('student/exam_class/(:any)', 'Student::exam_class/$1');
$routes->get('student/exam_class/(:any)/(:any)', 'Student::exam_class/$1/$2');
$routes->get('student/online_exam', 'Student::online_exam');
$routes->get('student/online_exam/(:any)', 'Student::online_exam/$1');
$routes->post('student/submit_exam', 'Student::submit_exam');
$routes->get('student/mark', 'Student::mark');
$routes->get('student/mark/(:any)', 'Student::mark/$1');
$routes->post('student/mark', 'Student::mark');
$routes->post('student/mark/(:any)', 'Student::mark/$1');
$routes->post('student/mark/(:any)/(:any)', 'Student::mark/$1/$2');
$routes->get('student/grade', 'Student::grade');
$routes->get('student/invoice', 'Student::invoice');
$routes->get('student/invoice/(:any)', 'Student::invoice/$1');
$routes->get('student/invoice_pdf/(:any)', 'Student::invoice_pdf/$1');
$routes->get('student/payment', 'Student::payment');
$routes->get('student/payment/(:any)', 'Student::payment/$1');
$routes->get('student/payment_success', 'Student::payment_success');
$routes->get('student/payment_success/(:any)', 'Student::payment_success/$1');
$routes->post('student/payment_success', 'Student::payment_success');
$routes->post('student/payment_success/(:any)', 'Student::payment_success/$1');
$routes->get('student/book', 'Student::book');
$routes->get('student/book/(:any)', 'Student::book/$1');
$routes->get('student/profile', 'Student::profile');
$routes->get('student/profile/(:any)', 'Student::profile/$1');
$routes->get('student/calendar', 'Student::calendar');
$routes->get('student/calendar/(:any)', 'Student::calendar/$1');
$routes->get('student/event', 'Student::event_calendar');
$routes->get('student/event/(:any)', 'Student::event_calendar/$1');
$routes->get('student/language', 'Student::language');
$routes->get('student/teacher', 'Student::teacher');
$routes->get('student/teacher/(:any)', 'Student::teacher/$1');
$routes->get('student/academy', 'Student::academy');
$routes->get('student/academy/(:any)', 'Student::academy/$1');
$routes->get('student/academy/(:any)/(:any)', 'Student::academy/$1/$2');
$routes->post('student/academy/(:any)', 'Student::academy/$1');
$routes->post('student/academy/(:any)/(:any)', 'Student::academy/$1/$2');
$routes->get('student/get_events', 'Student::get_events');
$routes->get('student/get_school_data', 'Student::get_school_data');
$routes->post('student/get_school_data', 'Student::get_school_data');
$routes->get('student/filter', 'Teacher::student/filter');
$routes->get('student/filter/(:any)', 'Teacher::student/filter/$1');
$routes->post('student/filter', 'Teacher::student/filter');
$routes->post('student/filter/(:any)', 'Teacher::student/filter/$1');
$routes->get('student/online_admission', 'Student::online_admission');
$routes->get('student/online_admission/(:any)', 'Student::online_admission/$1');
$routes->post('student/online_admission', 'Student::online_admission');
$routes->post('student/online_admission/(:any)', 'Student::online_admission/$1');
$routes->post('student/online_admission/(:any)/(:any)', 'Student::online_admission/$1/$2');
$routes->get('student/join_school/(:any)/(:any)', 'Student::join_school/$1/$2');
$routes->post('student/join_school/(:any)/(:any)', 'Student::join_school/$1/$2');
$routes->post('student/start_meeting', 'Student::start_meeting');
$routes->get('student/(:segment)', 'Student::$1');

$routes->get('teacher', 'Teacher::index');
$routes->get('teacher/dashboard', 'Teacher::dashboard');
$routes->post('teacher/start_meeting', 'Teacher::start_meeting');
$routes->get('teacher/student', 'Teacher::student');
$routes->get('teacher/student/(:any)', 'Teacher::student/$1');
$routes->get('teacher/student/(:any)/(:any)', 'Teacher::student/$1/$2');
$routes->get('teacher/student/(:any)/(:any)/(:any)', 'Teacher::student/$1/$2/$3');
$routes->post('teacher/student', 'Teacher::student');
$routes->post('teacher/student/(:any)', 'Teacher::student/$1');
$routes->post('teacher/student/(:any)/(:any)', 'Teacher::student/$1/$2');
$routes->post('teacher/student/(:any)/(:any)/(:any)', 'Teacher::student/$1/$2/$3');
// Legacy compatibility: keep 4-segment student actions routable.
$routes->get('teacher/student/(:any)/(:any)/(:any)/(:any)', 'Teacher::student/$1/$2/$3/$4');
$routes->post('teacher/student/(:any)/(:any)/(:any)/(:any)', 'Teacher::student/$1/$2/$3/$4');
$routes->get('teacher/attendance', 'Teacher::attendance');
$routes->get('teacher/attendance/(:any)', 'Teacher::attendance/$1');
$routes->get('teacher/attendance/(:any)/(:any)', 'Teacher::attendance/$1/$2');
$routes->get('teacher/attendance/(:any)/(:any)/(:any)', 'Teacher::attendance/$1/$2/$3');
$routes->post('teacher/attendance', 'Teacher::attendance');
$routes->post('teacher/attendance/(:any)', 'Teacher::attendance/$1');
$routes->post('teacher/attendance/(:any)/(:any)', 'Teacher::attendance/$1/$2');
$routes->post('teacher/attendance/(:any)/(:any)/(:any)', 'Teacher::attendance/$1/$2/$3');
$routes->get('teacher/event_calendar', 'Teacher::event_calendar');
$routes->get('teacher/event_calendar/(:any)', 'Teacher::event_calendar/$1');
$routes->post('teacher/event_calendar', 'Teacher::event_calendar');
$routes->post('teacher/event_calendar/(:any)', 'Teacher::event_calendar/$1');
$routes->get('teacher/get_events', 'Teacher::get_events');
$routes->get('teacher/get_user_school', 'Teacher::get_user_school');
$routes->post('teacher/get_school_data', 'Teacher::get_school_data');
$routes->get('teacher/create_event', 'Teacher::create_event');
$routes->post('teacher/create_event', 'Teacher::create_event');
$routes->get('teacher/update_event', 'Teacher::update_event');
$routes->post('teacher/update_event', 'Teacher::update_event');
$routes->get('teacher/delete_event', 'Teacher::delete_event');
$routes->post('teacher/delete_event', 'Teacher::delete_event');
$routes->get('teacher/exam', 'Teacher::exam');
$routes->get('teacher/exam/(:any)', 'Teacher::exam/$1');
$routes->get('teacher/exam/(:any)/(:any)', 'Teacher::exam/$1/$2');
$routes->get('teacher/exam/(:any)/(:any)/(:any)', 'Teacher::exam/$1/$2/$3');
$routes->post('teacher/exam', 'Teacher::exam');
$routes->post('teacher/exam/(:any)', 'Teacher::exam/$1');
$routes->post('teacher/exam/(:any)/(:any)', 'Teacher::exam/$1/$2');
$routes->post('teacher/exam/(:any)/(:any)/(:any)', 'Teacher::exam/$1/$2/$3');
$routes->get('teacher/class_wall', 'Wall::class');
$routes->get('teacher/class_wall/(:segment)', 'Wall::class/$1');
$routes->get('teacher/(:segment)', 'Teacher::$1');

$routes->get('superadmin', 'Superadmin::index');
$routes->get('superadmin/class_wall', 'Wall::class');
$routes->get('superadmin/class_wall/(:segment)', 'Wall::class/$1');
$routes->get('superadmin/dashboard', 'Superadmin::dashboard');
$routes->get('superadmin/community_list', 'Superadmin::school');
$routes->get('superadmin/community_list/(:any)', 'Superadmin::school/$1');
$routes->post('superadmin/start_meeting', 'Superadmin::start_meeting');
$routes->get('superadmin/get_events', 'Superadmin::get_events');
$routes->get('superadmin/get_user_school', 'Superadmin::get_user_school');
$routes->post('superadmin/get_school_data', 'Superadmin::get_school_data');
$routes->get('superadmin/create_event', 'Superadmin::create_event');
$routes->post('superadmin/create_event', 'Superadmin::create_event');
$routes->get('superadmin/update_event', 'Superadmin::update_event');
$routes->post('superadmin/update_event', 'Superadmin::update_event');
$routes->get('superadmin/delete_event', 'Superadmin::delete_event');
$routes->post('superadmin/delete_event', 'Superadmin::delete_event');
$routes->get('superadmin/system_settings', 'Superadmin::system_settings');
$routes->get('superadmin/system_settings/(:any)', 'Superadmin::system_settings/$1');
$routes->post('superadmin/system_settings', 'Superadmin::system_settings');
$routes->post('superadmin/system_settings/(:any)', 'Superadmin::system_settings/$1');
$routes->get('superadmin/payment_settings', 'Superadmin::payment_settings');
$routes->get('superadmin/payment_settings/(:any)', 'Superadmin::payment_settings/$1');
$routes->get('superadmin/payment_settings/(:any)/(:any)', 'Superadmin::payment_settings/$1/$2');
$routes->post('superadmin/payment_settings', 'Superadmin::payment_settings');
$routes->post('superadmin/payment_settings/(:any)', 'Superadmin::payment_settings/$1');
$routes->post('superadmin/payment_settings/(:any)/(:any)', 'Superadmin::payment_settings/$1/$2');
$routes->get('superadmin/billing_entities', 'Superadmin::billing_entities');
$routes->get('superadmin/billing_entities/(:any)', 'Superadmin::billing_entities/$1');
$routes->get('superadmin/billing_entities/(:any)/(:any)', 'Superadmin::billing_entities/$1/$2');
$routes->get('superadmin/billing_entities/(:any)/(:any)/(:any)', 'Superadmin::billing_entities/$1/$2/$3');
$routes->post('superadmin/billing_entities', 'Superadmin::billing_entities');
$routes->post('superadmin/billing_entities/(:any)', 'Superadmin::billing_entities/$1');
$routes->post('superadmin/billing_entities/(:any)/(:any)', 'Superadmin::billing_entities/$1/$2');
$routes->post('superadmin/billing_entities/(:any)/(:any)/(:any)', 'Superadmin::billing_entities/$1/$2/$3');
$routes->get('superadmin/payment_methods', 'Superadmin::payment_methods');
$routes->get('superadmin/payment_methods/(:any)', 'Superadmin::payment_methods/$1');
$routes->get('superadmin/payment_methods/(:any)/(:any)', 'Superadmin::payment_methods/$1/$2');
$routes->post('superadmin/payment_methods', 'Superadmin::payment_methods');
$routes->post('superadmin/payment_methods/(:any)', 'Superadmin::payment_methods/$1');
$routes->post('superadmin/payment_methods/(:any)/(:any)', 'Superadmin::payment_methods/$1/$2');
$routes->get('superadmin/language', 'Superadmin::language');
$routes->get('superadmin/language/(:any)', 'Superadmin::language/$1');
$routes->get('superadmin/language/(:any)/(:any)', 'Superadmin::language/$1/$2');
$routes->post('superadmin/language', 'Superadmin::language');
$routes->post('superadmin/language/(:any)', 'Superadmin::language/$1');
$routes->post('superadmin/language/(:any)/(:any)', 'Superadmin::language/$1/$2');
$routes->get('superadmin/smtp_settings', 'Superadmin::smtp_settings');
$routes->get('superadmin/smtp_settings/(:any)', 'Superadmin::smtp_settings/$1');
$routes->get('superadmin/smtp_settings/(:any)/(:any)', 'Superadmin::smtp_settings/$1/$2');
$routes->post('superadmin/smtp_settings', 'Superadmin::smtp_settings');
$routes->post('superadmin/smtp_settings/(:any)', 'Superadmin::smtp_settings/$1');
$routes->post('superadmin/smtp_settings/(:any)/(:any)', 'Superadmin::smtp_settings/$1/$2');
$routes->get('superadmin/school_settings', 'Superadmin::school_settings');
$routes->get('superadmin/school_settings/(:any)', 'Superadmin::school_settings/$1');
$routes->get('superadmin/school_settings/(:any)/(:any)', 'Superadmin::school_settings/$1/$2');
$routes->post('superadmin/school_settings', 'Superadmin::school_settings');
$routes->post('superadmin/school_settings/(:any)', 'Superadmin::school_settings/$1');
$routes->post('superadmin/school_settings/(:any)/(:any)', 'Superadmin::school_settings/$1/$2');
$routes->get('superadmin/website_settings', 'Superadmin::website_settings');
$routes->get('superadmin/website_settings/(:any)', 'Superadmin::website_settings/$1');
$routes->get('superadmin/website_settings/(:any)/(:any)', 'Superadmin::website_settings/$1/$2');
$routes->post('superadmin/website_settings', 'Superadmin::website_settings');
$routes->post('superadmin/website_settings/(:any)', 'Superadmin::website_settings/$1');
$routes->post('superadmin/website_settings/(:any)/(:any)', 'Superadmin::website_settings/$1/$2');
$routes->get('superadmin/website_update', 'Superadmin::website_update');
$routes->get('superadmin/website_update/(:any)', 'Superadmin::website_update/$1');
$routes->post('superadmin/website_update', 'Superadmin::website_update');
$routes->post('superadmin/website_update/(:any)', 'Superadmin::website_update/$1');
$routes->get('superadmin/expense_category', 'Superadmin::expense_category');
$routes->get('superadmin/expense_category/(:any)', 'Superadmin::expense_category/$1');
$routes->get('superadmin/expense_category/(:any)/(:any)', 'Superadmin::expense_category/$1/$2');
$routes->post('superadmin/expense_category', 'Superadmin::expense_category');
$routes->post('superadmin/expense_category/(:any)', 'Superadmin::expense_category/$1');
$routes->post('superadmin/expense_category/(:any)/(:any)', 'Superadmin::expense_category/$1/$2');
$routes->get('superadmin/expense', 'Superadmin::expense');
$routes->get('superadmin/expense/(:any)', 'Superadmin::expense/$1');
$routes->get('superadmin/expense/(:any)/(:any)', 'Superadmin::expense/$1/$2');
$routes->post('superadmin/expense', 'Superadmin::expense');
$routes->post('superadmin/expense/(:any)', 'Superadmin::expense/$1');
$routes->post('superadmin/expense/(:any)/(:any)', 'Superadmin::expense/$1/$2');
$routes->get('superadmin/school_crud', 'Superadmin::school_crud');
$routes->get('superadmin/school_crud/(:any)', 'Superadmin::school_crud/$1');
$routes->get('superadmin/school_crud/(:any)/(:any)', 'Superadmin::school_crud/$1/$2');
$routes->get('superadmin/school_crud/(:any)/(:any)/(:any)', 'Superadmin::school_crud/$1/$2/$3');
$routes->post('superadmin/school_crud', 'Superadmin::school_crud');
$routes->post('superadmin/school_crud/(:any)', 'Superadmin::school_crud/$1');
$routes->post('superadmin/school_crud/(:any)/(:any)', 'Superadmin::school_crud/$1/$2');
$routes->post('superadmin/school_crud/(:any)/(:any)/(:any)', 'Superadmin::school_crud/$1/$2/$3');
$routes->get('superadmin/online_admission_school', 'Superadmin::online_admission_school');
$routes->get('superadmin/online_admission_school/(:any)', 'Superadmin::online_admission_school/$1');
$routes->get('superadmin/online_admission_school/(:any)/(:any)', 'Superadmin::online_admission_school/$1/$2');
$routes->post('superadmin/online_admission_school', 'Superadmin::online_admission_school');
$routes->post('superadmin/online_admission_school/(:any)', 'Superadmin::online_admission_school/$1');
$routes->post('superadmin/online_admission_school/(:any)/(:any)', 'Superadmin::online_admission_school/$1/$2');
$routes->get('superadmin/(:segment)', 'Superadmin::$1');

$routes->get('accountant', 'Accountant::index');
$routes->get('accountant/(:segment)', 'Accountant::$1');

$routes->get('parents', 'Parents::index');
$routes->get('parents/(:segment)', 'Parents::$1');

$routes->get('librarian', 'Librarian::index');
$routes->get('librarian/(:segment)', 'Librarian::$1');

$routes->get('modal/(:segment)', 'Modal::$1');
$routes->get('modal/popup/(:segment)/(:segment)', 'Modal::popup/$1/$2');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3/$4');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3/$4/$5');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3/$4/$5/$6');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3/$4/$5/$6/$7');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3/$4/$5/$6/$7/$8');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3/$4/$5/$6/$7/$8/$9');
$routes->get('modal/popup/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Modal::popup/$1/$2/$3/$4/$5/$6/$7/$8/$9/$10');

$routes->get('wall', 'Wall::index');
$routes->get('wall/(:segment)', 'Wall::$1');
$routes->get('wall/class', 'Wall::class');
$routes->get('wall/class/(:segment)', 'Wall::class/$1');
$routes->get('wall/create_post/(:num)', 'Wall::create_post/$1');
$routes->get('wall/edit_post/(:num)', 'Wall::edit_post/$1');
$routes->post('wall/create_post_action/(:num)', 'Wall::create_post_action/$1');
$routes->get('class_wall', 'Wall::class');
$routes->get('class_wall/(:segment)', 'Wall::class/$1');

$routes->get('chat', 'Chat::index');
$routes->get('chat/(:segment)', 'Chat::$1');

$routes->get('articles', 'Articles::index');
$routes->get('articles/(:segment)', 'Articles::$1');

$routes->get('trends', 'Articles::index');
$routes->get('trends/(:segment)', 'Articles::show/$1');
$routes->get('trends/category/(:segment)', 'Articles::category/$1');
$routes->get('trends/tag/(:segment)', 'Articles::tag/$1');
$routes->post('trends/search', 'Articles::search');

$routes->get('en/trends', 'Articles::index');
$routes->get('en/trends/(:segment)', 'Articles::show/$1');
$routes->get('en/trends/category/(:segment)', 'Articles::category/$1');
$routes->get('en/trends/tag/(:segment)', 'Articles::tag/$1');
$routes->post('en/trends/search', 'Articles::search');

$routes->get('fr/trends', 'Articles::index');
$routes->get('fr/trends/(:segment)', 'Articles::show/$1');
$routes->get('fr/trends/category/(:segment)', 'Articles::category/$1');
$routes->get('fr/trends/tag/(:segment)', 'Articles::tag/$1');
$routes->post('fr/trends/search', 'Articles::search');

$routes->get('ar/trends', 'Articles::index');
$routes->get('ar/trends/(:segment)', 'Articles::show/$1');
$routes->get('ar/trends/category/(:segment)', 'Articles::category/$1');
$routes->get('ar/trends/tag/(:segment)', 'Articles::tag/$1');
$routes->post('ar/trends/search', 'Articles::search');

$routes->get('es/trends', 'Articles::index');
$routes->get('es/trends/(:segment)', 'Articles::show/$1');
$routes->get('es/trends/category/(:segment)', 'Articles::category/$1');
$routes->get('es/trends/tag/(:segment)', 'Articles::tag/$1');
$routes->post('es/trends/search', 'Articles::search');

$routes->get('nl/trends', 'Articles::index');
$routes->get('nl/trends/(:segment)', 'Articles::show/$1');
$routes->get('nl/trends/category/(:segment)', 'Articles::category/$1');
$routes->get('nl/trends/tag/(:segment)', 'Articles::tag/$1');
$routes->post('nl/trends/search', 'Articles::search');

$routes->get('admission', 'Admission::index');
$routes->get('admission/online_admission', 'Admission::online_admission');
$routes->post('admission/online_admission/submit/school', 'Admission::online_admission/submit/school');
$routes->get('admission/online_admission_student/(:segment)', 'Admission::online_admission_student/$1');
$routes->post('admission/post-check-duplication-ajax', 'Admission::postCheckDuplicationAjax');
$routes->get('admission/(:segment)', 'Admission::$1');

$routes->get('install', 'Install::index');
$routes->get('install/(:segment)', 'Install::$1');

$routes->get('meeting', 'Meeting::index');
$routes->get('meeting/(:segment)', 'Meeting::$1');

$routes->get('bigbluebutton', 'Bigbluebutton::index');
$routes->get('bigbluebutton/(:segment)', 'Bigbluebutton::$1');
$routes->get('bigbluebutton/join/(:any)/(:any)/(:any)', 'BigbluebuttonController::join/$1/$2/$3');
$routes->get('bigbluebutton/create/(:num)', 'Bigbluebutton::create_meeting/$1');
$routes->get('bigbluebutton/start_meeting/(:num)', 'Bigbluebutton::start_meeting/$1');
$routes->get('bigbluebutton/get_active_meetings', 'Bigbluebutton::get_active_meetings');
$routes->get('bigbluebutton/is_meeting_running/(:any)', 'Bigbluebutton::is_meeting_running/$1');
$routes->get('bigbluebutton/join_room/(:any)', 'BigbluebuttonController::join_room/$1');
$routes->get('bigbluebutton/create_room', 'Bigbluebutton::create_room');
$routes->get('bigbluebutton/webhook', 'Bigbluebutton::webhook');
$routes->get('meeting_states', 'Bigbluebutton::meeting_states');
$routes->post('meeting_states', 'Bigbluebutton::meeting_states');
$routes->get('bigbluebutton/meeting_states', 'Bigbluebutton::meeting_states');
$routes->post('bigbluebutton/meeting_states', 'Bigbluebutton::meeting_states');

$routes->get('cron', 'Cron::index');
$routes->get('cron/(:segment)', 'Cron::$1');
$routes->get('cron/fx_fetch_daily', 'Cron::fx_fetch_daily');
$routes->get('cron/fx_health', 'Cron::fx_health');
$routes->get('cron/fx_cleanup', 'Cron::fx_cleanup');
$routes->get('cron/fx_clear_cache', 'Cron::fx_clear_cache');
$routes->get('cron/fx_test_api', 'Cron::fx_test_api');

$routes->post('stripe-webhook', 'StripeWebhook::index');

$routes->get('api/admin', 'Api\Admin::index');
$routes->get('api/admin/(:segment)', 'Api\Admin::$1');
$routes->post('api/admin/(:segment)', 'Api\Admin::$1');

$routes->get('api/fxrates', 'Api\FxRates::index');
$routes->get('api/fxrates/(:segment)', 'Api\FxRates::$1');

$routes->get('api/wall', 'Api\Wall::index');
$routes->get('api/wall/(:segment)', 'Api\Wall::$1');
$routes->post('api/wall/(:segment)', 'Api\Wall::$1');

// Wall REST endpoints used by wall frontend views
$routes->get('api/communities/(:num)/wall', 'Api\Wall::community_get/$1');
$routes->post('api/communities/(:num)/wall/posts', 'Api\Wall::community_posts_post/$1');
$routes->post('api/communities/(:num)/announcements', 'Api\Wall::announcements_post/$1');
$routes->get('api/classes/(:num)/wall', 'Api\Wall::class_get/$1');
$routes->post('api/classes/(:num)/wall/posts', 'Api\Wall::class_posts_post/$1');
$routes->post('api/posts/(:num)/edit', 'Api\Wall::edit_post/$1');
$routes->post('api/posts/(:num)/report', 'Api\Wall::report_post/$1');
$routes->post('api/posts/(:num)/hide', 'Api\Wall::hide_post/$1');
$routes->post('api/posts/(:num)/unhide', 'Api\Wall::unhide_post/$1');
$routes->delete('api/posts/(:num)', 'Api\Wall::posts_delete/$1');
$routes->get('api/moderation/posts', 'Api\Wall::moderation_posts_get');
$routes->get('api/moderation/reports', 'Api\Wall::reports_get');
$routes->put('api/reports/(:num)/status', 'Api\Wall::report_status_put/$1');

$routes->get('addons/lessons', 'addons\Lessons::index');
$routes->get('addons/lessons/(:segment)', 'addons\Lessons::$1');
$routes->get('addons/lessons/(:segment)/(:segment)', 'addons\Lessons::$1/$2');
$routes->get('addons/lessons/(:segment)/(:segment)/(:segment)', 'addons\Lessons::$1/$2/$3');
$routes->get('addons/lessons/(:segment)/(:segment)/(:segment)/(:segment)', 'addons\Lessons::$1/$2/$3/$4');
$routes->post('addons/lessons/(:segment)', 'addons\Lessons::$1');
$routes->post('addons/lessons/(:segment)/(:segment)', 'addons\Lessons::$1/$2');
$routes->post('addons/lessons/(:segment)/(:segment)/(:segment)', 'addons\Lessons::$1/$2/$3');
$routes->post('addons/lessons/(:segment)/(:segment)/(:segment)/(:segment)', 'addons\Lessons::$1/$2/$3/$4');

$routes->get('addons/courses', 'addons\Courses::index');
$routes->get('addons/courses/(:segment)', 'addons\Courses::$1');
$routes->get('addons/courses/(:segment)/(:segment)', 'addons\Courses::$1/$2');
$routes->get('addons/courses/(:segment)/(:segment)/(:segment)', 'addons\Courses::$1/$2/$3');
$routes->get('addons/courses/(:segment)/(:segment)/(:segment)/(:segment)', 'addons\Courses::$1/$2/$3/$4');
$routes->post('addons/courses/(:segment)', 'addons\Courses::$1');
$routes->post('addons/courses/(:segment)/(:segment)', 'addons\Courses::$1/$2');
$routes->post('addons/courses/(:segment)/(:segment)/(:segment)', 'addons\Courses::$1/$2/$3');
$routes->post('addons/courses/(:segment)/(:segment)/(:segment)/(:segment)', 'addons\Courses::$1/$2/$3/$4');