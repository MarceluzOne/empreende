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
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('business_image')->nullable()->after('optional_info');
        });
    }

    public function down()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn('business_image');
        });
    }
};
