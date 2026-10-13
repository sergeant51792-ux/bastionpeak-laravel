<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code', 6);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('admin_note', 500)->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('otp_enabled')->default(false)->after('totp_enabled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_otps');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('otp_enabled');
        });
    }
};
