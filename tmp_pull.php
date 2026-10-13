<?php
// Temporary script to check git state on server
@unlink('/home/bastionpeaks/bastionpeak-laravel/bootstrap/cache/config.php');
require '/home/bastionpeaks/bastionpeak-laravel/vendor/autoload.php';
$app = require '/home/bastionpeaks/bastionpeak-laravel/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Run git pull via artisan's ability to run system commands
echo "Running: git -C /home/bastionpeaks/bastionpeak-laravel pull\n";
$output = [];
$code = 0;
exec('cd /home/bastionpeaks/bastionpeak-laravel && git reset --hard HEAD && git pull 2>&1', $output, $code);
echo "Exit code: $code\n";
echo implode("\n", $output);
