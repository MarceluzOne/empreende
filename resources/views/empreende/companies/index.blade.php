@extends('layouts.app')

@section('title', 'Empresas - Empreende Vitória')

@section('content')
    <div class="max-w-6xl mx-auto">
        {{-- Cabeçalho --}}
        <div class="mb-6">
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Empresas</h2>
            <p class="text-gray-500 italic text-sm">
                Empresas conhecidas pelo CNPJ — empregadores cadastrados e quem já publicou vagas.
            </p>
        </div>

        {{-- Busca por CNPJ --}}
        <form method="GET" action="{{ route('companies.index') }}" class="mb-5 flex gap-2">
            <input type="text" name="cnpj" value="{{ $search }}"
                class="flex-1 px-4 py-3 bg-white border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition font-mono text-sm"
                placeholder="Buscar por CNPJ (pode ser parcial)...">
            <button type="submit"
                class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition">
                <i class="fas fa-search mr-1"></i> Buscar
            </button>
            @if($search)
                <a href="{{ route('companies.index') }}"
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
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Empresa</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">CNPJ</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Perfil</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Cidade</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($companies as $company)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $company->name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $company->cnpj_formatted }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @if($company->is_registered)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">
                                            <i class="fas fa-building"></i> Cadastrada
                                        </span>
                                        @unless($company->active)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-50 text-red-700">
                                                <i class="fas fa-ban"></i> Desabilitada
                                            </span>
                                        @endunless
                                    @endif
                                    @if($company->vacancy_count > 0)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                                            <i class="fas fa-clipboard-list"></i> {{ $company->vacancy_count }} vaga(s)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $company->city ?? '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('companies.show', $company->uuid) }}"
                                    class="inline-block text-blue-600 hover:text-blue-800 text-sm font-bold">
                                    Ver <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                @if($search)
                                    Nenhuma empresa encontrada para "<span class="font-mono">{{ $search }}</span>".
                                @else
                                    Nenhuma empresa cadastrada ainda.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="mt-4">
                {{ $companies->appends(['cnpj' => $search])->links() }}
            </div>
        @endif
    </div>
@endsection
