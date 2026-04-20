@extends('layouts.app')

@section('title', 'Cadastrar Prestador - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Cabeçalho --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Novo Prestador de Serviço
            </h2>
            <p class="text-gray-600 italic">Cadastre profissionais ou empresas de Vitória de Santo Antão.</p>
        </div>
        <a href="{{ route('services.index') }}" class="flex items-center text-blue-900 hover:text-blue-700 font-bold transition">
            Voltar para Lista
        </a>
    </div>

    {{-- Formulário com Alpine.js --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden" x-data="{ providerType: 'individual' }">
        <form action="{{ route('services.store') }}" method="POST" class="p-6 md:p-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Classificação: Pessoa ou Empresa --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Tipo de Prestador *</label>
                    <div class="flex gap-4">
                        <label class="flex-1 flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition"
                            :class="providerType === 'individual' ? 'border-blue-900 bg-blue-50 text-blue-900' : 'border-gray-100 text-gray-400 hover:border-gray-300'">
                            <input type="radio" name="provider_type" value="individual" x-model="providerType" class="hidden" required>
                            <i class="fas fa-user mr-2"></i> <span class="font-bold">Pessoa Física</span>
                        </label>
                        
                        <label class="flex-1 flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition"
                            :class="providerType === 'company' ? 'border-blue-900 bg-blue-50 text-blue-900' : 'border-gray-100 text-gray-400 hover:border-gray-300'">
                            <input type="radio" name="provider_type" value="company" x-model="providerType" class="hidden">
                            <i class="fas fa-building mr-2"></i> <span class="font-bold">Empresa / MEI</span>
                        </label>
                    </div>
                </div>

                {{-- Nome Dinâmico --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide" 
                           x-text="providerType === 'individual' ? 'Nome Completo *' : 'Razão Social / Nome Fantasia *'"></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('name') border-red-500 @enderror" 
                            :placeholder="providerType === 'individual' ? 'Ex: Marcelo Arruda' : 'Ex: Empresa LTDA'" required>
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Serviço Oferecido --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Serviço Oferecido *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-concierge-bell"></i>
                        </span>
                        <input type="text" name="service_title" value="{{ old('service_title') }}" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('service_title') border-red-500 @enderror" 
                            placeholder="Ex: Manutenção Elétrica, Desenvolvimento Web, Consultoria..." required>
                    </div>
                    @error('service_title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- E-mail --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">E-mail de Contato *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('email') border-red-500 @enderror" 
                            placeholder="contato@exemplo.com" required>
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">WhatsApp *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fab fa-whatsapp"></i>
                        </span>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp') }}" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition @error('whatsapp') border-red-500 @enderror" 
                            placeholder="(00) 0 0000-0000" required>
                    </div>
                    @error('whatsapp') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Instagram --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Instagram (Opcional)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 font-bold">@</span>
                        <input type="text" name="instagram" value="{{ old('instagram') }}" 
                            class="w-full pl-10 pr-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" 
                            placeholder="seu.perfil">
                    </div>
                </div>

                {{-- Informações Adicionais --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Informações Opcionais</label>
                    <textarea name="optional_info" rows="4" 
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition" 
                        placeholder="Descreva brevemente detalhes sobre o serviço, horários de atendimento ou diferenciais...">{{ old('optional_info') }}</textarea>
                </div>
            </div>

            {{-- Ações --}}
            <div class="mt-10 flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700 font-semibold transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-800 transition shadow-lg flex items-center">
                    Finalizar Cadastro
                </button>
            </div>
        </form>
    </div>
</div>
@endsection