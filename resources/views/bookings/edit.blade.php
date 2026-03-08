@extends('layouts.app')

@section('title', 'Editar Agendamento - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-900 mr-2"></i> Editar Agendamento
            </h2>
            <p class="text-gray-600 italic">Atualize as informações do responsável ou a data do evento.</p>
        </div>
        <a href="{{ route('bookings.index') }}" class="text-blue-900 hover:underline font-semibold">
            <i class="fas fa-arrow-left mr-1"></i> Voltar para Lista
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <form action="{{ route('bookings.update', $booking->id) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nome do Responsável *</label>
                    <input type="text" name="responsible_name" 
                        value="{{ old('responsible_name', $booking->responsible_name) }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('responsible_name') border-red-500 @enderror" 
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">CPF (Opcional)</label>
                    <input type="text" name="cpf" id="cpf_mask" 
                        value="{{ old('cpf', $booking->cpf) }}" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                        placeholder="000.000.000-00">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Qtd. de Pessoas *</label>
                    <input type="number" name="guests_count" 
                        value="{{ old('guests_count', $booking->guests_count) }}" min="1"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                        required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Data e Hora *</label>
                    <input type="datetime-local" name="booking_date" 
                        value="{{ old('booking_date', $booking->booking_date->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                        required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Observações</label>
                    <textarea name="observation" rows="4" 
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">{{ old('observation', $booking->observation) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-800 transition shadow-md">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const element = document.getElementById('cpf_mask');
        const maskOptions = { mask: '000.000.000-00' };
        IMask(element, maskOptions);
    });
</script>
@endpush
@endsection