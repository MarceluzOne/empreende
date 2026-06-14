@extends('layouts.app')

@section('title', '{{ $event->title }} - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Imagem do evento --}}
    @if($event->image_path)
        <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-200" style="max-height: 280px;">
            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full object-cover" style="max-height: 280px;">
        </div>
    @endif

    {{-- Cabeçalho --}}
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
                <span class="text-xs font-bold uppercase px-2 py-1 rounded-full
                    {{ $event->status === 'completed' ? 'bg-green-100 text-green-700' : ($event->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                    {{ $event->status_label }}
                </span>
            </div>
            <p class="text-sm text-gray-400 mt-0.5">
                {{ $event->date->format('d/m/Y') }} às {{ substr($event->start_time, 0, 5) }} —
                {{ $event->duration_minutes }} min (até {{ $event->endTime() }})
            </p>
        </div>
        <div class="flex gap-2 flex-wrap justify-end">
            <a href="{{ route('events.pdf', $event) }}" target="_blank"
                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-file-pdf"></i>
                <span class="hidden sm:inline">Gerar Ata PDF</span>
            </a>
            <a href="{{ route('events.edit', $event) }}"
                class="flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit"></i>
                <span class="hidden sm:inline">Editar</span>
            </a>
            <a href="{{ route('events.index') }}" class="text-blue-900 font-bold hover:underline text-sm self-center ml-2">Voltar</a>
        </div>
    </div>

    {{-- Cards de info --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Palestrante</p>
            <p class="font-bold text-gray-800">{{ $event->speaker->name }}</p>
            @if($event->speaker->email)
                <p class="text-xs text-gray-500 mt-1">{{ $event->speaker->email }}</p>
            @endif
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Vagas</p>
            <p class="text-2xl font-bold {{ $event->isFull() ? 'text-red-600' : 'text-green-600' }}">
                {{ $event->availableSpots() }}
                <span class="text-sm font-normal text-gray-500">restantes de {{ $event->max_capacity }}</span>
            </p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-bold mb-1">Auditório</p>
            @php $allDates = $event->allDates(); @endphp
            @if(count($allDates) > 1)
                <p class="text-sm font-semibold text-green-700"><i class="fas fa-check-circle mr-1"></i>{{ count($allDates) }} dias reservados</p>
                <p class="text-xs text-gray-500 mt-1">
                    {{ \Carbon\Carbon::parse($allDates[0])->format('d/m/Y') }} a {{ \Carbon\Carbon::parse(end($allDates))->format('d/m/Y') }}
                </p>
            @elseif($event->booking)
                <p class="text-sm font-semibold text-green-700"><i class="fas fa-check-circle mr-1"></i>Reservado</p>
                <p class="text-xs text-gray-500 mt-1">{{ $event->booking->booking_date->format('d/m/Y H:i') }} até {{ $event->booking->end_date->format('H:i') }}</p>
            @else
                <p class="text-sm font-semibold text-gray-400">Sem reserva registrada</p>
            @endif
        </div>
    </div>

    {{-- Formulário de inscrição (não exibido quando o evento já foi encerrado) --}}
    @if($event->hasEnded())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex items-center gap-2 text-sm text-gray-600">
            <i class="fas fa-lock"></i> Evento encerrado — inscrições indisponíveis.
        </div>
    @else
    @php $locked = $event->isFull(); @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Inscrever Participante</h2>
            @if($event->isFull())
                <span class="text-xs font-bold uppercase px-3 py-1 rounded-full bg-red-100 text-red-700">Vagas Esgotadas</span>
            @endif
        </div>
        <form action="{{ route('events.participants.store', $event) }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Nome *</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm @error('name') border-red-500 @enderror"
                        placeholder="Nome completo do participante"
                        {{ $locked ? 'disabled' : '' }}>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">CPF</label>
                    <input type="text" name="cpf" id="participant_cpf" value="{{ old('cpf') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm font-mono @error('cpf') border-red-500 @enderror"
                        placeholder="000.000.000-00"
                        {{ $locked ? 'disabled' : '' }}>
                    @error('cpf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">WhatsApp</label>
                    <input type="text" name="whatsapp" id="participant_whatsapp" value="{{ old('whatsapp') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm @error('whatsapp') border-red-500 @enderror"
                        placeholder="(00)9 0000-0000"
                        {{ $locked ? 'disabled' : '' }}>
                    @error('whatsapp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm @error('email') border-red-500 @enderror"
                        placeholder="email@exemplo.com"
                        {{ $locked ? 'disabled' : '' }}>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @error('capacity')
                    <div class="md:col-span-2 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 font-semibold">
                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit"
                    {{ $locked ? 'disabled' : '' }}
                    class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-user-plus mr-1"></i> Inscrever
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Tabela de participantes --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Participantes Inscritos</h2>
            <span class="text-sm text-gray-500 font-semibold">{{ $event->participants->count() }} inscritos</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">CPF</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">WhatsApp</th>
                        <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">E-mail</th>
                        @if($event->status === 'completed')
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-center">Certificado</th>
                        @endif
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($event->participants as $i => $participant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-6 py-3 text-sm font-semibold text-gray-800">{{ $participant->name }}</td>
                            <td class="px-6 py-3 text-sm font-mono text-gray-600">
                                {{ $participant->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $participant->cpf) : '—' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">
                                @if($participant->whatsapp)
                                    @php
                                        $phone = preg_replace('/\D/', '', $participant->whatsapp);
                                        if (strlen($phone) <= 11) $phone = '55' . $phone;
                                        $wppMsg = "Olá, {$participant->name}! \n\nLembramos que você está inscrito(a) no evento *{$event->title}*.\n\n Data: " . $event->date->format('d/m/Y') . "\n - Horário: " . substr($event->start_time, 0, 5) . " às " . $event->endTime() . "\n Palestrante: " . $event->speaker->name . "\n\nContamos com sua presença!";
                                    @endphp
                                    <a href="https://wa.me/{{ $phone }}?text={{ urlencode($wppMsg) }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 font-medium transition"
                                        title="Enviar mensagem pelo WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                        {{ $participant->whatsapp }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $participant->email ?? '—' }}</td>
                            @if($event->status === 'completed')
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('events.certificate', [$event, $participant]) }}" target="_blank"
                                        title="Baixar certificado"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-800 transition">
                                        <i class="fas fa-certificate"></i>
                                        <span class="hidden md:inline">Certificado</span>
                                    </a>
                                </td>
                            @endif
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button" title="Editar participante"
                                        class="text-blue-500 hover:text-blue-700 transition"
                                        onclick="openEditModal({
                                            id: {{ json_encode($participant->id) }},
                                            name: {{ json_encode($participant->name) }},
                                            cpf: {{ json_encode($participant->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $participant->cpf) : '') }},
                                            whatsapp: {{ json_encode($participant->whatsapp ?? '') }},
                                            email: {{ json_encode($participant->email ?? '') }}
                                        })">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </button>
                                    <button type="button" title="Remover participante"
                                        class="text-red-500 hover:text-red-700 transition"
                                        onclick="openDeleteModal({{ json_encode(route('events.participants.destroy', [$event, $participant])) }}, {{ json_encode($participant->name) }})">
                                        <i class="fas fa-trash-alt fa-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $event->status === 'completed' ? 7 : 6 }}" class="px-6 py-8 text-center text-gray-400 italic">Nenhum participante inscrito ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Modal editar participante --}}
<div id="editParticipantModal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-lg">Editar Participante</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition text-xl leading-none">&times;</button>
        </div>
        <form id="editParticipantForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Nome *</label>
                <input type="text" name="name" id="edit_name"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                    placeholder="Nome completo" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">CPF</label>
                    <input type="text" name="cpf" id="edit_cpf"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm font-mono"
                        placeholder="000.000.000-00">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">WhatsApp</label>
                    <input type="text" name="whatsapp" id="edit_whatsapp"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                        placeholder="(00)9 0000-0000">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">E-mail</label>
                <input type="email" name="email" id="edit_email"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                    placeholder="email@exemplo.com">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()"
                    class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 transition">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal de confirmação de exclusão --}}
<div id="modal-delete-participant" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <i class="fas fa-user-minus text-red-500"></i>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-800">Remover participante</h2>
                <p class="text-sm text-gray-500">Tem certeza que deseja remover <strong id="delete-participant-name"></strong>?</p>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="button"
                onclick="document.getElementById('modal-delete-participant').style.display='none'"
                class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                Cancelar
            </button>
            <form id="delete-participant-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                    Remover
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cpfEl = document.getElementById('participant_cpf');
    if (cpfEl) IMask(cpfEl, { mask: '000.000.000-00' });

    const wppEl = document.getElementById('participant_whatsapp');
    if (wppEl) IMask(wppEl, { mask: '(00)0 0000-0000' });

    IMask(document.getElementById('edit_cpf'), { mask: '000.000.000-00' });
    IMask(document.getElementById('edit_whatsapp'), { mask: '(00)0 0000-0000' });
});

const baseUpdateUrl = '{{ route('events.participants.update', [$event, '__ID__']) }}';

function openEditModal(p) {
    document.getElementById('edit_name').value     = p.name;
    document.getElementById('edit_cpf').value      = p.cpf;
    document.getElementById('edit_whatsapp').value = p.whatsapp;
    document.getElementById('edit_email').value    = p.email;
    document.getElementById('editParticipantForm').action = baseUpdateUrl.replace('__ID__', p.id);

    const modal = document.getElementById('editParticipantModal');
    modal.style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editParticipantModal').style.display = 'none';
}

document.getElementById('editParticipantModal').addEventListener('click', function (e) {
    if (e.target === this) closeEditModal();
});

function openDeleteModal(url, name) {
    document.getElementById('delete-participant-name').textContent = name;
    document.getElementById('delete-participant-form').action = url;
    document.getElementById('modal-delete-participant').style.display = 'flex';
}

document.getElementById('modal-delete-participant').addEventListener('click', function (e) {
    if (e.target === this) this.style.display = 'none';
});

</script>
@endpush
@endsection
