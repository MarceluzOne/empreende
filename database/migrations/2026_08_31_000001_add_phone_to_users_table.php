<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
 *
 * Por Schema::table(), e não por um ALTER TABLE cru com `AFTER cpf`: a suíte
 * roda em sqlite :memory: (phpunit.xml), e sqlite não aceita `AFTER` em
 * `ADD COLUMN` — o ALTER derrubava todo teste com RefreshDatabase no boot.
 * A posição da coluna é cosmética e só existe no .sql de produção.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'phone')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
