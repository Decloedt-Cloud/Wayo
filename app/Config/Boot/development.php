<?php

/*
|--------------------------------------------------------------------------
| ERROR DISPLAY
|--------------------------------------------------------------------------
| In development, we want to show as many errors as possible to help
| make sure they don't make it to production. In production, we want to
| turn them off so users don't see sensitive information.
*/
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
|--------------------------------------------------------------------------
| DEBUG MODE
|--------------------------------------------------------------------------
| Set to true to enable debugging. When enabled, the profiler will be shown
| at the bottom of every page. This is useful for development.
*/
defined('CI_DEBUG') || define('CI_DEBUG', true);
