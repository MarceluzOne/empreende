<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marca a reabertura manual de um evento já encerrado pela data. Sem esta
     * coluna não há como diferenciar "ainda não começou" (status 'active', que
     * é o padrão) de "o admin reabriu depois de encerrado".
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->timestamp('reopened_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('reopened_at');
        });
    }
};
