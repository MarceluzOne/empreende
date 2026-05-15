<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->date('date');
            $table->time('start_time');
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('max_capacity');
            $table->foreignUuid('speaker_id')->constrained()->onDelete('restrict');
            $table->foreignUuid('booking_id')->nullable()->constrained('bookings')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
