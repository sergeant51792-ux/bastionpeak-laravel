<?php

/**
 * cPanel / shared-hosting front controller.
 *
 * This file is meant to be copied into your web root (usually `public_html`).
 * It bootstrap Laravel from the application folder that lives OUTSIDE the web
 * root and tells Laravel that the current directory is the public path, so
 * `public_path()`, `@vite`, `asset()` and `storage:link` all resolve to
 * `public_html` correctly.
 *
 * Adjust $APP_DIR_SEARCH only if your app folder is not one of the paths below.
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$APP_DIR_SEARCH = [
    __DIR__ . '/../bastionpeak-laravel',
    __DIR__ . '/../bastionpeak2/bastionpeak-laravel',
    __DIR__ . '/..',
    __DIR__,
];

$appDir = null;
foreach ($APP_DIR_SEARCH as $candidate) {
    if (is_file($candidate . '/vendor/autoload.php') && is_file($candidate . '/bootstrap/app.php')) {
        $appDir = realpath($candidate);
        break;
    }
}

if ($appDir === null) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "Laravel application directory not found.\n";
    echo "Edit cpanel/index.php and set \$APP_DIR_SEARCH to the folder that contains composer.json.\n";
    exit;
}

if (file_exists($maintenance = $appDir . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appDir . '/vendor/autoload.php';

$app = require $appDir . '/bootstrap/app.php';
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
