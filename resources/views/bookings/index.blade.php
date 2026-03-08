@extends('layouts.app')

@section('title', 'Agendamentos - Empreende Vitória')

@section('content')
{{-- Estrutura corrigida: vírgulas no lugar e variáveis padronizadas --}}
<div x-data="{ 
    openModal: false, 
    openDeleteModal: false, 
    selectedBooking: {}, 
    bookingToDelete: null,
    bookingToDeleteName: '' 
}" class="max-w-6xl mx-auto">
    
    {{-- Cabeçalho da Página --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-calendar-check text-blue-900 mr-2"></i> Agendamentos
            </h2>
            <p class="text-gray-600 italic text-sm md:text-base">Lista de reservas em Vitória de Santo Antão.</p>
        </div>
        <a href="{{ route('bookings.create') }}" class="bg-blue-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-800 transition shadow-sm flex items-center">
            <i class="fas fa-plus md:mr-2"></i>
            <span class="hidden md:inline">Novo Agendamento</span>
        </a>
    </div>

    {{-- Tabela de Dados --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 w-16">#</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 ">Responsável</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Qtd. Pessoas</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 ">Data e Hora</th>
                        <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $booking->responsible_name }}</td>
                            <td class="px-6 py-4 text-sm text-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded-full">
                                    {{ $booking->guests_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                {{ $booking->booking_date->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <div class="flex justify-center space-x-4">
                                    {{-- Botão Visualizar --}}
                                    <button @click="selectedBooking = {{ $booking->toJson() }}; openModal = true" 
                                            class="text-blue-600 hover:text-blue-900 transition">
                                        <i class="fas fa-eye fa-lg"></i>
                                    </button>

                                    {{-- Botão Editar --}}
                                    <a href="{{ route('bookings.edit', $booking->id) }}" class="text-yellow-600 hover:text-yellow-900 transition">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </a>

                                    {{-- Botão Excluir --}}
                                    <button @click="bookingToDelete = {{ $booking->id }}; bookingToDeleteName = '{{ addslashes($booking->responsible_name) }}'; openDeleteModal = true" 
                                            class="text-red-600 hover:text-red-900 transition">
                                        <i class="fas fa-trash-alt fa-lg"></i>
                                    </button>

                                    {{-- Form Invisível --}}
                                    <form id="delete-form-{{ $booking->id }}" action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Nenhum agendamento.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL DE VISUALIZAÇÃO RESTAURADO --}}
    <div x-show="openModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition x-cloak>
        <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-blue-900 p-5 flex justify-between items-center text-white">
                <h3 class="font-bold text-xl"><i class="fas fa-info-circle mr-2"></i> Detalhes do Registro</h3>
                <button @click="openModal = false" class="text-white hover:text-gray-300"><i class="fas fa-times fa-lg"></i></button>
            </div>
            <div class="p-8 space-y-6">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Responsável</label>
                    <p class="text-lg text-gray-800 font-bold border-b pb-2" x-text="selectedBooking.responsible_name"></p>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">CPF</label>
                        <p class="text-gray-800 font-medium" x-text="selectedBooking.cpf ? selectedBooking.cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4') : 'Não informado'"></p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total de Pessoas</label>
                        <p class="text-gray-800 font-medium"><i class="fas fa-users mr-1 text-blue-900"></i> <span x-text="selectedBooking.guests_count"></span></p>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Data do Evento</label>
                    <p class="text-gray-800 font-medium">
                        <i class="fas fa-clock mr-1 text-blue-900"></i> 
                        <span x-text="selectedBooking.booking_date ? new Date(selectedBooking.booking_date).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' }) : ''"></span>
                    </p>
                </div>

                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                    <label class="text-xs font-bold text-blue-400 uppercase tracking-wider">Observações Internas</label>
                    <p class="text-blue-900 text-sm mt-1 leading-relaxed" x-text="selectedBooking.observation || 'Nenhuma observação cadastrada.'"></p>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-2 border-t">
                <button @click="openModal = false" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-bold hover:bg-gray-300 transition text-sm">Fechar</button>
                <a :href="'/bookings/' + selectedBooking.id + '/edit'" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-800 transition text-sm">Editar Agora</a>
            </div>
        </div>
    </div>

    {{-- MODAL DE EXCLUSÃO ESTILIZADO --}}
    <div x-show="openDeleteModal" 
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-transition x-cloak>
        <div @click.away="openDeleteModal = false" class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border-t-4 border-red-600">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-trash-alt fa-2x"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Excluir Agendamento?</h3>
                <p class="text-gray-600 text-sm italic leading-relaxed">
                    Deseja realmente remover o agendamento de:<br>
                    <span class="font-bold text-red-600" x-text="bookingToDeleteName"></span>?
                </p>
            </div>
            <div class="p-4 bg-gray-50 flex space-x-2 border-t border-gray-100">
                <button @click="openDeleteModal = false" class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition">Manter</button>
                <button @click="document.getElementById('delete-form-' + bookingToDelete).submit()" 
                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition shadow-md">Sim, Excluir</button>
            </div>
        </div>
    </div>
</div>
@endsection