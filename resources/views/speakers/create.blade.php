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
        <form action="{{ route('speakers.store') }}" method="POST" class="p-8 space-y-5">
            @csrf

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneEl = document.getElementById('speaker_phone');
    if (phoneEl) IMask(phoneEl, { mask: '(00)0 0000-0000' });
});
</script>
@endpush
@endsection
