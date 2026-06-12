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
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden"
         x-data="{
            providerType: 'individual',
            imagePreview: null,
            imageError: '',
            cropModalOpen: false,
            _cropper: null,
            handleImage(e) {
                const file = e.target.files[0];
                if (!file) return;
                if (file.size > 20 * 1024 * 1024) {
                    this.imageError = 'A imagem deve ter no máximo 20MB.';
                    e.target.value = '';
                    return;
                }
                this.imageError = '';
                const reader = new FileReader();
                reader.onload = ev => {
                    this.cropModalOpen = true;
                    this.$nextTick(() => {
                        const img = document.getElementById('crop-image');
                        img.src = ev.target.result;
                        if (this._cropper) { this._cropper.destroy(); }
                        this._cropper = new Cropper(img, { aspectRatio: 16/9, viewMode: 1, autoCropArea: 1 });
                    });
                };
                reader.readAsDataURL(file);
            },
            applyCrop() {
                if (!this._cropper) return;
                this._cropper.getCroppedCanvas({ width: 1280, height: 720 }).toBlob(blob => {
                    const file = new File([blob], 'business_image.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    document.getElementById('business_image').files = dt.files;
                    this.imagePreview = URL.createObjectURL(blob);
                    this.closeCropModal();
                }, 'image/jpeg', 0.92);
            },
            closeCropModal() {
                if (this._cropper) { this._cropper.destroy(); this._cropper = null; }
                this.cropModalOpen = false;
                const fi = document.getElementById('business_image');
                if (!this.imagePreview) fi.value = '';
            }
         }">
        <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Classificação: Pessoa ou Empresa --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Tipo de Prestador *</label>
                    <div class="flex gap-4">
                        <label class="flex-1 flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition"
                            :class="providerType === 'individual' ? 'border-blue-900 bg-blue-50 text-blue-900' : 'border-gray-100 text-gray-400 hover:border-gray-300'">
                            <input type="radio" name="provider_type" value="individual" x-model="providerType" class="hidden" required>
                            <i class="fas fa-user mr-2"></i> <span class="font-bold">Serviço</span>
                        </label>
                        
                        <label class="flex-1 flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition"
                            :class="providerType === 'company' ? 'border-blue-900 bg-blue-50 text-blue-900' : 'border-gray-100 text-gray-400 hover:border-gray-300'">
                            <input type="radio" name="provider_type" value="company" x-model="providerType" class="hidden">
                            <i class="fas fa-building mr-2"></i> <span class="font-bold">Produto</span>
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

                {{-- Imagem do Negócio (16:9) --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                        Imagem do Negócio <span class="text-gray-400 font-normal normal-case">(opcional · recortada em 16:9 · máx. 20MB)</span>
                    </label>

                    <div class="flex flex-col gap-4">
                        {{-- Preview 16:9 --}}
                        <div class="w-full max-w-sm rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 overflow-hidden flex items-center justify-center" style="aspect-ratio:16/9;">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <div class="text-center text-gray-400 px-2">
                                    <i class="fas fa-image text-2xl mb-1"></i>
                                    <p class="text-xs leading-tight">Preview 16:9</p>
                                </div>
                            </template>
                        </div>

                        {{-- Área de upload --}}
                        <div class="flex-1">
                            <label for="business_image"
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition">
                                <i class="fas fa-crop-alt text-gray-400 text-xl mb-2"></i>
                                <span class="text-sm text-gray-500">Clique para selecionar e recortar</span>
                                <span class="text-xs text-gray-400 mt-1">JPG, PNG ou WEBP</span>
                                <input id="business_image" type="file" name="business_image" accept="image/jpeg,image/png,image/webp"
                                       class="hidden" @change="handleImage($event)">
                            </label>
                            <p x-show="imageError" x-text="imageError" class="text-red-500 text-xs mt-1"></p>
                            @error('business_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ações --}}
            <div class="mt-10 flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('services.index') }}" class="text-gray-500 hover:text-gray-700 font-semibold transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg flex items-center">
                    <i class="fas fa-check sm:hidden"></i>
                    <span class="hidden sm:inline">Finalizar Cadastro</span>
                </button>
            </div>
        </form>
    </div>
</div>
{{-- Modal de Crop --}}
<div x-show="cropModalOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,.7);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden" style="max-height:90vh;">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                <i class="fas fa-crop-alt text-blue-600"></i> Recortar Imagem
            </h3>
            <button type="button" @click="closeCropModal()" class="text-gray-400 hover:text-gray-700 text-xl leading-none">&times;</button>
        </div>
        <div class="overflow-auto flex-1 p-4 bg-gray-100 flex items-center justify-center" style="min-height:300px;">
            <img id="crop-image" src="" style="max-width:100%;display:block;">
        </div>
        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t bg-white">
            <button type="button" @click="closeCropModal()"
                    class="px-5 py-2 rounded-xl border border-gray-300 text-gray-600 font-semibold hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button type="button" @click="applyCrop()"
                    class="px-6 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-check"></i> Aplicar Corte
            </button>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
@endpush

@endsection