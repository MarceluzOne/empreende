@extends('layouts.app')

@section('title', 'Novo Usuário - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Cabeçalho --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-user-plus text-blue-900 mr-2"></i> Cadastrar Novo Usuário
            </h2>
            <p class="text-gray-600 italic">Crie um acesso para um novo colaborador do sistema.</p>
        </div>
        <a href="{{ route('users.index') }}" class="text-blue-900 hover:underline font-semibold flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Voltar para Lista
        </a>
    </div>

    {{-- Formulário --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <form action="{{ route('users.store') }}" method="POST" class="p-8 md:p-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Nome Completo --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nome Completo *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('name') border-red-500 @enderror" 
                            placeholder="Ex: José Silva" required>
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">E-mail de Acesso *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('email') border-red-500 @enderror" 
                            placeholder="exemplo@email.com" required>
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Nível de Acesso com Tradução --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nível de Acesso *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <select name="role_id" class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white appearance-none transition" required>
                            <option value="" disabled selected>Selecione o cargo</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name === 'admin' ? 'Administrador' : ($role->name === 'employee' ? 'Funcionário' : strtoupper($role->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Senha --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Senha Temporária *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('password') border-red-500 @enderror"
                            placeholder="Mínimo 8 caracteres" required>
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Confirmar Senha --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Confirmar Senha *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-check-double"></i>
                        </span>
                        <input type="password" name="password_confirmation" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition"
                            placeholder="Repita a senha" required>
                    </div>
                </div>
            </div>

            {{-- Botões --}}
            <div class="mt-10 flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-gray-700 font-semibold transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg flex items-center">
                    <i class="fas fa-user-check sm:hidden"></i>
                    <span class="hidden sm:inline">Criar Usuário</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection