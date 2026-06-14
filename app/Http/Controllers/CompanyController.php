<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(private CompanyService $service) {}

    public function index(Request $request)
    {
        return view('companies.index', [
            'companies' => $this->service->list($request->input('cnpj')),
            'search'    => $request->input('cnpj'),
        ]);
    }

    public function show(string $uuid)
    {
        $company = $this->service->detail($uuid);

        abort_if(!$company['empresa'] && $company['vacancies']->isEmpty(), 404);

        return view('companies.show', $company);
    }
}
