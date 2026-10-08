<?php

declare(strict_types=1);

namespace App\Widgets;

use Filament\Widgets\Widget;

class RecentActionsWidget extends Widget
{
    protected static ?string $maxHeight = '300px';

    protected function getView(): string
    {
        return 'filament.widgets.recent-actions';
    }
}
