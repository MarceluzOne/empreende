<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE attendances MODIFY customer_cpf VARCHAR(14) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE attendances MODIFY customer_cpf VARCHAR(11) NULL');
    }
};
