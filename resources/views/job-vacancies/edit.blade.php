@extends('layouts.app')

@section('title', 'Editar Vaga')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('job-vacancies.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Vaga</h1>
            <p class="text-sm text-gray-500">{{ $vacancy->position }} — {{ $vacancy->company_name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('job-vacancies.update', $vacancy) }}" method="POST"
        x-data="{
            cnpj: '{{ old('cnpj', $vacancy->formatted_cnpj) }}',
            remuneration: '{{ old('remuneration', $vacancy->remuneration) }}',
            maskCnpj(v) {
                v = v.replace(/\D/g, '').slice(0, 14);
                v = v.replace(/(\d{2})(\d)/, '$1.$2');
                v = v.replace(/(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2/$3');
                v = v.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                this.cnpj = v;
            },
            maskMoney(v) {
                let nums = v.replace(/\D/g, '');
                if (nums === '') { this.remuneration = ''; return; }
                let amount = parseInt(nums, 10) / 100;
                this.remuneration = amount.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            }
        }"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Status --}}
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status da Vaga</label>
            <select name="status"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="active"   {{ old('status', $vacancy->status) === 'active'   ? 'selected' : '' }}>Ativa (Visível no Portal)</option>
                <option value="inactive" {{ old('status', $vacancy->status) === 'inactive' ? 'selected' : '' }}>Inativa (Oculta)</option>
                <option value="filled"   {{ old('status', $vacancy->status) === 'filled'   ? 'selected' : '' }}>Preenchida (Encerrada)</option>
            </select>
        </div>

        {{-- Empresa --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">CNPJ *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input type="text" name="cnpj"
                        x-model="cnpj"
                        @input="maskCnpj($event.target.value)"
                        placeholder="00.000.000/0000-00"
                        maxlength="18"
                        class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('cnpj') border-red-400 @else border-gray-200 @enderror">
                </div>
                @error('cnpj') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nome da Empresa *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-building"></i>
                    </span>
                    <input type="text" name="company_name" value="{{ old('company_name', $vacancy->company_name) }}"
                        class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('company_name') border-red-400 @else border-gray-200 @enderror">
                </div>
                @error('company_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Vaga --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Título da Vaga *</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-briefcase"></i>
                </span>
                <input type="text" name="position" value="{{ old('position', $vacancy->position) }}"
                    class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('position') border-red-400 @else border-gray-200 @enderror">
            </div>
            @error('position') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Quantidade + Remuneração --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Quantidade de Vagas *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-users"></i>
                    </span>
                    <input type="number" name="quantity" value="{{ old('quantity', $vacancy->quantity) }}" min="1"
                        class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('quantity') border-red-400 @else border-gray-200 @enderror">
                </div>
                @error('quantity') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Remuneração</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                    <input type="text" name="remuneration"
                        x-model="remuneration"
                        @input="maskMoney($event.target.value)"
                        placeholder="R$ 0,00"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>
        </div>

        {{-- Experiência --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Experiência Mínima</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-clock"></i>
                </span>
                <select name="min_experience"
                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    <option value="">Sem requisito de experiência</option>
                    @foreach($experiences as $exp)
                        <option value="{{ $exp }}"
                            {{ old('min_experience', $vacancy->min_experience) === $exp ? 'selected' : '' }}>
                            {{ $exp }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Área de Interesse --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Área de Interesse *</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-tag"></i>
                </span>
                <select name="interest_area"
                    class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white @error('interest_area') border-red-400 @else border-gray-200 @enderror">
                    <option value="">Selecione a área...</option>
                    @foreach($interestAreas as $area)
                        <option value="{{ $area }}"
                            {{ old('interest_area', $vacancy->interest_area) === $area ? 'selected' : '' }}>
                            {{ $area }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('interest_area') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Requisitos --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Requisitos da Vaga *</label>
            <textarea name="requirements" rows="4"
                class="w-full px-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none @error('requirements') border-red-400 @else border-gray-200 @enderror">{{ old('requirements', $vacancy->requirements) }}</textarea>
            @error('requirements') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Benefícios --}}
        <div x-data="{ selected: {{ json_encode(old('benefits', $vacancy->benefits ?? [])) }} }">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Benefícios</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($benefits as $benefit)
                <label
                    :class="selected.includes('{{ $benefit }}') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer transition text-sm">
                    <input type="checkbox" name="benefits[]" value="{{ $benefit }}"
                        x-model="selected"
                        class="accent-blue-600 w-4 h-4">
                    {{ $benefit }}
                </label>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <a href="{{ route('job-vacancies.index') }}" class="text-gray-500 hover:text-gray-700 text-sm transition">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-3 bg-blue-900 text-white rounded-xl shadow-lg hover:bg-blue-800 transition font-semibold text-sm flex items-center gap-2">
                Salvar Alterações
            </button>
        </div>

    </form>
</div>
@endsection
