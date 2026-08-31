<?php

namespace App\Http\Controllers;

use App\Exceptions\AssistedResetException;
use App\Services\AssistedPasswordResetService;
use App\Services\CitizenService;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function __construct(private CitizenService $service) {}

    public function index(Request $request)
    {
        // `cpf` é o nome antigo do campo, mantido para não quebrar links salvos.
        $search = $request->input('busca', $request->input('cpf'));

        return view('citizens.index', [
            'citizens' => $this->service->list($search),
            'search'   => $search,
        ]);
    }

    public function show(string $uuid)
    {
        $citizen = $this->service->detail($uuid);

        abort_if(!$citizen['candidato'] && $citizen['attendances']->isEmpty(), 404);

        return view('citizens.show', $citizen);
    }

    /**
     * Gera o link de redefinição de senha para o funcionário repassar.
     * As recusas são de negócio (sem conta, sem CPF, sem telefone) e voltam
     * como aviso na própria tela.
     */
    public function passwordLink(string $uuid, AssistedPasswordResetService $reset)
    {
        try {
            return back()->with('reset_link', $reset->linkForCitizen($uuid));
        } catch (AssistedResetException $e) {
            return back()->with('reset_error', $e->getMessage());
        }
    }
}
