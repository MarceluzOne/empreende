<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\EventParticipant;
use App\Models\JobSeeker;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Support\Document;
use App\Support\Phone;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Consolida "cidadãos" do sistema identificados pelo CPF, unindo duas fontes:
 *  - Candidatos (JobSeeker), cujo CPF é gravado com máscara;
 *  - Atendimentos (Attendance.customer_cpf), gravado só com dígitos.
 *
 * Por isso o CPF é sempre normalizado para dígitos antes de cruzar as fontes.
 */
class CitizenService
{
    /**
     * Lista consolidada e paginada de cidadãos.
     *
     * @param array{busca?: ?string, conta?: ?string} $filters
     *        busca → nome, CPF ou e-mail da conta (todos parciais)
     *        conta → 'com' | 'sem', para achar quem pode ou não acessar o portal
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $term  = trim((string) ($filters['busca'] ?? ''));
        $conta = $filters['conta'] ?? null;

        $collection = collect($this->build());

        if ($term !== '') {
            $collection = $collection->filter(fn ($e) => $this->matches($e, $term));
        }

        if ($conta === 'com' || $conta === 'sem') {
            $esperado   = $conta === 'com';
            $collection = $collection->filter(fn ($e) => $e['has_account'] === $esperado);
        }

        $collection = $collection
            ->sortBy(fn ($e) => mb_strtolower($e['name'] ?? ''))
            ->map(fn ($e) => (object) $e)
            ->values();

        return $this->paginate($collection, $perPage);
    }

    /**
     * Detalhe de um cidadão a partir do UUID (derivado do CPF): perfil de
     * candidato (se houver) e histórico de atendimentos.
     */
    public function detail(string $uuid): array
    {
        $entry = collect($this->build())->first(fn ($e) => $e['uuid'] === $uuid);

        if (!$entry) {
            return [
                'uuid'          => $uuid,
                'cpf'           => null,
                'cpf_formatted' => null,
                'cnpjs'         => collect(),
                'name'          => null,
                'phone'         => null,
                'email'         => null,
                'candidato'     => null,
                'attendances'   => collect(),
                'account'       => null,
                'phones'        => collect(),
            ];
        }

        $digits = $entry['cpf'];

        $candidato = JobSeeker::with('user')->whereNotNull('cpf')->get()
            ->first(fn ($js) => $this->onlyDigits($js->cpf) === $digits);

        $attendances = Attendance::with('user')->whereNotNull('customer_cpf')->get()
            ->filter(fn ($a) => $this->onlyDigits($a->customer_cpf) === $digits)
            ->sortByDesc('scheduled_at')
            ->values();

        $cnpjs = $attendances
            ->map(fn ($a) => $this->onlyDigits($a->customer_cnpj))
            ->filter(fn ($c) => strlen($c) === 14)
            ->unique()
            ->map(fn ($c) => $this->formatDocument($c))
            ->values();

        $account = $this->accountFor($digits);

        return [
            'uuid'          => $entry['uuid'],
            'cpf'           => $digits,
            'cpf_formatted' => $entry['cpf_formatted'],
            'cnpjs'         => $cnpjs,
            'name'          => $entry['name'] ?? $candidato->name ?? optional($attendances->first())->customer_name,
            // A conta entra na cadeia do telefone porque quem só tem conta no
            // portal não aparece nas outras origens — e é justamente quem a
            // tela de edição precisa conseguir completar.
            'phone'         => $candidato->phone
                ?? optional($attendances->first())->customer_phone
                ?? optional($account)->phone,
            'email'         => $candidato->email ?? optional($account)->email,
            'candidato'     => $candidato,
            'attendances'   => $attendances,
            'account'       => $account,
            'phones'        => $this->phonesFor($digits),
        ];
    }

