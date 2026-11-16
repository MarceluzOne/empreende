<?php

namespace App\Services;

use App\Mail\JobVacancyMail;
use App\Models\Empresa;
use App\Models\JobSeeker;
use App\Models\JobVacancy;
use App\Support\Document;
use App\Support\Phone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class JobVacancyService
{
    private function sanitizeRemuneration(?string $value): ?string
    {
        if (!$value) return null;
        // Remove tudo exceto dígitos e vírgula/ponto, depois normaliza para decimal
        $clean = preg_replace('/[^\d,.]/', '', $value);
        // Formato pt-BR: 1.500,00 → 1500.00
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
        return is_numeric($clean) ? $clean : null;
    }

    public function store(array $data): JobVacancy
    {
        $data['user_id']      = Auth::id();
        $data['cnpj']         = preg_replace('/[^0-9]/', '', $data['cnpj']);
        $data['benefits']     = $data['benefits'] ?? [];
        $data['remuneration'] = $this->sanitizeRemuneration($data['remuneration'] ?? null);

        return JobVacancy::create($data);
    }

    public function update(JobVacancy $vacancy, array $data): JobVacancy
    {
        $data['cnpj']         = preg_replace('/[^0-9]/', '', $data['cnpj']);
        $data['benefits']     = $data['benefits'] ?? [];
        $data['remuneration'] = $this->sanitizeRemuneration($data['remuneration'] ?? null);

        $vacancy->update($data);
        return $vacancy;
    }

    /**
     * Contato da empresa de cada vaga, para o modal do portal do usuário.
     *
     * A empresa é achada pelo CNPJ da vaga, e não por quem a criou: vaga
     * cadastrada pela equipe tem o FUNCIONÁRIO como `user_id`, e o modal
     * mostrava o e-mail dele como se fosse o da empresa (e telefone, cidade e
     * descrição vazios). Sem empresa cadastrada para o CNPJ, o contato fica
     * vazio e o modal mostra só o nome digitado na vaga.
     *
     * O e-mail é o login da conta da empresa — só quando a conta é mesmo do
     * tipo empresa.
     *
     * @param  iterable<int, JobVacancy> $vacancies
     * @return array<string, array{telefone: ?string, cidade: ?string, descricao: ?string, email: ?string}>
     *         indexado pelo id da vaga
     */
    public function companyContacts(iterable $vacancies): array
    {
        $vacancies = collect($vacancies)->filter();
        $empresas  = $this->empresasByCnpj($vacancies->pluck('cnpj'));

        return $vacancies->mapWithKeys(function (JobVacancy $vacancy) use ($empresas) {
            $empresa = $empresas[Document::digits($vacancy->cnpj)] ?? null;
            $conta   = optional($empresa)->user;

            return [$vacancy->id => [
                'telefone'  => $empresa ? Phone::format($empresa->telefone) : null,
                'cidade'    => optional($empresa)->cidade,
                'descricao' => optional($empresa)->descricao,
                'email'     => optional($conta)->type === 'empresa' ? $conta->email : null,
            ]];
        })->all();
    }

    /**
     * Empresas cadastradas para os CNPJs dados, indexadas pelos dígitos.
     * `empresas.cnpj` grava com máscara e `job_vacancies.cnpj` só dígitos,
     * daí as variantes.
     *
     * @param  iterable<int, ?string> $cnpjs
     * @return array<string, Empresa>
     */
    private function empresasByCnpj(iterable $cnpjs): array
    {
        $variants = collect($cnpjs)->flatMap(fn ($cnpj) => Document::cnpjVariants($cnpj))->unique()->values()->all();

        if ($variants === []) {
            return [];
        }

        return Empresa::with('user')->whereIn('cnpj', $variants)->get()
            ->keyBy(fn (Empresa $empresa) => Document::digits($empresa->cnpj))
            ->all();
    }

    public function destroy(JobVacancy $vacancy): void
    {
        $vacancy->delete();
    }

    public function notifyMatchingJobSeekers(JobVacancy $vacancy): int
    {
        $seekers = JobSeeker::where('interest_area', $vacancy->interest_area)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('status', 'active')
            ->get();

        foreach ($seekers as $seeker) {
            Mail::to($seeker->email)->send(new JobVacancyMail($vacancy, $seeker));
        }

        return $seekers->count();
    }
}
