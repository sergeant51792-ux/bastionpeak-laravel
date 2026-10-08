<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->decimal('receiving_cap', 27, 18)->nullable();
            $table->string('receiving_cap_period')->nullable();
            $table->string('status')->default('active');
            $table->json('account_numbers')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();

            $table->index(['status', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
