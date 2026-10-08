<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('deposit_address')->nullable()->after('notes');
            $table->string('deposit_qr_path')->nullable()->after('deposit_address');
            $table->text('deposit_instructions')->nullable()->after('deposit_qr_path');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['deposit_address', 'deposit_qr_path', 'deposit_instructions']);
        });
    }
};
