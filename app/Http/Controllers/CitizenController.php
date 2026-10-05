<?php

namespace App\Http\Controllers;

use App\Exceptions\AssistedResetException;
use App\Http\Requests\UpdateCitizenRequest;
use App\Services\AssistedPasswordResetService;
use App\Services\CitizenService;
use App\Services\CitizenUpdateService;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function __construct(
        private CitizenService $service,
        private CitizenUpdateService $updater
    ) {}

    public function index(Request $request)
    {
        // `cpf` é o nome antigo do campo, mantido para não quebrar links salvos.
        $filters = [
            'busca' => $request->input('busca', $request->input('cpf')),
            'conta' => $request->input('conta'),
        ];

        return view('citizens.index', [
            'citizens'      => $this->service->list($filters),
            'search'        => $filters['busca'],
            'conta'         => $filters['conta'],
            'contasSemCpf'  => $this->service->accountsWithoutCpfCount(),
        ]);
    }

    public function show(string $uuid)
    {
        $citizen = $this->service->detail($uuid);

        abort_if($this->desconhecido($citizen), 404);

        return view('citizens.show', $citizen);
    }

    public function edit(string $uuid)
    {
        $citizen = $this->service->detail($uuid);

        abort_if($this->desconhecido($citizen), 404);

        return view('citizens.edit', $citizen);
    }

    public function update(UpdateCitizenRequest $request, string $uuid)
    {
        $cpf = $this->service->cpfForUuid($uuid);
        abort_if($cpf === null, 404);

        $novoCpf = $this->updater->update($cpf, $request->validated());

        return redirect()->route('citizens.show', $this->service->uuidFor($novoCpf))
            ->with('success', 'Dados do cidadão atualizados.');
    }

    /**
     * Gera o link de redefinição de senha para o atendente repassar.
     * As recusas são de negócio (sem conta, CPF incompleto, conta sem CPF) e
     * voltam como aviso na própria tela.
     */
    public function passwordLink(string $uuid, AssistedPasswordResetService $reset)
    {
        try {
            return back()->with('reset_link', $reset->linkForCitizen($uuid));
        } catch (AssistedResetException $e) {
            return back()->with('reset_error', $e->getMessage());
        }
    }

    /**
     * Ter só conta no portal já faz da pessoa um cidadão desta tela: é
     * justamente quem se cadastrou sozinho pelo site e nunca apareceu no
     * balcão que precisa ser alcançado aqui.
     *
     * @param array<string, mixed> $citizen
     */
    private function desconhecido(array $citizen): bool
    {
        return !$citizen['candidato']
            && $citizen['attendances']->isEmpty()
            && !$citizen['account'];
    }
}
