<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupNotifications extends Command
{
    protected $signature = 'bastion:cleanup-notifications';
    protected $description = 'Remove notifications older than 90 days';

    public function handle(): int
    {
        $cutoff = now()->subDays(90);

        $deleted = DB::table('notifications')
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deleted} notifications older than 90 days.");

        return self::SUCCESS;
    }
}
