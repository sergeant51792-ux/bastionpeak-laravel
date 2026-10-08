<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('permission.table_names.roles', 'roles'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create(config('permission.table_names.permissions', 'permissions'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create(config('permission.table_names.model_has_permissions', 'model_has_permissions'), function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');

            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign('permission_id')
                ->references('id')
                ->on(config('permission.table_names.permissions', 'permissions'))
                ->onDelete('cascade');
        });

        Schema::create(config('permission.table_names.model_has_roles', 'model_has_roles'), function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');

            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign('role_id')
                ->references('id')
                ->on(config('permission.table_names.roles', 'roles'))
                ->onDelete('cascade');
        });

        Schema::create(config('permission.table_names.role_has_permissions', 'role_has_permissions'), function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on(config('permission.table_names.permissions', 'permissions'))
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on(config('permission.table_names.roles', 'roles'))
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfActive(config('permission.table_names.role_has_permissions', 'role_has_permissions'));
        Schema::dropIfActive(config('permission.table_names.model_has_roles', 'model_has_roles'));
        Schema::dropIfActive(config('permission.table_names.model_has_permissions', 'model_has_permissions'));
        Schema::dropIfActive(config('permission.table_names.permissions', 'permissions'));
        Schema::dropIfActive(config('permission.table_names.roles', 'roles'));
    }
};
