@extends('layouts.app')

@section('title', 'Novo Agendamento - Empreende Vitória')

@section('content')
  <div class="max-w-4xl mx-auto" x-data="{ 
        type: '{{ old('type', 'single') }}',
        selectedDates: [],
        addDate(date) {
            if(date && !this.selectedDates.includes(date)) {
                this.selectedDates.push(date);
            }
        },
        removeDate(index) {
            this.selectedDates.splice(index, 1);
        }
    }">
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">Novo Agendamento</h2>
        <p class="text-gray-600 italic">Reserve o Auditório ou Sala de Reunião.</p>
      </div>
      <a href="{{ route('bookings.index') }}" class="text-blue-900 font-bold hover:underline text-sm">
        <i class="fas fa-arrow-left mr-1"></i> Voltar
      </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
      <form action="{{ route('bookings.store') }}" method="POST" class="p-8">
        @csrf
        <div class="md:col-span-2   mb-4">
          <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Local do Agendamento *</label>
          <select name="resource_type"
            class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-gray-700">
            <option value="auditorio" {{ old('resource_type') == 'auditorio' ? 'selected' : '' }}>Auditório </option>
            <option value="reuniao" {{ old('resource_type') == 'reuniao' ? 'selected' : '' }}>Sala de Reunião</option>
          </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

          {{-- Nome do Responsável --}}
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nome do Responsável
              *</label>
            <input type="text" name="responsible_name" value="{{ old('responsible_name') }}"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('responsible_name') border-red-500 @enderror"
              placeholder="Ex: Marcelo Arruda" required>
            @error('responsible_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
          </div>

          {{-- CPF e Quantidade --}}
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">CPF (Opcional)</label>
            <input type="text" name="cpf" id="cpf_mask" value="{{ old('cpf') }}"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
              placeholder="000.000.000-00">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Qtd. de Pessoas *</label>
            <input type="number" name="guests_count" value="{{ old('guests_count', 1) }}" min="1"
              class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
          </div>

          <hr class="md:col-span-2 border-gray-100">

          {{-- SELETOR DE TIPO DE RESERVA --}}
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide text-blue-900">Tipo de
              Reserva</label>
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

          {{-- CAMPOS DE HORÁRIO (Sempre visíveis) --}}
          <div class="p-4 bg-blue-900 rounded-xl md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 text-white">
            <div>
              <label class="block text-xs font-bold uppercase mb-1">Horário de Início *</label>
              <input type="time" name="start_time" value="{{ old('start_time') }}"
                class="w-full px-4 py-2 rounded-lg text-gray-900 outline-none" required>
            </div>
            <div>
              <label class="block text-xs font-bold uppercase mb-1">Horário de Término *</label>
              <input type="time" name="end_time" value="{{ old('end_time') }}"
                class="w-full px-4 py-2 rounded-lg text-gray-900 outline-none" required>
            </div>
          </div>

          {{-- LÓGICA DE DATAS DINÂMICAS --}}
          <div class="md:col-span-2 border-2 border-dashed border-gray-200 rounded-xl p-6 bg-gray-50">
            {{-- Caso: Dia Único --}}
            <div x-show="type === 'single'">
              <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Data da Reserva *</label>
              <input type="date" name="single_date" value="{{ old('single_date') }}"
                class="w-full px-4 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Caso: Dias Consecutivos --}}
            <div x-show="type === 'consecutive'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">De (Início) *</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                  class="w-full px-4 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Até (Fim) *</label>
                <input type="date" name="end_date_period" value="{{ old('end_date_period') }}"
                  class="w-full px-4 py-3 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
              </div>
            </div>

            {{-- Caso: Dias Alternados --}}
            <div x-show="type === 'alternated'">
              <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Adicionar Datas
                Específicas</label>
              <div class="flex gap-2 mb-4">
                <input type="date" x-ref="dateInput" class="flex-1 px-4 py-2 border rounded-lg outline-none">
                <button type="button" @click="addDate($refs.dateInput.value)"
                  class="bg-blue-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-800 transition">
                  <i class="fas fa-plus"></i>
                </button>
              </div>

              {{-- Lista de Datas Selecionadas --}}
              <div class="flex flex-wrap gap-2">
                <template x-for="(date, index) in selectedDates" :key="index">
                  <div
                    class="bg-white border border-blue-200 text-blue-900 px-3 py-1 rounded-full flex items-center text-sm font-bold shadow-sm">
                    <span x-text="date.split('-').reverse().join('/')"></span>
                    <input type="hidden" name="selected_dates[]" :value="date">
                    <button type="button" @click="removeDate(index)" class="ml-2 text-red-500 hover:text-red-700">
                      <i class="fas fa-times-circle"></i>
                    </button>
                  </div>
                </template>
              </div>
            </div>
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
            class="bg-blue-900 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-800 transition shadow-lg transform hover:-translate-y-1">
            Confirmar Reserva
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
      });
    </script>
  @endpush
@endsection