{{--
    Modal de confirmação reutilizável, no mesmo desenho do modal de sair da conta.

    Uso:
        @include('partials.confirm-modal', [
            'id'      => 'confirm-cpf',
            'title'   => 'Alterar o CPF?',
            'message' => 'Texto fixo (opcional — pode vir do JS).',
            'icon'    => 'fa-id-card',
            'tone'    => 'amber',          // amber para update, red para exclusão
            'confirm' => 'Alterar e salvar',
        ])

    E no script da página, para segurar o submit de um formulário:
        ConfirmModal.guard(form, 'confirm-cpf', 'mensagem');

    A mensagem também pode ser uma função: devolva null quando aquele envio não
    precisar de confirmação (ex.: o campo sensível não mudou).
--}}
@php
    $tone    = $tone    ?? 'red';
    $icon    = $icon    ?? 'fa-triangle-exclamation';
    $confirm = $confirm ?? 'Confirmar';
    $message = $message ?? '';
@endphp

<div id="{{ $id }}" data-confirm-modal style="display:none"
    class="fixed inset-0 z-50 items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm mx-4">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-{{ $tone }}-100 flex items-center justify-center shrink-0">
                <i class="fas {{ $icon }} text-{{ $tone }}-500"></i>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-800">{{ $title }}</h2>
                <p id="{{ $id }}-message" class="text-sm text-gray-500">{{ $message }}</p>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-4">
            <button type="button" data-confirm-cancel
                class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button type="button" id="{{ $id }}-confirm"
                class="px-4 py-2 text-sm rounded-lg bg-{{ $tone }}-500 text-white hover:bg-{{ $tone }}-600 transition">
                {{ $confirm }}
            </button>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            window.ConfirmModal = {
                open(id, message) {
                    if (message) {
                        document.getElementById(id + '-message').textContent = message;
                    }
                    document.getElementById(id).style.display = 'flex';
                },

                close(id) {
                    document.getElementById(id).style.display = 'none';
                },

                /**
                 * Segura o envio do formulário até o Confirmar do modal.
                 *
                 * `message` pode ser texto ou função; devolvendo null, o envio
                 * passa direto — é como cada tela decide quando perguntar.
                 */
                guard(form, id, message) {
                    let confirmado = false;

                    document.getElementById(id + '-confirm').addEventListener('click', function () {
                        confirmado = true;
                        ConfirmModal.close(id);
                        form.requestSubmit ? form.requestSubmit() : form.submit();
                    });

                    form.addEventListener('submit', function (event) {
                        if (confirmado) {
                            return;
                        }

                        const texto = typeof message === 'function' ? message() : message;

                        if (texto === null) {
                            return;
                        }

                        event.preventDefault();
                        ConfirmModal.open(id, texto);
                    });
                }
            };

            document.addEventListener('click', function (event) {
                const cancelar = event.target.closest('[data-confirm-cancel]');

                if (cancelar) {
                    cancelar.closest('[data-confirm-modal]').style.display = 'none';
                }
            });
        </script>
    @endpush
@endonce
