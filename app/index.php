<?php

declare(strict_types=1);

/**
 * Bridge for "/app" path when the web server serves the physical "app" directory.
 * Keep masked URLs and route users to the masked dashboard entrypoint.
 */
$queryString = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== ''
    ? ('?' . $_SERVER['QUERY_STRING'])
    : '';

header('Location: /app/dashboard' . $queryString, true, 302);
exit;

