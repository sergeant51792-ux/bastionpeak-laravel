<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained();
            $table->string('type')->default('main');
            $table->string('name');
            $table->string('label')->nullable();
            $table->string('account_number')->unique()->index();
            $table->decimal('balance', 27, 18)->default(0);
            $table->decimal('balance_cap', 27, 18)->nullable();
            $table->decimal('per_transaction_cap', 27, 18)->nullable();
            $table->decimal('daily_cap', 27, 18)->nullable();
            $table->decimal('monthly_cap', 27, 18)->nullable();
            $table->string('status')->default('active');
            $table->json('spend_restriction_payees')->nullable();
            $table->json('spend_restriction_categories')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['currency_id', 'status']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
