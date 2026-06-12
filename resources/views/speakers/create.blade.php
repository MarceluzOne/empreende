@extends('layouts.app')

@section('title', 'Novo Palestrante - Empreende Vitória')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Novo Palestrante</h2>
            <p class="text-gray-600 italic text-sm">Cadastre um palestrante para associar a eventos.</p>
        </div>
        <a href="{{ route('speakers.index') }}" class="text-blue-900 font-bold hover:underline text-sm">Voltar</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <form action="{{ route('speakers.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
            @csrf

            <div x-data="speakerPhotoCropper()" class="flex flex-col items-center gap-3">
                {{-- Input oculto que recebe o arquivo final cropado --}}
                <input type="file" id="croppedPhotoInput" name="photo" accept="image/*" class="hidden">

                {{-- Prévia circular --}}
                <div class="w-24 h-24 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 overflow-hidden flex items-center justify-center">
                    <template x-if="preview">
                        <img :src="preview" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!preview">
                        <i class="fas fa-user text-3xl text-gray-300"></i>
                    </template>
                </div>

                <button type="button" @click="$refs.rawInput.click()"
                    class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                    Escolher foto
                </button>
                <input type="file" x-ref="rawInput" accept="image/*" class="hidden" @change="openCropper($event)">
                @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                {{-- Modal de corte --}}
                <div x-show="cropperOpen" x-cloak
                    class="fixed inset-0 z-60 flex items-center justify-center bg-black/70 p-4"
                    @keydown.escape.window="closeCropper()">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col overflow-hidden">
                        <div class="flex justify-between items-center px-5 py-4 border-b">
                            <h3 class="text-base font-bold text-gray-800"><i class="fas fa-crop-alt mr-2 text-blue-600"></i>Ajustar Foto</h3>
                            <button type="button" @click="closeCropper()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
                        </div>
                        <div class="speaker-cropper p-4 bg-gray-100 flex justify-center" style="max-height:400px;">
                            <img x-ref="cropperImg" style="max-height:370px; max-width:100%; display:block;">
                        </div>
                        <p class="text-xs text-center text-gray-500 py-2">Arraste e redimensione para enquadrar a foto em formato circular</p>
                        <div class="flex justify-end gap-3 px-5 py-4 border-t bg-gray-50">
                            <button type="button" @click="closeCropper()"
                                class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800">Cancelar</button>
                            <button type="button" @click="applyCrop()"
                                class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-check mr-1"></i>Aplicar Corte
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nome *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('name') border-red-500 @enderror"
                    placeholder="Nome completo" required>
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Biografia</label>
                <textarea name="bio" rows="3"
                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="Breve descrição sobre o palestrante...">{{ old('bio') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('email') border-red-500 @enderror"
                        placeholder="email@exemplo.com">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Telefone</label>
                    <input type="text" name="phone" id="speaker_phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="(00)9 0000-0000">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('speakers.index') }}" class="text-gray-500 font-semibold hover:text-gray-700">Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                    Salvar Palestrante
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<style>
.speaker-cropper .cropper-view-box,
.speaker-cropper .cropper-face { border-radius: 50%; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneEl = document.getElementById('speaker_phone');
    if (phoneEl) IMask(phoneEl, { mask: '(00)0 0000-0000' });
});

function speakerPhotoCropper() {
    return {
        cropperOpen: false,
        preview: null,
        fileName: '',
        cropper: null,
        rawObjectUrl: null,

        openCropper(event) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 4 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 4MB.');
                event.target.value = '';
                return;
            }
            this.fileName = file.name;
            if (this.rawObjectUrl) URL.revokeObjectURL(this.rawObjectUrl);
            this.rawObjectUrl = URL.createObjectURL(file);
            this.cropperOpen = true;
            this.$nextTick(() => {
                const img = this.$refs.cropperImg;
                img.src = this.rawObjectUrl;
                if (this.cropper) this.cropper.destroy();
                this.cropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                });
            });
        },

        applyCrop() {
            if (!this.cropper) return;
            this.cropper.getCroppedCanvas({ width: 400, height: 400 }).toBlob((blob) => {
                const croppedFile = new File([blob], this.fileName, { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(croppedFile);
                document.getElementById('croppedPhotoInput').files = dt.files;
                this.preview = URL.createObjectURL(blob);
                this.closeCropper();
            }, 'image/jpeg', 0.9);
        },

        closeCropper() {
            this.cropperOpen = false;
            if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
            this.$refs.rawInput.value = '';
        }
    };
}
</script>
@endpush
@endsection
