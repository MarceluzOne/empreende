<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        DB::statement('ALTER TABLE attendances MODIFY user_id CHAR(36) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE attendances MODIFY user_id CHAR(36) NOT NULL');
    }
};
