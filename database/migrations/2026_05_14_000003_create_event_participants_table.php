<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
