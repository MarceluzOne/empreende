@extends('layouts.app')

@section('title', 'Agendamentos - Empreende Vitória')

@section('content')
    <div x-data="{
        openModal: false,
        openDeleteModal: false,
        openBulkDeleteModal: false,
        selectedBooking: {},
        bookingToDelete: null,
        bookingToDeleteName: '',
        selectedIds: [],
        toggleAll(event) {
            const checkboxes = document.querySelectorAll('.booking-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = event.target.checked;
                const id = parseInt(cb.value);
                if (event.target.checked) {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                } else {
                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                }
            });
        },
        toggle(id) {
            if (this.selectedIds.includes(id)) {
                this.selectedIds = this.selectedIds.filter(i => i !== id);
            } else {
                this.selectedIds.push(id);
            }
        }
    }">

        {{-- Cabeçalho da Página --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reservas</h1>
                <p class="text-sm text-gray-400 mt-0.5">Lista de reservas em Vitória de Santo Antão.</p>
            </div>
            <a href="{{ route('bookings.create') }}"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus text-xs sm:hidden"></i>
                <span class="hidden sm:inline">Nova Reserva</span>
            </a>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('bookings.index') }}" class="mb-6">
            <div class="flex flex-col md:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Buscar por responsável..."
                    class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">

                <select name="resource_type" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">
                    <option value="">Todos os espaços</option>
                    <option value="auditorio" {{ request('resource_type') === 'auditorio' ? 'selected' : '' }}>Auditório</option>
                    <option value="reuniao"   {{ request('resource_type') === 'reuniao'   ? 'selected' : '' }}>Sala de Reunião</option>
                </select>

                <input type="date" name="date" value="{{ request('date') }}"
                    class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm text-gray-600">

                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-search mr-1"></i> Filtrar
                </button>
                @if(request()->hasAny(['search','resource_type','date']))
                    <a href="{{ route('bookings.index') }}" class="px-5 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-100 transition flex items-center">
                        <i class="fas fa-times mr-1"></i> Limpar
                    </a>
                @endif
            </div>
        </form>

        {{-- Barra de ações em massa --}}
        <div x-show="selectedIds.length > 0" x-transition
            class="flex items-center justify-between bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-4">
            <p class="text-sm text-red-700 font-semibold">
                <span x-text="selectedIds.length"></span> agendamento(s) selecionado(s)
            </p>
            <button @click="openBulkDeleteModal = true"
                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-trash-alt"></i> Excluir Selecionados
            </button>
        </div>

        {{-- Tabela de Dados --}}
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-4 w-10">
                                <input type="checkbox" @change="toggleAll($event)"
                                    class="w-4 h-4 accent-blue-600 cursor-pointer rounded">
                            </th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 w-16">#</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 ">Responsável</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Qtd. Pessoas</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 ">Data e Hora</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 ">Local</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition" :class="selectedIds.includes({{ $booking->id }}) ? 'bg-red-50' : ''">
                                <td class="px-4 py-4">
                                    <input type="checkbox" value="{{ $booking->id }}"
                                        class="booking-checkbox w-4 h-4 accent-blue-600 cursor-pointer rounded"
                                        @change="toggle({{ $booking->id }})"
                                        :checked="selectedIds.includes({{ $booking->id }})">
                                </td>
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
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                    @if($booking->resource_type === 'auditorio')
                                        <span class="flex items-center">Auditório</span>
                                    @else
                                        <span class="flex items-center">Sala de Reunião</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <div class="flex justify-center space-x-4">
                                        {{-- Botão Visualizar --}}
                                        <button @click="selectedBooking = {{ $booking->toJson() }}; openModal = true"
                                            class="text-blue-600 hover:text-blue-900 transition">
                                            <i class="fas fa-eye fa-lg"></i>
                                        </button>

                                        {{-- Botão Editar --}}
                                        <a href="{{ route('bookings.edit', $booking->id) }}"
                                            class="text-yellow-600 hover:text-yellow-900 transition">
                                            <i class="fas fa-edit fa-lg"></i>
                                        </a>

                                        {{-- Botão Excluir --}}
                                        <button
                                            @click="bookingToDelete = {{ $booking->id }}; bookingToDeleteName = '{{ addslashes($booking->responsible_name) }}'; openDeleteModal = true"
                                            class="text-red-600 hover:text-red-900 transition">
                                            <i class="fas fa-trash-alt fa-lg"></i>
                                        </button>

                                        {{-- Form Invisível --}}
                                        <form id="delete-form-{{ $booking->id }}"
                                            action="{{ route('bookings.destroy', $booking->id) }}" method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">Nenhum agendamento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginação --}}
        @if($bookings->hasPages())
            <div class="mt-4">{{ $bookings->links() }}</div>
        @endif

        {{-- Form exclusão em massa (invisível) --}}
        <form id="bulk-delete-form" action="{{ route('bookings.destroyMultiple') }}" method="POST" class="hidden">
            @csrf @method('DELETE')
            <template x-for="id in selectedIds" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>

        {{-- MODAL DE VISUALIZAÇÃO --}}
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
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
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Espaço Reservado</label>
                            <p class="text-gray-800 font-bold flex items-center mt-1">
                                <template x-if="selectedBooking.resource_type === 'auditorio'">
                                    <span>🏛️ Auditório</span>
                                </template>
                                <template x-if="selectedBooking.resource_type === 'reuniao'">
                                    <span>👥 Sala de Reunião</span>
                                </template>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total de Pessoas</label>
                            <p class="text-gray-800 font-medium"><i class="fas fa-users mr-1 text-blue-900"></i> <span x-text="selectedBooking.guests_count"></span></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Data do Evento</label>
                            <p class="text-gray-800 font-medium">
                                <i class="fas fa-calendar-day mr-1 text-blue-900"></i>
                                <span x-text="selectedBooking.booking_date ? new Date(selectedBooking.booking_date).toLocaleDateString('pt-BR') : ''"></span>
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Horário (Início - Fim)</label>
                            <p class="text-gray-800 font-bold">
                                <i class="fas fa-clock mr-1 text-blue-900"></i>
                                <span x-text="selectedBooking.booking_date ? new Date(selectedBooking.booking_date).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'}) : ''"></span>
                                <span> às </span>
                                <span x-text="selectedBooking.end_date ? new Date(selectedBooking.end_date).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'}) : ''"></span>
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">CPF do Responsável</label>
                        <p class="text-gray-800 font-medium"
                            x-text="selectedBooking.cpf ? selectedBooking.cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4') : 'Não informado'"></p>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <label class="text-xs font-bold text-blue-400 uppercase tracking-wider">Observações Internas</label>
                        <p class="text-blue-900 text-sm mt-1 leading-relaxed" x-text="selectedBooking.observation || 'Nenhuma observação cadastrada.'"></p>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 flex justify-end space-x-2 border-t">
                    <button @click="openModal = false"
                        class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-bold hover:bg-gray-300 transition text-sm">Fechar</button>
                    <a :href="'/bookings/' + selectedBooking.id + '/edit'"
                        class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition text-sm">Editar Agora</a>
                </div>
            </div>
        </div>

        {{-- MODAL DE EXCLUSÃO INDIVIDUAL --}}
        <div x-show="openDeleteModal"
            class="fixed inset-0 z-60 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
            <div @click.away="openDeleteModal = false"
                class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border-t-4 border-red-600">
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
                    <button @click="openDeleteModal = false"
                        class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition">Manter</button>
                    <button @click="document.getElementById('delete-form-' + bookingToDelete).submit()"
                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition shadow-md">Sim, Excluir</button>
                </div>
            </div>
        </div>

        {{-- MODAL DE EXCLUSÃO EM MASSA --}}
        <div x-show="openBulkDeleteModal"
            class="fixed inset-0 z-60 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-transition x-cloak>
            <div @click.away="openBulkDeleteModal = false"
                class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border-t-4 border-red-600">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-trash-alt fa-2x"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Excluir Agendamentos?</h3>
                    <p class="text-gray-600 text-sm italic leading-relaxed">
                        Deseja realmente remover os
                        <span class="font-bold text-red-600" x-text="selectedIds.length"></span>
                        agendamento(s) selecionado(s)?<br>
                        <span class="text-xs text-gray-500">Esta ação não poderá ser desfeita.</span>
                    </p>
                </div>
                <div class="p-4 bg-gray-50 flex space-x-2 border-t border-gray-100">
                    <button @click="openBulkDeleteModal = false"
                        class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition">Cancelar</button>
                    <button @click="document.getElementById('bulk-delete-form').submit()"
                        class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 transition shadow-md">Sim, Excluir</button>
                </div>
            </div>
        </div>
    </div>
@endsection
