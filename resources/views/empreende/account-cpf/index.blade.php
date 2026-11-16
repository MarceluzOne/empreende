{{--
    Contas do portal sem CPF.

    Para quem perdeu a senha de uma conta antiga (anterior a agosto/2026) e por
    isso não passa pelo pedido de CPF do login. O atendente confere o documento
    com a pessoa, grava aqui e segue para o cadastro dela, onde gera o link.
--}}
@extends('layouts.app')

@section('title', 'Contas sem CPF - Empreende Vitória')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('citizens.index') }}" class="text-sm text-gray-400 hover:text-blue-600">
                <i class="fas fa-arrow-left mr-1"></i> Cidadãos
            </a>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter mt-1">Contas sem CPF</h2>
            <p class="text-gray-500 italic text-sm">
                Contas criadas no site antes de o cadastro pedir CPF. Sem ele, a pessoa não aparece em Cidadãos e não dá para gerar link de senha.
            </p>
        </div>

        <div class="mb-5 px-4 py-3 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-800">
            <i class="fas fa-circle-info mr-1"></i>
            Ache a conta pelo nome ou e-mail que a pessoa usou no cadastro, <strong>peça o CPF e confira com um documento</strong>
            antes de gravar. Depois de salvo, você vai direto para o cadastro dela, onde gera o link.
        </div>

        <form method="GET" action="{{ route('account-cpf.index') }}" class="mb-5 flex flex-wrap gap-2">
            <input type="text" name="busca" value="{{ $search }}"
                class="flex-1 min-w-[16rem] px-4 py-3 bg-white border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition text-sm"
                placeholder="Buscar por nome ou e-mail...">
            <button type="submit"
                class="bg-blue-600 text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition">
                <i class="fas fa-search mr-1"></i> Buscar
            </button>
            @if($search)
                <a href="{{ route('account-cpf.index') }}"
                    class="bg-gray-100 text-gray-600 px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
                    Limpar
                </a>
            @endif
        </form>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-left">
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Conta</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Criada em</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Informar CPF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($accounts as $account)
                        @php $temErro = old('conta') === (string) $account->id && $errors->has('cpf'); @endphp
                        <tr class="hover:bg-gray-50 transition align-top">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $account->name }}</div>
                                <div class="text-xs text-gray-500">{{ $account->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ optional($account->created_at)->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('account-cpf.store', $account) }}"
                                      class="form-conta-cpf flex flex-wrap gap-2" data-nome="{{ $account->name }}">
                                    @csrf
                                    <input type="hidden" name="conta" value="{{ $account->id }}">
                                    <input type="text" name="cpf" inputmode="numeric" autocomplete="off" required
                                        value="{{ old('conta') === (string) $account->id ? old('cpf') : '' }}"
                                        placeholder="000.000.000-00"
                                        class="input-cpf w-44 px-3 py-2 bg-white border {{ $temErro ? 'border-red-400' : 'border-gray-200' }} rounded-xl outline-none focus:border-blue-600 transition text-sm font-mono">
                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold transition">
                                        Gravar CPF
                                    </button>
                                </form>
                                @if($temErro)
                                    <p class="text-xs text-red-600 mt-1.5">{{ $errors->first('cpf') }}</p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400">
                                @if($search)
                                    Nenhuma conta sem CPF encontrada para "{{ $search }}".
                                @else
                                    Todas as contas do portal já têm CPF.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $accounts->links() }}
        </div>
    </div>

    @include('partials.confirm-modal', [
        'id'      => 'confirm-conta-cpf',
        'title'   => 'Gravar o CPF nesta conta?',
        'icon'    => 'fa-id-card',
        'tone'    => 'amber',
        'confirm' => 'Gravar CPF',
    ])

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.form-conta-cpf').forEach(function (form) {
                    const cpf = form.querySelector('.input-cpf');
                    IMask(cpf, { mask: '000.000.000-00' });

                    // Gravar o CPF é o que libera o link de senha desta conta,
                    // então pergunta antes, mostrando para quem vai.
                    ConfirmModal.guard(form, 'confirm-conta-cpf', function () {
                        return 'O CPF ' + cpf.value + ' passa a identificar a conta de ' + form.dataset.nome
                            + '. Você conferiu o documento com a pessoa?';
                    });
                });
            });
        </script>
    @endpush
@endsection
