@extends('layouts.app')

@section('title', 'Gerenciar Equipe - Empreende Vitória')

@section('content')
{{-- Inicializamos o estado do Alpine.js para os dois modais --}}
<div x-data="{ 
    openModal: false, 
    openDeleteModal: false, 
    selectedUser: {}, 
    userToDelete: null,
    userToDeleteName: '' 
}">

    {{-- Cabeçalho da Página --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gerenciar Equipe</h1>
            <p class="text-sm text-gray-400 mt-0.5">Visualize e gerencie os acessos ao sistema.</p>
        </div>
        <a href="{{ route('users.create') }}"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus text-xs sm:hidden"></i>
            <span class="hidden sm:inline">Novo Usuário</span>
        </a>
    </div>

    {{-- Tabela de Usuários --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 w-16">#</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700">Nome</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700">E-mail</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Nível de Acesso</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                @foreach($user->roles as $role)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                                        {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                        @if($role->name === 'admin') ADMINISTRADOR @elseif($role->name === 'employee') FUNCIONÁRIO @else {{ strtoupper($role->name) }} @endif
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex justify-center space-x-4">
                                    {{-- Botão Visualizar --}}
                                    <button @click="selectedUser = {{ json_encode($user) }}; openModal = true"
                                        class="text-blue-600 hover:text-blue-900 transition" title="Visualizar Detalhes">
                                        <i class="fas fa-eye fa-lg"></i>
                                    </button>

                                    {{-- Editar --}}
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="text-yellow-600 hover:text-yellow-900 transition" title="Editar Usuário">
                                        <i class="fas fa-user-edit fa-lg"></i>
                                    </a>

                                    {{-- Excluir --}}
                                    @if($user->id !== auth()->id())
                                        <button @click="userToDelete = {{ $user->id }}; userToDeleteName = '{{ $user->name }}'; openDeleteModal = true"
                                            class="text-red-600 hover:text-red-900 transition" title="Remover Usuário">
                                            <i class="fas fa-user-minus fa-lg"></i>
                                        </button>

                                        {{-- Formulário oculto para o Delete --}}
                                        <form :id="'delete-form-' + {{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs font-bold self-center">VOCÊ</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                Nenhum outro usuário cadastrado no sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL DE DETALHES DO PERFIL --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-cloak>

        <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0"
            x-transition:enter-end="scale-100 opacity-100">

            <div class="bg-blue-900 p-5 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg"><i class="fas fa-user-circle mr-2"></i> Detalhes do Perfil</h3>
                <button @click="openModal = false" class="hover:text-gray-300">
                    <i class="fas fa-times fa-lg"></i>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <div class="text-center pb-4 border-b border-gray-100">
                    <div class="w-20 h-20 bg-blue-100 text-blue-900 rounded-full flex items-center justify-center mx-auto mb-3 text-3xl">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800" x-text="selectedUser.name"></h4>
                    <p class="text-gray-500" x-text="selectedUser.email"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg text-center font-semibold">
                        <span class="block text-xs font-bold text-gray-400 uppercase">Membro desde</span>
                        <p class="text-sm text-gray-800" x-text="new Date(selectedUser.created_at).toLocaleDateString('pt-BR')"></p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center font-semibold">
                        <span class="block text-xs font-bold text-gray-400 uppercase">ID</span>
                        <p class="text-sm text-gray-800" x-text="'#' + selectedUser.id"></p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 flex justify-end space-x-2 border-t">
                <button @click="openModal = false" class="bg-gray-200 px-6 py-2 rounded-lg font-bold hover:bg-gray-300 transition text-sm">Fechar</button>
                <a :href="'/users/' + selectedUser.id + '/edit'" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition text-sm flex items-center">
                    <i class="fas fa-edit mr-2"></i> Editar
                </a>
            </div>
        </div>
    </div>

    {{-- MODAL DE CONFIRMAÇÃO DE EXCLUSÃO --}}
        <div x-show="openDeleteModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-cloak>

            <div @click.away="openDeleteModal = false" class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border-t-4 border-red-600"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100">

                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt fa-2x"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Excluir Acesso?</h3>
                    <p class="text-gray-600 text-sm italic">
                        Tem certeza que deseja remover <span class="font-bold text-gray-900" x-text="userToDeleteName"></span>?
                    </p>
                </div>

                <div class="p-4 bg-gray-50 flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-2">
                    <button @click="openDeleteModal = false" class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition">Cancelar</button>
                    <button @click="document.getElementById('delete-form-' + userToDelete).submit()" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition">Confirmar</button>
                </div>
            </div>
        </div>
</div>
@endsection