    /**
     * Quantas contas do portal estão sem CPF e por isso ficam fora da listagem.
     *
     * A listagem é indexada pelo CPF — o UUID da URL deriva dele —, então uma
     * conta sem CPF não tem identidade para virar linha. Em vez de esconder o
     * fato, a tela mostra o número e o caminho para resolver (o backfill).
     *
     * Conta só as que o desvio pelo currículo também não alcança: essas já
     * aparecem, marcadas com `account_without_cpf`.
     */
    public function accountsWithoutCpfCount(): int
    {
        $comCurriculo = JobSeeker::whereNotNull('user_id')
            ->whereNotNull('cpf')
            ->pluck('user_id')
            ->unique()
            ->all();

        return User::where('type', 'usuario')
            ->whereNull('cpf')
            ->when($comCurriculo !== [], fn ($q) => $q->whereNotIn('id', $comCurriculo))
            ->count();
    }

    /**
     * CPF do cidadão a partir do UUID da listagem, ou null se o UUID não
     * corresponder a ninguém.
     *
     * Deriva do mapa consolidado, e não de uma varredura só do currículo e dos
     * atendimentos: é o que mantém o UUID resolvível quando a pessoa entra no
     * mapa por outra origem — por exemplo quem só tem conta no portal.
     */
    public function cpfForUuid(string $uuid): ?string
    {
        $entry = collect($this->build())->first(fn ($e) => $e['uuid'] === $uuid);

        return $entry['cpf'] ?? null;
    }

    /**
     * UUID da URL a partir de um CPF em qualquer grafia. Usado depois de uma
     * edição que trocou o documento, para redirecionar ao endereço novo.
     */
    public function uuidFor(?string $cpf): string
    {
        return $this->uuidForCpf($this->onlyDigits($cpf));
    }

    /**
     * Conta do portal desta pessoa.
     *
     * Procura primeiro por `users.cpf`, que é o vínculo direto. O currículo é
     * a segunda tentativa e existe para um caso específico: contas criadas
     * antes da coluna `cpf` continuam com o campo nulo, e sem esse desvio a
     * tela diria "não tem conta" quando na verdade tem — só falta o backfill.
     *
     * O desvio pelo currículo exige `type = 'usuario'` nas DUAS pontas, e isso
     * não é zelo: `JobSeekerService::store()` grava `user_id = Auth::id()`, de
     * modo que todo currículo cadastrado no balcão aponta para a conta do
     * ATENDENTE. Sem o filtro, esta função devolveria a conta do funcionário
     * como se fosse a do cidadão — e gerar um link de senha ali destruiria o
     * token dele, já que `password_reset_tokens` guarda um token por e-mail.
     */
    public function accountFor(string $cpfDigits): ?User
    {
        $variants = Document::cpfVariants($cpfDigits);

        if ($variants === []) {
            return null;
        }

        $byCpf = User::whereIn('cpf', $variants)->where('type', 'usuario')->first();

        if ($byCpf) {
            return $byCpf;
        }

        $byCurriculo = optional(
            JobSeeker::whereIn('cpf', $variants)->whereNotNull('user_id')->first()
        )->user;

        return optional($byCurriculo)->type === 'usuario' ? $byCurriculo : null;
    }

    /**
     * Telefones conhecidos deste CPF, sem repetição, com as origens em que
     * cada número aparece.
     *
     * Servem de apoio ao atendimento: ajudam a conferir com quem se está
     * falando e por onde mandar o link. Não são pré-requisito para gerar o
     * link — quem confirma a identidade é o atendente, conferindo o CPF e os
     * dados do cadastro na tela.
     *
     * @return Collection<int, array{digits: string, formatted: ?string, sources: array<int, string>}>
     */
    public function phonesFor(string $cpfDigits): Collection
    {
        $variants = Document::cpfVariants($cpfDigits);

        if ($variants === []) {
            return collect();
        }

        $found = [];

        foreach ($this->phoneSources($variants) as $source => $numbers) {
            foreach ($numbers as $number) {
                $digits = $this->onlyDigits($number);

                if (strlen($digits) < 10) {
                    continue;
                }

                $found[$digits]['digits']    = $digits;
                $found[$digits]['formatted'] = Phone::format($digits);
                $found[$digits]['sources'][] = $source;
            }
        }

        return collect($found)->map(function (array $phone) {
            $phone['sources'] = array_values(array_unique($phone['sources']));

            return $phone;
        })->values();
    }

