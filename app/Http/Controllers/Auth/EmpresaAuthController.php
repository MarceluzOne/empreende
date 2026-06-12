<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpresaAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->type === 'empresa') {
            return redirect()->route('portal.empresa');
        }
        return view('auth.empresa.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->where('type', 'empresa')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->withInput($request->only('email'));
        }

        Auth::login($user);

        return redirect()->route('portal.empresa');
    }

    public function showRegister()
    {
        if (Auth::check() && Auth::user()->type === 'empresa') {
            return redirect()->route('portal.empresa');
        }
        return view('auth.empresa.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'razao_social'          => 'required|string|max:255',
            'cnpj'                  => 'required|string|max:18|unique:empresas,cnpj',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'telefone'              => 'nullable|string|max:20',
            'cidade'                => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->razao_social,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'type'     => 'empresa',
            ]);

            Empresa::create([
                'user_id'      => $user->id,
                'razao_social' => $request->razao_social,
                'cnpj'         => $request->cnpj,
                'telefone'     => $request->telefone,
                'cidade'       => $request->cidade,
            ]);

            Auth::login($user);
        });

        return redirect()->route('portal.empresa');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
