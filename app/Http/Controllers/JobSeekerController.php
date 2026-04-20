<?php

namespace App\Http\Controllers;

use App\Models\JobSeeker;
use App\Services\JobSeekerService;
use Illuminate\Http\Request;

class JobSeekerController extends Controller
{
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

    public function __construct(private JobSeekerService $service) {}

    public function index(Request $request)
    {
        $seekers = JobSeeker::with('user')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'ilike', '%'.$request->search.'%')
                  ->orWhere('job_function', 'ilike', '%'.$request->search.'%');
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
            'experiences'   => $this->experiences,
            'interestAreas' => $this->interestAreas,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'cpf'           => 'required|string|max:14',
            'job_function'  => 'required|string|max:255',
            'experience'    => 'nullable|string|max:50',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'interest_area' => 'required|string|max:100',
            'curriculo'     => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'name.required'          => 'O nome é obrigatório.',
            'cpf.required'           => 'O CPF é obrigatório.',
            'job_function.required'  => 'Informe a função desejada.',
            'interest_area.required' => 'Selecione uma área de interesse.',
            'curriculo.mimes'        => 'O currículo deve ser um arquivo PDF.',
            'curriculo.max'          => 'O currículo não pode ultrapassar 5MB.',
        ]);

        $this->service->store($request->except('curriculo'), $request->file('curriculo'));

        return redirect()->route('job-seekers.index')->with('success', 'Cadastro realizado com sucesso!');
    }

    public function edit(JobSeeker $jobSeeker)
    {
        return view('job-seekers.edit', [
            'seeker'        => $jobSeeker,
            'experiences'   => $this->experiences,
            'interestAreas' => $this->interestAreas,
        ]);
    }

    public function update(Request $request, JobSeeker $jobSeeker)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'cpf'           => 'required|string|max:14',
            'job_function'  => 'required|string|max:255',
            'experience'    => 'nullable|string|max:50',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'interest_area' => 'required|string|max:100',
            'status'        => 'required|in:active,inactive',
            'curriculo'     => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'curriculo.mimes' => 'O currículo deve ser um arquivo PDF.',
            'curriculo.max'   => 'O currículo não pode ultrapassar 5MB.',
        ]);

        $this->service->update($jobSeeker, $request->except('curriculo'), $request->file('curriculo'));

        return redirect()->route('job-seekers.index')
            ->with('success', "Cadastro de {$jobSeeker->name} atualizado com sucesso!");
    }

    public function show(JobSeeker $jobSeeker)
    {
        return response()->json($jobSeeker);
    }

    public function destroy(JobSeeker $jobSeeker)
    {
        $name = $jobSeeker->name;
        $this->service->destroy($jobSeeker);

        return redirect()->route('job-seekers.index')
            ->with('success', "Cadastro de {$name} removido com sucesso!");
    }
}
