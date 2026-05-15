@extends('layouts.app')

@section('title', 'Eventos - Empreende Vitória')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Eventos</h1>
            <p class="text-sm text-gray-400 mt-0.5">Gerencie os eventos do Empreende Vitória.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('speakers.index') }}"
                class="flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-microphone text-xs"></i>
                <span class="hidden sm:inline">Palestrantes</span>
            </a>
            <a href="{{ route('events.create') }}"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus text-xs sm:hidden"></i>
                <span class="hidden sm:inline">Novo Evento</span>
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('events.index') }}" class="mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Buscar por título..."
                class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
            <input type="date" name="date" value="{{ request('date') }}"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">
            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                <i class="fas fa-search mr-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['search','date']))
                <a href="{{ route('events.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition flex items-center">
                    <i class="fas fa-times mr-1"></i> Limpar
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Evento</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Palestrante</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Data / Horário</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Vagas</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $event->title }}</div>
                                <div class="text-xs text-gray-500">{{ $event->duration_minutes }} min</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $event->speaker->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @php $dates = $event->allDates(); @endphp
                                @if(count($dates) > 1)
                                    {{ \Carbon\Carbon::parse($dates[0])->format('d/m/Y') }}
                                    <span class="text-xs text-gray-400">+{{ count($dates) - 1 }} dia(s)</span><br>
                                    <span class="text-xs">às {{ substr($event->start_time, 0, 5) }}</span>
                                @else
                                    {{ $event->date->format('d/m/Y') }} às {{ substr($event->start_time, 0, 5) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold {{ $event->isFull() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $event->participants->count() }}/{{ $event->max_capacity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($event->isFull())
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-red-100 text-red-700">Lotado</span>
                                @elseif($event->date->isPast())
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-gray-100 text-gray-600">Encerrado</span>
                                @else
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-green-100 text-green-700">Disponível</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:text-blue-900 transition" title="Ver evento">
                                        <i class="fas fa-eye fa-lg"></i>
                                    </a>
                                    <a href="{{ route('events.edit', $event) }}" class="text-yellow-600 hover:text-yellow-900 transition" title="Editar">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </a>
                                    @if(auth()->user()->roles->contains('name', 'admin'))
                                    <form action="{{ route('events.destroy', $event) }}" method="POST"
                                          onsubmit="return confirm('Tem certeza que deseja excluir este evento? A reserva do auditório também será removida.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Excluir">
                                            <i class="fas fa-trash-alt fa-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Nenhum evento cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection
