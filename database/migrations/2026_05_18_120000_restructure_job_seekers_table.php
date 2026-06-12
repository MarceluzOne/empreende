<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'curriculo_path']);

            $table->string('city')->nullable()->after('job_function');
            $table->string('state', 2)->nullable()->after('city');
            $table->string('linkedin_url')->nullable()->after('email');
            $table->string('github_url')->nullable()->after('linkedin_url');
            $table->text('summary')->nullable()->after('github_url');
            $table->text('skills')->nullable()->after('summary');
            $table->json('experiences')->nullable()->after('skills');
            $table->json('education')->nullable()->after('experiences');
            $table->json('languages')->nullable()->after('education');
            $table->json('certifications')->nullable()->after('languages');
        });
    }

    public function down(): void
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->dropColumn([
                'city', 'state', 'linkedin_url', 'github_url',
                'summary', 'skills', 'experiences', 'education',
                'languages', 'certifications',
            ]);

            $table->string('cpf', 11)->nullable();
            $table->string('curriculo_path')->nullable();
        });
    }
};
