<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Bastion Peak Internal Banking System — Process Deposit Proof Job
 *
 * Placeholder for future image-matching logic for deposit proof verification.
 * This job can be queued to process deposit proof images in the background.
 */
class ProcessDepositProof implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The transaction ID to process proof for.
     *
     * @var int
     */
    public int $transactionId;

    /**
     * The path to the proof file.
     *
     * @var string|null
     */
    public ?string $proofFile;

    /**
     * Create a new job instance.
     *
     * @param int $transactionId
     * @param string|null $proofFile
     * @return void
     */
    public function __construct(int $transactionId, ?string $proofFile)
    {
        $this->transactionId = $transactionId;
        $this->proofFile = $proofFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $transaction = Transaction::findOrFail($this->transactionId);

        // Future: integrate image-matching service to verify deposit proof
        // against bank statement records.
        // For now, this is a no-op placeholder.
    }
}
