<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Bastion Peak Internal Banking System — Master Database Seeder
 *
 * Coordinates all seed data: roles, currencies, admin user, customers, and merchants.
 * Run with: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CurrencySeeder::class,
            DepositMethodSeeder::class,
            FinancialServiceSeeder::class,
            AdminUserSeeder::class,
            CustomerSeeder::class,
            MerchantSeeder::class,
        ]);
    }
}
