<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\Card;

use App\Models\Currency;
use App\Models\Message;

use App\Models\Notification;
use App\Models\Transaction;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Bastion Peak Internal Banking System — Customer Seeder
 *
 * Seeds 8 realistic customers with accounts, transactions, notifications,
 * message threads, cards, and audit log entries.
 */
class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $customerRole = Role::where('name', 'Customer')->first();
        $admin = User::whereEmail('admin@bastionpeak.internal')->first();

        $customers = [
            [
                'name'      => 'Amara Okafor',
                'email'     => 'amara@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass1!'),
                'status'    => 'active',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 64600.00, 'label' => 'Main Wallet'],
                    ['type' => 'crypto', 'currency_code' => 'BTC', 'balance' => 0.40873600, 'label' => 'Bitcoin Holdings'],
                    ['type' => 'travel', 'currency_code' => 'USD', 'balance' => 4200.00, 'label' => 'Travel Only'],
                ],
            ],
            [
                'name'      => 'Chidi Eze',
                'email'     => 'chidi@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass2!'),
                'status'    => 'frozen',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 12300.00, 'label' => 'Main Wallet'],
                ],
            ],
            [
                'name'      => 'Tola Bello',
                'email'     => 'tola@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass3!'),
                'status'    => 'active',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 8400.00, 'label' => 'Main Wallet'],
                    ['type' => 'points', 'currency_code' => 'PTS', 'balance' => 12400, 'label' => 'Points Balance'],
                ],
            ],
            [
                'name'      => 'Ada Nwosu',
                'email'     => 'ada@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass4!'),
                'status'    => 'active',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 45200.00, 'label' => 'Main Wallet'],
                ],
            ],
            [
                'name'      => 'Kemi Adeyemi',
                'email'     => 'kemi@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass5!'),
                'status'    => 'active',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 23100.00, 'label' => 'Main Wallet'],
                    ['type' => 'travel', 'currency_code' => 'USD', 'balance' => 6800.00, 'label' => 'Travel Only'],
                ],
            ],
            [
                'name'      => 'Obinna Eze',
                'email'     => 'obinna@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass6!'),
                'status'    => 'active',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 3200.00, 'label' => 'Main Wallet'],
                ],
            ],
            [
                'name'      => 'Ngozi Okafor',
                'email'     => 'ngozi@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass7!'),
                'status'    => 'active',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 18700.00, 'label' => 'Main Wallet'],
                    ['type' => 'points', 'currency_code' => 'PTS', 'balance' => 8200, 'label' => 'Points Balance'],
                ],
            ],
            [
                'name'      => 'Emeka Ofor',
                'email'     => 'emeka@bastionpeak.internal',
                'password'  => Hash::make('CustomerPass8!'),
                'status'    => 'locked',
                'accounts'  => [
                    ['type' => 'main_wallet', 'currency_code' => 'USD', 'balance' => 7500.00, 'label' => 'Main Wallet'],
                ],
            ],
        ];

        foreach ($customers as $idx => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'email'             => $data['email'],
                    'password'          => $data['password'],
                    'status'            => $data['status'],
                    'email_verified_at' => now(),
                    'created_at'        => now()->subDays(rand(30, 90)),
                    'updated_at'        => now(),
                ]
            );

            if ($customerRole && !$user->hasRole($customerRole)) {
                $user->assignRole($customerRole);
            }

            $accountIds = [];
            foreach ($data['accounts'] as $accData) {
                $currency = Currency::where('code', $accData['currency_code'])->first();
                $accountNumber = $this->generateAccountNumber();
                $account = Account::create([
                    'user_id'       => $user->id,
                    'currency_id'   => $currency->id,
                    'type'          => $accData['type'],
                    'name'          => $accData['label'],
                    'label'         => $accData['label'],
                    'account_number' => $accountNumber,
                    'balance'       => $accData['balance'],
                    'status'        => 'active',
                    'created_at'    => now()->subDays(rand(30, 90)),
                    'updated_at'    => now(),
                ]);
                $accountIds[] = $account->id;
                $this->seedAccountDepositMethods($account, $currency);
            }

            $mainAccountId = $accountIds[0] ?? null;
            $this->seedTransactions($user->id, $accountIds, $idx);
            $this->seedNotifications($user->id, $data['status'], $idx);
            $this->seedMessageThreads($user->id, $admin?->id, $idx);
            $this->seedCard($user->id, $mainAccountId, $idx);
            $this->seedAuditLogs($admin?->id, $user->id, $idx);
        }
    }

    /**
     * Generate a realistic 10 XXXX XXXX XXXX account number.
     *
     * @return string
     */
    private function generateAccountNumber(): string
    {
        $parts = [
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
            str_pad((string)random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
        ];

        return '10 ' . implode(' ', $parts);
    }

    /**
     * Seed transactions for a customer's accounts.
     *
     * @param int $userId
     * @param array $accountIds
     * @param int $seedIndex
     * @return void
     */
    private function seedTransactions(int $userId, array $accountIds, int $seedIndex): void
    {
        $types = ['deposit_credit', 'credit', 'refund', 'payment_debit', 'debit'];
        $memos = [
            'Salary deposit',
            'Freelance payment',
            'Vendor refund',
            'Office supplies purchase',
            'Travel reimbursement',
            'Utility payment',
            'Client payment',
            'Insurance premium',
            'Subscription renewal',
            'Equipment purchase',
            'Conference fee',
            'Training course',
            'Client refund',
            'Bonus credit',
            'Adjustment',
        ];

        $count = rand(30, 50);
        $now = Carbon::now();
        $minIdempotency = $userId * 10000;

        for ($i = 0; $i < $count; $i++) {
            $type = $types[array_rand($types)];
            $accountId = $accountIds[array_rand($accountIds)];
            $account = Account::find($accountId);
            $amount = $this->realisticAmount($type, $account->currency->code);
            $daysAgo = rand(1, 90);
            $createdAt = $now->copy()->subDays($daysAgo)->addHours(rand(0, 23))->addMinutes(rand(0, 59));

            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $accountId,
                'type'              => $type,
                'amount'            => $amount,
                'net_amount'        => $amount,
                'currency_id'       => $account->currency_id,
                'status'            => 'pending_review',
                'memo'              => $memos[array_rand($memos)],
                'reference'         => 'TXN-' . strtoupper(uniqid()),
                'idempotency_key'   => 'idemp-' . uniqid() . '-' . md5((string)($i . $daysAgo . microtime())),
                'created_at'        => $createdAt,
                'updated_at'        => $createdAt,
            ]);

            $counterpartAccount = Account::where('user_id', '!=', $userId)
                ->inRandomOrder()
                ->first() ?? $account;

            if ($counterpartAccount->id !== $accountId) {
            }
        }
    }

    /**
     * Attach deposit methods to an account with realistic addresses.
     *
     * @param \App\Models\Account $account
     * @param \App\Models\Currency $currency
     * @return void
     */
    private function seedAccountDepositMethods(Account $account, Currency $currency): void
    {
        $depositMethodModel = new \App\Models\DepositMethod();
        $availableMethods = \App\Models\DepositMethod::where('is_active', true)->get();

        $currencySpecific = [];
        if ($currency->code === 'USD') {
            $currencySpecific = ['wire_usd', 'ach', 'zelle'];
        } elseif ($currency->code === 'BTC') {
            $currencySpecific = ['crypto_btc'];
        } elseif ($currency->code === 'ETH') {
            $currencySpecific = ['crypto_eth'];
        } elseif ($currency->code === 'PTS') {
            $currencySpecific = ['other'];
        }

        foreach ($currencySpecific as $code) {
            $method = $availableMethods->where('code', $code)->first();
            if (!$method) {
                continue;
            }

            $address = match ($code) {
                'wire_usd' => '4445 ' . str_repeat('X', 12) . '0001 0001 0001',
                'ach' => '021000021 ' . rand(100000000, 999999999) . '00',
                'zelle' => 'user' . rand(100, 999) . '@bank.example.com',
                'crypto_btc' => 'bc1q' . strtolower(\Illuminate\Support\Str::random(34)),
                'crypto_eth' => '0x' . strtolower(\Illuminate\Support\Str::random(40)),
                'other' => 'Deposit reference: DEPOSIT-' . strtoupper(\Illuminate\Support\Str::random(8)),
                default => null,
            };

            $instructions = match ($code) {
                'wire_usd' => "Send USD via bank wire transfer.\nAccount: 4445 **** **** 0001\nRouting: 021000021\nReference: Use your account number",
                'ach' => "Send via ACH transfer.\nRouting: 021000021\nAccount: ****{$address}",
                'zelle' => "Send via Zelle to: {$address}\nReference: Your account number",
                'crypto_btc' => "Send BTC to the address below.\nMemo: Include your account ID in the transaction",
                'crypto_eth' => "Send ETH to the address below.\nMemo: Include your account ID in the transaction",
                'other' => "Deposit instructions will be provided by support.",
                default => null,
            };

            $account->depositMethods()->syncWithoutDetaching([
                $method->id => [
                    'address' => $address,
                    'instructions' => $instructions,
                    'is_active' => true,
                    'metadata' => json_encode(['currency' => $currency->code]),
                ],
            ]);
        }
    }

    /**
     * Generate a realistic transaction amount.
     *
     * @param string $type
     * @param string $currencyCode
     * @return float
     */
    private function realisticAmount(string $type, string $currencyCode): float
    {
        if ($currencyCode === 'BTC') {
            return round(rand(1, 5000) / 100000000, 8);
        }

        if ($currencyCode === 'ETH') {
            return round(rand(1, 5000) / 100000000, 8);
        }

        if ($currencyCode === 'PTS') {
            return (float)rand(1, 50000);
        }

        // USD — avoid round numbers
        if (in_array($type, ['deposit_credit', 'credit', 'refund'])) {
            return round(rand(500, 15000) / 100, 2);
        }

        return round(rand(200, 8000) / 100, 2);
    }

    /**
     * Seed notifications for a customer.
     *
     * @param int $userId
     * @param string $status
     * @param int $seedIndex
     * @return void
     */
    private function seedNotifications(int $userId, string $status, int $seedIndex): void
    {
        $types = [
            'deposit_approved',
            'deposit_rejected',
            'withdrawal_approved',
            'account_locked',
            'low_balance',
            'admin_message',
        ];

        $count = rand(3, 8);
        $now = Carbon::now();

        for ($i = 0; $i < $count; $i++) {
            $type = $types[array_rand($types)];
            $isRead = (bool)rand(0, 1);
            $daysAgo = rand(1, 60);
            $createdAt = $now->copy()->subDays($daysAgo)->addHours(rand(0, 23));

            $titleMap = [
                'deposit_approved'   => 'Deposit Approved',
                'deposit_rejected'   => 'Deposit Rejected',
                'withdrawal_approved' => 'Withdrawal Approved',
                'account_locked'     => 'Account Locked',
                'low_balance'        => 'Low Balance Warning',
                'admin_message'      => 'Message from Administration',
            ];

            $bodyMap = [
                'deposit_approved'   => 'Your deposit request has been approved and credited to your account.',
                'deposit_rejected'   => 'Your deposit request was rejected. Please contact support for details.',
                'withdrawal_approved' => 'Your withdrawal request has been approved and processed.',
                'account_locked'     => 'Your account has been locked due to suspicious activity. Contact support.',
                'low_balance'        => 'Your account balance is below the recommended threshold.',
                'admin_message'      => 'You have received a message from the Bastion Peak administration team.',
            ];

            Notification::create([
                'user_id'    => $userId,
                'type'       => $type,
                'title'      => $titleMap[$type],
                'body'       => $bodyMap[$type],
                'channel'    => $isRead ? 'in-app' : 'email',
                'read_at'    => $isRead ? now() : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    /**
     * Seed message threads for a customer.
     *
     * @param int $userId
     * @param int|null $adminId
     * @param int $seedIndex
     * @return void
     */
    private function seedMessageThreads(int $userId, ?int $adminId, int $seedIndex): void
    {
        $threadCount = rand(2, 3);
        $now = Carbon::now();
        $subjects = [
            'Account Inquiry',
            'Deposit Support',
            'Transaction Dispute',
            'Card Replacement Request',
            'Limit Adjustment',
        ];

        for ($i = 0; $i < $threadCount; $i++) {
            $subject = $subjects[array_rand($subjects)];
            $threadId = 'thread-' . strtolower(str_replace(' ', '-', $subject)) . '-' . $userId . '-' . $i;
            $threadCreatedAt = $now->copy()->subDays(rand(5, 60));

            $messageCount = rand(2, 6);
            for ($j = 0; $j < $messageCount; $j++) {
                $senderId = ($j % 2 === 0) ? $userId : ($adminId ?? $userId);
                Message::create([
                    'thread_id'    => $threadId,
                    'sender_id'    => $senderId,
                    'subject'      => $subject,
                    'body'         => 'This is a sample message regarding ' . strtolower($subject) . ' for the Bastion Peak internal banking system.',
                    'read_at'      => (bool)rand(0, 1) ? now() : null,
                    'created_at'   => $threadCreatedAt->copy()->addMinutes(rand(10, 1440) * ($j + 1)),
                    'updated_at'   => $threadCreatedAt->copy()->addMinutes(rand(10, 1440) * ($j + 1)),
                ]);
            }
        }
    }

    /**
     * Seed a card for a customer's main account.
     *
     * @param int $userId
     * @param int|null $accountId
     * @param int $seedIndex
     * @return void
     */
    private function seedCard(int $userId, ?int $accountId, int $seedIndex): void
    {
        if ($accountId === null) {
            return;
        }

        $prefixes = ['4532', '5412', '3782', '6011'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = $prefix . str_pad((string)random_int(0, 999999999999), 12 - strlen($prefix), '0', STR_PAD_LEFT);
        $formatted = preg_replace('/(.{4})/', '$1 ', $number);

        Card::create([
            'user_id'            => $userId,
            'account_id'         => $accountId,
            'card_number_masked' => trim($formatted),
            'card_type'          => 'virtual',
            'valid_from'         => now(),
            'valid_to'           => now()->addYears(rand(2, 4)),
            'status'             => 'active',
            'created_at'         => now()->subDays(rand(10, 60)),
            'updated_at'         => now(),
        ]);
    }

    /**
     * Seed audit log entries for admin actions.
     *
     * @param int|null $adminId
     * @param int $userId
     * @param int $seedIndex
     * @return void
     */
    private function seedAuditLogs(?int $adminId, int $userId, int $seedIndex): void
    {
        $actions = [
            'account_created',
            'account_updated',
            'account_status_changed',
            'transaction_approved',
            'transaction_rejected',
            'limit_updated',
            'user_status_changed',
            'deposit_verified',
            'merchant_approved',
            'report_generated',
        ];

        $count = rand(10, 20);
        $now = Carbon::now();

        for ($i = 0; $i < $count; $i++) {
            $action = $actions[array_rand($actions)];
            $daysAgo = rand(1, 90);
            $createdAt = $now->copy()->subDays($daysAgo)->addHours(rand(0, 23))->addMinutes(rand(0, 59));

            AuditLog::create([
                'admin_id'     => $adminId,
                'action'       => $action,
                'target_type'  => 'App\Models\User',
                'target_id'    => $userId,
                'ip_address'   => '10.0.0.' . rand(1, 254),
                'user_agent'   => 'BastionPeak-Admin/1.0',
                'created_at'   => $createdAt,
                'updated_at'   => $createdAt,
            ]);
        }
    }
}
