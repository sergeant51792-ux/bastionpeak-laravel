<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$superAdmins = App\Models\User::role('Super Admin')->get();
echo "Super Admin users: " . $superAdmins->count() . PHP_EOL;
foreach ($superAdmins as $admin) {
    echo " - " . $admin->name . " (" . $admin->email . ")" . PHP_EOL;
}

$customers = App\Models\User::role('Customer')->get();
echo "Customer users: " . $customers->count() . PHP_EOL;
foreach ($customers as $customer) {
    echo " - " . $customer->name . " (" . $customer->email . ")" . PHP_EOL;
}
