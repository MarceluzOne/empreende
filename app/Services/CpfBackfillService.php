<?php

namespace App\Services;

use App\Models\EventParticipant;
use App\Models\JobSeeker;
use App\Models\User;
use App\Support\Document;

/**
 * Preenche users.cpf das contas criadas antes da coluna existir.
 *
 * Sem isso a checagem de conta existente não protege ninguém: quem tem conta
 * antiga (cpf nulo) consegue abrir uma segunda conta com o mesmo CPF e outro
 * e-mail, e o currículo fica preso na conta velha.
 *
 * Duas passadas, da fonte mais confiável para a menos:
 *  1. currículo vinculado à conta (job_seekers.user_id);
 *  2. inscrição em evento com o mesmo e-mail da conta — alcança quem usou o
 *     sistema sem nunca ter montado currículo.
 *
 * A lógica vive aqui, e não na migration, porque em produção não há SSH nem
 * artisan: quem executa é a tela do backoffice. A migration só chama.
 */
class CpfBackfillService
{
    /**
     * CPFs atribuídos durante esta execução.
     *
     * No ensaio nada vai ao banco, então sem esta reserva duas contas com o
     * mesmo CPF seriam contadas duas vezes — e a prévia prometeria mais do que
     * a execução real entregaria.
     *
     * @var array<string, bool>
     */
    private array $reservedCpfs = [];

    /** @var array<string, bool> */
    private array $reservedUsers = [];

    /**
     * Simula a execução sem gravar nada.
     *
     * @return array<string, int>
     */
    public function preview(): array
    {
        return $this->execute(true);
    }

    /**
     * @return array<string, int>
     */
    public function run(): array
    {
        return $this->execute(false);
    }

    /**
     * @return array<string, int>
     */
    private function execute(bool $dryRun): array
    {
        $this->reservedCpfs  = [];
        $this->reservedUsers = [];

        $fromSeekers = $this->fillFromJobSeekers($dryRun);
        $fromEvents  = $this->fillFromEventParticipants($dryRun);
        $filled      = $fromSeekers + $fromEvents;

        return [
            'curriculos' => $fromSeekers,
            'eventos'    => $fromEvents,
            'total'      => $filled,
            'restantes'  => $this->accountsWithoutCpf() - ($dryRun ? $filled : 0),
        ];
    }

    /**
     * Contas que continuam sem CPF. No ensaio o banco ainda não mudou, então o
     * chamador desconta o que seria preenchido.
     */
    private function accountsWithoutCpf(): int
    {
        return User::whereNull('cpf')->count();
    }

    private function fillFromJobSeekers(bool $dryRun): int
    {
        $seekers = JobSeeker::whereNotNull('user_id')
            ->whereNotNull('cpf')
            ->get(['user_id', 'cpf']);

        $filled = 0;

        foreach ($seekers as $seeker) {
            $filled += $this->apply($seeker->user_id, $seeker->cpf, $dryRun) ? 1 : 0;
        }

        return $filled;
    }

    private function fillFromEventParticipants(bool $dryRun): int
    {
        $participants = EventParticipant::whereNotNull('cpf')
            ->whereNotNull('email')
            ->get(['email', 'cpf']);

        $filled = 0;

        foreach ($participants as $participant) {
            $userId = User::where('email', $participant->email)->value('id');

            if ($userId !== null) {
                $filled += $this->apply($userId, $participant->cpf, $dryRun) ? 1 : 0;
            }
        }

        return $filled;
    }

    private function apply(string $userId, ?string $cpf, bool $dryRun): bool
    {
        $digits = Document::digits($cpf);

        if (! $this->isAvailable($userId, $digits)) {
            return false;
        }

        if (! $dryRun && User::where('id', $userId)->whereNull('cpf')->update(['cpf' => $digits]) === 0) {
            return false;
        }

        $this->reservedUsers[$userId] = true;
        $this->reservedCpfs[$digits]  = true;

        return true;
    }

    /**
     * O CPF só entra se ninguém mais o tiver — no banco ou nesta mesma
     * execução — e se a conta ainda estiver sem documento.
     */
    private function isAvailable(string $userId, string $digits): bool
    {
        if (strlen($digits) !== 11) {
            return false;
        }

        if (isset($this->reservedUsers[$userId]) || isset($this->reservedCpfs[$digits])) {
            return false;
        }

        if (User::where('cpf', $digits)->exists()) {
            return false;
        }

        return ! User::where('id', $userId)->whereNotNull('cpf')->exists();
    }
}
