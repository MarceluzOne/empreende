<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountCpfException;
use App\Http\Requests\StoreAccountCpfRequest;
use App\Services\AccountCpfService;

/**
 * Tela em que o usuário de conta antiga informa o CPF antes de seguir no
 * portal. Quem chega aqui foi mandado pelo middleware EnsureAccountHasCpf.
 */
class PortalAccountCpfController extends Controller
{
    public function __construct(private AccountCpfService $service) {}

    public function show()
    {
        if (! empty(auth()->user()->cpf)) {
            return redirect()->route('portal.usuario');
        }

        return view('auth.usuario.cpf');
    }

    public function store(StoreAccountCpfRequest $request)
    {
        try {
            $this->service->assign($request->user(), $request->input('cpf'));
        } catch (AccountCpfException $e) {
            return back()->withErrors(['cpf' => $e->getMessage()])->withInput();
        }

        return redirect()->intended(route('portal.usuario'))->with('success', 'CPF salvo. Obrigado!');
    }
}
