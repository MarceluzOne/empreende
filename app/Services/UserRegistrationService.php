<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\EventParticipant;
use App\Models\JobSeeker;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Support\Document;
use App\Support\Email;
use Illuminate\Support\Facades\Hash;

/**
 * Cadastro de usuário do portal identificado pelo CPF.
 *
 * O CPF é a única chave que atravessa as origens do sistema (currículo,
 * inscrição em evento, atendimento e cadastro de prestador), então é ele que
 * permite reconhecer, na criação da conta, o que a pessoa já tem no sistema.
 *
 * O reconhecimento acontece só depois da conta criada — não há consulta por
 * CPF antes da autenticação, para não expor dados de terceiros a quem apenas
 * digita documentos na tela de cadastro.
 *
 * Cada origem grava o CPF de um jeito: o currículo guarda com máscara, as
 * demais só com dígitos. Por isso toda consulta usa as duas formas.
 */
class UserRegistrationService
{
    /**
     * Cria a conta e vincula o que já existir para o mesmo CPF.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        $cpf = Document::digits($data['cpf'] ?? null);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'cpf'      => $cpf !== '' ? $cpf : null,
            'password' => Hash::make($data['password']),
            'type'     => 'usuario',
        ]);

        $this->linkExistingRecords($user);

        return $user;
    }

    /**
     * Conta já existente que impede este cadastro, se houver.
     *
     * O CPF tem prioridade sobre o e-mail: ele identifica a pessoa, então é o
     * sinal mais útil ("você já tem conta") mesmo quando o e-mail digitado
     * pertence a uma terceira conta.
     *
     * @return array{matched: string, type: string, email_masked: ?string}|null
     */
    public function accountConflict(?string $cpf, ?string $email): ?array
    {
        $variants = Document::cpfVariants($cpf);

        $byCpf   = $variants === [] ? null : User::whereIn('cpf', $variants)->first();
        $byEmail = $email === null ? null : User::where('email', $email)->first();

        if (!$byCpf && !$byEmail) {
            return null;
        }

        $account = $byCpf ?: $byEmail;

        return [
            'matched'      => $this->matchedField($byCpf, $byEmail),
            'type'         => $account->type,
            'email_masked' => Email::mask($account->email),
        ];
    }

    /**
     * 'both' só quando CPF e e-mail caem na mesma conta; caindo em contas
     * diferentes, vale o CPF.
     */
    private function matchedField(?User $byCpf, ?User $byEmail): string
    {
        if ($byCpf && $byEmail && $byCpf->is($byEmail)) {
            return 'both';
        }

        return $byCpf ? 'cpf' : 'email';
    }

    /**
     * Resumo, em linguagem corrente, dos registros anteriores reconhecidos
     * pelo CPF da conta. Retorna null quando não há nada a informar.
     */
    public function linkedSummary(User $user): ?string
    {
        $variants = Document::cpfVariants($user->cpf);

        if ($variants === []) {
            return null;
        }

        $items = $this->describe($this->sources($variants));

        if ($items === []) {
            return null;
        }

        return 'Encontramos cadastros anteriores com o seu CPF e vinculamos à sua conta: '
            .$this->joinItems($items).'.';
    }

    /**
     * Quantidade de registros por origem.
     *
     * @param array<int, string> $variants
     * @return array<string, int|bool>
     */
    private function sources(array $variants): array
    {
        return [
            'curriculo'    => JobSeeker::whereIn('cpf', $variants)->exists(),
            'eventos'      => EventParticipant::whereIn('cpf', $variants)->count(),
            'atendimentos' => Attendance::whereIn('customer_cpf', $variants)->count(),
            'servicos'     => ServiceProvider::whereIn('cpf', $variants)->count(),
        ];
    }

    /**
     * @param array<string, int|bool> $sources
     * @return array<int, string>
     */
    private function describe(array $sources): array
    {
        $items = [];

        if ($sources['curriculo']) {
            $items[] = 'seu currículo';
        }
        if ($sources['eventos']) {
            $items[] = $this->plural($sources['eventos'], 'inscrição em evento', 'inscrições em eventos');
        }
        if ($sources['atendimentos']) {
            $items[] = $this->plural($sources['atendimentos'], 'atendimento', 'atendimentos');
        }
        if ($sources['servicos']) {
            $items[] = $this->plural($sources['servicos'], 'cadastro de prestador', 'cadastros de prestador');
        }

        return $items;
    }

    private function plural(int $count, string $singular, string $plural): string
    {
        return $count.' '.($count > 1 ? $plural : $singular);
    }

    /**
     * @param array<int, string> $items
     */
    private function joinItems(array $items): string
    {
        if (count($items) === 1) {
            return $items[0];
        }

        $last = array_pop($items);

        return implode(', ', $items).' e '.$last;
    }

    /**
     * Assume o currículo órfão do mesmo CPF (cadastro feito pela equipe antes
     * de a pessoa ter conta) e passa a apontá-lo para o e-mail da conta — é
     * para lá que vão as notificações de vaga, que saem de job_seekers.email.
     *
     * As demais origens são casadas pelo CPF em tempo de consulta no portal,
     * então não precisam de vínculo gravado.
     */
    private function linkExistingRecords(User $user): void
    {
        $variants = Document::cpfVariants($user->cpf);

        if ($variants === []) {
            return;
        }

        JobSeeker::whereIn('cpf', $variants)
            ->whereNull('user_id')
            ->update([
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);
    }
}
