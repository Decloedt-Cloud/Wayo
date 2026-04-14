<?php

/*
|--------------------------------------------------------------------------
| ERROR DISPLAY
|--------------------------------------------------------------------------
| In production, we want to turn off error display to prevent sensitive
| information from being shown to users.
*/
error_reporting(0);
ini_set('display_errors', '0');

/*
|--------------------------------------------------------------------------
| DEBUG MODE
|--------------------------------------------------------------------------
| Set to false to disable debugging in production.
*/
defined('CI_DEBUG') || define('CI_DEBUG', false);
