<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->default('other'); // wire, crypto, swift, sepa, ach, zelle, faster_payments, bacs, chaps
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->json('template')->nullable(); // form field configuration
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('account_deposit_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('deposit_method_id')->constrained()->cascadeOnDelete();
            $table->string('address')->nullable(); // crypto address or bank account
            $table->string('qr_path')->nullable(); // QR code image path
            $table->text('instructions')->nullable(); // deposit instructions
            $table->json('metadata')->nullable(); // network, routing number, swift code, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['account_id', 'deposit_method_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_deposit_methods');
        Schema::dropIfExists('deposit_methods');
    }
};
