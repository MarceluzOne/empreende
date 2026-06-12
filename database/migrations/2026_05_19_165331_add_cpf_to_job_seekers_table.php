<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('job_seekers', function (Blueprint $table) {
            $table->dropColumn('cpf');
        });
    }
};
