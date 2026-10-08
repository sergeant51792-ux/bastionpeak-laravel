<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Bastion Peak Internal Banking System — Admin User Seeder
 *
 * Creates the primary Super Admin account for Bastion Peak.
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@bastionpeak.internal'],
            [
                'name'              => 'Bastion Peak Administrator',
                'email'             => 'admin@bastionpeak.internal',
                'password'          => Hash::make('AdminPass123!'),
                'status'            => 'active',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        if ($superAdminRole && !$admin->hasRole($superAdminRole)) {
            $admin->assignRole($superAdminRole);
        }
    }
}
