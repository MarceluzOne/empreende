<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'E-mail ou senha inválidos.',
            ])->onlyInput('email');
        }

        if ($user->type !== 'funcionario') {
            $portal = match ($user->type) {
                'usuario'  => 'de candidatos (/login/usuario)',
                'empresa'  => 'de empresas (/login/empresa)',
                default    => 'correto',
            };
            return back()->withErrors([
                'email' => "Esta conta não tem acesso a este painel. Utilize o portal {$portal}.",
            ])->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/panel');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
