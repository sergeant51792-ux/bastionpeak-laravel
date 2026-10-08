<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('symbol', 10);
            $table->string('name');
            $table->string('color')->nullable();
            $table->unsignedInteger('decimals')->default(2);
            $table->boolean('is_base')->default(false);
            $table->decimal('exchange_rate', 27, 18)->default(1);
            $table->timestamp('rate_updated_at')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->index(['is_enabled', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
