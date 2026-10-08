<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained();
            $table->string('card_number_masked');
            $table->string('card_type');
            $table->string('status')->default('active');
            $table->decimal('per_transaction_cap', 27, 18)->nullable();
            $table->decimal('daily_cap', 27, 18)->nullable();
            $table->decimal('monthly_cap', 27, 18)->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->string('pin_hash')->nullable();
            $table->json('category_restrictions')->nullable();
            $table->json('region_restrictions')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
