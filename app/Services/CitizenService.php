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
     * Lista consolidada e paginada de cidadãos, opcionalmente filtrada por
     * nome ou CPF (ambos aceitam busca parcial).
     */
    public function list(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $term   = trim((string) $search);
        $people = $this->build();

        $collection = collect($people);

        if ($term !== '') {
            $collection = $collection->filter(fn ($e) => $this->matches($e, $term));
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

        return [
            'uuid'          => $entry['uuid'],
            'cpf'           => $digits,
            'cpf_formatted' => $entry['cpf_formatted'],
            'cnpjs'         => $cnpjs,
            'name'          => $entry['name'] ?? $candidato->name ?? optional($attendances->first())->customer_name,
            'candidato'     => $candidato,
            'attendances'   => $attendances,
            'account'       => $this->accountFor($digits),
            'phones'        => $this->phonesFor($digits),
        ];
    }

    /**
     * CPF do cidadão a partir do UUID da listagem, ou null se o UUID não
     * corresponder a ninguém.
     */
    public function cpfForUuid(string $uuid): ?string
    {
        $entry = collect($this->build())->first(fn ($e) => $e['uuid'] === $uuid);

        return $entry['cpf'] ?? null;
    }

    /**
     * Conta do portal desta pessoa.
     *
     * Procura primeiro por `users.cpf`, que é o vínculo direto. O currículo é
     * a segunda tentativa e existe para um caso específico: contas criadas
     * antes da coluna `cpf` continuam com o campo nulo, e sem esse desvio a
     * tela diria "não tem conta" quando na verdade tem — só falta o backfill.
     */
    public function accountFor(string $cpfDigits): ?User
    {
        $variants = Document::cpfVariants($cpfDigits);

        if ($variants === []) {
            return null;
        }

        return User::whereIn('cpf', $variants)->where('type', 'usuario')->first()
            ?: optional(JobSeeker::whereIn('cpf', $variants)->whereNotNull('user_id')->first())->user;
    }

    /**
     * Telefones conhecidos deste CPF, sem repetição, com as origens em que
     * cada número aparece.
     *
     * São o segundo fator do link de redefinição assistida: o funcionário só
     * envia para um número que o sistema já conhecia, e não para um ditado na
     * hora por quem pediu.
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
     * Grava o telefone na conta do portal, para o caso em que o cadastro não
     * tem número em nenhuma origem e o funcionário o colhe no atendimento.
     *
     * Só dígitos: é assim que phonesFor() compara os números das origens.
     */
    public function storeAccountPhone(User $account, string $phone): void
    {
        $account->phone = $this->onlyDigits($phone);
        $account->save();
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

        return $people;
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
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Casa o termo buscado com o CPF (comparado só por dígitos, para ignorar a
     * máscara) ou com o nome (sem acento e em caixa baixa).
     *
     * @param array<string, mixed> $entry
     */
    private function matches(array $entry, string $term): bool
    {
        $digits = $this->onlyDigits($term);

        if ($digits !== '' && str_contains($entry['cpf'], $digits)) {
            return true;
        }

        return $entry['name'] !== null
            && str_contains($this->normalize($entry['name']), $this->normalize($term));
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
