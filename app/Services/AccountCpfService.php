<?php

namespace App\Services;

use App\Exceptions\AccountCpfException;
use App\Models\JobSeeker;
use App\Models\User;
use App\Support\Document;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;

/**
 * CPF das contas do portal criadas antes de o cadastro pedi-lo.
 *
 * Sem CPF a conta fica fora da listagem de Cidadãos e sem link de senha
 * possível, porque é o CPF que confirma a identidade na redefinição. O
 * backfill automático (CpfBackfillService) cobre quem tem currículo ou
 * inscrição em evento; o resto chega aqui por dois caminhos:
 *  - o próprio cidadão, obrigado a informar no próximo login;
 *  - o atendente, para quem já perdeu a senha e não consegue entrar.
 *
 * Só preenche — nunca troca. Corrigir um CPF gravado é assunto da edição do
 * cidadão (CitizenUpdateService), que propaga a troca pelas outras origens.
 */
class AccountCpfService
{
    public function __construct(private UserRegistrationService $registration) {}

    /**
     * Contas de usuário sem CPF, para a tela do atendente.
     */
    public function withoutCpf(?string $search): LengthAwarePaginator
    {
        $search = trim((string) $search);

        return User::where('type', 'usuario')
            ->whereNull('cpf')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Grava o CPF e assume o currículo órfão do mesmo documento, como o
     * cadastro faz. Devolve o CPF gravado (só dígitos).
     *
     * Auditado nos dois caminhos: vincular um CPF a uma conta é o que, depois,
     * autoriza um link de senha para ela.
     */
    public function assign(User $account, string $cpf, bool $byStaff = false): string
    {
        $digits = Document::digits($cpf);

        $this->ensureAssignable($account, $digits);
        $this->write($account, $digits);
        $this->registration->linkExistingRecords($account);

        AuditService::log('updated', $account, null, $byStaff
            ? "Informou no atendimento o CPF da conta de {$account->name}"
            : "CPF informado pelo próprio usuário no login: {$account->name}");

        return $digits;
    }

    private function ensureAssignable(User $account, string $digits): void
    {
        if ($account->type !== 'usuario') {
            throw AccountCpfException::notPortalAccount();
        }

        if (! empty($account->cpf)) {
            throw AccountCpfException::alreadyFilled();
        }

        if ($this->takenElsewhere($account, $digits)) {
            throw AccountCpfException::taken();
        }
    }

    /**
     * O CPF está em uso se outra conta o tem, ou se um currículo dele aponta
     * para OUTRA conta de usuário.
     *
     * Currículo sem dono, ou apontando para conta de funcionário, não conta:
     * `JobSeekerService::store()` grava o `user_id` do atendente em todo
     * cadastro de balcão, e esse currículo é justamente o que esta conta vai
     * assumir.
     */
    private function takenElsewhere(User $account, string $digits): bool
    {
        $variants = Document::cpfVariants($digits);

        if (User::whereIn('cpf', $variants)->exists()) {
            return true;
        }

        $donos = JobSeeker::whereIn('cpf', $variants)
            ->whereNotNull('user_id')
            ->where('user_id', '!=', $account->id)
            ->pluck('user_id');

        return $donos->isNotEmpty()
            && User::whereIn('id', $donos)->where('type', 'usuario')->exists();
    }

    /**
     * O `whereNull` e o índice único fecham a corrida entre a checagem e a
     * gravação: dois envios simultâneos não terminam com o mesmo CPF.
     */
    private function write(User $account, string $digits): void
    {
        try {
            $updated = User::where('id', $account->id)->whereNull('cpf')->update(['cpf' => $digits]);
        } catch (QueryException $e) {
            throw AccountCpfException::taken();
        }

        if ($updated === 0) {
            throw AccountCpfException::alreadyFilled();
        }

        $account->cpf = $digits;
    }
}
