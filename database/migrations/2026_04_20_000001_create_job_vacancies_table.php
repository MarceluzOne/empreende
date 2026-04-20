<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('cnpj', 14);
            $table->string('company_name');
            $table->string('position');
            $table->integer('quantity');
            $table->string('remuneration')->nullable();
            $table->text('requirements');
            $table->json('benefits')->nullable();
            $table->string('min_experience')->nullable();
            $table->string('interest_area')->nullable();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
