@extends('layouts.app')

@section('title', 'Novo Agendamento - Empreende Vitória')

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

    <div class="mb-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Nova reserva</h2>
        <p class="text-gray-600 italic">Reserve o Auditório ou Sala de Reunião.</p>
      </div>
      <a href="{{ route('bookings.index') }}" class="text-blue-900 font-bold hover:underline text-sm">
        Voltar
      </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
      <form action="{{ route('bookings.store') }}" method="POST" class="p-8">
        @csrf
        <div class="md:col-span-2 mb-4">
          <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
            Local da Reserva
          </label>

          <div class="relative w-full">
            <select name="resource_type"
              x-model="resourceType"
              @change="$dispatch('resource-changed', { value: $event.target.value })"
              class="appearance-none w-full px-4 py-3 pr-12 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-gray-700 cursor-pointer">
              <option value="auditorio" {{ old('resource_type') == 'auditorio' ? 'selected' : '' }}>Auditório</option>
              <option value="reuniao" {{ old('resource_type') == 'reuniao' ? 'selected' : '' }}>Sala de Reunião</option>
            </select>

            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                  d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  clip-rule="evenodd" />
              </svg>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          {{-- Nome do Responsável --}}
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nome do Responsável</label>
            <input type="text" name="responsible_name" value="{{ old('responsible_name') }}"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('responsible_name') border-red-500 @enderror"
              placeholder="Ex: Marcelo Arruda" required>
            @error('responsible_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
          </div>

          {{-- CPF e Telefone --}}
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">CPF (Opcional)</label>
            <input type="text" name="cpf" id="cpf_mask" value="{{ old('cpf') }}"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
              placeholder="000.000.000-00">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Telefone para Contato</label>
            <input type="text" name="phone" id="phone_mask" value="{{ old('phone') }}"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('phone') border-red-500 @enderror"
              placeholder="(00) 00000-0000">
            @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Qtd. de Pessoas</label>
            <input type="number" name="guests_count" value="{{ old('guests_count', 1) }}" min="1"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
          </div>

          <hr class="md:col-span-2 border-gray-100">

          {{-- SELETOR DE TIPO DE RESERVA --}}
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Tipo de Reserva</label>
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

          {{-- CALENDÁRIO VISUAL + GRADE DE HORÁRIOS --}}
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
              Disponibilidade —
              <span x-text="type === 'single' ? 'clique em um dia para selecioná-lo' : (type === 'consecutive' ? 'clique no dia de início, depois no de fim' : 'clique para adicionar dias')"></span>
            </label>
            @include('bookings.partials._calendar')
          </div>

          {{-- Inputs hidden para envio do formulário --}}
          <div class="md:col-span-2">
            <input type="hidden" name="start_time" x-model="startTime">
            <input type="hidden" name="end_time" x-model="endTime">
            <input type="hidden" name="single_date" x-model="singleDate">
            <input type="hidden" name="start_date" x-model="startDate">
            <input type="hidden" name="end_date_period" x-model="endDatePeriod">
            <template x-for="(date, index) in selectedDates" :key="index">
              <input type="hidden" name="selected_dates[]" :value="date">
            </template>
          </div>

          {{-- Resumo do horário selecionado --}}
          <div x-show="startTime && endTime" class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
            <i class="fas fa-clock mr-1"></i>
            Horário selecionado: <span x-text="startTime"></span> até <span x-text="endTime"></span>
          </div>

          {{-- Resumo das datas selecionadas (modo alternado) --}}
          <div x-show="type === 'alternated' && selectedDates.length > 0" class="md:col-span-2">
            <p class="text-xs font-bold text-gray-500 uppercase mb-2">Dias selecionados:</p>
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

          {{-- Resumo das datas consecutivas --}}
          <div x-show="type === 'consecutive' && startDate" class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
            <i class="fas fa-calendar-alt mr-1"></i>
            <span x-show="!endDatePeriod">De: <span x-text="startDate.split('-').reverse().join('/')"></span> — clique no dia de fim no calendário</span>
            <span x-show="endDatePeriod">De <span x-text="startDate.split('-').reverse().join('/')"></span> até <span x-text="endDatePeriod.split('-').reverse().join('/')"></span></span>
          </div>

          {{-- Resumo do dia único --}}
          <div x-show="type === 'single' && singleDate" class="md:col-span-2 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
            <i class="fas fa-calendar-day mr-1"></i>
            Data selecionada: <span x-text="singleDate.split('-').reverse().join('/')"></span>
          </div>

          {{-- Observações --}}
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Observações</label>
            <textarea name="observation" rows="3"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
              placeholder="Alguma necessidade especial?">{{ old('observation') }}</textarea>
          </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-4 border-t pt-6">
          <a href="{{ route('bookings.index') }}" class="text-gray-500 font-semibold hover:text-gray-700">Cancelar</a>
          <button type="submit"
            class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg transform hover:-translate-y-1">
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
        const element = document.getElementById('cpf_mask');
        if (element) {
          IMask(element, { mask: '000.000.000-00' });
        }

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
