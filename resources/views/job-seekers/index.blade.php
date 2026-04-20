@extends('layouts.app')

@section('title', 'Banco de Talentos')

@section('content')
<div
    x-data="{
        openModal: false,
        openDeleteModal: false,
        selectedSeeker: {},
        seekerToDelete: null,
        seekerToDeleteName: '',
        showSeeker(data) {
            this.selectedSeeker = data;
            this.openModal = true;
        },
        prepDelete(id, name) {
            this.seekerToDelete = id;
            this.seekerToDeleteName = name;
            this.openDeleteModal = true;
        }
    }"
>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Banco de Talentos</h1>
            <p class="text-sm text-gray-500 mt-1">Cadastro de pessoas em busca de oportunidades</p>
        </div>
        <a href="{{ route('job-seekers.create') }}"
            class="flex items-center gap-2 bg-blue-900 text-white px-4 py-2 rounded-xl shadow hover:bg-blue-800 transition text-sm font-semibold">
            Novo Cadastro
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('job-seekers.index') }}"
        class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Buscar</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nome ou função..."
                    class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
        </div>
        <div class="min-w-[180px]">
            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Área de Interesse</label>
            <select name="interest_area"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">Todas</option>
                @foreach($interestAreas as $area)
                    <option value="{{ $area }}" {{ request('interest_area') === $area ? 'selected' : '' }}>
                        {{ $area }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[130px]">
            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Status</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">Todos</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-semibold hover:bg-blue-800 transition">
                Filtrar
            </button>
            @if(request('search') || request('interest_area') || request('status'))
                <a href="{{ route('job-seekers.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200 transition">
                    Limpar
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Função</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Área</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Experiência</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Currículo</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($seekers as $seeker)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $seekers->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800">{{ $seeker->name }}</p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $seeker->formatted_cpf }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $seeker->job_function }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell text-xs">{{ $seeker->interest_area }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden lg:table-cell text-xs">
                        {{ $seeker->experience ?? 'Sem requisito' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($seeker->curriculo_path)
                            <a href="{{ $seeker->curriculo_url }}" target="_blank"
                                class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $seeker->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $seeker->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <button @click="showSeeker({{ $seeker->toJson() }})"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Visualizar">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            <a href="{{ route('job-seekers.edit', $seeker) }}"
                                class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Editar">
                                <i class="fas fa-pencil-alt text-sm"></i>
                            </a>
                            <button @click="prepDelete({{ $seeker->id }}, '{{ addslashes($seeker->name) }}')"
                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Remover">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                            <form id="delete-form-{{ $seeker->id }}"
                                action="{{ route('job-seekers.destroy', $seeker) }}"
                                method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                        Nenhum cadastro encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($seekers->hasPages())
        <div class="mt-4">{{ $seekers->links() }}</div>
    @endif

    {{-- Detail Modal --}}
    <div x-show="openModal" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="openModal = false">
        <div @click.outside="openModal = false"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-blue-900 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center font-bold text-lg"
                        x-text="selectedSeeker.name ? selectedSeeker.name.charAt(0).toUpperCase() : ''"></div>
                    <div>
                        <p x-text="selectedSeeker.name" class="font-bold text-base"></p>
                        <p x-text="selectedSeeker.job_function" class="text-blue-300 text-xs"></p>
                    </div>
                </div>
                <button @click="openModal = false" class="text-blue-200 hover:text-white transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- Status + Área --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Status</p>
                        <p x-text="selectedSeeker.status_label"
                            :class="selectedSeeker.status === 'active' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100'"
                            class="inline-block px-2 py-1 rounded-full text-xs font-semibold"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Área de Interesse</p>
                        <p x-text="selectedSeeker.interest_area" class="text-gray-700 text-sm"></p>
                    </div>
                </div>

                {{-- CPF + Telefone --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">CPF</p>
                        <p x-text="selectedSeeker.formatted_cpf" class="text-gray-700 font-mono text-sm"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Telefone</p>
                        <p x-text="selectedSeeker.formatted_phone || '—'" class="text-gray-700 text-sm"></p>
                    </div>
                </div>

                {{-- Email + Experiência --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">E-mail</p>
                        <p x-text="selectedSeeker.email || '—'" class="text-gray-700 text-sm break-all"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Experiência</p>
                        <p x-text="selectedSeeker.experience || 'Sem requisito'" class="text-gray-700 text-sm"></p>
                    </div>
                </div>

                {{-- Currículo --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Currículo</p>
                    <template x-if="selectedSeeker.curriculo_url">
                        <a :href="selectedSeeker.curriculo_url" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-semibold hover:bg-red-100 transition">
                            <i class="fas fa-file-pdf text-lg"></i>
                            Abrir / Baixar Currículo (PDF)
                        </a>
                    </template>
                    <template x-if="!selectedSeeker.curriculo_url">
                        <p class="text-gray-400 text-sm italic">Nenhum currículo anexado.</p>
                    </template>
                </div>

            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="openDeleteModal" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="openDeleteModal = false">
        <div @click.outside="openDeleteModal = false"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border-t-4 border-red-600">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt text-2xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Remover Cadastro</h3>
                <p class="text-gray-500 text-sm mb-6">
                    Deseja remover o cadastro de <strong x-text="seekerToDeleteName"></strong>?
                    O currículo também será excluído permanentemente.
                </p>
                <div class="flex gap-3 justify-center">
                    <button @click="openDeleteModal = false"
                        class="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button @click="document.getElementById('delete-form-' + seekerToDelete).submit()"
                        class="px-5 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition">
                        Sim, remover
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
