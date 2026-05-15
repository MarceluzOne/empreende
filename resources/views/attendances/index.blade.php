@extends('layouts.app')

@section('title', 'Atendimentos - Empreende Vitória')

@section('content')
<div x-data="{ 
    openModal: false, 
    selectedAttendance: {} 
}">

    {{-- Cabeçalho e Ação --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Registro de Atendimentos</h1>
            <p class="text-sm text-gray-400 mt-0.5">Histórico de cidadãos atendidos no Empreende Vitória.</p>
        </div>
        <a href="{{ route('attendances.create') }}"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus text-xs sm:hidden"></i>
            <span class="hidden sm:inline">Novo Atendimento</span>
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('attendances.index') }}" class="mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Buscar por nome do cidadão..."
                class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">

            <select name="service_type" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">
                <option value="">Todos os serviços</option>
                @foreach($serviceTypes as $type)
                    <option value="{{ $type }}" {{ request('service_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>

            <select name="status" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">
                <option value="">Todos os status</option>
                <option value="scheduled"  {{ request('status') === 'scheduled'  ? 'selected' : '' }}>Agendado</option>
                <option value="completed"  {{ request('status') === 'completed'  ? 'selected' : '' }}>Concluído</option>
                <option value="pending"    {{ request('status') === 'pending'    ? 'selected' : '' }}>Pendente</option>
                <option value="forwarded"  {{ request('status') === 'forwarded'  ? 'selected' : '' }}>Encaminhado</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                <i class="fas fa-search mr-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['search','status','service_type']))
                <a href="{{ route('attendances.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition flex items-center">
                    <i class="fas fa-times mr-1"></i> Limpar
                </a>
            @endif
        </div>
    </form>

    {{-- Cards de Resumo --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center">
            <div class="w-12 h-12 bg-blue-100 text-blue-900 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-calendar-day fa-lg"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-bold">Atendimentos de Hoje</p>
                <p class="text-xl font-bold text-gray-800">{{ $attendances->where('scheduled_at', '>=', today())->where('scheduled_at', '<', today()->addDay())->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Tabela de Atendimentos --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Cidadão</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Serviço</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Data/Hora</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $attendance->customer_name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $attendance->customer_cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $attendance->customer_cpf) : 'Sem CPF' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700 bg-gray-100 px-2 py-1 rounded-md font-semibold">
                                    {{ $attendance->service_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusClasses = [
                                        'completed' => 'bg-green-100 text-green-700',
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                        'forwarded' => 'bg-blue-100 text-blue-700',
                                        'scheduled' => 'bg-purple-100 text-purple-700', // Roxo para agendados
                                    ];
                                    $statusLabels = [
                                        'completed' => 'Concluído',
                                        'pending'   => 'Pendente',
                                        'forwarded' => 'Encaminhado',
                                        'scheduled' => 'Agendado',
                                    ];
                                @endphp
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full {{ $statusClasses[$attendance->status] ?? 'bg-gray-100' }}">
                                    {{ $statusLabels[$attendance->status] ?? $attendance->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                
                                {{ $attendance->scheduled_at ? $attendance->scheduled_at->format('d/m/Y H:i') : $attendance->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-3">
                                    <button @click="selectedAttendance = {{ $attendance->toJson() }}; openModal = true"
                                            class="text-blue-900 hover:text-blue-700 transition" title="Ver Detalhes">
                                        <i class="fas fa-eye fa-lg"></i>
                                    </button>
                                    <a href="{{ route('attendances.edit', $attendance->id) }}" class="text-gray-400 hover:text-gray-600 transition">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </a>
                                    @if(auth()->user()->roles->contains('name', 'admin'))
                                    <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este atendimento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Excluir">
                                            <i class="fas fa-trash fa-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Nenhum atendimento registrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $attendances->links() }}
        </div>
    </div>

    {{-- MODAL DE DETALHES --}}
<div x-show="openModal" 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     x-transition x-cloak>
    <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="bg-blue-900 p-5 flex justify-between items-center text-white">
            <h3 class="font-bold text-xl"> Resumo do Atendimento</h3>
            <button @click="openModal = false" class="text-white hover:text-gray-300"><i class="fas fa-times fa-lg"></i></button>
        </div>
        <div class="p-8 space-y-6">
            <div class="flex justify-between items-start">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Cidadão</label>
                    <p class="text-lg text-gray-800 font-bold" x-text="selectedAttendance.customer_name"></p>
                </div>
                <div class="text-right">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Serviço</label>
                    <p class="text-sm font-bold text-blue-900" x-text="selectedAttendance.service_type"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">CPF</label>
                    <p class="text-sm text-gray-700 font-mono mt-1"
                        x-text="selectedAttendance.customer_cpf
                            ? selectedAttendance.customer_cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4')
                            : 'Não informado'">
                    </p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Telefone para Contato</label>
                    <p class="text-sm text-gray-700 mt-1"
                        x-text="selectedAttendance.customer_phone
                            ? selectedAttendance.customer_phone.replace(/(\d{2})(\d{1})(\d{4})(\d{4})/, '($1)$2 $3-$4')
                            : 'Não informado'">
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Descrição / Evolução</label>
                <p class="text-gray-700 text-sm mt-2 leading-relaxed italic" x-text="selectedAttendance.description"></p>
            </div>

            {{-- QUEM REALIZOU O AGENDAMENTO --}}
            <div class="flex items-center text-sm text-gray-600 bg-blue-50/50 p-3 rounded-lg border border-blue-100">
                <span class="font-semibold mr-1">Atendente:</span>
                <span x-text="selectedAttendance.user ? selectedAttendance.user.name : 'Não identificado'"></span>
            </div>

            <div class="flex justify-between text-[11px] text-gray-500 italic pt-2 border-t border-gray-100">
                <span>Compromisso em: <span class="font-bold" x-text="selectedAttendance.scheduled_at ? new Date(selectedAttendance.scheduled_at).toLocaleString('pt-BR') : new Date(selectedAttendance.created_at).toLocaleString('pt-BR')"></span></span>
            </div>
        </div>
        <div class="p-4 bg-gray-50 flex justify-end border-t">
            <button @click="openModal = false" class="bg-blue-900 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Fechar</button>
        </div>
    </div>
</div>
</div>
@endsection