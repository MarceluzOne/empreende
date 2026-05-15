@extends('layouts.app')

@section('title', 'Vagas de Emprego')

@section('content')
<div
    x-data="{
        openModal: false,
        openDeleteModal: false,
        openNotifyModal: false,
        selectedVacancy: {},
        vacancyToDelete: null,
        vacancyToDeleteName: '',
        vacancyToNotify: null,
        vacancyToNotifyName: '',
        vacancyToNotifyArea: '',
        showVacancy(data) {
            this.selectedVacancy = data;
            this.openModal = true;
        },
        prepDelete(id, name) {
            this.vacancyToDelete = id;
            this.vacancyToDeleteName = name;
            this.openDeleteModal = true;
        },
        prepNotify(id, name, area) {
            this.vacancyToNotify = id;
            this.vacancyToNotifyName = name;
            this.vacancyToNotifyArea = area;
            this.openNotifyModal = true;
        }
    }"
>

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vagas de Emprego</h1>
            <p class="text-sm text-gray-400 mt-0.5">Gerencie as vagas cadastradas pelas empresas</p>
        </div>
        <a href="{{ route('job-vacancies.create') }}"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus text-xs sm:hidden"></i>
            <span class="hidden sm:inline">Nova Vaga</span>
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('job-vacancies.index') }}"
        class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Buscar</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Empresa ou cargo..."
                    class="w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>
        </div>
        <div class="min-w-[150px]">
            <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Status</label>
            <select name="status"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                <option value="">Todos</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Ativa</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativa</option>
                <option value="filled"   {{ request('status') === 'filled'   ? 'selected' : '' }}>Preenchida</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                Filtrar
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('job-vacancies.index') }}"
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
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Empresa</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Vaga</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Qtd</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Remuneração</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($vacancies as $vacancy)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $vacancies->firstItem() + $loop->index }}</td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800">{{ $vacancy->company_name }}</p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $vacancy->formatted_cnpj }}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $vacancy->position }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-bold text-sm">
                            {{ $vacancy->quantity }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">
                        {{ $vacancy->remuneration ?? 'A combinar' }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $badge = match($vacancy->status) {
                                'active'   => 'bg-green-100 text-green-700',
                                'inactive' => 'bg-red-100 text-red-700',
                                'filled'   => 'bg-gray-100 text-gray-600',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                            {{ $vacancy->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <button @click="showVacancy({{ $vacancy->toJson() }})"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Visualizar">
                                <i class="fas fa-eye fa-lg"></i>
                            </button>
                            <a href="{{ route('job-vacancies.edit', $vacancy) }}"
                                class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Editar">
                                <i class="fas fa-edit fa-lg"></i>
                            </a>
                            <button @click="prepDelete({{ $vacancy->id }}, '{{ addslashes($vacancy->position) }}')"
                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Remover">
                                <i class="fas fa-trash-alt fa-lg"></i>
                            </button>
                            {{-- <button @click="prepNotify({{ $vacancy->id }}, '{{ addslashes($vacancy->position) }}', '{{ addslashes($vacancy->interest_area ?? '') }}')"
                                class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition" title="Notificar candidatos por e-mail">
                                <i class="fas fa-envelope text-sm"></i>
                            </button> --}}
                            <form id="delete-form-{{ $vacancy->id }}"
                                action="{{ route('job-vacancies.destroy', $vacancy) }}"
                                method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                            <form id="notify-form-{{ $vacancy->id }}"
                                action="{{ route('job-vacancies.notify', $vacancy) }}"
                                method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                        Nenhuma vaga encontrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($vacancies->hasPages())
        <div class="mt-4">{{ $vacancies->links() }}</div>
    @endif

    {{-- Detail Modal --}}
    <div x-show="openModal" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="openModal = false">
        <div @click.outside="openModal = false"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-blue-900 text-white px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center">
                        <i class="fas fa-briefcase text-sm"></i>
                    </div>
                    <div>
                        <p x-text="selectedVacancy.position" class="font-bold text-base"></p>
                        <p x-text="selectedVacancy.company_name" class="text-blue-300 text-xs"></p>
                    </div>
                </div>
                <button @click="openModal = false" class="text-blue-200 hover:text-white transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- Status + CNPJ --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Status</p>
                        <p x-text="selectedVacancy.status_label"
                            :class="{
                                'text-green-700 bg-green-100': selectedVacancy.status === 'active',
                                'text-red-700 bg-red-100': selectedVacancy.status === 'inactive',
                                'text-gray-600 bg-gray-100': selectedVacancy.status === 'filled'
                            }"
                            class="inline-block px-2 py-1 rounded-full text-xs font-semibold">
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">CNPJ</p>
                        <p x-text="selectedVacancy.formatted_cnpj" class="text-gray-700 font-mono text-sm"></p>
                    </div>
                </div>

                {{-- Vagas + Remuneração --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Qtd. de Vagas</p>
                        <p x-text="selectedVacancy.quantity" class="text-gray-800 font-semibold"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Remuneração</p>
                        <p x-text="selectedVacancy.remuneration || 'A combinar'" class="text-gray-800"></p>
                    </div>
                </div>

                {{-- Experiência + Área de Interesse --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Experiência Mínima</p>
                        <p x-text="selectedVacancy.min_experience || 'Sem requisito'" class="text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Área de Interesse</p>
                        <p x-text="selectedVacancy.interest_area || '—'" class="text-gray-800"></p>
                    </div>
                </div>

                {{-- Requisitos --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-1">Requisitos</p>
                    <p x-text="selectedVacancy.requirements" class="text-gray-700 text-sm leading-relaxed"
                        style="white-space: pre-wrap"></p>
                </div>

                {{-- Benefícios --}}
                <div x-show="selectedVacancy.benefits && selectedVacancy.benefits.length > 0">
                    <p class="text-xs font-semibold text-gray-400 uppercase mb-2">Benefícios</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="benefit in (selectedVacancy.benefits || [])" :key="benefit">
                            <span x-text="benefit"
                                class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium border border-blue-100">
                            </span>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Notify Modal --}}
    <div x-show="openNotifyModal" x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        @keydown.escape.window="openNotifyModal = false">
        <div @click.outside="openNotifyModal = false"
            class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border-t-4 border-green-600">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-2xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Notificar Candidatos</h3>
                <p class="text-gray-500 text-sm mb-2">
                    Deseja enviar e-mail sobre a vaga <strong x-text="vacancyToNotifyName"></strong>
                    para todos os candidatos cadastrados na área
                    <strong x-text="vacancyToNotifyArea"></strong>?
                </p>
                <p class="text-gray-400 text-xs mb-6">Apenas candidatos com e-mail cadastrado e status ativo receberão a mensagem.</p>
                <div class="flex gap-3 justify-center">
                    <button @click="openNotifyModal = false"
                        class="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button @click="document.getElementById('notify-form-' + vacancyToNotify).submit()"
                        class="px-5 py-2 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition">
                        Sim, enviar
                    </button>
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
                <h3 class="text-lg font-bold text-gray-800 mb-2">Remover Vaga</h3>
                <p class="text-gray-500 text-sm mb-6">
                    Deseja remover a vaga <strong x-text="vacancyToDeleteName"></strong>?
                    Esta ação não pode ser desfeita.
                </p>
                <div class="flex gap-3 justify-center">
                    <button @click="openDeleteModal = false"
                        class="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                        Cancelar
                    </button>
                    <button @click="document.getElementById('delete-form-' + vacancyToDelete).submit()"
                        class="px-5 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition">
                        Sim, remover
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
