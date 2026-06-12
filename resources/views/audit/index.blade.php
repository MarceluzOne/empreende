@extends('layouts.app')

@section('title', 'Auditoria - Empreende Vitória')

@section('content')
<div>
    {{-- Cabeçalho --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Auditoria do Sistema</h1>
            <p class="text-sm text-gray-400 mt-0.5">Registro de todas as ações realizadas pelos usuários.</p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('audit.index') }}" class="mb-6">
        <div class="flex flex-col md:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Buscar por descrição..."
                class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">

            <select name="user_id" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">
                <option value="">Todos os usuários</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            <select name="action" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">
                <option value="">Todas as ações</option>
                <option value="created"  {{ request('action') === 'created'  ? 'selected' : '' }}>Criação</option>
                <option value="updated"  {{ request('action') === 'updated'  ? 'selected' : '' }}>Atualização</option>
                <option value="deleted"  {{ request('action') === 'deleted'  ? 'selected' : '' }}>Exclusão</option>
            </select>

            <input type="date" name="date" value="{{ request('date') }}"
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">

            <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                <i class="fas fa-search mr-1"></i> Filtrar
            </button>
            @if(request()->hasAny(['search','user_id','action','date']))
                <a href="{{ route('audit.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition flex items-center">
                    <i class="fas fa-times mr-1"></i> Limpar
                </a>
            @endif
        </div>
    </form>

    {{-- Tabela --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase">Data / Hora</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase">Usuário</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase text-center">Ação</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase">Descrição</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        @php
                            $actionStyles = [
                                'created' => ['bg-green-100 text-green-700',  'fas fa-plus-circle'],
                                'updated' => ['bg-yellow-100 text-yellow-700', 'fas fa-edit'],
                                'deleted' => ['bg-red-100 text-red-600',       'fas fa-trash'],
                            ];
                            [$badge, $icon] = $actionStyles[$log->action] ?? ['bg-gray-100 text-gray-600', 'fas fa-circle'];

                            $actionLabels = [
                                'created' => 'Criação',
                                'updated' => 'Atualização',
                                'deleted' => 'Exclusão',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y') }}
                                <span class="block text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm font-semibold text-gray-800">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sistema</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase px-2 py-1 rounded-full {{ $badge }}">
                                    <i class="{{ $icon }} text-[10px]"></i>
                                    {{ $actionLabels[$log->action] ?? $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-700">
                                {{ $log->description }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-400 font-mono">
                                {{ $log->ip ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                Nenhum registro encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
