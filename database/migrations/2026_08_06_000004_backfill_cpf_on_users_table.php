<?php

use App\Models\EventParticipant;
use App\Models\JobSeeker;
use App\Models\User;
use App\Support\Document;
use Illuminate\Database\Migrations\Migration;

/**
 * Preenche users.cpf das contas criadas antes da coluna existir.
 *
 * Sem isso a checagem de conta existente não protege ninguém: qualquer pessoa
 * com conta antiga (cpf nulo) poderia abrir uma segunda conta com o mesmo CPF
 * e outro e-mail, e o currículo ficaria preso na conta velha.
 *
 * Duas passadas, da fonte mais confiável para a menos:
 *  1. currículo vinculado à conta (job_seekers.user_id);
 *  2. inscrição em evento com o mesmo e-mail da conta — alcança quem usou o
 *     sistema sem nunca ter montado currículo.
 *
 * Em ambas, só grava se o CPF ainda não pertencer a outra conta, para não
 * esbarrar no índice único.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->backfillFromJobSeekers();
        $this->backfillFromEventParticipants();
    }

    public function down(): void
    {
        // Sem reversão: não há como distinguir o CPF preenchido aqui do
        // informado pelo próprio usuário no cadastro.
    }

    private function backfillFromJobSeekers(): void
    {
        $seekers = JobSeeker::whereNotNull('user_id')
            ->whereNotNull('cpf')
            ->get(['user_id', 'cpf']);

        foreach ($seekers as $seeker) {
            $this->apply($seeker->user_id, $seeker->cpf);
        }
    }

    private function backfillFromEventParticipants(): void
    {
        $participants = EventParticipant::whereNotNull('cpf')
            ->whereNotNull('email')
            ->get(['email', 'cpf']);

        foreach ($participants as $participant) {
            $userId = User::where('email', $participant->email)->whereNull('cpf')->value('id');

            if ($userId !== null) {
                $this->apply($userId, $participant->cpf);
            }
        }
    }

    private function apply(string $userId, ?string $cpf): void
    {
        $digits = Document::digits($cpf);

        if (strlen($digits) !== 11 || User::where('cpf', $digits)->exists()) {
            return;
        }

        User::where('id', $userId)->whereNull('cpf')->update(['cpf' => $digits]);
    }
};
