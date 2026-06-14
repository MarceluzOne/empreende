@extends('layouts.app')

@section('title', 'Nova Reserva - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
        type: '{{ old('type', 'single') }}',
        resourceType: '{{ old('resource_type', 'auditorio') }}',
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
        }
    }"
    @date-selected.window="onDateSelected($event)"
    @time-selected.window="onTimeSelected($event)">

    {{-- Cabeçalho --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">Nova Reserva</h2>
            <p class="text-gray-500 italic text-sm">Reserve o Auditório ou Sala de Reunião.</p>
        </div>
        <a href="{{ route('bookings.index') }}"
            class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl font-bold hover:bg-gray-200 transition text-sm">
            Voltar
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <form action="{{ route('bookings.store') }}" method="POST" class="p-8 md:p-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Local da Reserva --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Local da Reserva *</label>
                    <select name="resource_type"
                        x-model="resourceType"
                        @change="$dispatch('resource-changed', { value: $event.target.value })"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-700"
                        required>
                        <option value="auditorio" {{ old('resource_type') == 'auditorio' ? 'selected' : '' }}>Auditório</option>
                        <option value="reuniao" {{ old('resource_type') == 'reuniao' ? 'selected' : '' }}>Sala de Reunião</option>
                    </select>
                </div>

                {{-- Nome do Responsável --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nome do Responsável *</label>
                    <input type="text" name="responsible_name" value="{{ old('responsible_name') }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800 @error('responsible_name') border-red-500 @enderror"
                        placeholder="Nome completo" required>
                    @error('responsible_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- CPF e Telefone --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">CPF (Opcional)</label>
                    <input type="text" name="cpf" id="cpf_mask" value="{{ old('cpf') }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800"
                        placeholder="000.000.000-00">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Telefone para Contato</label>
                    <input type="text" name="phone" id="phone_mask" value="{{ old('phone') }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800"
                        placeholder="(00) 00000-0000">
                    @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Qtd. de Pessoas *</label>
                    <input type="number" name="guests_count" value="{{ old('guests_count', 1) }}" min="1"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800"
                        required>
                </div>

                {{-- Tipo de Reserva --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Tipo de Reserva</label>
                    <div class="flex bg-gray-100 p-1 rounded-2xl w-full">
                        <button type="button" @click="type = 'single'"
                            :class="type === 'single' ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 py-2 rounded-xl font-bold text-sm transition-all">
                            <input type="radio" name="type" value="single" x-model="type" class="hidden">
                            Dia Único
                        </button>
                        <button type="button" @click="type = 'consecutive'"
                            :class="type === 'consecutive' ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 py-2 rounded-xl font-bold text-sm transition-all">
                            <input type="radio" name="type" value="consecutive" x-model="type" class="hidden">
                            Consecutivos
                        </button>
                        <button type="button" @click="type = 'alternated'"
                            :class="type === 'alternated' ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-4 py-2 rounded-xl font-bold text-sm transition-all">
                            <input type="radio" name="type" value="alternated" x-model="type" class="hidden">
                            Alternados
                        </button>
                    </div>
                </div>

                {{-- Calendário --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                        Disponibilidade —
                        <span x-text="type === 'single' ? 'clique em um dia para selecioná-lo' : (type === 'consecutive' ? 'clique no início e depois no fim' : 'clique para adicionar dias')"></span>
                    </label>
                    @include('bookings.partials._calendar')
                </div>

                {{-- Hidden inputs --}}
                <div class="md:col-span-2 hidden">
                    <input type="hidden" name="start_time" x-model="startTime">
                    <input type="hidden" name="end_time" x-model="endTime">
                    <input type="hidden" name="single_date" x-model="singleDate">
                    <input type="hidden" name="start_date" x-model="startDate">
                    <input type="hidden" name="end_date_period" x-model="endDatePeriod">
                    <template x-for="(date, index) in selectedDates" :key="index">
                        <input type="hidden" name="selected_dates[]" :value="date">
                    </template>
                </div>

                {{-- Resumo horário --}}
                <div x-show="startTime && endTime" class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-clock mr-1"></i>
                    Horário: <span x-text="startTime"></span> até <span x-text="endTime"></span>
                </div>

                {{-- Resumo datas alternadas --}}
                <div x-show="type === 'alternated' && selectedDates.length > 0" class="md:col-span-2">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Dias selecionados:</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(date, index) in selectedDates" :key="'tag-' + index">
                            <div class="bg-blue-100 border border-blue-300 text-blue-900 px-3 py-1 rounded-full flex items-center text-sm font-bold">
                                <span x-text="date.split('-').reverse().join('/')"></span>
                                <button type="button" @click="removeDate(index)" class="ml-2 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Resumo consecutivo --}}
                <div x-show="type === 'consecutive' && startDate" class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <span x-show="!endDatePeriod">De: <span x-text="startDate.split('-').reverse().join('/')"></span> — clique no dia de fim</span>
                    <span x-show="endDatePeriod">De <span x-text="startDate.split('-').reverse().join('/')"></span> até <span x-text="endDatePeriod.split('-').reverse().join('/')"></span></span>
                </div>

                {{-- Resumo dia único --}}
                <div x-show="type === 'single' && singleDate" class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-calendar-day mr-1"></i>
                    Data selecionada: <span x-text="singleDate.split('-').reverse().join('/')"></span>
                </div>

                {{-- Motivo --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Motivo da Reserva *</label>
                    <input type="text" name="reason" value="{{ old('reason') }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800 @error('reason') border-red-500 @enderror"
                        placeholder="Ex: Reunião, Palestra, Treinamento..." required>
                    @error('reason') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Observações --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observações</label>
                    <textarea name="observation" rows="3"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-semibold text-gray-800 placeholder:italic"
                        placeholder="Alguma necessidade especial?">{{ old('observation') }}</textarea>
                </div>
            </div>

            <div class="mt-10">
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-5 rounded-2xl font-semibold uppercase tracking-widest shadow-2xl active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-check sm:hidden"></i>
                    <span class="hidden sm:inline">Confirmar Reserva</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/imask"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cpf = document.getElementById('cpf_mask');
        if (cpf) IMask(cpf, { mask: '000.000.000-00' });

        const phone = document.getElementById('phone_mask');
        if (phone) {
            IMask(phone, {
                mask: [{ mask: '(00) 0000-0000' }, { mask: '(00) 00000-0000' }],
                dispatch: (appended, dynamicMasked) => {
                    const value = (dynamicMasked.value + appended).replace(/\D/g, '');
                    return dynamicMasked.compiledMasks[value.length > 10 ? 1 : 0];
                }
            });
        }
    });
</script>
@endpush
@endsection
