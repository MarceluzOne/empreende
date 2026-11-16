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
                        {{ $empresa->formatted_telefone ?? '—' }}
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

        {{-- Vagas publicadas. `vagaAberta` guarda o id da vaga cujo modal de
             candidatos está aberto (null = nenhum). --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
             x-data="{ vagaAberta: null }" @keydown.escape.window="vagaAberta = null">
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
                                        <button type="button" @click="vagaAberta = '{{ $vaga->id }}'"
                                            class="text-blue-600 hover:text-blue-800" title="Visualizar candidatos">
                                            <i class="fas fa-eye"></i>
                                        </button>
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

                {{-- Modal de candidatos, um por vaga --}}
                @foreach($vacancies as $vaga)
                    @php $contagem = $vaga->applications->countBy('status'); @endphp
                    <div x-show="vagaAberta === '{{ $vaga->id }}'" style="display:none"
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                         @click.self="vagaAberta = null">
                        <div class="bg-white rounded-2xl shadow-lg w-full max-w-3xl max-h-[85vh] flex flex-col">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-base font-black text-gray-800">{{ $vaga->position }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $vaga->applications->count() }} {{ $vaga->applications->count() === 1 ? 'candidatura' : 'candidaturas' }}
                                        @if($vaga->applications->isNotEmpty())
                                            — {{ $contagem['accepted'] ?? 0 }} aceita(s),
                                            {{ $contagem['rejected'] ?? 0 }} recusada(s),
                                            {{ $contagem['pending'] ?? 0 }} pendente(s)
                                        @endif
                                    </p>
                                </div>
                                <button type="button" @click="vagaAberta = null" class="text-gray-400 hover:text-gray-600" title="Fechar">
                                    <i class="fas fa-xmark text-lg"></i>
                                </button>
                            </div>

                            <div class="overflow-y-auto">
                                @if($vaga->applications->isEmpty())
                                    <p class="px-6 py-10 text-sm text-gray-400 italic text-center">Ninguém se candidatou a esta vaga ainda.</p>
                                @else
                                    <table class="w-full">
                                        <thead class="sticky top-0">
                                            <tr class="bg-gray-50 border-b border-gray-200 text-left">
                                                <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Candidato</th>
                                                <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Inscrito em</th>
                                                <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Situação</th>
                                                <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Currículo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($vaga->applications as $candidatura)
                                                @php
                                                    $seeker = $candidatura->seeker;
                                                    $cor = [
                                                        'accepted' => 'bg-green-50 text-green-700',
                                                        'rejected' => 'bg-red-50 text-red-700',
                                                        'pending'  => 'bg-amber-50 text-amber-700',
                                                    ][$candidatura->status] ?? 'bg-gray-100 text-gray-600';
                                                    $contato = $seeker ? collect([$seeker->formatted_phone, $seeker->email])->filter()->join(' · ') : '';
                                                @endphp
                                                <tr>
                                                    <td class="px-6 py-3">
                                                        <div class="text-sm font-bold text-gray-800">{{ optional($seeker)->name ?? 'Candidato removido' }}</div>
                                                        @if($contato)
                                                            <div class="text-xs text-gray-400">{{ $contato }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-3 text-sm text-gray-500 whitespace-nowrap">
                                                        {{ optional($candidatura->created_at)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="px-6 py-3">
                                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $cor }}">{{ $candidatura->status_label }}</span>
                                                    </td>
                                                    <td class="px-6 py-3 text-right">
                                                        @if($seeker)
                                                            <a href="{{ route('job-seekers.curriculo', $seeker) }}" target="_blank" rel="noopener"
                                                               class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800">
                                                                <i class="fas fa-file-pdf"></i> Ver currículo
                                                            </a>
                                                        @else
                                                            <span class="text-xs text-gray-300">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Acesso ao portal: geração do link de senha da conta da empresa.
             Mesmo fluxo do cidadão; o que confirma a identidade na tela pública
             é o CNPJ em vez do CPF. Mais curto porque empresas.user_id é FK
             real e empresas.cnpj é NOT NULL UNIQUE — não há documento faltando
             nem cruzamento por grafia a fazer. --}}
        @php
            $conta      = optional($empresa)->user;
            $temConta   = $conta !== null && $conta->type === 'empresa';
            $linkGerado = session('reset_link');
            $telefone   = App\Support\Phone::format(optional($empresa)->telefone);
        @endphp

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-5">
            <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider mb-4">
                <i class="fas fa-key text-amber-500 mr-1"></i> Acesso ao portal
            </h3>

            @if(session('reset_error'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
                    <i class="fas fa-circle-exclamation mr-1"></i> {{ session('reset_error') }}
                </div>
            @endif

            @if(! $empresa)
                <p class="text-sm text-gray-400 italic">
                    Esta empresa aparece aqui por ter publicado vaga, mas não tem cadastro próprio no portal — não há
                    conta nem senha.
                </p>

            @elseif(! $temConta)
                <p class="text-sm text-gray-400 italic">
                    Este cadastro de empresa não tem conta de acesso vinculada — não há senha a redefinir.
                </p>

            @else
                <div class="text-sm text-gray-600 mb-4 space-y-1">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Login</span>
                        <span class="ml-2 font-mono">{{ $conta->email }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Telefone no cadastro</span>
                        @if($telefone)
                            <span class="ml-2 font-mono">{{ $telefone }}</span>
                        @else
                            <span class="ml-2 text-gray-400 italic">nenhum</span>
                            <a href="{{ route('companies.empresa.edit', $empresa) }}"
                                class="ml-1 text-xs text-blue-600 font-bold underline">cadastrar</a>
                        @endif
                    </div>
                </div>

                @if(! $linkGerado)
                    @can('gerar-link-senha')
                        <p class="text-xs text-gray-500 mb-3">
                            Confirme a identidade antes de gerar: peça o CNPJ e confira com o cadastro acima. A senha é
                            escolhida por quem recebe o link — a equipe não fica sabendo qual é.
                        </p>

                        <button type="button" id="gerarLinkBtn"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition">
                            <i class="fas fa-link"></i> Gerar link de redefinição de senha
                        </button>
                    @else
                        <p class="text-xs text-gray-500">
                            Você não tem permissão para gerar link de senha. Peça a um administrador.
                        </p>
                    @endcan
                @else
                    <div class="rounded-xl border border-green-100 bg-green-50 p-4">
                        <p class="text-xs font-bold text-green-800 uppercase tracking-wider mb-2">
                            <i class="fas fa-check-circle mr-1"></i> Link gerado — válido até {{ $linkGerado['expires_at'] }}
                        </p>

                        <div class="flex gap-2 mb-3">
                            <input type="text" id="resetLinkInput" readonly value="{{ $linkGerado['url'] }}"
                                class="flex-1 px-3 py-2 text-xs font-mono bg-white border border-green-200 rounded-lg text-gray-700">
                            <button type="button" id="copiarLinkBtn"
                                class="px-3 py-2 rounded-lg bg-white border border-green-200 text-green-700 text-xs font-bold hover:bg-green-100 transition">
                                <i class="fas fa-copy"></i> Copiar
                            </button>
                        </div>

                        <p class="text-xs text-amber-800 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                            <i class="fas fa-triangle-exclamation mr-1"></i>
                            Repasse só para quem você confirmou. O link pede o <strong>CNPJ</strong> da empresa para
                            funcionar, vale uma única vez, e gerar um novo cancela este.
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @if(!empty($temConta) && empty($linkGerado) && auth()->user()->can('gerar-link-senha'))
        <div id="confirmLinkModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <h5 class="text-lg font-black text-gray-800 mb-2">Gerar link de senha</h5>
                <p class="text-sm text-gray-600 mb-1">
                    Gerar um link de redefinição de senha para <strong>{{ $conta->name }}</strong>?
                </p>
                <p class="text-xs text-gray-500 mb-5">
                    Qualquer link gerado antes para esta conta deixa de funcionar. A ação fica registrada na auditoria
                    com o seu nome.
                </p>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancelarLinkBtn"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold transition">
                        Cancelar
                    </button>
                    <form method="POST" action="{{ route('companies.password-link', $empresa) }}">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition">
                            Confirmar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        (function () {
            var abrir  = document.getElementById('gerarLinkBtn');
            var modal  = document.getElementById('confirmLinkModal');
            var fechar = document.getElementById('cancelarLinkBtn');

            if (abrir && modal) {
                abrir.addEventListener('click', function () { modal.classList.replace('hidden', 'flex'); });
                fechar.addEventListener('click', function () { modal.classList.replace('flex', 'hidden'); });
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) modal.classList.replace('flex', 'hidden');
                });
            }

            var copiar = document.getElementById('copiarLinkBtn');
            if (copiar) {
                copiar.addEventListener('click', function () {
                    var campo = document.getElementById('resetLinkInput');
                    campo.select();
                    campo.setSelectionRange(0, 99999);
                    document.execCommand('copy');
                    copiar.innerHTML = '<i class="fas fa-check"></i> Copiado';
                });
            }
        })();
    </script>
@endsection
