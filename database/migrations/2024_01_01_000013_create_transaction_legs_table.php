<?php

declare(strict_types=1);

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_legs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('side');
            $table->decimal('amount', 27, 18);
            $table->foreignId('currency_id')->constrained();
            $table->timestamps();

            $table->index(['transaction_id', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_legs');
    }
};
