<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Test admin routes as Customer user
$customer = App\Models\User::role('Customer')->first();
if (!$customer) {
    die("No customer user found\n");
}

Auth::login($customer);
echo "Logged in as Customer: " . $customer->name . PHP_EOL;

$adminRoutes = [
    '/admin',
    '/admin/users',
    '/admin/users/create',
    '/admin/approvals',
    '/admin/cards',
    '/admin/transactions',
    '/admin/settings',
];

foreach ($adminRoutes as $route) {
    $request = Request::create($route, 'GET');
    $request->setUser($customer);
    
    try {
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        echo "GET $route - Status: $status";
        if ($status === 403) {
            echo " (FORBIDDEN - correct)";
        } elseif ($status === 302) {
            echo " (REDIRECT - likely to login)";
        } else {
            echo " (UNEXPECTED)";
        }
        echo PHP_EOL;
    } catch (Exception $e) {
        echo "GET $route - ERROR: " . $e->getMessage() . PHP_EOL;
    }
}

Auth::logout();

// Test admin routes as Super Admin user
$admin = App\Models\User::role('Super Admin')->first();
if (!$admin) {
    die("No super admin user found\n");
}

Auth::login($admin);
echo PHP_EOL . "Logged in as Super Admin: " . $admin->name . PHP_EOL;

foreach ($adminRoutes as $route) {
    $request = Request::create($route, 'GET');
    $request->setUser($admin);
    
    try {
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        echo "GET $route - Status: $status";
        if ($status === 200) {
            echo " (OK - correct)";
        } elseif ($status === 302) {
            echo " (REDIRECT - check)";
        } else {
            echo " (UNEXPECTED)";
        }
        echo PHP_EOL;
    } catch (Exception $e) {
        echo "GET $route - ERROR: " . $e->getMessage() . PHP_EOL;
    }
}

Auth::logout();
echo PHP_EOL . "Route testing complete." . PHP_EOL;
