<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCitizenRequest;
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

    public function edit(string $uuid)
    {
        $citizen = $this->service->detail($uuid);

        abort_if(!$citizen['candidato'] && $citizen['attendances']->isEmpty(), 404);

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
}
