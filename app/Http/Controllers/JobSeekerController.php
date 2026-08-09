<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobSeekerRequest;
use App\Models\JobSeeker;
use App\Services\AuditService;
use App\Services\JobSeekerService;
use Illuminate\Http\Request;

class JobSeekerController extends Controller
{
    private array $experienceLevels = [
        'Sem experiência',
        'Até 6 meses',
        '6 meses a 1 ano',
        '1 ano a 2 anos',
        '2 anos a 3 anos',
        '3 anos a 5 anos',
        'Mais de 5 anos',
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

    public function __construct(private JobSeekerService $service) {}

    public function index(Request $request)
    {
        $seekers = JobSeeker::with('user')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('job_function', 'like', '%'.$request->search.'%');
            }))
            ->when($request->interest_area, fn($q) => $q->where('interest_area', $request->interest_area))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('job-seekers.index', [
            'seekers'       => $seekers,
            'interestAreas' => $this->interestAreas,
        ]);
    }

    public function create()
    {
        return view('job-seekers.create', [
            'experienceLevels' => $this->experienceLevels,
            'interestAreas'    => $this->interestAreas,
        ]);
    }

    public function store(StoreJobSeekerRequest $request)
    {
        $seeker = $this->service->store($request->all());
        AuditService::log('created', $seeker);

        return redirect()->route('job-seekers.index')->with('success', 'Cadastro realizado com sucesso!');
    }

    public function storeFromPortal(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'job_function'  => 'required|string|max:255',
            'interest_area' => 'required|string|max:100',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:2',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'summary'       => 'nullable|string|max:2000',
        ], [
            'name.required'          => 'O nome é obrigatório.',
            'job_function.required'  => 'Informe a função desejada.',
            'interest_area.required' => 'Selecione uma área de interesse.',
        ]);

        $this->service->store($request->all());

        return redirect()->route('portal.usuario')->with('success', 'Perfil criado com sucesso!');
    }

    public function edit(JobSeeker $jobSeeker)
    {
        return view('job-seekers.edit', [
            'seeker'           => $jobSeeker,
            'experienceLevels' => $this->experienceLevels,
            'interestAreas'    => $this->interestAreas,
        ]);
    }

    public function update(Request $request, JobSeeker $jobSeeker)
    {
        $request->validate([
            'name'                         => 'required|string|max:255',
            'job_function'                 => 'required|string|max:255',
            'city'                         => 'nullable|string|max:100',
            'state'                        => 'nullable|string|max:2',
            'phone'                        => 'nullable|string|max:20',
            'email'                        => 'nullable|email|max:255',
            'linkedin_url'                 => 'nullable|url|max:255',
            'github_url'                   => 'nullable|url|max:255',
            'summary'                      => 'nullable|string|max:2000',
            'skills'                       => 'nullable|string|max:1000',
            'interest_area'                => 'required|string|max:100',
            'experience'                   => 'nullable|string|max:50',
            'experiences'                  => 'nullable|array',
            'experiences.*.company'        => 'nullable|string|max:255',
            'experiences.*.role'           => 'nullable|string|max:255',
            'experiences.*.start'          => 'nullable|string|max:20',
            'experiences.*.end'            => 'nullable|string|max:20',
            'experiences.*.activities'     => 'nullable|string|max:2000',
            'education'                    => 'nullable|array',
            'education.*.course'           => 'nullable|string|max:255',
            'education.*.institution'      => 'nullable|string|max:255',
            'education.*.year'             => 'nullable|string|max:50',
            'languages'                    => 'nullable|array',
            'languages.*.language'         => 'nullable|string|max:100',
            'languages.*.level'            => 'nullable|string|max:50',
            'certifications'               => 'nullable|array',
            'certifications.*'             => 'nullable|string|max:255',
            'status'                       => 'required|in:active,inactive',
        ]);

        $this->service->update($jobSeeker, $request->all());
        AuditService::log('updated', $jobSeeker);

        return redirect()->route('job-seekers.index')
            ->with('success', "Cadastro de {$jobSeeker->name} atualizado com sucesso!");
    }

    public function show(JobSeeker $jobSeeker)
    {
        return response()->json($jobSeeker);
    }

    /**
     * Currículo do candidato em PDF, exibido inline (para o funcionário ver).
     */
    public function curriculo(JobSeeker $jobSeeker)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('job-seekers.curriculo-pdf', [
            'seeker' => $jobSeeker,
        ]);

        return $pdf->stream('curriculo-'.\Illuminate\Support\Str::slug($jobSeeker->name ?: 'candidato').'.pdf');
    }

    public function destroy(JobSeeker $jobSeeker)
    {
        $name = $jobSeeker->name;
        AuditService::log('deleted', $jobSeeker);
        $this->service->destroy($jobSeeker);

        return redirect()->route('job-seekers.index')
            ->with('success', "Cadastro de {$name} removido com sucesso!");
    }
}
