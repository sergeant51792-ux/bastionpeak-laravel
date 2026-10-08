<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Bastion Peak'),

    'large_transaction_threshold' => (int) env('BASTION_LARGE_TRANSACTION_THRESHOLD', 10000),
    'failed_login_lockout' => (int) env('BASTION_FAILED_LOGIN_LOCKOUT', 5),
    'session_timeout' => (int) env('BASTION_SESSION_TIMEOUT', 120),
    'deposit_requires_approval' => filter_var(env('BASTION_DEPOSIT_REQUIRES_APPROVAL', true), FILTER_VALIDATE_BOOLEAN),
    'payment_requires_approval' => filter_var(env('BASTION_PAYMENT_REQUIRES_APPROVAL', true), FILTER_VALIDATE_BOOLEAN),
    'default_currency' => env('BASTION_DEFAULT_CURRENCY', 'USD'),
    'base_currency' => env('BASTION_BASE_CURRENCY', 'USD'),
    'max_upload_size' => (int) env('BASTION_MAX_UPLOAD_SIZE', 5120),
];
