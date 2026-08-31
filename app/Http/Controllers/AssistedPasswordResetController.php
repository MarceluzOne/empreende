<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssistedPasswordResetRequest;
use App\Services\AssistedPasswordResetService;

/**
 * Tela pública aberta pelo link que o funcionário gerou no atendimento.
 * Não mostra nome nem e-mail: quem abrir precisa saber o CPF da conta.
 */
class AssistedPasswordResetController extends Controller
{
    public function __construct(private AssistedPasswordResetService $service)
    {
    }

    public function show(string $token)
    {
        return view('auth.assisted-reset', ['token' => $token]);
    }

    public function update(AssistedPasswordResetRequest $request)
    {
        $done = $this->service->reset(
            $request->input('token'),
            $request->input('cpf'),
            $request->input('password')
        );

        return $done ? $this->success() : $this->failure();
    }

    private function success()
    {
        return redirect()->route('usuario.login')
            ->with('status', 'Senha alterada com sucesso! Entre com a senha nova.');
    }

    /**
     * Mensagem única para CPF errado, link vencido e link já usado: separar os
     * casos diria a um estranho se aquele CPF tem conta no portal.
     */
    private function failure()
    {
        return back()->withErrors([
            'cpf' => 'Não foi possível redefinir a senha. Confira o CPF digitado ou peça um link novo no atendimento.',
        ]);
    }
}
