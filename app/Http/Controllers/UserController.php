<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->attach($validated['role_id']);

        return redirect()->route('users.index')->with('success', 'Usuário criado!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8|confirmed',
        'role_id'  => 'required|exists:roles,id'
    ], [
        'password.confirmed' => 'A confirmação da senha não confere.',
        'password.min'       => 'A nova senha deve ter pelo menos 8 caracteres.'
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];

    if ($request->filled('password')) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    $user->roles()->sync([$validated['role_id']]);

    return redirect()->route('users.index')
        ->with('success', "Usuário {$user->name} atualizado com sucesso!");
}

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir a si mesmo!');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário removido!');
    }
}