<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepositMethod;

class DepositMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'code' => 'wire_usd',
                'name' => 'Wire Transfer (USD)',
                'type' => 'wire',
                'description' => 'Send USD via bank wire transfer.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'wire_gbp',
                'name' => 'Wire Transfer (GBP)',
                'type' => 'wire',
                'description' => 'Send GBP via bank wire transfer.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'swift',
                'name' => 'SWIFT',
                'type' => 'swift',
                'description' => 'International SWIFT transfer.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'sepa',
                'name' => 'SEPA (EUR)',
                'type' => 'sepa',
                'description' => 'Euro SEPA transfer within EEA.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'ach',
                'name' => 'ACH (US)',
                'type' => 'ach',
                'description' => 'US domestic ACH transfer.',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'code' => 'zelle',
                'name' => 'Zelle (US)',
                'type' => 'zelle',
                'description' => 'Zelle instant payment (US only).',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'code' => 'faster_payments',
                'name' => 'Faster Payments (UK)',
                'type' => 'faster_payments',
                'description' => 'UK domestic faster payments.',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'code' => 'bacs',
                'name' => 'BACS (UK)',
                'type' => 'bacs',
                'description' => 'UK BACS direct credit / direct debit.',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'code' => 'chaps',
                'name' => 'CHAPS (UK)',
                'type' => 'chaps',
                'description' => 'UK CHAPS same-day transfer.',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'code' => 'crypto_usdt',
                'name' => 'Cryptocurrency (USDT)',
                'type' => 'crypto',
                'description' => 'Deposit via USDT on TRC20/ERC20.',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'code' => 'crypto_btc',
                'name' => 'Cryptocurrency (BTC)',
                'type' => 'crypto',
                'description' => 'Deposit via Bitcoin.',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'code' => 'crypto_eth',
                'name' => 'Cryptocurrency (ETH)',
                'type' => 'crypto',
                'description' => 'Deposit via Ethereum.',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'code' => 'other',
                'name' => 'Other',
                'type' => 'other',
                'description' => 'Any other deposit method.',
                'is_active' => true,
                'sort_order' => 99,
            ],
        ];

        foreach ($methods as $method) {
            DepositMethod::updateOrCreate(['code' => $method['code']], $method);
        }
    }
}