    /**
     * Telefone de cada origem que grava um, rotulada em português para a tela.
     *
     * @param array<int, string> $variants
     * @return array<string, array<int, string|null>>
     */
    private function phoneSources(array $variants): array
    {
        return [
            'conta'       => User::whereIn('cpf', $variants)->pluck('phone')->all(),
            'currículo'   => JobSeeker::whereIn('cpf', $variants)->pluck('phone')->all(),
            'atendimento' => Attendance::whereIn('customer_cpf', $variants)->pluck('customer_phone')->all(),
            'evento'      => EventParticipant::whereIn('cpf', $variants)->pluck('whatsapp')->all(),
            'prestador'   => ServiceProvider::whereIn('cpf', $variants)->pluck('whatsapp')->all(),
        ];
    }

    /**
     * Monta o mapa cpf-normalizado => dados consolidados.
     *
     * @return array<string, array<string, mixed>>
     */
    private function build(): array
    {
        $people = [];

        foreach (JobSeeker::with('user')->whereNotNull('cpf')->get() as $js) {
            $cpf = $this->onlyDigits($js->cpf);
            if ($cpf === '') {
                continue;
            }
            $entry = $this->ensure($people, $cpf);
            $entry['name']          = $entry['name'] ?: $js->name;
            $entry['is_candidato']  = true;
            $entry['job_seeker_id'] = $js->id;
            $entry['user_id']       = $js->user_id;
            $people[$cpf] = $entry;
        }

        foreach (Attendance::whereNotNull('customer_cpf')->get() as $a) {
            $cpf = $this->onlyDigits($a->customer_cpf);
            if ($cpf === '') {
                continue;
            }
            $entry = $this->ensure($people, $cpf);
            $entry['name'] = $entry['name'] ?: $a->customer_name;
            $entry['attendance_count']++;
            $cnpj = $this->onlyDigits($a->customer_cnpj);
            if (strlen($cnpj) === 14) {
                $formatted = $this->formatDocument($cnpj);
                if (!in_array($formatted, $entry['cnpjs'], true)) {
                    $entry['cnpjs'][] = $formatted;
                }
            }
            if ($a->scheduled_at && (!$entry['last_attendance_at'] || $a->scheduled_at->gt($entry['last_attendance_at']))) {
                $entry['last_attendance_at'] = $a->scheduled_at;
            }
            $people[$cpf] = $entry;
        }

        $this->addAccounts($people);

        return $people;
    }

    /**
     * Terceira origem: as contas do portal.
     *
     * Sem ela a tela e cega para quem se cadastrou sozinho pelo site e nunca
     * fez curriculo nem foi atendido -- e e exatamente essa pessoa que perde a
     * senha e precisa ser encontrada aqui.
     *
     * Duas passadas porque ha dois jeitos de uma conta existir:
     *  - com CPF proprio, o caso normal desde 2026-08-06;
     *  - sem CPF, nas contas anteriores a coluna. Essas so sao alcancaveis
     *    pelo curriculo, e e o mesmo desvio que accountFor() faz -- se a
     *    listagem nao fizesse, diria "sem conta" para quem a tela de detalhe
     *    mostra com conta.
     *
     * O filtro type='usuario' e obrigatorio nas duas: job_seekers.user_id
     * guarda o id do ATENDENTE quando o curriculo foi cadastrado no balcao
     * (JobSeekerService::store()), nao o do cidadao.
     *
     * @param array<string, array<string, mixed>> $people
     */
    private function addAccounts(array &$people): void
    {
        $contas = User::where('type', 'usuario')
            ->whereNotNull('cpf')
            ->get(['id', 'name', 'email', 'cpf', 'created_at']);

        foreach ($contas as $conta) {
            $cpf = $this->onlyDigits($conta->cpf);
            if ($cpf === '') {
                continue;
            }
            $entry = $this->ensure($people, $cpf);
            // O nome do curriculo e do atendimento vem antes: foi digitado por
            // um atendente, e tende a estar mais correto que o do autocadastro.
            $entry['name']               = $entry['name'] ?: $conta->name;
            $entry['has_account']        = true;
            $entry['account_email']      = $conta->email;
            $entry['account_created_at'] = $conta->created_at;
            $entry['user_id']            = $entry['user_id'] ?: $conta->id;
            $people[$cpf] = $entry;
        }

        $this->flagAccountsWithoutCpf($people);
    }

