<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Services\LedgerService;
use Barryvdh\DomPDF\Facade\Pdf;

class StatementController extends Controller
{
    public function __construct(protected LedgerService $ledger) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        $accountIds = $user->accounts()->pluck('accounts.id');

        return view('customer.statements', [
            'user'     => $user,
            'accounts' => $user->accounts()->with('currency')->get(),
            'history'  => \App\Models\Transaction::where(function ($q) use ($accountIds) {
                $q->whereIn('from_account_id', $accountIds)->orWhereIn('to_account_id', $accountIds);
            })->orderByDesc('created_at')
                ->limit(20)
                ->get(),
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'account_id'  => ['required', 'integer', 'exists:accounts,id'],
            'from_date'   => ['required', 'date'],
            'to_date'     => ['required', 'date', 'after_or_equal:from_date'],
            'format'      => ['required', 'in:pdf,csv'],
        ]);

        $user = Auth::user();
        $account = Account::where('id', $request->account_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $transactions = $this->ledger->getStatementData($account->id, $request->from_date, $request->to_date);

        if ($request->format === 'csv') {
            return $this->exportCsv($transactions, $account, $request->from_date, $request->to_date);
        }

        $pdf = Pdf::loadView('statements.pdf', [
            'account'      => $account,
            'transactions' => $transactions,
            'fromDate'     => $request->from_date,
            'toDate'       => $request->to_date,
            'company'      => config('app.name'),
        ]);

        return $pdf->download("statement-{$account->account_number}-{$request->from_date}-to-{$request->to_date}.pdf");
    }

    private function exportCsv($transactions, $account, string $from, string $to)
    {
        $filename = "statement-{$account->account_number}-{$from}-to-{$to}.csv";
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Reference', 'Type', 'Description', 'Amount', 'Balance']);
            foreach ($transactions as $txn) {
                fputcsv($file, [
                    $txn->created_at->format('Y-m-d H:i'),
                    $txn->transaction_id,
                    $txn->type->label(),
                    $txn->memo ?? '',
                    $txn->amount,
                    $txn->balance_after ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
