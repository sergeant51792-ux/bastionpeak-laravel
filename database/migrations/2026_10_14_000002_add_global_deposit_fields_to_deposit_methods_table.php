<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposit_methods', function (Blueprint $table) {
            $table->text('address')->nullable()->after('sort_order');
            $table->text('instructions')->nullable()->after('address');
            $table->string('qr_path')->nullable()->after('instructions');
            $table->boolean('is_global')->default(true)->after('qr_path');
        });
    }

    public function down(): void
    {
        Schema::table('deposit_methods', function (Blueprint $table) {
            $table->dropColumn(['address', 'instructions', 'qr_path', 'is_global']);
        });
    }
};