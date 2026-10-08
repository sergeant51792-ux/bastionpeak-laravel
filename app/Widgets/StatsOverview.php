<?php

declare(strict_types=1);

namespace App\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total balance', '$1,284,300')
                ->description('Across 86 accounts')
                ->color('primary'),
            Stat::make('Transactions today', '41')
                ->description('Volume: $23,450')
                ->color('success'),
            Stat::make('Pending approvals', '14')
                ->description('9 deposits, 5 payments')
                ->color('warning'),
            Stat::make('Anomalies', '3')
                ->description('Requires attention')
                ->color('danger'),
        ];
    }
}
