<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    public function handle(Request $request, Closure $next, string $type)
    {
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Acesso não autorizado.');
        }

        $user = auth()->user();

        if ($user->type !== $type) {
            return match ($user->type) {
                'empresa'  => redirect()->route('portal.empresa')->with('error', 'Acesso restrito a funcionários.'),
                'usuario'  => redirect()->route('portal.usuario')->with('error', 'Acesso restrito a funcionários.'),
                default    => redirect('/')->with('error', 'Acesso não autorizado.'),
            };
        }

        return $next($request);
    }
}
