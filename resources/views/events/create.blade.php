@extends('layouts.app')

@section('title', 'Novo Evento - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
        type: '{{ old('type', 'single') }}',
        startTime: '{{ old('start_time', '') }}',
        endTime: '{{ old('end_time', '') }}',
        consecutiveStep: 'start',
        singleDate: '{{ old('single_date', '') }}',
        startDate: '{{ old('start_date', '') }}',
        endDatePeriod: '{{ old('end_date_period', '') }}',
        selectedDates: [],
        addDate(date) {
            if (date && !this.selectedDates.includes(date)) {
                this.selectedDates.push(date);
                this.$dispatch('dates-updated', { dates: this.selectedDates });
            }
        },
        removeDate(index) {
            this.selectedDates.splice(index, 1);
            this.$dispatch('dates-updated', { dates: this.selectedDates });
        },
        onDateSelected(e) {
            const date = e.detail.date;
            if (this.type === 'single') {
                this.singleDate = date;
            } else if (this.type === 'consecutive') {
                if (this.consecutiveStep === 'start') {
                    this.startDate = date;
                    this.consecutiveStep = 'end';
                } else {
                    this.endDatePeriod = date;
                    this.consecutiveStep = 'start';
                }
            } else if (this.type === 'alternated') {
                if (this.selectedDates.includes(date)) {
                    this.removeDate(this.selectedDates.indexOf(date));
                } else {
                    this.addDate(date);
                }
            }
        },
        onTimeSelected(e) {
            this.startTime = e.detail.start;
            this.endTime   = e.detail.end;
            const [sh, sm] = this.startTime.split(':').map(Number);
            const [eh, em] = this.endTime.split(':').map(Number);
            this.durationMinutes = (eh * 60 + em) - (sh * 60 + sm);
        },
        durationMinutes: 0
    }"
    @date-selected.window="onDateSelected($event)"
    @time-selected.window="onTimeSelected($event)">

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Novo Evento</h2>
            <p class="text-gray-600 italic text-sm">O auditório será reservado automaticamente nos dias selecionados.</p>
        </div>
        <a href="{{ route('events.index') }}" class="text-blue-900 font-bold hover:underline text-sm">Voltar</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <form action="{{ route('events.store') }}" method="POST" class="p-8">
            @csrf

            {{-- Título e Palestrante --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Título do Evento *</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('title') border-red-500 @enderror"
                        placeholder="Ex: Workshop de Formalização MEI" required>
                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Palestrante *</label>
                    <div class="flex gap-2">
                        <select name="speaker_id"
                            class="flex-1 px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white @error('speaker_id') border-red-500 @enderror" required>
                            <option value="">Selecione</option>
                            @foreach($speakers as $speaker)
                                <option value="{{ $speaker->id }}" {{ old('speaker_id') == $speaker->id ? 'selected' : '' }}>
                                    {{ $speaker->name }}
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('speakers.create') }}" target="_blank"
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 border rounded-lg text-sm text-gray-600 font-semibold transition whitespace-nowrap">
                            + Novo
                        </a>
                    </div>
                    @error('speaker_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    @if($speakers->isEmpty())
                        <p class="text-xs text-orange-600 mt-1 font-semibold">
                            Nenhum palestrante. <a href="{{ route('speakers.create') }}" class="underline">Cadastre um primeiro.</a>
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Capacidade *</label>
                    <input type="number" name="max_capacity" value="{{ old('max_capacity', 50) }}" min="1"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('max_capacity') border-red-500 @enderror" required>
                    @error('max_capacity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="border-gray-100 mb-5">

            {{-- Tipo de reserva de dias --}}
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Duração do Evento</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label class="flex items-center p-3 border rounded-xl cursor-pointer transition hover:bg-gray-50"
                        :class="type === 'single' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="single" x-model="type" class="mr-2">
                        <span class="text-sm font-semibold">Dia Único</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-xl cursor-pointer transition hover:bg-gray-50"
                        :class="type === 'consecutive' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="consecutive" x-model="type" class="mr-2">
                        <span class="text-sm font-semibold">Dias Consecutivos</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-xl cursor-pointer transition hover:bg-gray-50"
                        :class="type === 'alternated' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="alternated" x-model="type" class="mr-2">
                        <span class="text-sm font-semibold">Dias Alternados</span>
                    </label>
                </div>
            </div>

            {{-- Calendário --}}
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Disponibilidade do Auditório —
                    <span x-text="type === 'single' ? 'clique em um dia' : (type === 'consecutive' ? 'clique no início e depois no fim' : 'clique para adicionar dias')"></span>
                </label>
                {{-- Input hidden para o calendário ler resource_type --}}
                <input type="hidden" name="resource_type" value="auditorio">
                @include('bookings.partials._calendar')
            </div>

            {{-- Hidden inputs para submissão --}}
            <div>
                <input type="hidden" name="start_time" x-model="startTime">
                <input type="hidden" name="end_time" x-model="endTime">
                <input type="hidden" name="duration_minutes" x-model="durationMinutes">
                <input type="hidden" name="single_date" x-model="singleDate">
                <input type="hidden" name="start_date" x-model="startDate">
                <input type="hidden" name="end_date_period" x-model="endDatePeriod">
                <template x-for="(date, index) in selectedDates" :key="index">
                    <input type="hidden" name="selected_dates[]" :value="date">
                </template>
            </div>

            {{-- Resumos de seleção --}}
            <div class="space-y-2 mb-5">
                <div x-show="startTime && endTime" class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-clock mr-1"></i>
                    Horário: <span x-text="startTime"></span> até <span x-text="endTime"></span>
                </div>
                <div x-show="type === 'single' && singleDate" class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-calendar-day mr-1"></i>
                    Data: <span x-text="singleDate.split('-').reverse().join('/')"></span>
                </div>
                <div x-show="type === 'consecutive' && startDate" class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <span x-show="!endDatePeriod">De <span x-text="startDate.split('-').reverse().join('/')"></span> — clique no dia de fim</span>
                    <span x-show="endDatePeriod">De <span x-text="startDate.split('-').reverse().join('/')"></span> até <span x-text="endDatePeriod.split('-').reverse().join('/')"></span></span>
                </div>
                <div x-show="type === 'alternated' && selectedDates.length > 0" class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-xs font-bold text-blue-700 uppercase mb-2">Dias selecionados:</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(date, index) in selectedDates" :key="'tag-'+index">
                            <div class="bg-white border border-blue-300 text-blue-900 px-3 py-1 rounded-full flex items-center text-sm font-bold">
                                <span x-text="date.split('-').reverse().join('/')"></span>
                                <button type="button" @click="removeDate(index)" class="ml-2 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('events.index') }}" class="text-gray-500 font-semibold hover:text-gray-700">Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                    <i class="fas fa-check sm:hidden"></i>
                    <span class="hidden sm:inline">Criar Evento</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const element = document.getElementById('cpf_mask');
    if (element) IMask(element, { mask: '000.000.000-00' });
});
</script>
@endpush
@endsection
