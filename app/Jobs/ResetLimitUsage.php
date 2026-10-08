<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\LedgerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Bastion Peak Internal Banking System — Reset Limit Usage Job
 *
 * Daily cron job to reset daily cap counters on all accounts.
 */
class ResetLimitUsage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @param LedgerService $ledgerService
     * @return void
     */
    public function handle(LedgerService $ledgerService): void
    {
        $ledgerService->resetLimitUsage();
    }
}
