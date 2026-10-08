<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Middleware\RequireRole;

echo "=== Testing RequireRole Middleware Directly ===\n\n";

// Test 1: Customer should get 403
$customer = App\Models\User::role('Customer')->first();
if (!$customer) {
    die("ERROR: No customer user found\n");
}

Auth::login($customer);
echo "Test 1: Customer user accessing admin route (RequireRole: Super Admin)\n";
echo "Logged in as: " . $customer->name . " (Customer role)\n";

$request = Request::create('/admin', 'GET');
$request->setUser($customer);

$middleware = new RequireRole();
try {
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    }, 'Super Admin');
    $status = $response->getStatusCode();
    if ($status === 403) {
        echo "  ✓ 403 Forbidden (correct)\n";
    } else {
        echo "  ✗ $status (expected 403)\n";
    }
} catch (Exception $e) {
    if ($e->getMessage() === 'You do not have access to this page.') {
        echo "  ✓ 403 Forbidden (correct - via abort)\n";
    } else {
        echo "  ✗ ERROR: " . $e->getMessage() . "\n";
    }
}

Auth::logout();

// Test 2: Super Admin should get 200
$admin = App\Models\User::role('Super Admin')->first();
if (!$admin) {
    die("ERROR: No super admin user found\n");
}

Auth::login($admin);
echo "\nTest 2: Super Admin user accessing admin route (RequireRole: Super Admin)\n";
echo "Logged in as: " . $admin->name . " (Super Admin role)\n";

$request = Request::create('/admin', 'GET');
$request->setUser($admin);

try {
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    }, 'Super Admin');
    $status = $response->getStatusCode();
    if ($status === 200) {
        echo "  ✓ 200 OK (correct)\n";
    } else {
        echo "  ✗ $status (expected 200)\n";
    }
} catch (Exception $e) {
    echo "  ✗ ERROR: " . $e->getMessage() . "\n";
}

Auth::logout();

// Test 3: Unauthenticated user
echo "\nTest 3: Unauthenticated user accessing admin route\n";
echo "Not logged in\n";

$request = Request::create('/admin', 'GET');

try {
    $response = $middleware->handle($request, function () {
        return response('OK', 200);
    }, 'Super Admin');
    $status = $response->getStatusCode();
    if ($status === 403) {
        echo "  ✓ 403 Forbidden (correct)\n";
    } else {
        echo "  ✗ $status (expected 403)\n";
    }
} catch (Exception $e) {
    if ($e->getMessage() === 'You do not have access to this page.') {
        echo "  ✓ 403 Forbidden (correct - via abort)\n";
    } else {
        echo "  ✗ ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n=== All middleware tests completed ===\n";
