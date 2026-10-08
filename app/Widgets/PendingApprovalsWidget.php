<?php

declare(strict_types=1);

namespace App\Widgets;

use Filament\Widgets\Widget;

class PendingApprovalsWidget extends Widget
{
    protected static ?string $pollingInterval = '30s';
    protected static ?string $maxHeight = '400px';

    protected function getView(): string
    {
        return 'filament.widgets.pending-approvals';
    }
}
