<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->type === 'usuario') {
            return redirect()->route('portal.usuario');
        }
        return view('auth.usuario.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->where('type', 'usuario')->first();

        if (!$user) {
            $otherType = User::where('email', $request->email)->whereIn('type', ['empresa', 'funcionario'])->exists();
            $message = $otherType
                ? 'Este e-mail pertence a uma conta de empresa ou funcionário. Utilize o portal correto para acessar.'
                : 'E-mail ou senha inválidos.';
            return back()->withErrors(['email' => $message])->withInput($request->only('email'));
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->withInput($request->only('email'));
        }

        Auth::login($user);

        return redirect()->route('portal.usuario');
    }

    public function showRegister()
    {
        if (Auth::check() && Auth::user()->type === 'usuario') {
            return redirect()->route('portal.usuario');
        }
        return view('auth.usuario.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'type'     => 'usuario',
        ]);

        Auth::login($user);

        return redirect()->route('portal.usuario');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
