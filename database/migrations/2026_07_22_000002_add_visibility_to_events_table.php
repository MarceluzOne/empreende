<?php

use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('visibility', ['public', 'private'])->default('public')->after('status');
            $table->string('share_token', 32)->nullable()->unique()->after('visibility');
        });

        // Gera um token de acesso para os eventos já existentes.
        Event::whereNull('share_token')->get()->each(function (Event $event) {
            $event->forceFill(['share_token' => Str::random(12)])->save();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['visibility', 'share_token']);
        });
    }
};
