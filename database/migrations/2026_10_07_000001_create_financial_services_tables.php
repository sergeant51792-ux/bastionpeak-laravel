<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_services', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // loan, grant, tax_refund
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('currency_code')->default('USD');
            $table->decimal('min_amount', 15, 4)->default(0);
            $table->decimal('max_amount', 15, 4)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('term_months')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('criteria')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('financial_service_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('financial_service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 4);
            $table->text('purpose')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'disbursed', 'paid'])->default('draft');
            $table->decimal('approved_amount', 15, 4)->nullable();
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('term_months')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['financial_service_id', 'status']);
        });

        Schema::create('tax_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('financial_service_applications')->nullOnDelete();
            $table->string('tax_year');
            $table->string('filing_status')->nullable();
            $table->decimal('claimed_amount', 15, 4)->default(0);
            $table->decimal('expected_refund', 15, 4)->default(0);
            $table->string('irs_reference')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_refunds');
        Schema::dropIfExists('financial_service_applications');
        Schema::dropIfExists('financial_services');
    }
};
