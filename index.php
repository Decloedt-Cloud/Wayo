<?php

/**
 * CodeIgniter 4 - Front Controller
 *
 * This file is the entry point for all web requests.
 * It boots the CI4 framework and hands off to the router.
 */

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */
$minPhpVersion = '8.2';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Path to the Composer autoloader
define('COMPOSER_PATH', FCPATH . 'vendor/autoload.php');

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * CI3 COMPATIBILITY: DEFINE BASEPATH
 *---------------------------------------------------------------
 * Many files migrated from CI3 contain:
 *   defined('BASEPATH') OR exit('No direct script access allowed');
 * CI4 does not define BASEPATH, so we define it here early
 * to prevent those guards from killing the request.
 */
if (! defined('BASEPATH')) {
    define('BASEPATH', FCPATH . 'system' . DIRECTORY_SEPARATOR);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 */

// Load the Paths config file
require FCPATH . 'app/Config/Paths.php';
$paths = new Paths();

// Load the framework bootstrap
require $paths->systemDirectory . '/Boot.php';

// Launch!
exit(Boot::bootWeb($paths));
