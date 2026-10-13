<?php
// Temporary artisan-style command to pull and migrate
$appDir = '/home/bastionpeaks/bastionpeak-laravel';
@unlink($appDir . '/bootstrap/cache/config.php');
@unlink($appDir . '/bootstrap/cache/routes-v2.php');
require $appDir . '/vendor/autoload.php';
$app = require $appDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Git pull
$output = [];
$code = 0;
exec('cd ' . escapeshellarg($appDir) . ' && git reset --hard HEAD && git pull 2>&1', $output, $code);
echo "git pull (exit {$code})\n" . implode("\n", $output);

// Clear and re-cache
foreach (['config:clear', 'route:clear', 'view:clear'] as $cmd) {
    $kernel->call($cmd);
}

// Migrate
$code = $kernel->call('migrate', ['--force' => true]);
echo "\nmigrate (exit {$code})\n" . trim($kernel->output());

// Re-cache
$r = $kernel->call('config:cache');
$r = $kernel->call('route:cache');
$r = $kernel->call('view:cache');
echo "\nDone!\n";
