<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LedgerService;
use App\Models\Transaction;

class ReportController extends Controller
{
    public function __invoke(Request $request): \Illuminate\View\View
    {
        $tab = $request->query('tab', 'financial');
        $from = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->query('to', now()->format('Y-m-d'));

        return view('admin.reports', [
            'tab'            => $tab,
            'from'           => $from,
            'to'             => $to,
            'systemBalance'  => app(LedgerService::class)->getSystemBalance(),
            'volume'         => Transaction::whereBetween('created_at', [$from, $to])->count(),
            'volumeIn'       => Transaction::whereBetween('created_at', [$from, $to])
                ->whereIn('type', ['credit', 'deposit_credit', 'refund'])->sum('amount'),
            'volumeOut'      => Transaction::whereBetween('created_at', [$from, $to])
                ->whereIn('type', ['debit', 'payment_debit', 'adjustment'])->sum('amount'),
        ]);
    }

    public function export(Request $request)
    {
        // Stub for CSV/PDF export — production implementation uses LedgerService
        return response('Export endpoint', 501);
    }
}
