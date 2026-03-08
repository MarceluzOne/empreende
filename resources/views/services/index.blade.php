@extends('layouts.app')

@section('title', 'Prestadores de Serviço - Empreende Vitória')

@section('content')
  {{-- Estrutura blindada contra erros de parse de JSON --}}
  <div x-data="{ 
      openModal: false, 
      openDeleteModal: false, 
      selectedService: {}, 
      serviceToDelete: null,
      serviceToDeleteName: '',

      showProvider(data) {
          this.selectedService = data;
          this.openModal = true;
      },

      prepDelete(id, name) {
          this.serviceToDelete = id;
          this.serviceToDeleteName = name;
          this.openDeleteModal = true;
      }
  }" class="max-w-6xl mx-auto">

    {{-- Cabeçalho --}}
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-gray-800">
          <i class="fas fa-tools text-blue-900 mr-2"></i> Prestadores de Serviço
        </h2>
        <p class="text-gray-600 italic">Rede de profissionais em Vitória de Santo Antão.</p>
      </div>
      <a href="{{ route('services.create') }}"
        class="bg-blue-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-800 transition shadow-sm flex items-center">
        <i class="fas fa-plus md:mr-2"></i>
        <span class="hidden md:inline">Cadastrar Serviço</span>
      </a>
    </div>

    {{-- Tabela --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-4 text-sm font-semibold text-gray-700 w-16">#</th>
              <th class="px-6 py-4 text-sm font-semibold text-gray-700">Nome / Empresa</th>
              <th class="px-6 py-4 text-sm font-semibold text-gray-700">Serviço</th>
              <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Tipo</th>
              <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Status</th>
              <th class="px-6 py-4 text-sm font-semibold text-gray-700 text-center">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($providers as $provider)
                <tr class="hover:bg-gray-50 transition">
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                  <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $provider->name }}</td>
                  <td class="px-6 py-4 text-sm text-gray-600">{{ $provider->service_title }}</td>
                  <td class="px-6 py-4 text-sm text-center">
                    <span
                      class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                          {{ $provider->provider_type === 'company' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                      {{ $provider->type_label }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-center">
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
              {{ $provider->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
              {{ $provider->status === 'inactive' ? 'bg-red-100 text-red-700' : '' }}
              {{ $provider->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                      {{ $provider->status_label }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-center">
                    <div class="flex justify-center space-x-3">
                      {{-- Visualizar --}}
                      <button @click="showProvider({{ $provider->toJson() }})"
                        class="text-blue-600 hover:text-blue-900 transition">
                        <i class="fas fa-eye fa-lg"></i>
                      </button>

                      {{-- Editar --}}
                      <a href="{{ route('services.edit', $provider->id) }}"
                        class="text-yellow-600 hover:text-yellow-900 transition">
                        <i class="fas fa-edit fa-lg"></i>
                      </a>

                      {{-- Excluir --}}
                      <button @click="prepDelete({{ $provider->id }}, '{{ addslashes($provider->name) }}')"
                        class="text-red-600 hover:text-red-900 transition">
                        <i class="fas fa-trash-alt fa-lg"></i>
                      </button>

                      <form :id="'delete-form-' + {{ $provider->id }}" action="{{ route('services.destroy', $provider->id) }}"
                        method="POST" class="hidden">
                        @csrf @method('DELETE')
                      </form>
                    </div>
                  </td>
                </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Nenhum registro encontrado.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- MODAL DE VISUALIZAÇÃO --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
      x-cloak x-transition>
      <div @click.away="openModal = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-blue-900 p-5 flex justify-between items-center text-white">
          <h3 class="font-bold"><i class="fas fa-address-card mr-2"></i> Perfil Detalhado</h3>
          <button @click="openModal = false"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-8 space-y-4">
          <div class="text-center">
            <h4 class="text-xl font-bold text-gray-800" x-text="selectedService.name"></h4>
            <p class="text-blue-900 font-semibold" x-text="selectedService.service_title"></p>
          </div>
          <div class="grid grid-cols-1 gap-3 text-sm">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
              <i class="fab fa-whatsapp text-green-600 mr-3 fa-lg"></i>
              <span class="font-bold text-gray-700" x-text="selectedService.whatsapp"></span>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg text-gray-700">
              <i class="fas fa-envelope text-red-500 mr-3 fa-lg"></i>
              <span x-text="selectedService.email"></span>
            </div>
          </div>
          <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <p class="text-blue-900 text-sm italic" x-text="selectedService.optional_info || 'Sem informações extras.'">
            </p>
          </div>
        </div>
        <div class="p-4 bg-gray-50 flex justify-end border-t">
          <button @click="openModal = false" class="bg-gray-200 px-6 py-2 rounded-lg font-bold text-sm">Fechar</button>
        </div>
      </div>
    </div>

    {{-- MODAL DE EXCLUSÃO --}}
    <div x-show="openDeleteModal"
      class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak x-transition>
      <div @click.away="openDeleteModal = false"
        class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden border-t-4 border-red-600">
        <div class="p-6 text-center">
          <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-trash-alt fa-2x"></i>
          </div>
          <h3 class="text-xl font-bold text-gray-800 mb-2">Excluir Registro?</h3>
          <p class="text-gray-600 text-sm">Remover permanentemente o cadastro de <br><span class="font-bold text-red-600"
              x-text="serviceToDeleteName"></span>?</p>
        </div>
        <div class="p-4 bg-gray-50 flex space-x-2">
          <button @click="openDeleteModal = false" class="w-full bg-gray-200 py-2 rounded-lg font-bold">Cancelar</button>
          <button @click="document.getElementById('delete-form-' + serviceToDelete).submit()"
            class="w-full bg-red-600 text-white py-2 rounded-lg font-bold shadow-md">Sim, Excluir</button>
        </div>
      </div>
    </div>
  </div>
@endsection