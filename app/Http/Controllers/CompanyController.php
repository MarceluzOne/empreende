<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\JobVacancy;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function editEmpresa(Empresa $empresa)
    {
        return view('companies.empresa-edit', ['empresa' => $empresa]);
    }

    public function updateEmpresa(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj'         => ['required', 'string', 'max:18', Rule::unique('empresas', 'cnpj')->ignore($empresa->id)],
            'telefone'     => 'nullable|string|max:20',
            'cidade'       => 'nullable|string|max:255',
            'descricao'    => 'nullable|string|max:2000',
        ]);

        $empresa->update($data);

        return redirect()
            ->route('companies.show', $this->service->uuidFor($empresa->cnpj))
            ->with('success', 'Dados da empresa atualizados.');
    }

    /**
     * Habilita/desabilita a empresa. Ao desabilitar, as vagas ATIVAS dela
     * (casadas pelo CNPJ) também são desativadas — saindo do site público;
     * ao habilitar, as que estavam inativas voltam. Vagas 'filled' não mudam.
     */
    public function toggleEmpresa(Empresa $empresa)
    {
        $empresa->update(['active' => ! $empresa->active]);

        $digits = preg_replace('/\D/', '', (string) $empresa->cnpj);
        $from   = $empresa->active ? 'inactive' : 'active';
        $to     = $empresa->active ? 'active' : 'inactive';

        JobVacancy::where('status', $from)->whereNotNull('cnpj')->get()
            ->filter(fn ($v) => preg_replace('/\D/', '', (string) $v->cnpj) === $digits)
            ->each(fn ($v) => $v->update(['status' => $to]));

        return back()->with('success', $empresa->active
            ? 'Empresa habilitada (vagas reativadas).'
            : 'Empresa desabilitada (vagas ocultadas do site).');
    }
}
