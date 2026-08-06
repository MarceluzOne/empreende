<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CPF do prestador: guardado apenas para vincular o cadastro público de
 * /servicos à conta do candidato no portal. Não aparece na vitrine.
 * Nullable porque os cadastros já existentes não têm CPF informado.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('cpf', 11)->nullable()->after('email')->index();
        });
    }

    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropIndex(['cpf']);
            $table->dropColumn('cpf');
        });
    }
};
