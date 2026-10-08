<?php

declare(strict_types=1);

namespace App\Pages\Admin;

use App\Models\SystemSetting;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title = 'Settings';
    protected static ?string $slug = 'settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.settings';

    public ?string $tab = 'general';
    public ?string $settings = null;

    public function mount(): void
    {
        $this->tab = request()->query('tab', 'general');
        $this->settings = SystemSetting::all()->groupBy('group');
    }
}
