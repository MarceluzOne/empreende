@extends('layouts.app')

@section('title', 'Novo Cadastro — Banco de Talentos')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('job-seekers.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Novo Cadastro</h1>
            <p class="text-sm text-gray-500">Banco de Talentos — cadastro de candidatos</p>
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

    <form action="{{ route('job-seekers.store') }}" method="POST"
        x-data="{
            cpf: '{{ old('cpf', '') }}',
            maskCpf(v) {
                v = v.replace(/\D/g, '').slice(0, 11);
                if (v.length > 9) v = v.slice(0, 3) + '.' + v.slice(3, 6) + '.' + v.slice(6, 9) + '-' + v.slice(9);
                else if (v.length > 6) v = v.slice(0, 3) + '.' + v.slice(3, 6) + '.' + v.slice(6);
                else if (v.length > 3) v = v.slice(0, 3) + '.' + v.slice(3);
                this.cpf = v;
            },
            phone: '{{ old('phone', '') }}',
            maskPhone(v) {
                v = v.replace(/\D/g, '').slice(0, 11);
                v = v.replace(/(\d{2})(\d)/, '($1)$2');
                v = v.replace(/\((\d{2})\)(\d{5})(\d{4})$/, '($1)$2-$3');
                v = v.replace(/\((\d{2})\)(\d{4})(\d{4})$/, '($1)$2-$3');
                this.phone = v;
            },
            experiences: {{ Js::from(old('experiences', [])) }},
            addExperience() { this.experiences.push({ company:'', role:'', start:'', end:'', current:false, activities:'' }); },
            removeExperience(i) { this.experiences.splice(i, 1); },
            maskDate(v) {
                v = v.replace(/\D/g, '').slice(0, 6);
                if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
                return v;
            },
            education: {{ Js::from(old('education', [])) }},
            addEducation() { this.education.push({ course:'', institution:'', year:'' }); },
            removeEducation(i) { this.education.splice(i, 1); },
            languages: {{ Js::from(old('languages', [])) }},
            addLanguage() { this.languages.push({ language:'', level:'' }); },
            removeLanguage(i) { this.languages.splice(i, 1); },
            certifications: {{ Js::from(old('certifications', [])) }},
            addCertification() { this.certifications.push(''); },
            removeCertification(i) { this.certifications.splice(i, 1); },
            updateCert(i, v) { this.certifications[i] = v; }
        }"
        class="space-y-5">
        @csrf

        {{-- Seção 1: Dados Pessoais --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-base font-bold text-gray-700 flex items-center gap-2">
                <i class="fas fa-user text-blue-500"></i> Dados Pessoais
            </h2>

            {{-- Nome / CPF --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nome Completo *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Nome completo do candidato"
                            class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('name') border-red-400 @else border-gray-200 @enderror">
                    </div>
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">CPF *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="cpf"
                            x-model="cpf"
                            @input="maskCpf($event.target.value)"
                            placeholder="000.000.000-00" maxlength="14" inputmode="numeric"
                            class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('cpf') border-red-400 @else border-gray-200 @enderror">
                    </div>
                    @error('cpf') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Cargo / Cidade / Estado --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cargo / Objetivo *</label>
                    <input type="text" name="job_function" value="{{ old('job_function') }}"
                        placeholder="Ex: Dev Full Stack, Analista..."
                        class="w-full px-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('job_function') border-red-400 @else border-gray-200 @enderror">
                    @error('job_function') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        placeholder="São Paulo"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estado (UF)</label>
                    <input type="text" name="state" value="{{ old('state') }}"
                        placeholder="SP" maxlength="2"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none uppercase">
                </div>
            </div>

            {{-- Telefone / Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telefone / WhatsApp</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-phone"></i></span>
                        <input type="text" name="phone"
                            x-model="phone"
                            @input="maskPhone($event.target.value)"
                            placeholder="(00)00000-0000" maxlength="15"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-mail</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="email@exemplo.com"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('email') border-red-400 @else border-gray-200 @enderror">
                    </div>
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- LinkedIn / GitHub --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">LinkedIn</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fab fa-linkedin"></i></span>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}"
                            placeholder="https://linkedin.com/in/..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('linkedin_url') border-red-400 @else border-gray-200 @enderror">
                    </div>
                    @error('linkedin_url') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">GitHub</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fab fa-github"></i></span>
                        <input type="url" name="github_url" value="{{ old('github_url') }}"
                            placeholder="https://github.com/..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none @error('github_url') border-red-400 @else border-gray-200 @enderror">
                    </div>
                    @error('github_url') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Área de Interesse / Nível de Experiência --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Área de Interesse *</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-layer-group"></i></span>
                        <select name="interest_area"
                            class="w-full pl-10 pr-4 py-3 border rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white @error('interest_area') border-red-400 @else border-gray-200 @enderror">
                            <option value="">Selecione...</option>
                            @foreach($interestAreas as $area)
                                <option value="{{ $area }}" {{ old('interest_area') === $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('interest_area') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nível de Experiência</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400"><i class="fas fa-chart-line"></i></span>
                        <select name="experience"
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">Não informado</option>
                            @foreach($experienceLevels as $level)
                                <option value="{{ $level }}" {{ old('experience') === $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Seção 2: Resumo Profissional --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
            <h2 class="text-base font-bold text-gray-700 flex items-center gap-2">
                <i class="fas fa-align-left text-blue-500"></i> Resumo Profissional
            </h2>
            <textarea name="summary" rows="4"
                placeholder="Descreva brevemente sua trajetória, principais tecnologias/habilidades e o que você busca (3 a 5 linhas)..."
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none">{{ old('summary') }}</textarea>
        </div>

        {{-- Seção 3: Experiência Profissional --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-briefcase text-blue-500"></i> Experiência Profissional
                </h2>
                <button type="button" @click="addExperience()"
                    class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                    <i class="fas fa-plus"></i> Adicionar
                </button>
            </div>

            <template x-for="(exp, i) in experiences" :key="i">
                <div class="border border-gray-200 rounded-xl p-4 space-y-3 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" :name="`experiences[${i}][company]`" x-model="exp.company"
                            placeholder="Empresa"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <input type="text" :name="`experiences[${i}][role]`" x-model="exp.role"
                            placeholder="Cargo"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" :name="`experiences[${i}][start]`" x-model="exp.start"
                            @input="exp.start = maskDate($event.target.value)"
                            placeholder="Início (mm/aaaa)" maxlength="7"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <div class="space-y-1">
                            <input type="text" :name="`experiences[${i}][end]`" x-model="exp.end"
                                @input="exp.end = maskDate($event.target.value)"
                                placeholder="Fim (mm/aaaa)" maxlength="7"
                                :disabled="exp.current"
                                :class="exp.current ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : ''"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <label class="flex items-center gap-2 text-xs text-gray-500 cursor-pointer select-none">
                                <input type="checkbox" :name="`experiences[${i}][current]`"
                                    x-model="exp.current"
                                    @change="if(exp.current) exp.end = ''"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Emprego atual
                            </label>
                        </div>
                    </div>
                    <textarea :name="`experiences[${i}][activities]`" x-model="exp.activities"
                        placeholder="Atividades e conquistas (use tópicos ou texto livre)..." rows="3"
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none"></textarea>
                    <button type="button" @click="removeExperience(i)"
                        class="text-xs text-red-500 hover:underline">
                        <i class="fas fa-trash-alt mr-1"></i> Remover
                    </button>
                </div>
            </template>

            <p x-show="experiences.length === 0" class="text-sm text-gray-400 italic">
                Nenhuma experiência adicionada. Clique em "Adicionar" para começar.
            </p>
        </div>

        {{-- Seção 4: Formação Acadêmica --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-graduation-cap text-blue-500"></i> Formação Acadêmica
                </h2>
                <button type="button" @click="addEducation()"
                    class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                    <i class="fas fa-plus"></i> Adicionar
                </button>
            </div>

            <template x-for="(edu, i) in education" :key="i">
                <div class="border border-gray-200 rounded-xl p-4 space-y-3 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="text" :name="`education[${i}][course]`" x-model="edu.course"
                            placeholder="Curso"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <input type="text" :name="`education[${i}][institution]`" x-model="edu.institution"
                            placeholder="Instituição"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <input type="text" :name="`education[${i}][year]`" x-model="edu.year"
                            placeholder="Status / Ano (ex: 2024, Em andamento)"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                    <button type="button" @click="removeEducation(i)"
                        class="text-xs text-red-500 hover:underline">
                        <i class="fas fa-trash-alt mr-1"></i> Remover
                    </button>
                </div>
            </template>

            <p x-show="education.length === 0" class="text-sm text-gray-400 italic">
                Nenhuma formação adicionada.
            </p>
        </div>

        {{-- Seção 5: Competências --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-3">
            <h2 class="text-base font-bold text-gray-700 flex items-center gap-2">
                <i class="fas fa-tools text-blue-500"></i> Competências / Habilidades Técnicas
            </h2>
            <textarea name="skills" rows="3"
                placeholder="Ex: PHP, Laravel, Vue.js, MySQL, Excel, Liderança, Comunicação..."
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-none">{{ old('skills') }}</textarea>
        </div>

        {{-- Seção 6: Idiomas e Certificações --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h2 class="text-base font-bold text-gray-700 flex items-center gap-2">
                <i class="fas fa-globe text-blue-500"></i> Idiomas e Certificações
                <span class="text-xs font-normal text-gray-400">(opcional)</span>
            </h2>

            {{-- Idiomas --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Idiomas</p>
                    <button type="button" @click="addLanguage()"
                        class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                        <i class="fas fa-plus"></i> Adicionar
                    </button>
                </div>
                <template x-for="(lang, i) in languages" :key="i">
                    <div class="flex items-center gap-3">
                        <input type="text" :name="`languages[${i}][language]`" x-model="lang.language"
                            placeholder="Idioma (ex: Inglês)"
                            class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <select :name="`languages[${i}][level]`" x-model="lang.level"
                            class="w-44 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">Nível...</option>
                            <option value="Básico">Básico</option>
                            <option value="Intermediário">Intermediário</option>
                            <option value="Avançado">Avançado</option>
                            <option value="Fluente">Fluente</option>
                            <option value="Nativo">Nativo</option>
                        </select>
                        <button type="button" @click="removeLanguage(i)"
                            class="text-red-400 hover:text-red-600 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
                <p x-show="languages.length === 0" class="text-sm text-gray-400 italic">Nenhum idioma adicionado.</p>
            </div>

            {{-- Certificações --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-gray-600">Certificações / Cursos</p>
                    <button type="button" @click="addCertification()"
                        class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                        <i class="fas fa-plus"></i> Adicionar
                    </button>
                </div>
                <template x-for="(cert, i) in certifications" :key="i">
                    <div class="flex items-center gap-3">
                        <input type="text" :name="`certifications[${i}]`"
                            :value="cert"
                            @input="updateCert(i, $event.target.value)"
                            placeholder="Ex: AWS Cloud Practitioner, Google Analytics..."
                            class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <button type="button" @click="removeCertification(i)"
                            class="text-red-400 hover:text-red-600 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </template>
                <p x-show="certifications.length === 0" class="text-sm text-gray-400 italic">Nenhuma certificação adicionada.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('job-seekers.index') }}" class="text-gray-500 hover:text-gray-700 text-sm transition">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-3 bg-blue-600 text-white rounded-xl shadow-lg hover:bg-blue-700 transition font-semibold text-sm flex items-center gap-2">
                <span>Cadastrar</span>
            </button>
        </div>

    </form>
</div>
@endsection
