{{--
    Tela temporária do backfill de CPF. Remover junto com CpfBackfillController
    e a rota assim que o preenchimento estiver rodado em produção.
--}}
@extends('layouts.app')

@section('title', 'Backfill de CPF - Empreende Vitória')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Backfill de CPF</h1>
            <p class="text-sm text-gray-400 mt-0.5">Preenche o CPF das contas criadas antes de o campo existir, usando o currículo e as inscrições em eventos.</p>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 mb-6 text-sm flex items-start gap-2">
        <i class="fas fa-triangle-exclamation mt-0.5"></i>
        <span>Página temporária, criada só para a migração dos dados. Peça a remoção assim que o preenchimento estiver feito.</span>
    </div>

    @if(session('ok'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-4 text-sm">
            <i class="fas fa-circle-check mr-1"></i> {{ session('ok') }}
        </div>
    @endif

    @if(session('erro'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-4 text-sm">
            <strong class="block mb-1">Falhou:</strong>
            <code class="text-xs break-all">{{ session('erro') }}</code>
        </div>
    @endif

    @if(! $ready)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-700 flex items-center gap-2 mb-2">
                <i class="fas fa-database text-red-500"></i> A coluna ainda não existe
            </h2>
            <p class="text-sm text-gray-500 mb-4">
                Antes de preencher é preciso criar a coluna <code>users.cpf</code> no banco. Rode este SQL no painel da hospedagem e recarregue esta página:
            </p>
            <pre class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-xs text-gray-800 overflow-x-auto"><code>ALTER TABLE users ADD COLUMN cpf VARCHAR(11) NULL AFTER email;
CREATE UNIQUE INDEX users_cpf_unique ON users (cpf);</code></pre>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Prévia --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-700 flex items-center gap-2 mb-1">
                    <i class="fas fa-eye text-blue-500"></i> Prévia
                </h2>
                <p class="text-xs text-gray-400 mb-4">Simulação — nada foi gravado ainda.</p>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between border border-gray-100 rounded-xl px-4 py-3">
                        <span class="text-gray-500">Pelo currículo</span>
                        <span class="font-bold text-gray-800">{{ $preview['curriculos'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border border-gray-100 rounded-xl px-4 py-3">
                        <span class="text-gray-500">Por inscrição em evento</span>
                        <span class="font-bold text-gray-800">{{ $preview['eventos'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border border-blue-100 bg-blue-50 rounded-xl px-4 py-3">
                        <span class="text-blue-700 font-medium">Total a preencher</span>
                        <span class="font-bold text-blue-700">{{ $preview['total'] }}</span>
                    </div>
                    <div class="flex items-center justify-between border border-gray-100 rounded-xl px-4 py-3">
                        <span class="text-gray-500">Continuariam sem CPF</span>
                        <span class="font-bold text-gray-800">{{ $preview['restantes'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Execução --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-700 flex items-center gap-2 mb-1">
                    <i class="fas fa-play text-blue-500"></i> Executar
                </h2>
                <p class="text-xs text-gray-400 mb-4">Grava o CPF nas contas listadas na prévia.</p>

                <ul class="text-sm text-gray-500 space-y-2 mb-5 list-disc list-inside">
                    <li>Só preenche contas que estão <strong>sem</strong> CPF — nada é sobrescrito.</li>
                    <li>Um CPF já usado por outra conta é ignorado, para não esbarrar no índice único.</li>
                    <li>Pode rodar mais de uma vez sem risco: a segunda execução não encontra nada a fazer.</li>
                    <li>As contas que sobrarem <strong>seguem sem CPF</strong> — nada preenche depois. São as que não têm currículo nem inscrição em evento, ou cujo documento está incompleto no cadastro antigo.</li>
                </ul>

                <form method="POST"
                      action="{{ route('cpf.backfill.run') }}"
                      onsubmit="return confirm('Preencher o CPF de {{ $preview['total'] }} conta(s)? A ação não tem desfazer.')">
                    @csrf
                    <button type="submit"
                        @if($preview['total'] === 0) disabled @endif
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-xl text-sm font-bold">
                        @if($preview['total'] === 0)
                            Nada a preencher
                        @else
                            Preencher {{ $preview['total'] }} conta(s)
                        @endif
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
