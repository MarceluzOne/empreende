@extends('layouts.app')

@section('title', ($name ?? 'Cidadão') . ' - Empreende Vitória')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Voltar --}}
        <a href="{{ route('citizens.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <i class="fas fa-arrow-left"></i> Voltar para cidadãos
        </a>

        {{-- Cabeçalho --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-white text-xl font-black shrink-0">
                {{ strtoupper(mb_substr($name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-black text-gray-800">{{ $name ?? '—' }}</h2>
                <p class="text-gray-500 font-mono text-sm">CPF: {{ $cpf_formatted }}</p>
                @if(!empty($cnpjs) && count($cnpjs))
                    <p class="text-gray-400 font-mono text-xs mt-0.5">
                        CNPJ: {{ $cnpjs->implode(' · ') }}
                    </p>
                @endif
            </div>

            <a href="{{ route('citizens.edit', $uuid) }}"
                class="shrink-0 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                <i class="fas fa-pen mr-1"></i> Editar
            </a>
        </div>

        {{-- Perfil de candidato --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-user-graduate text-indigo-500 mr-1"></i> Candidato
                </h3>
                @if($candidato)
                    <div class="flex items-center gap-4">
                        <a href="{{ route('job-seekers.edit', $candidato) }}"
                            class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                            <i class="fas fa-pen mr-1"></i> Editar currículo
                        </a>
                        <a href="{{ route('job-seekers.curriculo', $candidato->id) }}" target="_blank"
                            class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                            <i class="fas fa-file-pdf mr-1"></i> Ver currículo (PDF)
                        </a>
                    </div>
                @endif
            </div>

            @if($candidato)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Área de interesse</span>
                        {{ $candidato->interest_area ?? $candidato->job_function ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Cidade / UF</span>
                        {{ trim(($candidato->city ?? '') . ($candidato->state ? ' / ' . $candidato->state : '')) ?: '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Telefone</span>
                        {{ $candidato->formatted_phone ?? $candidato->phone ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">E-mail</span>
                        {{ $candidato->email ?? optional($candidato->user)->email ?? '—' }}
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Esta pessoa não possui currículo de candidato cadastrado.</p>
            @endif
        </div>

        {{-- Histórico de atendimentos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-briefcase text-blue-500 mr-1"></i>
                    Atendimentos ({{ $attendances->count() }})
                </h3>
            </div>

            @if($attendances->isEmpty())
                <p class="px-6 py-8 text-sm text-gray-400 italic text-center">Nenhum atendimento registrado para este CPF.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-left">
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Serviço</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">CNPJ</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Situação</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Atendente</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($attendances as $a)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $a->service_type }}</div>
                                    @if($a->description)
                                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ $a->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                    {{ $a->customer_cnpj_formatted ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $a->scheduled_at ? $a->scheduled_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($a->status === 'completed')
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700">Concluído</span>
                                    @else
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-orange-50 text-orange-700">Agendado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ optional($a->user)->name ?? 'Agendamento pelo site' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Acesso ao portal: geração do link de redefinição de senha --}}
        @php
            // O que trava a geração é só o que impediria a pessoa de usar o
            // link: um CPF incompleto ou uma conta sem CPF gravado, porque é o
            // CPF que ela digita para provar que o link é dela.
            //
            // Telefone NÃO é pré-requisito. Quem confirma a identidade é o
            // atendente, conferindo o CPF e os dados do cadastro na tela; os
            // números abaixo são apoio para essa conferência e para o envio.
            $cpfValido   = $cpf !== null && strlen($cpf) === 11;
            $temConta    = (bool) $account;
            $contaComCpf = $temConta && $account->cpf !== null;
            $podeGerar   = $cpfValido && $contaComCpf;
            $linkGerado  = session('reset_link');
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

            @if(! $cpfValido)
                <div class="px-4 py-3 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-800">
                    O CPF deste cadastro está incompleto ({{ $cpf_formatted }}) — sem os 11 dígitos não há como casar os
                    registros nem confirmar a identidade na tela de redefinição. Corrija o CPF no cadastro de origem
                    antes de gerar o link.
                </div>

            @elseif(! $temConta)
                {{-- Quem foi cadastrado no balcao nao tem conta: nao e "perdeu
                     a senha", e nunca houve senha. O atendente cria a conta
                     aqui e entrega o mesmo link, para a pessoa definir a dela.
                     A senha nasce aleatoria e ninguem a conhece. --}}
                @php $emailsConhecidos = $known_emails ?? []; @endphp

                <p class="text-sm text-gray-600 mb-1">
                    Esta pessoa <strong>ainda não tem conta no portal</strong> — não é senha perdida, é conta que nunca
                    existiu. Isso acontece com quem foi cadastrado no atendimento.
                </p>
                <p class="text-xs text-gray-500 mb-4">
                    Ativar cria a conta e gera o link para ela mesma definir a primeira senha. Confirme a identidade
                    antes: peça o CPF e confira com o cadastro acima.
                </p>

                <form method="POST" action="{{ route('citizens.account.activate', $uuid) }}"
                    class="flex flex-wrap gap-2 items-start">
                    @csrf

                    @if($emailsConhecidos === [])
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                E-mail da pessoa *
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="nome@exemplo.com"
                                class="px-3 py-2 rounded-xl border text-sm w-72 outline-none focus:ring-2 focus:ring-amber-400 {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }}">
                            @error('email')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-400 mt-1 max-w-xs">
                                Não há e-mail em nenhum registro deste CPF, e o e-mail é o login da conta.
                            </p>
                        </div>
                    @else
                        <div class="text-sm text-gray-600 mr-2">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Login será</span>
                            <span class="font-mono">{{ $emailsConhecidos[0] }}</span>
                        </div>
                    @endif

                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition">
                        <i class="fas fa-user-plus mr-1"></i> Ativar conta e gerar link
                    </button>
                </form>

            @elseif(! $contaComCpf)
                <div class="px-4 py-3 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-800">
                    A conta <strong>{{ $account->name }}</strong> existe, mas está sem CPF gravado — e é o CPF que
                    confirma a identidade na tela de redefinição.
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('cpf.backfill') }}" class="font-bold underline">Rode o backfill de CPF</a> antes de gerar o link.
                    @else
                        Peça a um administrador para rodar o backfill de CPF.
                    @endif
                </div>

            @else
                <div class="text-sm text-gray-600 mb-4 space-y-1">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Conta</span>
                        <span class="ml-2 font-mono">{{ $account->email }}</span>
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Telefones no cadastro</span>
                        @if($phones->isEmpty())
                            <span class="ml-2 text-gray-400 italic">nenhum</span>
                        @else
                            @foreach($phones as $phone)
                                <span class="ml-2 font-mono">{{ $phone['formatted'] }}</span>
                                <span class="text-xs text-gray-400">({{ implode(' · ', $phone['sources']) }})</span>
                            @endforeach
                        @endif
                    </div>
                </div>

                @if(! $linkGerado)
                    <p class="text-xs text-gray-500 mb-3">
                        Confirme a identidade antes de gerar: peça o CPF e confira com o cadastro acima. A senha é
                        escolhida pela própria pessoa — a equipe não fica sabendo qual é.
                    </p>

                    <button type="button" id="gerarLinkBtn"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition">
                        <i class="fas fa-link"></i> Gerar link de redefinição de senha
                    </button>
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
                            Repasse só para a pessoa de quem você confirmou a identidade. O link pede o CPF da conta
                            para funcionar, vale uma única vez, e gerar um novo cancela este.
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Confirmação antes de gerar: o link novo invalida qualquer link anterior --}}
    @if(!empty($podeGerar) && empty($linkGerado))
        <div id="confirmLinkModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <h5 class="text-lg font-black text-gray-800 mb-2">Gerar link de senha</h5>
                <p class="text-sm text-gray-600 mb-1">
                    Gerar um link de redefinição de senha para <strong>{{ $name }}</strong>?
                </p>
                <p class="text-xs text-gray-500 mb-5">
                    Qualquer link gerado antes para esta pessoa deixa de funcionar. A ação fica registrada na auditoria
                    com o seu nome.
                </p>
                <div class="flex justify-end gap-2">
                    <button type="button" id="cancelarLinkBtn"
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold transition">
                        Cancelar
                    </button>
                    <form method="POST" action="{{ route('citizens.password-link', $uuid) }}">
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
