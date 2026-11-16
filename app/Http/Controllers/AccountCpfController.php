<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountCpfException;
use App\Http\Requests\StoreAccountCpfRequest;
use App\Models\User;
use App\Services\AccountCpfService;
use App\Services\CitizenService;
use Illuminate\Http\Request;

/**
 * Contas do portal sem CPF, para o atendente completar.
 *
 * É o caminho de quem perdeu a senha e por isso não passa pelo pedido de CPF
 * do login: o atendente confere o documento com a pessoa, grava aqui e cai
 * direto no cadastro do cidadão, onde gera o link.
 *
 * Mesma permissão do link de senha (`gerar-link-senha`), porque gravar o CPF
 * é o passo que autoriza esse link.
 */
class AccountCpfController extends Controller
{
    public function __construct(private AccountCpfService $service) {}

    public function index(Request $request)
    {
        return view('account-cpf.index', [
            'accounts' => $this->service->withoutCpf($request->input('busca')),
            'search'   => $request->input('busca'),
        ]);
    }

    public function store(StoreAccountCpfRequest $request, User $user, CitizenService $citizens)
    {
        try {
            $cpf = $this->service->assign($user, $request->input('cpf'), true);
        } catch (AccountCpfException $e) {
            return back()->withErrors(['cpf' => $e->getMessage()])->withInput();
        }

        return redirect()->route('citizens.show', $citizens->uuidFor($cpf))
            ->with('success', "CPF gravado na conta de {$user->name}. Agora já dá para gerar o link de senha.");
    }
}
