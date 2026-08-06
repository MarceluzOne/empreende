<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link do grupo de WhatsApp do evento (convite chat.whatsapp.com),
     * divulgado aos inscritos. Opcional: nem todo evento tem grupo.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('whatsapp_group_link')->nullable()->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('whatsapp_group_link');
        });
    }
};
