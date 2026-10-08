<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Bastion Peak Internal Banking System — Role Seeder
 *
 * Creates system roles and assigns permissions.
 * Roles: Super Admin, Customer, Auditor
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Ensure permissions are cached/registered
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $customer  = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        $auditor   = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);

        // Define permissions
        $permissions = [
            'view accounts',
            'view transactions',
            'view audit logs',
            'view reports',
            'view own account',
            'view own transactions',
            'submit deposit',
            'submit payment',
            'view own notifications',
            'view own messages',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Collect all permissions and assign to Super Admin
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $superAdmin->givePermissionTo($permission);
        }

        // Auditor: view-only permissions
        $auditor->syncPermissions([
            'view accounts',
            'view transactions',
            'view audit logs',
            'view reports',
        ]);

        // Customer: limited permissions
        $customer->syncPermissions([
            'view own account',
            'view own transactions',
            'submit deposit',
            'submit payment',
            'view own notifications',
            'view own messages',
        ]);
    }
}
