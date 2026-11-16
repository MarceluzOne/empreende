<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Conta de usuário sem CPF não passa do portal sem informá-lo.
 *
 * O cadastro pelo site só passou a pedir CPF em agosto/2026; as contas
 * anteriores ficaram sem, e sem CPF não há como o atendimento gerar link de
 * senha para elas. Pedir no login resolve aos poucos quem ainda usa o portal.
 *
 * O endereço pedido é guardado para a pessoa voltar a ele depois — o fluxo
 * "Garantir minha vaga" chega ao portal com o evento na URL.
 */
class EnsureAccountHasCpf
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || $user->type !== 'usuario' || ! empty($user->cpf)) {
            return $next($request);
        }

        if ($request->routeIs('portal.usuario.cpf', 'portal.usuario.cpf.store')) {
            return $next($request);
        }

        if ($request->isMethod('GET')) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return redirect()->route('portal.usuario.cpf');
    }
}
