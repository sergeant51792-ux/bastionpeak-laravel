<?php

declare(strict_types=1);

namespace App\Pages\Admin;

use App\Services\LedgerService;
use App\Models\Transaction;
use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?string $title = 'Reports';
    protected static ?string $slug = 'reports';
    protected static ?string $navigationGroup = 'Analytics';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.reports';

    public ?string $tab = 'financial';
    public ?string $from = null;
    public ?string $to = null;
    public ?float $systemBalance = null;
    public ?int $volume = null;
    public ?float $volumeIn = null;
    public ?float $volumeOut = null;

    public function mount(): void
    {
        $this->tab = request()->query('tab', 'financial');
        $this->from = request()->query('from', now()->startOfMonth()->format('Y-m-d'));
        $this->to = request()->query('to', now()->format('Y-m-d'));

        $this->systemBalance = app(LedgerService::class)->getSystemBalance();
        $this->volume = Transaction::whereBetween('created_at', [$this->from, $this->to])->count();
        $this->volumeIn = (float) Transaction::whereBetween('created_at', [$this->from, $this->to])
            ->whereIn('type', ['credit', 'deposit_credit', 'refund'])
            ->sum('amount');
        $this->volumeOut = (float) Transaction::whereBetween('created_at', [$this->from, $this->to])
            ->whereIn('type', ['debit', 'payment_debit', 'adjustment'])
            ->sum('amount');
    }
}
