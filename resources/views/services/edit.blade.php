@extends('layouts.app')

@section('title', 'Editar Prestador - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Cabeçalho --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-edit text-blue-900 mr-2"></i> Editar Prestador de Serviço
            </h2>
            <p class="text-gray-600 italic">Atualizando: <span class="font-bold text-blue-900">{{ $service->name }}</span></p>
        </div>
        <a href="{{ route('services.index') }}" class="flex items-center text-blue-900 hover:text-blue-700 font-bold transition">
            <i class="fas fa-arrow-left mr-2"></i> Voltar para Lista
        </a>
    </div>

    {{-- Formulário com Alpine.js --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" 
         x-data="{ providerType: '{{ old('provider_type', $service->provider_type) }}' }">
        
        <form action="{{ route('services.update', $service->id) }}" method="POST" class="p-6 md:p-10">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Seção de Status (Nova) --}}
                <div class="md:col-span-2 bg-gray-50 p-6 rounded-xl border border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Status do Cadastro</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-toggle-on"></i>
                        </span>
                        <select name="status" class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white transition">
                            <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>Ativo (Visível no Portal)</option>
                            <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inativo (Oculto)</option>
                            <option value="pending" {{ old('status', $service->status) == 'pending' ? 'selected' : '' }}>Pendente (Aguardando Análise)</option>
                        </select>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 italic">Cadastros via portal iniciam como 'Ativo' por padrão.</p>
                </div>

                {{-- Tipo de Prestador --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Tipo de Prestador *</label>
                    <div class="flex gap-4">
                        <label class="flex-1 flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition"
                            :class="providerType === 'individual' ? 'border-blue-900 bg-blue-50 text-blue-900' : 'border-gray-100 text-gray-400'">
                            <input type="radio" name="provider_type" value="individual" x-model="providerType" class="hidden">
                            <i class="fas fa-user mr-2"></i> <span class="font-bold">Pessoa Física</span>
                        </label>
                        
                        <label class="flex-1 flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition"
                            :class="providerType === 'company' ? 'border-blue-900 bg-blue-50 text-blue-900' : 'border-gray-100 text-gray-400'">
                            <input type="radio" name="provider_type" value="company" x-model="providerType" class="hidden">
                            <i class="fas fa-building mr-2"></i> <span class="font-bold">Empresa</span>
                        </label>
                    </div>
                </div>

                {{-- Nome --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide" 
                           x-text="providerType === 'individual' ? 'Nome Completo *' : 'Razão Social / Fantasia *'"></label>
                    <input type="text" name="name" value="{{ old('name', $service->name) }}" 
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('name') border-red-500 @enderror" required>
                </div>

                {{-- Serviço --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Serviço Oferecido *</label>
                    <input type="text" name="service_title" value="{{ old('service_title', $service->service_title) }}" 
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('service_title') border-red-500 @enderror" required>
                </div>

                {{-- Contatos --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">E-mail *</label>
                    <input type="email" name="email" value="{{ old('email', $service->email) }}" 
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">WhatsApp *</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $service->whatsapp) }}" 
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Instagram</label>
                    <input type="text" name="instagram" value="{{ old('instagram', $service->instagram) }}" 
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>

                {{-- Observações --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Informações Adicionais</label>
                    <textarea name="optional_info" rows="3" 
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">{{ old('optional_info', $service->optional_info) }}</textarea>
                </div>
            </div>

            {{-- Ações --}}
            <div class="mt-10 flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700 font-semibold transition">Cancelar</a>
                <button type="submit" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-800 transition shadow-lg">
                    <i class="fas fa-save mr-2"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</div>
@endsection