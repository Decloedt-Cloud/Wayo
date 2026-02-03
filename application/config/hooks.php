<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['display_override'] = array(
    'class'    => 'App_rewriter',
    'function' => 'handle_outgoing_output',
    'filename' => 'App_rewriter.php',
    'filepath' => 'hooks'
);

// Language detection hook - runs after controller constructor
$hook['post_controller_constructor'][] = array(
    'class'    => 'Language_detector',
    'function' => 'detect',
    'filename' => 'Language_detector.php',
    'filepath' => 'hooks'
);

$hook['post_controller_constructor'][] = array(
    'class'    => 'App_Access',
    'function' => 'block_direct_access',
    'filename' => 'App_Access.php',
    'filepath' => 'hooks'
);
