<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\JobVacancy;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;

/**
 * Consolida "empresas" do sistema identificadas pelo CNPJ, unindo duas fontes:
 *  - Empresa (empregador cadastrado, com login);
 *  - JobVacancy.cnpj (empresas que publicaram vagas, podendo não ter cadastro).
 *
 * O CNPJ é normalizado para dígitos antes de cruzar as fontes. A rota usa um
 * UUID v5 derivado do CNPJ (não expõe o documento na URL), igual a Cidadãos.
 */
class CompanyService
{
    public function list(?string $cnpjSearch = null, int $perPage = 15): LengthAwarePaginator
    {
        $search = $this->onlyDigits($cnpjSearch);
        $companies = $this->build();

        $collection = collect($companies);

        if ($search !== '') {
            $collection = $collection->filter(fn ($e) => str_contains($e['cnpj'], $search));
        }

        $collection = $collection
            ->sortBy(fn ($e) => mb_strtolower($e['name'] ?? ''))
            ->map(fn ($e) => (object) $e)
            ->values();

        return $this->paginate($collection, $perPage);
    }

    public function detail(string $uuid): array
    {
        $entry = collect($this->build())->first(fn ($e) => $e['uuid'] === $uuid);

        if (!$entry) {
            return [
                'uuid'           => $uuid,
                'cnpj_formatted' => null,
                'name'           => null,
                'empresa'        => null,
                'vacancies'      => collect(),
            ];
        }

        $digits = $entry['cnpj'];

        $empresa = Empresa::with('user')->whereNotNull('cnpj')->get()
            ->first(fn ($e) => $this->onlyDigits($e->cnpj) === $digits);

        $vacancies = JobVacancy::withCount('applications')->whereNotNull('cnpj')->get()
            ->filter(fn ($v) => $this->onlyDigits($v->cnpj) === $digits)
            ->sortByDesc('created_at')
            ->values();

        return [
            'uuid'           => $entry['uuid'],
            'cnpj_formatted' => $entry['cnpj_formatted'],
            'name'           => $entry['name'] ?? optional($empresa)->razao_social ?? optional($vacancies->first())->company_name,
            'empresa'        => $empresa,
            'vacancies'      => $vacancies,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function build(): array
    {
        $companies = [];

        foreach (Empresa::with('user')->whereNotNull('cnpj')->get() as $e) {
            $cnpj = $this->onlyDigits($e->cnpj);
            if ($cnpj === '') {
                continue;
            }
            $entry = $this->ensure($companies, $cnpj);
            $entry['name']          = $entry['name'] ?: $e->razao_social;
            $entry['is_registered'] = true;
            $entry['city']          = $e->cidade;
            $companies[$cnpj] = $entry;
        }

        foreach (JobVacancy::whereNotNull('cnpj')->get() as $v) {
            $cnpj = $this->onlyDigits($v->cnpj);
            if ($cnpj === '') {
                continue;
            }
            $entry = $this->ensure($companies, $cnpj);
            $entry['name'] = $entry['name'] ?: $v->company_name;
            $entry['vacancy_count']++;
            $companies[$cnpj] = $entry;
        }

        return $companies;
    }

    /**
     * @param array<string, array<string, mixed>> $companies
     * @return array<string, mixed>
     */
    private function ensure(array &$companies, string $cnpj): array
    {
        return $companies[$cnpj] ?? [
            'cnpj'           => $cnpj,
            'uuid'           => $this->uuidForCnpj($cnpj),
            'cnpj_formatted' => $this->formatCnpj($cnpj),
            'name'           => null,
            'is_registered'  => false,
            'city'           => null,
            'vacancy_count'  => 0,
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

    private function uuidForCnpj(string $digits): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_OID, 'empreende-empresa:'.$digits)->toString();
    }

    private function formatCnpj(string $digits): string
    {
        if (strlen($digits) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $digits);
        }
        return $digits;
    }
}
