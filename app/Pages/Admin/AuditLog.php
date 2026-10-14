<?php

declare(strict_types=1);

namespace App\Pages\Admin;

use App\Models\AuditLog as AuditLogModel;
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

    public ?string $search = null;

    public function mount(): void
    {
        $this->search = request()->query('search');
    }

    public function getLogsProperty()
    {
        return AuditLogModel::when($this->search, function ($query) {
            $query->where('action', 'like', '%' . $this->search . '%')
                  ->orWhere('target_type', 'like', '%' . $this->search . '%');
        })
        ->orderByDesc('created_at')
        ->paginate(20)
        ->withQueryString();
    }
}
