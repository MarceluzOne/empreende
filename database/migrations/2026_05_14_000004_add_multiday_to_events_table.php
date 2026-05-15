<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('type', 20)->default('single')->after('booking_id');
            $table->json('extra_dates')->nullable()->after('type');
        });

        Schema::create('event_bookings', function (Blueprint $table) {
            $table->foreignUuid('event_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('booking_id')->constrained()->onDelete('cascade');
            $table->primary(['event_id', 'booking_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_bookings');
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['type', 'extra_dates']);
        });
    }
};
