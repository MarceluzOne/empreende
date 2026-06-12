@extends('layouts.app')

@section('title', 'Editar Agendamento - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto"
    x-data="{
        resourceType: '{{ old('resource_type', $booking->resource_type) }}',
        startTime: '{{ old('start_time', $booking->booking_date->format('H:i')) }}',
        endTime: '{{ old('end_time', $booking->end_date?->format('H:i') ?? '') }}',
        onDateSelected(e) {
            const input = document.querySelector('[name=date]');
            if (input) input.value = e.detail.date;
        },
        onTimeSelected(e) {
            this.startTime = e.detail.start;
            this.endTime   = e.detail.end;
        }
    }"
    @date-selected.window="onDateSelected($event)"
    @time-selected.window="onTimeSelected($event)">

    {{-- Cabeçalho --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Editar Agendamento</h2>
            <p class="text-gray-500 italic text-sm">Atualizando: <span class="font-bold text-blue-900">{{ $booking->responsible_name }}</span></p>
        </div>
        <a href="{{ route('bookings.index') }}"
            class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl font-bold hover:bg-gray-200 transition text-sm">
            Voltar
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <form action="{{ route('bookings.update', $booking->id) }}" method="POST" class="p-8 md:p-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Local do Agendamento --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Local do Agendamento *</label>
                    <select name="resource_type"
                        x-model="resourceType"
                        @change="$dispatch('resource-changed', { value: $event.target.value })"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-700"
                        required>
                        <option value="auditorio" {{ old('resource_type', $booking->resource_type) == 'auditorio' ? 'selected' : '' }}>Auditório</option>
                        <option value="reuniao" {{ old('resource_type', $booking->resource_type) == 'reuniao' ? 'selected' : '' }}>Sala de Reunião</option>
                    </select>
                </div>

                {{-- Nome do Responsável --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nome do Responsável *</label>
                    <input type="text" name="responsible_name"
                        value="{{ old('responsible_name', $booking->responsible_name) }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800 @error('responsible_name') border-red-500 @enderror"
                        placeholder="Nome completo" required>
                    @error('responsible_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- CPF e Quantidade --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">CPF (Opcional)</label>
                    <input type="text" name="cpf" id="cpf_mask"
                        value="{{ old('cpf', $booking->cpf) }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800"
                        placeholder="000.000.000-00">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Qtd. de Pessoas *</label>
                    <input type="number" name="guests_count"
                        value="{{ old('guests_count', $booking->guests_count) }}" min="1"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800"
                        required>
                </div>

                {{-- Calendário --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                        Disponibilidade — clique em um dia para selecioná-lo
                    </label>
                    @include('bookings.partials._calendar', [
                        'preselectedDate'  => old('date', $booking->booking_date->format('Y-m-d')),
                        'preselectedStart' => old('start_time', $booking->booking_date->format('H:i')),
                        'preselectedEnd'   => old('end_time', $booking->end_date?->format('H:i') ?? ''),
                    ])
                </div>

                {{-- Hidden inputs de data/hora (preenchidos pelo calendário) --}}
                <div class="md:col-span-2 hidden">
                    <input type="date" name="date"
                        value="{{ old('date', $booking->booking_date->format('Y-m-d')) }}">
                    <input type="time" name="start_time" x-model="startTime">
                    <input type="time" name="end_time" x-model="endTime">
                </div>

                {{-- Resumo do horário selecionado --}}
                <div x-show="startTime && endTime" class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-clock mr-1"></i>
                    Horário selecionado: <span x-text="startTime"></span> até <span x-text="endTime"></span>
                </div>

                {{-- Motivo --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Motivo da Reserva *</label>
                    <input type="text" name="reason"
                        value="{{ old('reason', $booking->reason) }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800 @error('reason') border-red-500 @enderror"
                        placeholder="Ex: Reunião, Palestra, Treinamento..." required>
                    @error('reason') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Observações --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observações</label>
                    <textarea name="observation" rows="3"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-semibold text-gray-800 placeholder:italic"
                        placeholder="Alguma necessidade especial?">{{ old('observation', $booking->observation) }}</textarea>
                </div>
            </div>

            <div class="mt-10">
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-semibold uppercase tracking-widest shadow-2xl active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-check sm:hidden"></i>
                    <span class="hidden sm:inline">Atualizar Agendamento</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cpf = document.getElementById('cpf_mask');
        if (cpf) IMask(cpf, { mask: '000.000.000-00' });
    });
</script>
@endpush
@endsection
