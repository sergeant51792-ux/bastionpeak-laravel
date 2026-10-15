<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('transfer_blocked')->default(false)->after('approved_by');
            $table->text('transfer_block_reason')->nullable()->after('transfer_blocked');
            $table->timestamp('transfer_blocked_at')->nullable()->after('transfer_block_reason');
            $table->unsignedBigInteger('transfer_blocked_by')->nullable()->after('transfer_blocked_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['transfer_blocked', 'transfer_block_reason', 'transfer_blocked_at', 'transfer_blocked_by']);
        });
    }
};