    /**
     * Marca as entradas cuja conta existe mas esta sem CPF gravado, chegando
     * nelas pelo curriculo. O aviso na linha poupa o atendente de abrir o
     * cadastro para descobrir que o link nao sai sem o backfill.
     *
     * @param array<string, array<string, mixed>> $people
     */
    private function flagAccountsWithoutCpf(array &$people): void
    {
        $porCurriculo = JobSeeker::whereNotNull('cpf')
            ->whereNotNull('user_id')
            ->pluck('user_id', 'cpf');

        if ($porCurriculo->isEmpty()) {
            return;
        }

        $semCpf = User::whereIn('id', $porCurriculo->values()->unique()->all())
            ->where('type', 'usuario')
            ->whereNull('cpf')
            ->pluck('email', 'id');

        foreach ($porCurriculo as $documento => $userId) {
            $cpf = $this->onlyDigits($documento);

            if ($cpf === '' || !isset($people[$cpf]) || !$semCpf->has($userId)) {
                continue;
            }

            $people[$cpf]['has_account']         = true;
            $people[$cpf]['account_without_cpf'] = true;
            $people[$cpf]['account_email']       = $semCpf->get($userId);
        }
    }

    /**
     * @param array<string, array<string, mixed>> $people
     * @return array<string, mixed>
     */
    private function ensure(array &$people, string $cpf): array
    {
        return $people[$cpf] ?? [
            'cpf'                => $cpf,
            'uuid'               => $this->uuidForCpf($cpf),
            'cpf_formatted'      => $this->formatDocument($cpf),
            'cnpjs'              => [],
            'name'               => null,
            'is_candidato'       => false,
            'job_seeker_id'      => null,
            'user_id'            => null,
            'attendance_count'   => 0,
            'last_attendance_at' => null,
            'has_account'         => false,
            'account_without_cpf' => false,
            'account_email'       => null,
            'account_created_at'  => null,
        ];
    }

    private function paginate(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page  = LengthAwarePaginator::resolveCurrentPage();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $items->count(),
            $perPage,
            $page,
            [
                'path'  => LengthAwarePaginator::resolveCurrentPath(),
                // Sem isto a pagina 2 perde busca e filtro.
                'query' => request()->except('page'),
            ]
        );
    }

    /**
     * Casa o termo buscado com o CPF (só por dígitos, para ignorar a máscara),
     * com o nome ou com o e-mail da conta — todos sem acento e em caixa baixa.
     *
     * O e-mail entra porque no balcão a pessoa lembra do login com mais
     * frequência do que do próprio CPF decorado.
     *
     * @param array<string, mixed> $entry
     */
    private function matches(array $entry, string $term): bool
    {
        $digits = $this->onlyDigits($term);

        if ($digits !== '' && str_contains($entry['cpf'], $digits)) {
            return true;
        }

        $normalizado = $this->normalize($term);

        foreach ([$entry['name'], $entry['account_email']] as $campo) {
            if ($campo !== null && str_contains($this->normalize($campo), $normalizado)) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(Str::ascii($value));
    }

    private function onlyDigits(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value);
    }

    /**
     * UUID determinístico (v5) derivado do CPF — estável para o mesmo CPF e
     * não expõe o documento na URL. Não é reversível: o detalhe recompõe o
     * mapa e casa pelo próprio UUID.
     */
    private function uuidForCpf(string $digits): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_OID, 'empreende-cidadao:'.$digits)->toString();
    }

    private function formatDocument(string $digits): string
    {
        if (strlen($digits) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digits);
        }
        if (strlen($digits) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits);
        }
        return $digits;
    }
}
