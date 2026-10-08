<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\LedgerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bastion Peak Internal Banking System — Check Balance After Transaction Listener
 *
 * Validates balance integrity after every posted transaction.
 */
class CheckBalanceAfterTransaction implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @param LedgerService $ledgerService
     * @return void
     */
    public function __construct(protected LedgerService $ledgerService)
    {
    }

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle(mixed $event): void
    {
        $accountId = $event->accountId ?? null;

        if ($accountId === null) {
            return;
        }

        $mismatch = $this->ledgerService->checkBalanceIntegrity((int)$accountId);

        if ($mismatch > 0.0) {
            // Log mismatch — in a real system this would trigger an alert
            logger()->warning('Bastion Peak balance integrity mismatch detected', [
                'account_id' => $accountId,
                'mismatch'   => $mismatch,
            ]);
        }
    }
}
