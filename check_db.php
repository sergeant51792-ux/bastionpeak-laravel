<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Users: " . App\Models\User::count() . PHP_EOL;
echo "Roles: " . Spatie\Permission\Models\Role::count() . PHP_EOL;

$roles = Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    echo "Role: " . $role->name . PHP_EOL;
}

echo "Super Admin role exists: " . (Spatie\Permission\Models\Role::where('name','Super Admin')->exists() ? 'yes' : 'no') . PHP_EOL;
echo "Customer role exists: " . (Spatie\Permission\Models\Role::where('name','Customer')->exists() ? 'yes' : 'no') . PHP_EOL;
