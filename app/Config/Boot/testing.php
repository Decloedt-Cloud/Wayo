<?php

/*
|--------------------------------------------------------------------------
| ERROR DISPLAY
|--------------------------------------------------------------------------
| In testing, we want to show errors for debugging.
*/
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
|--------------------------------------------------------------------------
| DEBUG MODE
|--------------------------------------------------------------------------
| Set to true for testing environment.
*/
defined('CI_DEBUG') || define('CI_DEBUG', true);
