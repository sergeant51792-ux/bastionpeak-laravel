<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique()->index();
            $table->string('type');
            $table->string('status')->default('pending_review');
            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('merchant_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 27, 18);
            $table->foreignId('currency_id')->constrained();
            $table->decimal('fee', 27, 18)->default(0);
            $table->decimal('net_amount', 27, 18);
            $table->string('reference')->nullable();
            $table->text('memo')->nullable();
            $table->string('proof_file_path')->nullable();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('operator_type')->nullable();
            $table->string('idempotency_key')->unique()->nullable();
            $table->foreignId('reversal_of_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('parent_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('matched_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('credited_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['type', 'status']);
            $table->index(['from_user_id', 'created_at']);
            $table->index(['to_user_id', 'created_at']);
            $table->index(['operator_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
