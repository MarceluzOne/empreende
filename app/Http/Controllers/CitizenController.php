<?php

namespace App\Http\Controllers;

use App\Services\CitizenService;
use Illuminate\Http\Request;

class CitizenController extends Controller
{
    public function __construct(private CitizenService $service) {}

    public function index(Request $request)
    {
        $citizens = $this->service->list($request->input('cpf'));

        return view('citizens.index', [
            'citizens' => $citizens,
            'search'   => $request->input('cpf'),
        ]);
    }

    public function show(string $uuid)
    {
        $citizen = $this->service->detail($uuid);

        abort_if(!$citizen['candidato'] && $citizen['attendances']->isEmpty(), 404);

        return view('citizens.show', $citizen);
    }
}
