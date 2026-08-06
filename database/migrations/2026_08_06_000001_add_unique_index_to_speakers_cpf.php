<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CPF do palestrante passa a ser único. Registros antigos com cpf NULL
     * continuam válidos — no MySQL o índice único aceita vários NULL.
     */
    public function up(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            $table->unique('cpf', 'speakers_cpf_unique');
        });
    }

    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            $table->dropUnique('speakers_cpf_unique');
        });
    }
};
