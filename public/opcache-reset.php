<?php

$key = $_GET['key'] ?? '';
if (!hash_equals(getenv('DEPLOY_KEY') ?: 'bastionpeak-deploy-secret-737940260', $key)) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache reset successfully';
} else {
    echo 'OPcache not available';
}

if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo ' | APCu cache cleared';
}

echo "\nDone.";
