<?php

/**
 * Constants Configuration
 * 
 * CI4 Migration Template - Based on CI3 constants.php
 * Copy to app/Config/Constants.php when migrating to CI4
 * 
 * In CI4, constants are defined in app/Config/Constants.php
 */

// --------------------------------------------------------------------------
// CI3 Compatibility Constants (for backward compatibility)
// --------------------------------------------------------------------------

if (!defined('SHOW_DEBUG_BACKTRACE')) {
    define('SHOW_DEBUG_BACKTRACE', true);
}

if (!defined('FILE_READ_MODE')) {
    define('FILE_READ_MODE', 0644);
}

if (!defined('FILE_WRITE_MODE')) {
    define('FILE_WRITE_MODE', 0666);
}

if (!defined('DIR_READ_MODE')) {
    define('DIR_READ_MODE', 0755);
}

if (!defined('DIR_WRITE_MODE')) {
    define('DIR_WRITE_MODE', 0755);
}

if (!defined('FILE_CREATE_MODE')) {
    define('FILE_CREATE_MODE', 0666);
}

if (!defined('FOPEN_READ')) {
    define('FOPEN_READ', 'rb');
}

if (!defined('FOPEN_READ_WRITE')) {
    define('FOPEN_READ_WRITE', 'r+b');
}

if (!defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')) {
    define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb');
}

if (!defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')) {
    define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b');
}

if (!defined('FOPEN_WRITE_CREATE')) {
    define('FOPEN_WRITE_CREATE', 'ab');
}

if (!defined('FOPEN_READ_WRITE_CREATE')) {
    define('FOPEN_READ_WRITE_CREATE', 'a+b');
}

if (!defined('FOPEN_WRITE_CREATE_STRICT')) {
    define('FOPEN_WRITE_CREATE_STRICT', 'xb');
}

if (!defined('FOPEN_READ_WRITE_CREATE_STRICT')) {
    define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');
}

// --------------------------------------------------------------------------
// Application-Specific Constants (from CI3 config)
// --------------------------------------------------------------------------

// Timezone
if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'Africa/Casablanca');
}

// Default session timeout (in seconds)
if (!defined('SESSION_TIMEOUT')) {
    define('SESSION_TIMEOUT', 7200);
}

// School management system constants
if (!defined('MAX_IMAGE_SIZE')) {
    define('MAX_IMAGE_SIZE', 2048); // KB
}

if (!defined('ALLOWED_IMAGE_TYPES')) {
    define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

// Pagination
if (!defined('PER_PAGE')) {
    define('PER_PAGE', 25);
}

// --------------------------------------------------------------------------
// CI4-Specific Constants
// --------------------------------------------------------------------------

// App namespace
if (!defined('APP_NAMESPACE')) {
    define('APP_NAMESPACE', 'App');
}

// Root namespace
if (!defined('ROOT_NAMESPACE')) {
    define('ROOT_NAMESPACE', 'App');
}

// View path
if (!defined('VIEWPATH')) {
    define('VIEWPATH', APPPATH . 'Views/');
}
