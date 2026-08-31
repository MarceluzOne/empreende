<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Telefone da conta do portal.
 *
 * As outras origens (currículo, atendimento, evento, prestador) já guardavam um
 * telefone, mas nenhuma delas cobre quem só tem conta — e é justamente esse
 * número que a redefinição de senha assistida usa para entregar o link a alguém
 * que o sistema já conhecia. Aqui o funcionário grava o telefone na hora do
 * atendimento, quando o cadastro não tem nenhum.
 *
 * Só dígitos, sem máscara: é assim que AssistedPasswordResetService e
 * CitizenService::phonesFor() comparam os números das várias origens.
 *
 * Guardada por hasColumn porque em produção não roda migration (ver a rota
 * /setup-inicial): lá o ALTER TABLE é aplicado à mão e esta migration serve
 * para o ambiente local e para deixar o passo registrado.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            DB::statement('ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER cpf');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'phone')) {
            DB::statement('ALTER TABLE users DROP COLUMN phone');
        }
    }
};
