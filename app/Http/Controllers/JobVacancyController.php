<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Services\JobVacancyService;
use Illuminate\Http\Request;

class JobVacancyController extends Controller
{
    private array $benefits = [
        'Gympass', 'Wellhub', 'Vale Refeição', 'Vale Transporte',
        'Plano de Saúde', 'Plano Odontológico', 'Home Office',
        'Seguro de Vida', 'Bônus', 'PLR', 'Auxílio Creche', 'Auxílio Educação',
    ];

    private array $experiences = [
        'Sem experiência', '6 meses', '1 ano', '2 anos', '3 anos', '5 anos ou mais',
    ];

    private array $interestAreas = [
        'Administração',
        'Tecnologia da Informação',
        'Saúde',
        'Educação',
        'Construção Civil',
        'Comércio e Vendas',
        'Indústria',
        'Logística',
        'Gastronomia',
        'Serviços Gerais',
        'Jurídico',
        'Contabilidade / Finanças',
        'Outros',
    ];

    public function __construct(private JobVacancyService $service) {}

    public function index(Request $request)
    {
        $vacancies = JobVacancy::with('user')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('company_name', 'ilike', '%'.$request->search.'%')
                  ->orWhere('position', 'ilike', '%'.$request->search.'%');
            }))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('job-vacancies.index', compact('vacancies'));
    }

    public function create()
    {
        return view('job-vacancies.create', [
            'benefits'      => $this->benefits,
            'experiences'   => $this->experiences,
            'interestAreas' => $this->interestAreas,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cnpj'           => 'required|string|max:18',
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'quantity'       => 'required|integer|min:1',
            'remuneration'   => 'nullable|string|max:100',
            'requirements'   => 'required|string',
            'benefits'       => 'nullable|array',
            'min_experience' => 'nullable|string|max:50',
            'interest_area'  => 'required|string|max:100',
        ], [
            'cnpj.required'           => 'O CNPJ é obrigatório.',
            'company_name.required'   => 'O nome da empresa é obrigatório.',
            'position.required'       => 'O título da vaga é obrigatório.',
            'quantity.required'       => 'Informe a quantidade de vagas.',
            'requirements.required'   => 'Descreva os requisitos da vaga.',
            'interest_area.required'  => 'Selecione uma área de interesse.',
        ]);

        $this->service->store($request->all());

        return redirect()->route('job-vacancies.index')->with('success', 'Vaga cadastrada com sucesso!');
    }

    public function edit(JobVacancy $jobVacancy)
    {
        return view('job-vacancies.edit', [
            'vacancy'       => $jobVacancy,
            'benefits'      => $this->benefits,
            'experiences'   => $this->experiences,
            'interestAreas' => $this->interestAreas,
        ]);
    }

    public function update(Request $request, JobVacancy $jobVacancy)
    {
        $request->validate([
            'cnpj'           => 'required|string|max:18',
            'company_name'   => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'quantity'       => 'required|integer|min:1',
            'remuneration'   => 'nullable|string|max:100',
            'requirements'   => 'required|string',
            'benefits'       => 'nullable|array',
            'min_experience' => 'nullable|string|max:50',
            'interest_area'  => 'required|string|max:100',
            'status'         => 'required|in:active,inactive,filled',
        ]);

        $this->service->update($jobVacancy, $request->all());

        return redirect()->route('job-vacancies.index')
            ->with('success', "Vaga \"{$jobVacancy->position}\" atualizada com sucesso!");
    }

    public function show(JobVacancy $jobVacancy)
    {
        return response()->json($jobVacancy);
    }

    public function notify(JobVacancy $jobVacancy)
    {
        if (! $jobVacancy->interest_area) {
            return back()->with('error', 'Esta vaga não possui área de interesse definida.');
        }

        $count = $this->service->notifyMatchingJobSeekers($jobVacancy);

        if ($count === 0) {
            return back()->with('info', 'Nenhum candidato com e-mail cadastrado encontrado para a área de interesse desta vaga.');
        }

        return back()->with('success', "{$count} candidato(s) notificado(s) sobre a vaga \"{$jobVacancy->position}\".");
    }

    public function destroy(JobVacancy $jobVacancy)
    {
        $this->service->destroy($jobVacancy);

        return redirect()->route('job-vacancies.index')->with('success', 'Vaga removida com sucesso!');
    }
}
