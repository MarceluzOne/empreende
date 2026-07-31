<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            // Marca o diretor do Empreende Vitória, que assina todos os
            // certificados. Só um palestrante fica com a flag ligada — o
            // SpeakerController desliga a dos demais ao marcar um novo.
            $table->boolean('is_director')->default(false)->after('signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('speakers', function (Blueprint $table) {
            $table->dropColumn('is_director');
        });
    }
};
