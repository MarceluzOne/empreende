@extends('layouts.app')

@section('title', 'Editar cidadão - Empreende Vitória')

@section('content')
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('citizens.show', $uuid) }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <i class="fas fa-arrow-left"></i> Voltar para o cidadão
        </a>

        <div class="mb-6">
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Editar cidadão</h2>
            <p class="text-gray-500 italic text-sm">{{ $name ?? 'Sem nome registrado' }} — {{ $cpf_formatted }}</p>
        </div>

        {{-- Dados da pessoa --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-id-card text-blue-500 mr-1"></i> Dados do cidadão
                </h3>
            </div>

            <form action="{{ route('citizens.update', $uuid) }}" method="POST" id="form-cidadao"
                class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf @method('PUT')

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nome completo *</label>
                    <input type="text" name="name" value="{{ old('name', $name) }}" required
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm @error('name') border-red-500 @enderror">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">CPF *</label>
                    <input type="text" name="cpf" id="cidadao_cpf" value="{{ old('cpf', $cpf_formatted) }}" required
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono @error('cpf') border-red-500 @enderror">
                    @error('cpf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Telefone</label>
                    <input type="text" name="phone" id="cidadao_phone" value="{{ old('phone', App\Support\Phone::format($phone)) }}"
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm @error('phone') border-red-500 @enderror">
                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">E-mail</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}"
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm @error('email') border-red-500 @enderror">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @if($conta)
                        <p class="text-xs text-gray-400 mt-1">
                            Este e-mail também é o login da conta de acesso. Deixando em branco, o login atual é mantido.
                        </p>
                    @endif
                </div>

                <div class="md:col-span-2 bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800">
                    <p class="font-bold mb-1">
                        <i class="fas fa-triangle-exclamation mr-1"></i> A alteração vale para todos os registros desta pessoa
                    </p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        <li>Currículo de candidato: {{ $candidato ? 'sim' : 'não possui' }}</li>
                        <li>Atendimentos registrados: {{ $attendances->count() }}</li>
                        <li>Conta de acesso: {{ $conta ? $conta->email : 'não possui' }}</li>
                    </ul>
                    <p class="mt-2">
                        Trocar o CPF reagrupa a pessoa em todos eles — use apenas para corrigir um documento digitado errado.
                    </p>
                </div>

                <div class="md:col-span-2 flex justify-end gap-2">
                    <a href="{{ route('citizens.show', $uuid) }}"
                        class="px-5 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">
                        Salvar
                    </button>
                </div>
            </form>
        </div>

        {{-- Conta de acesso --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-key text-indigo-500 mr-1"></i> Conta de acesso
                </h3>
            </div>

            @if($conta)
                <div class="p-6 text-sm text-gray-600">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Login</span>
                    <span class="font-mono font-bold text-gray-800">{{ $conta->email }}</span>

                    {{-- A senha nunca é definida aqui: quem a escolhe é o próprio
                         cidadão, pelo link que o atendimento gera na tela de
                         detalhe. A equipe não conhece a senha de ninguém. --}}
                    <p class="text-xs text-gray-400 mt-2">
                        A senha é definida pelo próprio cidadão. Alterar o e-mail acima troca o login desta conta.
                    </p>
                </div>
            @else
                <p class="px-6 py-8 text-sm text-gray-400 italic text-center">
                    Esta pessoa ainda não tem conta de acesso no portal.
                </p>
            @endif
        </div>

        @if($candidato)
            <p class="text-sm text-gray-500 mt-5 text-center">
                Para currículo, experiências e formação,
                <a href="{{ route('job-seekers.edit', $candidato) }}" class="text-blue-600 font-bold hover:text-blue-800">
                    edite o cadastro de candidato
                </a>.
            </p>
        @endif
    </div>

    @include('partials.confirm-modal', [
        'id'      => 'confirm-cpf',
        'title'   => 'Alterar o CPF?',
        'icon'    => 'fa-id-card',
        'tone'    => 'amber',
        'confirm' => 'Alterar e salvar',
    ])

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const cpf   = document.getElementById('cidadao_cpf');
                const phone = document.getElementById('cidadao_phone');

                if (phone) IMask(phone, { mask: [{ mask: '(00) 0000-0000' }, { mask: '(00)0 0000-0000' }] });

                if (!cpf) {
                    return;
                }

                IMask(cpf, { mask: '000.000.000-00' });

                const original = @json($cpf);

                const mascarar = function (digitos) {
                    return digitos.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
                };

                // Trocar o CPF reescreve currículo, atendimentos e conta de uma
                // vez, então pergunta — mas só quando o documento mudou de fato.
                ConfirmModal.guard(document.getElementById('form-cidadao'), 'confirm-cpf', function () {
                    const digitado = (cpf.value || '').replace(/\D/g, '');

                    if (digitado === original) {
                        return null;
                    }

                    return 'O CPF passa de ' + mascarar(original) + ' para ' + mascarar(digitado)
                        + ' no currículo, nos atendimentos e na conta desta pessoa.';
                });
            });
        </script>
    @endpush
@endsection
