@extends('layouts.app')

@section('title', 'Meu Perfil - Empreende Vitória')

@section('content')
  <div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">
          Meu Perfil
        </h2>
        <p class="text-gray-600 italic">Gerencie seus dados de acesso ao sistema.</p>
      </div>
      <a href="{{ route('dashboard') }}" class="text-blue-900 hover:underline font-semibold flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Voltar
      </a>
    </div>

    @if(session('success'))
      <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
        <i class="fas fa-check-circle text-green-500"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">

      {{-- Avatar / cabeçalho --}}
      <div class="bg-blue-900 px-8 py-6 flex items-center gap-5">
        <div class="w-16 h-16 rounded-full bg-blue-700 flex items-center justify-center text-2xl font-bold text-white uppercase">
          {{ mb_substr($user->name, 0, 1) }}
        </div>
        <div>
          <p class="text-white font-bold text-lg leading-tight">{{ $user->name }}</p>
          <span class="mt-1 inline-block text-xs bg-blue-700 text-blue-100 px-2 py-0.5 rounded-full">
            @if($user->roles->contains('name', 'admin'))
              Administrador
            @elseif($user->roles->contains('name', 'employee'))
              Funcionário
            @else
              Usuário
            @endif
          </span>
        </div>
      </div>

      <form action="{{ route('profile.update') }}" method="POST" class="p-8">
        @csrf
        @method('PUT')

        <div class="space-y-5">

          {{-- Nome --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-user text-gray-400 mr-1"></i> Nome Completo *
            </label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('name') border-red-500 @enderror"
              required>
            @error('name')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          {{-- E-mail --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-envelope text-gray-400 mr-1"></i> E-mail de Acesso *
            </label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('email') border-red-500 @enderror"
              required>
            @error('email')
              <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
          </div>

          {{-- Divider senha --}}
          <div class="border-t pt-5">
            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-1">Alterar Senha</h3>
            <p class="text-xs text-gray-500 italic mb-4">Deixe em branco para manter a senha atual.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nova Senha</label>
                <input type="password" name="password" autocomplete="new-password"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('password') border-red-500 @enderror">
                @error('password')
                  <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirmar Nova Senha</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
              </div>
            </div>
          </div>

        </div>

        <div class="mt-8 flex items-center justify-end space-x-4">
          <a href="{{ route('dashboard') }}" class="text-gray-600 hover:underline">Cancelar</a>
          <button type="submit"
            class="bg-blue-600 text-white px-8 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-sm">
              <i class="fas fa-check sm:hidden"></i>
              <span class="hidden sm:inline">Salvar Alterações</span>
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
