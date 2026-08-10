<?php

use App\Services\CpfBackfillService;
use Illuminate\Database\Migrations\Migration;

/**
 * Preenche users.cpf das contas criadas antes da coluna existir.
 *
 * A lógica está em CpfBackfillService porque o servidor de produção não tem
 * SSH nem artisan: lá quem executa é a tela do backoffice
 * (/portal/funcionario/backfill-cpf). Esta migration existe para o ambiente
 * local e para deixar o passo registrado no histórico.
 */
return new class extends Migration
{
    public function up(): void
    {
        app(CpfBackfillService::class)->run();
    }

    public function down(): void
    {
        // Sem reversão: não há como distinguir o CPF preenchido aqui do
        // informado pelo próprio usuário no cadastro.
    }
};
