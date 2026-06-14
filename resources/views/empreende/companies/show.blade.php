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
            <div class="flex items-center justify-between mb-4 gap-2 flex-wrap">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-building text-emerald-500 mr-1"></i> Cadastro
                </h3>
                @if($empresa)
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $empresa->active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                            {{ $empresa->active ? 'Habilitada' : 'Desabilitada' }}
                        </span>
                        <a href="{{ route('companies.empresa.edit', $empresa) }}"
                            class="text-xs font-bold px-3 py-1 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </a>
                        <form action="{{ route('companies.empresa.toggle', $empresa) }}" method="POST"
                            onsubmit="return confirm('{{ $empresa->active ? 'Desabilitar esta empresa? As vagas ativas dela sairão do site e o login será bloqueado.' : 'Habilitar esta empresa?' }}')">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="text-xs font-bold px-3 py-1 rounded-lg transition {{ $empresa->active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                <i class="fas {{ $empresa->active ? 'fa-ban' : 'fa-check' }} mr-1"></i>
                                {{ $empresa->active ? 'Desabilitar' : 'Habilitar' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>

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
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($vacancies as $vaga)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $vaga->position }}</div>
                                    @if($vaga->formatted_remuneration)
                                        <div class="text-xs text-gray-400">{{ $vaga->formatted_remuneration }}</div>
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
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('job-vacancies.edit', $vaga) }}" class="text-gray-500 hover:text-gray-700" title="Editar vaga">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($vaga->status !== 'filled')
                                        <form action="{{ route('job-vacancies.toggle', $vaga) }}" method="POST"
                                            onsubmit="return confirm('{{ $vaga->status === 'active' ? 'Desabilitar esta vaga? Sairá do site.' : 'Habilitar esta vaga?' }}')">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $vaga->status === 'active' ? 'Desabilitar' : 'Habilitar' }}"
                                                class="{{ $vaga->status === 'active' ? 'text-red-500 hover:text-red-700' : 'text-emerald-600 hover:text-emerald-800' }}">
                                                <i class="fas {{ $vaga->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
