<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

echo "=== Testing Admin Route Protection ===\n\n";

// Test 1: Customer should get 403 on admin routes
$customer = App\Models\User::role('Customer')->first();
if (!$customer) {
    die("ERROR: No customer user found\n");
}

Auth::login($customer);
echo "Test 1: Customer user accessing admin routes\n";
echo "Logged in as: " . $customer->name . " (Customer role)\n";

$adminRoutes = [
    '/admin',
    '/admin/users',
    '/admin/users/create',
];

foreach ($adminRoutes as $route) {
    $request = Request::create($route, 'GET');
    $request->setUser($customer);
    
    try {
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        if ($status === 403) {
            echo "  ✓ GET $route - 403 Forbidden (correct)\n";
        } else {
            echo "  ✗ GET $route - $status (expected 403)\n";
        }
    } catch (Exception $e) {
        echo "  ✗ GET $route - ERROR: " . $e->getMessage() . "\n";
    }
}

Auth::logout();

// Test 2: Super Admin should get 200 on admin routes
$admin = App\Models\User::role('Super Admin')->first();
if (!$admin) {
    die("ERROR: No super admin user found\n");
}

Auth::login($admin);
echo "\nTest 2: Super Admin user accessing admin routes\n";
echo "Logged in as: " . $admin->name . " (Super Admin role)\n";

foreach ($adminRoutes as $route) {
    $request = Request::create($route, 'GET');
    $request->setUser($admin);
    
    try {
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        if ($status === 200) {
            echo "  ✓ GET $route - 200 OK (correct)\n";
        } else {
            echo "  ✗ GET $route - $status (expected 200)\n";
        }
    } catch (Exception $e) {
        echo "  ✗ GET $route - ERROR: " . $e->getMessage() . "\n";
    }
}

Auth::logout();

// Test 3: Unauthenticated user should be redirected
echo "\nTest 3: Unauthenticated user accessing admin routes\n";
echo "Not logged in\n";

foreach ($adminRoutes as $route) {
    $request = Request::create($route, 'GET');
    
    try {
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        if ($status === 302) {
            echo "  ✓ GET $route - 302 Redirect (correct - to login)\n";
        } else {
            echo "  ✗ GET $route - $status (expected 302)\n";
        }
    } catch (Exception $e) {
        echo "  ✗ GET $route - ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== All tests completed ===\n";
