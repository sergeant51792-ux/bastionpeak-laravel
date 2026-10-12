<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalise customer-facing wording in existing records:
     * "Main Wallet" -> "Primary", "Deposit Approved" -> "Money Added",
     * "Deposit Rejected" -> "Funding Declined", "Deposit Support" -> "Funding Support".
     */
    public function up(): void
    {
        DB::table('accounts')
            ->where('name', 'Main Wallet')
            ->update(['name' => 'Primary', 'label' => 'Primary']);

        DB::table('accounts')
            ->where('label', 'Main Wallet')
            ->update(['label' => 'Primary']);

        DB::table('notifications')
            ->where('title', 'Deposit Approved')
            ->update(['title' => 'Money Added']);

        DB::table('notifications')
            ->where('title', 'Deposit Rejected')
            ->update(['title' => 'Funding Declined']);

        DB::table('notifications')
            ->where('body', 'like', 'Your deposit request has been approved%')
            ->update(['body' => 'Your funding request has been approved and credited to your account.']);

        DB::table('notifications')
            ->where('body', 'like', 'Your deposit request was rejected%')
            ->update(['body' => 'Your funding request was declined. Please contact support for details.']);

        DB::table('messages')
            ->where('subject', 'Deposit Support')
            ->update(['subject' => 'Funding Support']);
    }

    public function down(): void
    {
        DB::table('accounts')
            ->where('name', 'Primary')
            ->where('type', 'main_wallet')
            ->update(['name' => 'Main Wallet', 'label' => 'Main Wallet']);

        DB::table('notifications')
            ->where('title', 'Money Added')
            ->update(['title' => 'Deposit Approved']);

        DB::table('notifications')
            ->where('title', 'Funding Declined')
            ->update(['title' => 'Deposit Rejected']);

        DB::table('messages')
            ->where('subject', 'Funding Support')
            ->update(['subject' => 'Deposit Support']);
    }
};