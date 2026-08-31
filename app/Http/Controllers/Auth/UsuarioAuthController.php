<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUsuarioRequest;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioAuthController extends Controller
{
    public function __construct(private UserRegistrationService $registration) {}

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->type === 'usuario') {
            return $this->redirectToPortal(request('evento'));
        }
        return view('auth.usuario.login', ['evento' => request('evento')]);
    }

    /**
     * Redireciona para o portal do usuário. Se vier um id de evento (fluxo
     * "Garantir minha vaga" vindo de /cursos), abre já a aba de eventos e o
     * card do evento para inscrição.
     */
    private function redirectToPortal($evento)
    {
        if (!empty($evento)) {
            return redirect()->to(route('portal.usuario', ['evento' => $evento]) . '#eventos-disponiveis');
        }
        return redirect()->route('portal.usuario');
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
            return back()->withErrors(['email' => $message])->withInput($request->only('email', 'evento'));
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'E-mail ou senha inválidos.'])->withInput($request->only('email', 'evento'));
        }

        Auth::login($user);

        return $this->redirectToPortal($request->input('evento'));
    }

    public function showRegister()
    {
        if (Auth::check() && Auth::user()->type === 'usuario') {
            return redirect()->route('portal.usuario');
        }
        return view('auth.usuario.register');
    }

    public function register(RegisterUsuarioRequest $request)
    {
        $user = $this->registration->create($request->validated());

        Auth::login($user);

        return redirect()->route('portal.usuario')
            ->with('info', $this->registration->linkedSummary($user));
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
