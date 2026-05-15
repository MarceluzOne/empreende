<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained()->onDelete('cascade'); 

            $table->string('customer_name');
            $table->string('customer_cpf', 11)->nullable();

            $table->string('service_type');
            $table->text('description');
            

            $table->dateTime('scheduled_at')->nullable(); 

            $table->enum('status', [
                'scheduled', 
                'processing', 
                'completed', 
                'pending', 
                'forwarded'
            ])->default('scheduled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
