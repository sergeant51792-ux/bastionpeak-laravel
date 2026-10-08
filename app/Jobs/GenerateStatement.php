<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Account;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Bastion Peak Internal Banking System — Generate Statement Job
 *
 * Generates a PDF statement for an account using barryvdh/laravel-dompdf.
 */
class GenerateStatement implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The account ID to generate a statement for.
     *
     * @var int
     */
    public int $accountId;

    /**
     * The start date for the statement period.
     *
     * @var string
     */
    public string $startDate;

    /**
     * The end date for the statement period.
     *
     * @var string
     */
    public string $endDate;

    /**
     * Create a new job instance.
     *
     * @param int $accountId
     * @param string $startDate
     * @param string $endDate
     * @return void
     */
    public function __construct(int $accountId, string $startDate, string $endDate)
    {
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     *
     * @return string|null Path to generated PDF
     */
    public function handle(): ?string
    {
        $account = Account::findOrFail($this->accountId);
        $transactions = Transaction::where('account_id', $this->accountId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderByDesc('created_at')
            ->get();

        $data = [
            'account'      => $account,
            'transactions' => $transactions,
            'startDate'    => $this->startDate,
            'endDate'      => $this->endDate,
        ];

        $pdf = Pdf::loadView('statements.account-statement', $data);

        return $pdf->download('bastionpeak-statement-' . $account->account_number . '.pdf');
    }
}
