<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_participant_id')->constrained()->onDelete('cascade');
            // Presença de um participante em uma data específica do evento.
            // A existência da linha significa "presente naquele dia".
            $table->date('event_date');
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
            $table->unique(['event_participant_id', 'event_date']);
        });

        // Limpeza: uma versão anterior desta feature usava um booleano `attended`
        // em event_participants. Removido em favor da presença por dia.
        if (Schema::hasColumn('event_participants', 'attended')) {
            Schema::table('event_participants', function (Blueprint $table) {
                $table->dropColumn('attended');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendances');
    }
};
