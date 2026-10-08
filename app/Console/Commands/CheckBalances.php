<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LedgerService;

class CheckBalances extends Command
{
    protected $signature = 'bastion:check-balances';
    protected $description = 'Run balance integrity check on all accounts and log anomalies';

    public function handle(LedgerService $ledger): int
    {
        $accounts = \App\Models\Account::all();
        $anomalies = 0;

        foreach ($accounts as $account) {
            $mismatch = $ledger->checkBalanceIntegrity($account->id);

            if ($mismatch !== 0) {
                $anomalies++;

                $this->warn("Balance mismatch on account {$account->account_number}: differs by {$mismatch}");

                \App\Models\AuditLog::create([
                    'admin_id'      => null,
                    'action'        => 'balance_mismatch_detected',
                    'target_type'   => 'account',
                    'target_id'     => $account->id,
                    'old_value'     => ['shown' => (string) $account->balance],
                    'new_value'     => ['calculated' => (string) $ledger->recalculateBalance($account->id)],
                    'ip_address'    => null,
                    'user_agent'    => 'scheduled',
                ]);
            }
        }

        $this->info("Checked {$accounts->count()} accounts. {$anomalies} anomalies found.");

        return self::SUCCESS;
    }
}
