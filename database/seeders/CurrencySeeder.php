<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

/**
 * Bastion Peak Internal Banking System — Currency Seeder
 *
 * Seeds the supported currencies for the Bastion Peak ledger.
 */
class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $currencies = [
            [
                'code'        => 'USD',
                'name'        => 'US Dollar',
                'symbol'      => '$',
                'color'       => 'ledger-green',
                'decimals'    => 2,
                'is_base'     => true,
                'exchange_rate' => 1.0,
                'rate_updated_at' => now(),
                'is_enabled'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'BTC',
                'name'        => 'Bitcoin',
                'symbol'      => '₿',
                'color'       => 'amber',
                'decimals'    => 8,
                'is_base'     => false,
                'exchange_rate' => 97500.0,
                'rate_updated_at' => now(),
                'is_enabled'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'ETH',
                'name'        => 'Ethereum',
                'symbol'      => 'Ξ',
                'color'       => 'blue',
                'decimals'    => 8,
                'is_base'     => false,
                'exchange_rate' => 3500.0,
                'rate_updated_at' => now(),
                'is_enabled'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'code'        => 'PTS',
                'name'        => 'Internal Points',
                'symbol'      => 'pts',
                'color'       => 'iris',
                'decimals'    => 0,
                'is_base'     => false,
                'exchange_rate' => 0.01,
                'rate_updated_at' => now(),
                'is_enabled'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        foreach ($currencies as $data) {
            Currency::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
