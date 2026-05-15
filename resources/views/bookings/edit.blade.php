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

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Agendamento</h2>
            <p class="text-gray-600 italic">Atualize as informações deste registro específico.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="text-blue-900 hover:underline font-semibold">
            Voltar para Lista
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <form action="{{ route('bookings.update', $booking->id) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Local do Agendamento --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Local do Agendamento *</label>
                    <select name="resource_type"
                        x-model="resourceType"
                        @change="$dispatch('resource-changed', { value: $event.target.value })"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-gray-50 font-semibold" required>
                        <option value="auditorio" {{ old('resource_type', $booking->resource_type) == 'auditorio' ? 'selected' : '' }}>Auditório</option>
                        <option value="reuniao" {{ old('resource_type', $booking->resource_type) == 'reuniao' ? 'selected' : '' }}>Sala de Reunião</option>
                    </select>
                </div>

                {{-- Nome do Responsável --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nome do Responsável *</label>
                    <input type="text" name="responsible_name"
                        value="{{ old('responsible_name', $booking->responsible_name) }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('responsible_name') border-red-500 @enderror"
                        required>
                </div>

                {{-- CPF e Quantidade --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">CPF (Opcional)</label>
                    <input type="text" name="cpf" id="cpf_mask"
                        value="{{ old('cpf', $booking->cpf) }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="000.000.000-00">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Qtd. de Pessoas *</label>
                    <input type="number" name="guests_count"
                        value="{{ old('guests_count', $booking->guests_count) }}" min="1"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        required>
                </div>

                <hr class="md:col-span-2 border-gray-100">

                {{-- CALENDÁRIO VISUAL + GRADE DE HORÁRIOS --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                        Disponibilidade — clique em um dia para selecioná-lo
                    </label>
                    @include('bookings.partials._calendar', [
                        'preselectedDate'  => old('date', $booking->booking_date->format('Y-m-d')),
                        'preselectedStart' => old('start_time', $booking->booking_date->format('H:i')),
                        'preselectedEnd'   => old('end_time', $booking->end_date?->format('H:i') ?? ''),
                    ])
                </div>

                {{-- Data e Horários --}}
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Data *</label>
                        <input type="date" name="date"
                            value="{{ old('date', $booking->booking_date->format('Y-m-d')) }}"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        <p class="text-xs text-gray-500 mt-1">Ou selecione no calendário acima.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Hora Início *</label>
                        <input type="time" name="start_time" x-model="startTime"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Hora Término *</label>
                        <input type="time" name="end_time" x-model="endTime"
                            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Observações</label>
                    <textarea name="observation" rows="3"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('observation', $booking->observation) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('bookings.index') }}" class="text-gray-500 font-semibold hover:text-gray-700">Descartar Alterações</a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg transform hover:-translate-y-1">
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
        const element = document.getElementById('cpf_mask');
        if (element) {
            IMask(element, { mask: '000.000.000-00' });
        }
    });
</script>
@endpush
@endsection
