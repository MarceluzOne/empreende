<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\JobSeeker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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
     * Lista consolidada e paginada de cidadãos, opcionalmente filtrada por CPF.
     */
    public function list(?string $cpfSearch = null, int $perPage = 15): LengthAwarePaginator
    {
        $search = $this->onlyDigits($cpfSearch);
        $people = $this->build();

        $collection = collect($people);

        if ($search !== '') {
            $collection = $collection->filter(fn ($e) => str_contains($e['cpf'], $search));
        }

        $collection = $collection
            ->sortBy(fn ($e) => mb_strtolower($e['name'] ?? ''))
            ->map(fn ($e) => (object) $e)
            ->values();

        return $this->paginate($collection, $perPage);
    }

    /**
     * Detalhe de um cidadão: perfil de candidato (se houver) e histórico de
     * atendimentos, casados pelo CPF normalizado.
     */
    public function detail(string $cpf): array
    {
        $digits = $this->onlyDigits($cpf);

        $candidato = JobSeeker::with('user')->whereNotNull('cpf')->get()
            ->first(fn ($js) => $this->onlyDigits($js->cpf) === $digits);

        $attendances = Attendance::with('user')->whereNotNull('customer_cpf')->get()
            ->filter(fn ($a) => $this->onlyDigits($a->customer_cpf) === $digits)
            ->sortByDesc('scheduled_at')
            ->values();

        $name = $candidato->name
            ?? optional($attendances->first())->customer_name
            ?? null;

        return [
            'cpf'           => $digits,
            'cpf_formatted' => $this->formatDocument($digits),
            'name'          => $name,
            'candidato'     => $candidato,
            'attendances'   => $attendances,
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
            'cpf_formatted'      => $this->formatDocument($cpf),
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

    private function onlyDigits(?string $value): string
    {
        return preg_replace('/\D/', '', (string) $value);
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
