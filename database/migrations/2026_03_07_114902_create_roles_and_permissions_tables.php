<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabela de Papéis (Roles)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); 
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabela de Permissões (Permissions)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            // Auto-relacionamento para as sub-permissões do seu Controller
            $table->foreignId('permission_id')->nullable()->constrained('permissions')->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Tabela Pivô: Role <-> Permission
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
        });

        // 4. Tabela Pivô: User <-> Role
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_and_permissions_tables');
    }
};
