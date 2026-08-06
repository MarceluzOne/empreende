<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\JobApplication;
use App\Models\JobSeeker;
use App\Models\JobVacancy;
use App\Models\ServiceProvider;
use App\Http\Requests\UpdateEmpresaRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateServiceProviderRequest;
use App\Services\EmpresaService;
use App\Services\JobVacancyService;
use App\Services\ServiceProviderService;
use App\Services\UserService;
use Illuminate\Http\Request;

class PortalEmpresaController extends Controller
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
        'Administração', 'Tecnologia da Informação', 'Saúde', 'Educação',
        'Construção Civil', 'Comércio e Vendas', 'Indústria', 'Logística',
        'Gastronomia', 'Serviços Gerais', 'Jurídico', 'Contabilidade / Finanças', 'Outros',
    ];

    public function __construct(
        private JobVacancyService $service,
        private ServiceProviderService $services
    ) {}

    public function index()
    {
        $user = auth()->user();

        $minhasVagas = JobVacancy::where('user_id', $user->id)
            ->withCount('applications')
            ->with(['applications.seeker'])
            ->latest()
            ->get();

        $totalCandidaturas = JobApplication::whereHas('vacancy', fn($q) => $q->where('user_id', $user->id))->count();

        // Cadastros feitos na página pública de Empresas Locais, reconhecidos
        // pelo CNPJ informado lá ou pelo e-mail da conta.
        $minhasVitrines = $this->services->forCompany($user->email, $this->companyCnpj());

        $empresa = Empresa::where('user_id', $user->id)->first();

        return view('portal.empresa.index', compact('minhasVagas', 'totalCandidaturas', 'minhasVitrines', 'empresa'));
    }

    // ── Conta ──────────────────────────────────────────────────

    public function updateDados(UpdateEmpresaRequest $request, EmpresaService $empresas)
    {
        $empresas->updateProfile($request->user(), $request->validated());

        return back()->with('success', 'Dados cadastrais atualizados com sucesso!');
    }

    public function updateSenha(UpdatePasswordRequest $request, UserService $users)
    {
        $users->changePassword($request->user(), $request->input('password'));

        return back()->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Edição do cadastro público de /empresas-locais pela própria empresa.
     * Volta para análise da equipe a cada alteração.
     */
    public function updateServico(UpdateServiceProviderRequest $request, ServiceProvider $service)
    {
        abort_unless($this->ownsService($service), 403, 'Este cadastro não pertence à sua conta.');

        $this->services->updateFromPortal(
            $service,
            $request->safe()->except('business_image'),
            $request->input('business_image')
        );

        return back()->with('success', 'Vitrine atualizada! O cadastro voltou para análise da equipe.');
    }

    private function ownsService(ServiceProvider $service): bool
    {
        return $this->services->belongsToCompany($service, auth()->user()->email, $this->companyCnpj());
    }

    /**
     * CNPJ da empresa logada em dígitos — a tabela empresas guarda com
     * máscara e service_providers guarda sem.
     */
    private function companyCnpj(): ?string
    {
        $cnpj = Empresa::where('user_id', auth()->id())->value('cnpj');
        $digits = $cnpj ? preg_replace('/\D/', '', $cnpj) : '';

        return $digits !== '' ? $digits : null;
    }

    public function createVaga()
    {
        $empresa = Empresa::where('user_id', auth()->id())->first();

        return view('portal.empresa.vagas.create', [
            'benefits'      => $this->benefits,
            'experiences'   => $this->experiences,
            'interestAreas' => $this->interestAreas,
            'empresa'       => $empresa,
        ]);
    }

    public function storeVaga(Request $request)
    {
        $request->validate([
            'cnpj'          => 'required|string|max:18',
            'company_name'  => 'required|string|max:255',
            'position'      => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'remuneration'  => 'nullable|string|max:100',
            'requirements'  => 'required|string',
            'benefits'      => 'nullable|array',
            'min_experience'=> 'nullable|string|max:50',
            'interest_area' => 'required|string|max:100',
        ], [
            'cnpj.required'         => 'O CNPJ é obrigatório.',
            'company_name.required' => 'O nome da empresa é obrigatório.',
            'position.required'     => 'O título da vaga é obrigatório.',
            'quantity.required'     => 'A quantidade de vagas é obrigatória.',
            'requirements.required' => 'Os requisitos da vaga são obrigatórios.',
            'interest_area.required'=> 'Selecione uma área de interesse.',
        ]);

        $this->service->store($request->all());

        return redirect()->route('portal.empresa')->with('success', 'Vaga publicada com sucesso!');
    }

    public function editVaga(JobVacancy $vaga)
    {
        abort_if($vaga->user_id !== auth()->id(), 403);

        return view('portal.empresa.vagas.edit', [
            'vaga'          => $vaga,
            'benefits'      => $this->benefits,
            'experiences'   => $this->experiences,
            'interestAreas' => $this->interestAreas,
        ]);
    }

    public function updateVaga(Request $request, JobVacancy $vaga)
    {
        abort_if($vaga->user_id !== auth()->id(), 403);

        $request->validate([
            'cnpj'          => 'required|string|max:18',
            'company_name'  => 'required|string|max:255',
            'position'      => 'required|string|max:255',
            'quantity'      => 'required|integer|min:1',
            'remuneration'  => 'nullable|string|max:100',
            'requirements'  => 'required|string',
            'benefits'      => 'nullable|array',
            'min_experience'=> 'nullable|string|max:50',
            'interest_area' => 'required|string|max:100',
            'status'        => 'required|in:active,inactive,filled',
        ], [
            'cnpj.required'         => 'O CNPJ é obrigatório.',
            'company_name.required' => 'O nome da empresa é obrigatório.',
            'position.required'     => 'O título da vaga é obrigatório.',
            'quantity.required'     => 'A quantidade de vagas é obrigatória.',
            'requirements.required' => 'Os requisitos da vaga são obrigatórios.',
            'interest_area.required'=> 'Selecione uma área de interesse.',
            'status.required'       => 'O status da vaga é obrigatório.',
        ]);

        $this->service->update($vaga, $request->all());

        $vaga->refresh();
        if ($vaga->quantity > 0 && $vaga->status === 'filled') {
            $vaga->update(['status' => 'active']);
        }

        return redirect()->route('portal.empresa')->with('success', 'Vaga atualizada com sucesso!');
    }

    public function destroyVaga(JobVacancy $vaga)
    {
        abort_if($vaga->user_id !== auth()->id(), 403);

        $this->service->destroy($vaga);

        return redirect()->route('portal.empresa')->with('success', 'Vaga excluída com sucesso!');
    }

    public function encerrarVaga(JobVacancy $vaga)
    {
        abort_if($vaga->user_id !== auth()->id(), 403);

        $vaga->update(['status' => 'inactive']);

        return back()->with('success', 'Vaga encerrada com sucesso.');
    }

    public function showCandidato(JobSeeker $jobSeeker)
    {
        $aplicacoes = $jobSeeker->applications()
            ->whereHas('vacancy', fn($q) => $q->where('user_id', auth()->id()))
            ->with('vacancy')
            ->latest()
            ->get();

        abort_if($aplicacoes->isEmpty(), 403);

        $aplicacao = $aplicacoes->first();

        return view('portal.empresa.candidato.show', compact('jobSeeker', 'aplicacao'));
    }
}
