<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('attendances', 'customer_cnpj')) {
            DB::statement('ALTER TABLE attendances ADD COLUMN customer_cnpj VARCHAR(14) NULL AFTER customer_cpf');
        }

        // Move CNPJs legados que estavam gravados no campo unificado customer_cpf
        // para a nova coluna dedicada, deixando customer_cpf apenas para CPF.
        DB::statement("UPDATE attendances SET customer_cnpj = customer_cpf, customer_cpf = NULL WHERE LENGTH(customer_cpf) = 14");
    }

    public function down()
    {
        // Reverte: devolve o CNPJ ao campo customer_cpf antes de remover a coluna.
        DB::statement("UPDATE attendances SET customer_cpf = customer_cnpj WHERE customer_cnpj IS NOT NULL AND customer_cpf IS NULL");

        if (Schema::hasColumn('attendances', 'customer_cnpj')) {
            DB::statement('ALTER TABLE attendances DROP COLUMN customer_cnpj');
        }
    }
};
