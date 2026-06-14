@extends('layouts.app')

@section('title', ($name ?? 'Empresa') . ' - Empreende Vitória')

@section('content')
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('companies.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <i class="fas fa-arrow-left"></i> Voltar para empresas
        </a>

        {{-- Cabeçalho --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-600 flex items-center justify-center text-white text-xl font-black shrink-0">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">{{ $name ?? '—' }}</h2>
                <p class="text-gray-500 font-mono text-sm">{{ $cnpj_formatted }}</p>
            </div>
        </div>

        {{-- Dados cadastrais --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
            <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider mb-4">
                <i class="fas fa-building text-emerald-500 mr-1"></i> Cadastro
            </h3>

            @if($empresa)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Razão social</span>
                        {{ $empresa->razao_social ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Cidade</span>
                        {{ $empresa->cidade ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Telefone</span>
                        {{ $empresa->telefone ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">E-mail</span>
                        {{ optional($empresa->user)->email ?? '—' }}
                    </div>
                    @if($empresa->descricao)
                    <div class="sm:col-span-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Descrição</span>
                        {{ $empresa->descricao }}
                    </div>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Empresa sem cadastro próprio — conhecida apenas pelas vagas publicadas.</p>
            @endif
        </div>

        {{-- Vagas publicadas --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-clipboard-list text-blue-500 mr-1"></i>
                    Vagas publicadas ({{ $vacancies->count() }})
                </h3>
            </div>

            @if($vacancies->isEmpty())
                <p class="px-6 py-8 text-sm text-gray-400 italic text-center">Nenhuma vaga publicada por esta empresa.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-left">
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Vaga</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Área</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Situação</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Candidaturas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($vacancies as $vaga)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $vaga->position }}</div>
                                    @if($vaga->remuneration)
                                        <div class="text-xs text-gray-400">{{ $vaga->remuneration }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $vaga->interest_area ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($vaga->status === 'active')
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700">{{ $vaga->status_label }}</span>
                                    @elseif($vaga->status === 'filled')
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">{{ $vaga->status_label }}</span>
                                    @else
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $vaga->status_label }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">{{ $vaga->applications_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
