<?php

namespace App\Http\Controllers;

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
}
