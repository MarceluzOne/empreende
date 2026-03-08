@extends('layouts.app')

@section('title', 'Editar Usuário - Empreende Vitória')

@section('content')
  <div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">
          <i class="fas fa-user-edit text-blue-900 mr-2"></i> Editar Usuário
        </h2>
        <p class="text-gray-600 italic">Atualize os dados de acesso de <strong>{{ $user->name }}</strong>.</p>
      </div>
      <a href="{{ route('users.index') }}" class="text-blue-900 hover:underline font-semibold flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Voltar
      </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
      <form action="{{ route('users.update', $user->id) }}" method="POST" class="p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nome Completo *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('name') border-red-500 @enderror"
              required>
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div class="md:col-span-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">E-mail de Acesso *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('email') border-red-500 @enderror"
              required>
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div class="md:col-span-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nível de Acesso *</label>
            <select name="role_id"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('role_id') border-red-500 @enderror"
              required>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ (old('role_id', $user->roles->first()->id ?? '') == $role->id) ? 'selected' : '' }}>
                  @if($role->name === 'admin')
                    Administrador
                  @elseif($role->name === 'employee')
                    Funcionário
                  @else
                    {{ strtoupper($role->name) }}
                  @endif
                </option>
              @endforeach
            </select>
            @error('role_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div class="md:col-span-2 border-t pt-4 mt-2">
            <h3 class="text-sm font-bold text-gray-400 uppercase">Alterar Senha (Opcional)</h3>
            <p class="text-xs text-gray-500 italic">Deixe em branco para manter a senha atual.</p>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nova Senha</label>
            <input type="password" name="password"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('password') border-red-500 @enderror">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Nova Senha</label>
            <input type="password" name="password_confirmation"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-4">
          <a href="{{ route('users.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
          <button type="submit"
            class="bg-blue-900 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-800 transition shadow-md">
            <i class="fas fa-save mr-2"></i> Atualizar Usuário
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection