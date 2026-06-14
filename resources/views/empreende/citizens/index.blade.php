@extends('layouts.app')

@section('title', 'Cidadãos - Empreende Vitória')

@section('content')
    <div class="max-w-6xl mx-auto">
        {{-- Cabeçalho --}}
        <div class="mb-6">
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Cidadãos</h2>
            <p class="text-gray-500 italic text-sm">
                Pessoas conhecidas pelo CPF — candidatos cadastrados e quem já passou por atendimento.
            </p>
        </div>

        {{-- Busca por CPF --}}
        <form method="GET" action="{{ route('citizens.index') }}" class="mb-5 flex gap-2">
            <input type="text" name="cpf" value="{{ $search }}"
                class="flex-1 px-4 py-3 bg-white border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition font-mono text-sm"
                placeholder="Buscar por CPF (pode ser parcial)...">
            <button type="submit"
                class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition">
                <i class="fas fa-search mr-1"></i> Buscar
            </button>
            @if($search)
                <a href="{{ route('citizens.index') }}"
                    class="bg-gray-100 text-gray-600 px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                    Limpar
                </a>
            @endif
        </form>

        {{-- Tabela --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-left">
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">CPF / CNPJ</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Perfil</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Último atendimento</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($citizens as $citizen)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $citizen->name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $citizen->cpf_formatted }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($citizen->is_candidato)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">
                                            <i class="fas fa-user-graduate"></i> Candidato
                                        </span>
                                    @endif
                                    @if($citizen->attendance_count > 0)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                                            <i class="fas fa-briefcase"></i> {{ $citizen->attendance_count }} atend.
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $citizen->last_attendance_at ? $citizen->last_attendance_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('citizens.show', $citizen->uuid) }}"
                                    class="inline-block text-blue-600 hover:text-blue-800 text-sm font-bold">
                                    Ver <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                @if($search)
                                    Nenhum cidadão encontrado para "<span class="font-mono">{{ $search }}</span>".
                                @else
                                    Nenhum cidadão cadastrado ainda.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($citizens->hasPages())
            <div class="mt-4">
                {{ $citizens->appends(['cpf' => $search])->links() }}
            </div>
        @endif
    </div>
@endsection
