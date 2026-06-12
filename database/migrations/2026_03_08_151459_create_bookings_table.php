<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('responsible_name');
            
            $table->string('cpf')->nullable();
            
            $table->dateTime('booking_date');
            
            $table->integer('guests_count')->default(1);
            
            $table->text('observation')->nullable();

            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};