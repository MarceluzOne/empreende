@extends('layouts.app')

@section('title', 'Cidadãos - Empreende Vitória')

@section('content')
    <div class="max-w-6xl mx-auto">
        {{-- Cabeçalho --}}
        <div class="mb-6">
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Cidadãos</h2>
            <p class="text-gray-500 italic text-sm">
                Pessoas conhecidas pelo CPF — candidatos, quem passou por atendimento e quem tem conta no portal.
            </p>
        </div>

        {{-- Busca e filtro de conta --}}
        <form method="GET" action="{{ route('citizens.index') }}" class="mb-5 flex flex-wrap gap-2">
            <input type="text" name="busca" value="{{ $search }}"
                class="flex-1 min-w-[16rem] px-4 py-3 bg-white border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition text-sm"
                placeholder="Buscar por nome, CPF ou e-mail (pode ser parcial)...">

            <select name="conta"
                class="px-4 py-3 bg-white border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition text-sm">
                <option value="">Com e sem conta</option>
                <option value="com" @selected($conta === 'com')>Só com conta no portal</option>
                <option value="sem" @selected($conta === 'sem')>Só sem conta</option>
            </select>

            <button type="submit"
                class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition">
                <i class="fas fa-search mr-1"></i> Buscar
            </button>
            @if($search || $conta)
                <a href="{{ route('citizens.index') }}"
                    class="bg-gray-100 text-gray-600 px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                    Limpar
                </a>
            @endif
        </form>

        {{-- Contas sem CPF: a listagem e indexada pelo CPF, entao elas nao tem
             como virar linha aqui. Mostrar o numero em vez de omitir o fato. --}}
        @if($contasSemCpf > 0)
            <div class="mb-5 px-4 py-3 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-800">
                <i class="fas fa-circle-info mr-1"></i>
                {{ $contasSemCpf }} {{ $contasSemCpf === 1 ? 'conta do portal está' : 'contas do portal estão' }}
                sem CPF gravado e não {{ $contasSemCpf === 1 ? 'aparece' : 'aparecem' }} nesta lista.
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('cpf.backfill') }}" class="font-bold underline">Preencher o CPF dessas contas</a>.
                @else
                    Peça a um administrador para rodar o backfill de CPF.
                @endif
            </div>
        @endif

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
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                <div>{{ $citizen->cpf_formatted }}</div>
                                @foreach($citizen->cnpjs ?? [] as $cnpj)
                                    <div class="text-xs text-gray-400">CNPJ: {{ $cnpj }}</div>
                                @endforeach
                            </td>
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
                                    @if($citizen->account_without_cpf)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700"
                                            title="A conta existe mas está sem CPF gravado — o link de senha não sai até rodar o backfill.">
                                            <i class="fas fa-circle-exclamation"></i> Conta sem CPF
                                        </span>
                                    @elseif($citizen->has_account)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700"
                                            title="{{ $citizen->account_email }}">
                                            <i class="fas fa-circle-user"></i> Conta
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $citizen->last_attendance_at ? $citizen->last_attendance_at->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-4">
                                    <a href="{{ route('citizens.edit', $citizen->uuid) }}"
                                        class="text-gray-400 hover:text-blue-600 text-sm font-bold" title="Editar dados">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a href="{{ route('citizens.show', $citizen->uuid) }}"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                                        Ver <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                @if($search || $conta)
                                    Nenhum cidadão encontrado para esta busca.
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
                {{ $citizens->links() }}
            </div>
        @endif
    </div>
@endsection
