@extends('layouts.app')

@section('title', 'Novo Agendamento - Empreende Vitória')

@section('content')
  <div class="max-w-4xl mx-auto">
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Novo Agendamento</h2>
      <p class="text-gray-600 italic">Preencha os dados abaixo para reservar o espaço.</p>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
      <form action="{{ route('bookings.store') }}" method="POST" class="p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nome do Responsável *</label>
            <input type="text" name="responsible_name" value="{{ old('responsible_name') }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('responsible_name') border-red-500 @enderror"
              placeholder="Ex: Marcelo Arruda" required>
            @error('responsible_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">CPF (Opcional)</label>
            <input type="text" name="cpf" id="cpf_mask" value="{{ old('cpf') }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
              placeholder="000.000.000-00">
          </div>

          @push('scripts')
            <script>
              document.addEventListener('DOMContentLoaded', function () {
                const element = document.getElementById('cpf_mask');
                const maskOptions = {
                  mask: '000.000.000-00'
                };
                const mask = IMask(element, maskOptions);
              });
            </script>
          @endpush

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Qtd. de Pessoas *</label>
            <input type="number" name="guests_count" value="{{ old('guests_count', 1) }}" min="1"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('guests_count') border-red-500 @enderror"
              required>
            @error('guests_count') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Data e Hora *</label>
            <input type="datetime-local" name="booking_date" value="{{ old('booking_date') }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('booking_date') border-red-500 @enderror"
              required>
            @error('booking_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Observações</label>
            <textarea name="observation" rows="4"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
              placeholder="Alguma necessidade especial?">{{ old('observation') }}</textarea>
          </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-4">
          <a href="{{ route('bookings.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
          <button type="submit"
            class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-800 transition">
            Confirmar Agendamento
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection