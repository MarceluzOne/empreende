@extends('layouts.app')

@section('title', 'Editar Cadastro')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('job-seekers.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Cadastro</h1>
            <p class="text-sm text-gray-500">{{ $seeker->name }}</p>
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

    <form action="{{ route('job-seekers.update', $seeker) }}" method="POST" enctype="multipart/form-data"
        x-data="{
            cpf: '{{ old('cpf', $seeker->formatted_cpf) }}',
            phone: '{{ old('phone', $seeker->formatted_phone) }}',
            maskCpf(v) {
                v = v.replace(/\D/g, '').slice(0, 11);
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
                v = v.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, '$1.$2.$3-$4');
                this.cpf = v;
            },
            maskPhone(v) {
                v = v.replace(/\D/g, '').slice(0, 11);
                v = v.replace(/(\d{2})(\d)/, '($1)$2');
                v = v.replace(/\((\d{2})\)(\d{5})(\d{4})$/, '($1)$2-$3');
                v = v.replace(/\((\d{2})\)(\d{4})(\d{4})$/, '($1)$2-$3');
                this.phone = v;
            },
            fileName: '',
            setFile(e) {
                this.fileName = e.target.files[0] ? e.target.files[0].name : '';
            }
        }"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
        @csrf
        @method('PUT')

        {{-- Status --}}
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status do Cadastro</label>
            <select name="status"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                <option value="active"   {{ old('status', $seeker->status) === 'active'   ? 'selected' : '' }}>Ativo</option>
                <option value="inactive" {{ old('status', $seeker->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
            </select>
        </div>

        {{-- Nome + CPF --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="name" value="{{ old('name', $seeker->name) }}"
                        class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('name') border-red-400 @else border-gray-200 @enderror">
                </div>
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">CPF *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input type="text" name="cpf"
                        x-model="cpf"
                        @input="maskCpf($event.target.value)"
                        maxlength="14"
                        class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('cpf') border-red-400 @else border-gray-200 @enderror">
                </div>
                @error('cpf') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Função + Área de Interesse --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Função Desejada *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-briefcase"></i>
                    </span>
                    <input type="text" name="job_function" value="{{ old('job_function', $seeker->job_function) }}"
                        class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('job_function') border-red-400 @else border-gray-200 @enderror">
                </div>
                @error('job_function') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Área de Interesse *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-layer-group"></i>
                    </span>
                    <select name="interest_area"
                        class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white @error('interest_area') border-red-400 @else border-gray-200 @enderror">
                        <option value="">Selecione...</option>
                        @foreach($interestAreas as $area)
                            <option value="{{ $area }}"
                                {{ old('interest_area', $seeker->interest_area) === $area ? 'selected' : '' }}>
                                {{ $area }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('interest_area') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Experiência --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Experiência na Área</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-clock"></i>
                </span>
                <select name="experience"
                    class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    <option value="">Sem experiência / Não informado</option>
                    @foreach($experiences as $exp)
                        <option value="{{ $exp }}"
                            {{ old('experience', $seeker->experience) === $exp ? 'selected' : '' }}>
                            {{ $exp }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Telefone + Email --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Telefone / WhatsApp</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-phone"></i>
                    </span>
                    <input type="text" name="phone"
                        x-model="phone"
                        @input="maskPhone($event.target.value)"
                        maxlength="15"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email', $seeker->email) }}"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('email') border-red-400 @else border-gray-200 @enderror">
                </div>
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Currículo --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Currículo (PDF)</label>

            @if($seeker->curriculo_path)
                <div class="mb-3 flex items-center gap-3 p-3 bg-red-50 border border-red-100 rounded-xl">
                    <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-700">Currículo atual</p>
                        <p class="text-xs text-gray-500">Envie um novo arquivo para substituir</p>
                    </div>
                    <a href="{{ $seeker->curriculo_url }}" target="_blank"
                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition">
                        <i class="fas fa-download mr-1"></i> Abrir
                    </a>
                </div>
            @endif

            <label class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer
                hover:border-blue-400 hover:bg-blue-50 transition group"
                :class="fileName ? 'border-blue-400 bg-blue-50' : ''">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                    :class="fileName ? 'bg-red-100' : 'bg-gray-100 group-hover:bg-blue-100'">
                    <i class="fas text-xl"
                        :class="fileName ? 'fa-file-pdf text-red-600' : 'fa-upload text-gray-400 group-hover:text-blue-500'"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm"
                        :class="fileName ? 'text-blue-700' : 'text-gray-600'"
                        x-text="fileName || '{{ $seeker->curriculo_path ? 'Clique para substituir o currículo' : 'Clique para anexar o currículo' }}'"></p>
                    <p class="text-xs text-gray-400 mt-0.5">Somente PDF — máximo 5MB</p>
                </div>
                <input type="file" name="curriculo" accept=".pdf" class="hidden" @change="setFile($event)">
            </label>
            @error('curriculo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <a href="{{ route('job-seekers.index') }}" class="text-gray-500 hover:text-gray-700 text-sm transition">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-3 bg-blue-900 text-white rounded-xl shadow-lg hover:bg-blue-800 transition font-semibold text-sm flex items-center gap-2">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
        </div>

    </form>
</div>
@endsection
