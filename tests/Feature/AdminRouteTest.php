<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminRouteTest extends TestCase
{
    public function test_customer_cannot_access_admin_routes()
    {
        $customer = User::role('Customer')->first();
        $this->assertNotNull($customer, 'No customer user found for testing');
        
        Auth::login($customer);
        
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
            $response = $this->get($route);
            $response->assertStatus(403);
        }
        
        Auth::logout();
    }
    
    public function test_super_admin_can_access_admin_routes()
    {
        $admin = User::role('Super Admin')->first();
        $this->assertNotNull($admin, 'No super admin user found for testing');
        
        Auth::login($admin);
        
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
            $response = $this->get($route);
            $response->assertStatus(200);
        }
        
        Auth::logout();
    }
    
    public function test_unauthenticated_user_cannot_access_admin_routes()
    {
        Auth::logout();
        
        $adminRoutes = [
            '/admin',
            '/admin/users',
            '/admin/users/create',
        ];
        
        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(302); // Redirect to login
        }
    }
}
