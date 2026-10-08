<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Merchant;
use Illuminate\Database\Seeder;

/**
 * Bastion Peak Internal Banking System — Merchant Seeder
 *
 * Seeds 8 approved merchants for the Bastion Peak payment network.
 */
class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $merchants = [
            [
                'name'           => 'Greenfield Foods',
                'category'       => 'Food',
                'receiving_cap'    => 10000.00,
                'status'      => 'active',
                'account_numbers' => json_encode(['account_1' => '10 1234 5678 9001']),
                'instructions'   => 'Deposits accepted during business hours. Minimum deposit: $500.',
            ],
            [
                'name'           => 'Skyline Travel',
                'category'       => 'Transport',
                'receiving_cap'    => 25000.00,
                'status'      => 'active',
                'account_numbers' => json_encode(['account_1' => '10 2345 6789 0012']),
                'instructions'   => 'Travel deposits must include booking reference in memo.',
            ],
            [
                'name'           => 'OfficeMax Supplies',
                'category'       => 'Office supplies',
                'receiving_cap'    => 5000.00,
                'status'      => 'active',
                'account_numbers' => json_encode(['account_1' => '10 3456 7890 1123']),
                'instructions'   => 'Office supply orders above $1,000 require PO number.',
            ],
            [
                'name'           => 'QuickMart Stores',
                'category'       => 'Food',
                'receiving_cap'    => 15000.00,
                'status'      => 'active',
                'account_numbers' => json_encode(['account_1' => '10 4567 8901 2234']),
                'instructions'   => 'Retail store chain — deposits for bulk orders only.',
            ],
            [
                'name'           => 'CityLink Logistics',
                'category'       => 'Transport',
                'receiving_cap'    => 20000.00,
                'status'      => 'active',
                'account_numbers' => json_encode(['account_1' => '10 5678 9012 3345']),
                'instructions'   => 'Logistics payments must reference shipment ID.',
            ],
            [
                'name'           => 'TechPoint Solutions',
                'category'       => 'Office supplies',
                'receiving_cap'    => 8000.00,
                'status'      => 'active',
                'account_numbers' => json_encode(['account_1' => '10 6789 0123 4456']),
                'instructions'   => 'Tech equipment purchases require approval for items over $500.',
            ],
            [
                'name'           => 'FreshCart Market',
                'category'       => 'Food',
                'receiving_cap'    => 12000.00,
                'status'      => 'deactivated',
                'account_numbers' => json_encode(['account_1' => '10 7890 1234 5567']),
                'instructions'   => 'Currently deactivated. Contact admin for reactivation.',
            ],
            [
                'name'           => 'Metro Cab Services',
                'category'       => 'Transport',
                'receiving_cap'    => 18000.00,
                'status'      => 'active',
                'account_numbers' => json_encode(['account_1' => '10 8901 2345 6678']),
                'instructions'   => 'Transport services — daily ride caps apply per employee account.',
            ],
        ];

        foreach ($merchants as $data) {
            Merchant::updateOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
