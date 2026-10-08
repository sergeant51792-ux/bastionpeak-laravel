<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('period');
            $table->string('type');
            $table->decimal('amount', 27, 18);
            $table->decimal('used_amount', 27, 18)->default(0);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'period', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_limits');
    }
};
