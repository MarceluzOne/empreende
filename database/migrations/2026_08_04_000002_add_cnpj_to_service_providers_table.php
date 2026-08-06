<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CNPJ da empresa: par do CPF do prestador individual. Guardado só para
 * vincular o cadastro público de /empresas-locais à conta da empresa no
 * portal. Não aparece na vitrine.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('cnpj', 14)->nullable()->after('cpf')->index();
        });
    }

    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropIndex(['cnpj']);
            $table->dropColumn('cnpj');
        });
    }
};
