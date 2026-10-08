<?php

declare(strict_types=1);

namespace App\Pages\Admin;

use Filament\Pages\Page;

class AuditLog extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Audit log';
    protected static ?string $title = 'Audit log';
    protected static ?string $slug = 'audit-log';
    protected static ?string $navigationGroup = 'Compliance';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.audit-log';
}
