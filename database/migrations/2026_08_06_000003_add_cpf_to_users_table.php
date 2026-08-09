<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CPF da conta do portal, informado no cadastro do usuário. Serve para
 * reconhecer registros que a mesma pessoa já tem no sistema (currículo,
 * inscrições em eventos, atendimentos e cadastros de prestador), que hoje
 * só eram casados pelo CPF do currículo ou pelo e-mail da conta.
 *
 * Nullable porque as contas já existentes não têm CPF, e porque só se
 * aplica a usuários do tipo 'usuario'. O índice é único para impedir duas
 * contas com o mesmo CPF — o MySQL admite vários NULL em índice único, então
 * as contas legadas não conflitam entre si.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf', 11)->nullable()->after('email')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->dropColumn('cpf');
        });
    }
};
