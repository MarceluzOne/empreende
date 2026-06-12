@extends('layouts.app')

@section('title', 'Novo Evento - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{
        type: '{{ old('type', 'single') }}',
        startTime: '{{ old('start_time', '') }}',
        endTime: '{{ old('end_time', '') }}',
        consecutiveStep: 'start',
        singleDate: '{{ old('single_date', '') }}',
        startDate: '{{ old('start_date', '') }}',
        endDatePeriod: '{{ old('end_date_period', '') }}',
        selectedDates: [],
        addDate(date) {
            if (date && !this.selectedDates.includes(date)) {
                this.selectedDates.push(date);
                this.$dispatch('dates-updated', { dates: this.selectedDates });
            }
        },
        removeDate(index) {
            this.selectedDates.splice(index, 1);
            this.$dispatch('dates-updated', { dates: this.selectedDates });
        },
        onDateSelected(e) {
            delete this.formErrors.date;
            const date = e.detail.date;
            if (this.type === 'single') {
                this.singleDate = date;
            } else if (this.type === 'consecutive') {
                if (this.consecutiveStep === 'start') {
                    this.startDate = date;
                    this.consecutiveStep = 'end';
                } else {
                    this.endDatePeriod = date;
                    this.consecutiveStep = 'start';
                }
            } else if (this.type === 'alternated') {
                if (this.selectedDates.includes(date)) {
                    this.removeDate(this.selectedDates.indexOf(date));
                } else {
                    this.addDate(date);
                }
            }
        },
        onTimeSelected(e) {
            delete this.formErrors.time;
            this.startTime = e.detail.start;
            this.endTime   = e.detail.end;
            const [sh, sm] = this.startTime.split(':').map(Number);
            const [eh, em] = this.endTime.split(':').map(Number);
            this.durationMinutes = (eh * 60 + em) - (sh * 60 + sm);
        },
        durationMinutes: 0,

        // Validação do formulário
        formErrors: {},
        validateAndSubmit(e) {
            this.formErrors = {};
            const title = document.querySelector('[name=title]')?.value?.trim();
            const speakerId = document.getElementById('speaker_select')?.value;

            if (!title) this.formErrors.title = 'O título do evento é obrigatório.';
            if (!speakerId) this.formErrors.speaker = 'Selecione um palestrante.';

            const hasDate =
                (this.type === 'single' && this.singleDate) ||
                (this.type === 'consecutive' && this.startDate && this.endDatePeriod) ||
                (this.type === 'alternated' && this.selectedDates.length > 0);

            if (!hasDate) {
                if (this.type === 'single') this.formErrors.date = 'Selecione uma data para o evento.';
                else if (this.type === 'consecutive') this.formErrors.date = 'Selecione as datas de início e fim.';
                else this.formErrors.date = 'Selecione ao menos uma data.';
            }

            if (!this.startTime || !this.endTime) {
                this.formErrors.time = 'Selecione o horário do evento.';
            }

            if (Object.keys(this.formErrors).length > 0) {
                this.$nextTick(() => {
                    const first = document.querySelector('[data-form-error]');
                    if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
                return;
            }

            e.target.submit();
        },

        // Modal novo palestrante
        speakerModal: false,
        speakerForm: { name: '', bio: '', email: '', phone: '' },
        speakerErrors: {},
        speakerLoading: false,
        openSpeakerModal() {
            this.speakerForm = { name: '', bio: '', email: '', phone: '' };
            this.speakerErrors = {};
            this.speakerModal = true;
            this.$nextTick(() => {
                const el = this.$refs.speakerPhone;
                if (el && !el._imask) {
                    el._imask = IMask(el, { mask: '(00) 00000-0000' });
                    el.addEventListener('input', () => {
                        this.speakerForm.phone = el._imask.value;
                    });
                } else if (el && el._imask) {
                    el._imask.value = '';
                }
            });
        },
        async saveSpeaker() {
            this.speakerErrors = {};
            this.speakerLoading = true;
            try {
                const res = await fetch('{{ route('speakers.quick-store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.speakerForm),
                });
                const data = await res.json();
                if (!res.ok) {
                    this.speakerErrors = data.errors ?? {};
                    return;
                }
                // Adiciona a nova opção ao select e seleciona
                const select = document.getElementById('speaker_select');
                const opt = new Option(data.name, data.id, true, true);
                select.add(opt);
                this.speakerModal = false;
            } finally {
                this.speakerLoading = false;
            }
        }
    }"
    @date-selected.window="onDateSelected($event)"
    @time-selected.window="onTimeSelected($event)">

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Novo Evento</h2>
            <p class="text-gray-600 italic text-sm">O auditório será reservado automaticamente nos dias selecionados.</p>
        </div>
        <a href="{{ route('events.index') }}" class="text-blue-900 font-bold hover:underline text-sm">Voltar</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="p-8" @submit.prevent="validateAndSubmit($event)">
            @csrf

            {{-- Título e Palestrante --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Título do Evento *</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        :class="formErrors.title ? 'border-red-500' : ''"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('title') border-red-500 @enderror"
                        placeholder="Ex: Workshop de Formalização MEI">
                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    <p x-show="formErrors.title" x-text="formErrors.title" data-form-error class="text-red-500 text-xs mt-1"></p>
                </div>

                {{-- Imagem com corte quadrado --}}
                <div class="md:col-span-2" x-data="imageCropper()">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Imagem do Evento</label>

                    {{-- Input file oculto (recebe o arquivo final cortado) --}}
                    <input type="file" id="croppedFileInput" name="image" accept="image/*" class="hidden">

                    {{-- Área de seleção / prévia --}}
                    <div class="flex items-center gap-4">
                        <button type="button" @click="$refs.rawInput.click()"
                            class="px-5 py-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-sm font-semibold text-blue-700 transition">
                            <i class="fas fa-image mr-2"></i>Selecionar Imagem
                        </button>
                        <span x-show="!preview" class="text-xs text-gray-400">Nenhuma imagem selecionada</span>
                        <div x-show="preview" class="flex items-center gap-3">
                            <img :src="preview" class="w-16 h-16 rounded-lg object-cover border border-gray-200 shadow-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-700" x-text="fileName"></p>
                                <button type="button" @click="$refs.rawInput.click()" class="text-xs text-blue-600 hover:underline">Trocar imagem</button>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">JPG, PNG ou WebP — máx. 2MB — formato quadrado (1:1)</p>

                    {{-- Input oculto para seleção inicial --}}
                    <input type="file" x-ref="rawInput" accept="image/*" class="hidden" @change="openCropper($event)">

                    {{-- Modal de corte --}}
                    <div x-show="cropperOpen" x-cloak
                        class="fixed inset-0 z-60 flex items-center justify-center bg-black/70 p-4"
                        @keydown.escape.window="closeCropper()">
                        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col overflow-hidden">
                            <div class="flex justify-between items-center px-5 py-4 border-b">
                                <h3 class="text-base font-bold text-gray-800"><i class="fas fa-crop-alt mr-2 text-blue-600"></i>Ajustar Imagem</h3>
                                <button type="button" @click="closeCropper()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-lg"></i></button>
                            </div>
                            <div class="p-4 bg-gray-100 flex justify-center" style="max-height:400px;">
                                <img x-ref="cropperImg" style="max-height:370px; max-width:100%; display:block;">
                            </div>
                            <p class="text-xs text-center text-gray-500 py-2">Arraste e redimensione para enquadrar a imagem em formato quadrado</p>
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
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Palestrante *</label>
                    <div class="flex gap-2">
                        <select id="speaker_select" name="speaker_id"
                            :class="formErrors.speaker ? 'border-red-500' : ''"
                            class="flex-1 px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white @error('speaker_id') border-red-500 @enderror">
                            <option value="">Selecione</option>
                            @foreach($speakers as $speaker)
                                <option value="{{ $speaker->id }}" {{ old('speaker_id') == $speaker->id ? 'selected' : '' }}>
                                    {{ $speaker->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" @click="openSpeakerModal()"
                            class="px-4 py-3 bg-gray-100 hover:bg-gray-200 border rounded-lg text-sm text-gray-600 font-semibold transition whitespace-nowrap">
                            + Novo
                        </button>
                    </div>
                    @error('speaker_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    <p x-show="formErrors.speaker" x-text="formErrors.speaker" data-form-error class="text-red-500 text-xs mt-1"></p>
                    @if($speakers->isEmpty())
                        <p class="text-xs text-orange-600 mt-1 font-semibold">
                            Nenhum palestrante cadastrado. Clique em <strong>+ Novo</strong> para cadastrar.
                        </p>
                    @endif
                </div>

                {{-- Modal: Novo Palestrante --}}
                <div x-show="speakerModal" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                    @keydown.escape.window="speakerModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6"
                        @click.outside="speakerModal = false">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-gray-800">Novo Palestrante</h3>
                            <button type="button" @click="speakerModal = false" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nome *</label>
                                <input type="text" x-model="speakerForm.name"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                                    :class="speakerErrors.name ? 'border-red-500' : ''"
                                    placeholder="Nome completo">
                                <p x-show="speakerErrors.name" x-text="speakerErrors.name?.[0]" class="text-red-500 text-xs mt-1"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">E-mail</label>
                                <input type="email" x-model="speakerForm.email"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                                    :class="speakerErrors.email ? 'border-red-500' : ''"
                                    placeholder="email@exemplo.com">
                                <p x-show="speakerErrors.email" x-text="speakerErrors.email?.[0]" class="text-red-500 text-xs mt-1"></p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Telefone</label>
                                <input type="text" x-ref="speakerPhone"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"
                                    placeholder="(27) 99999-0000">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Bio / Especialidade</label>
                                <textarea x-model="speakerForm.bio" rows="2"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm resize-none"
                                    placeholder="Breve descrição..."></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="speakerModal = false"
                                class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800">
                                Cancelar
                            </button>
                            <button type="button" @click="saveSpeaker()" :disabled="speakerLoading"
                                class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition disabled:opacity-60">
                                <span x-show="!speakerLoading">Salvar Palestrante</span>
                                <span x-show="speakerLoading"><i class="fas fa-spinner fa-spin mr-1"></i> Salvando...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Capacidade *</label>
                    <input type="number" name="max_capacity" value="{{ old('max_capacity', 50) }}" min="1"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('max_capacity') border-red-500 @enderror" required>
                    @error('max_capacity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="border-gray-100 mb-5">

            {{-- Tipo de reserva de dias --}}
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">Duração do Evento</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <label class="flex items-center p-3 border rounded-xl cursor-pointer transition hover:bg-gray-50"
                        :class="type === 'single' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="single" x-model="type" class="mr-2">
                        <span class="text-sm font-semibold">Dia Único</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-xl cursor-pointer transition hover:bg-gray-50"
                        :class="type === 'consecutive' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="consecutive" x-model="type" class="mr-2">
                        <span class="text-sm font-semibold">Dias Consecutivos</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-xl cursor-pointer transition hover:bg-gray-50"
                        :class="type === 'alternated' ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                        <input type="radio" name="type" value="alternated" x-model="type" class="mr-2">
                        <span class="text-sm font-semibold">Dias Alternados</span>
                    </label>
                </div>
            </div>

            {{-- Calendário --}}
            <div class="mb-5">
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">
                    Disponibilidade do Auditório —
                    <span x-text="type === 'single' ? 'clique em um dia' : (type === 'consecutive' ? 'clique no início e depois no fim' : 'clique para adicionar dias')"></span>
                </label>
                {{-- Input hidden para o calendário ler resource_type --}}
                <input type="hidden" name="resource_type" value="auditorio">
                @include('bookings.partials._calendar')
            </div>

            {{-- Hidden inputs para submissão --}}
            <div>
                <input type="hidden" name="start_time" x-model="startTime">
                <input type="hidden" name="end_time" x-model="endTime">
                <input type="hidden" name="duration_minutes" x-model="durationMinutes">
                <input type="hidden" name="single_date" x-model="singleDate">
                <input type="hidden" name="start_date" x-model="startDate">
                <input type="hidden" name="end_date_period" x-model="endDatePeriod">
                <template x-for="(date, index) in selectedDates" :key="index">
                    <input type="hidden" name="selected_dates[]" :value="date">
                </template>
            </div>

            {{-- Erros de data/horário --}}
            <div class="space-y-2 mb-2">
                <p x-show="formErrors.date" x-text="formErrors.date" data-form-error class="text-red-500 text-sm font-semibold flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> <span x-text="formErrors.date"></span></p>
                <p x-show="formErrors.time" x-text="formErrors.time" data-form-error class="text-red-500 text-sm font-semibold flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> <span x-text="formErrors.time"></span></p>
            </div>

            {{-- Resumos de seleção --}}
            <div class="space-y-2 mb-5">
                <div x-show="startTime && endTime" class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-clock mr-1"></i>
                    Horário: <span x-text="startTime"></span> até <span x-text="endTime"></span>
                </div>
                <div x-show="type === 'single' && singleDate" class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-calendar-day mr-1"></i>
                    Data: <span x-text="singleDate.split('-').reverse().join('/')"></span>
                </div>
                <div x-show="type === 'consecutive' && startDate" class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800 font-semibold">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <span x-show="!endDatePeriod">De <span x-text="startDate.split('-').reverse().join('/')"></span> — clique no dia de fim</span>
                    <span x-show="endDatePeriod">De <span x-text="startDate.split('-').reverse().join('/')"></span> até <span x-text="endDatePeriod.split('-').reverse().join('/')"></span></span>
                </div>
                <div x-show="type === 'alternated' && selectedDates.length > 0" class="p-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-xs font-bold text-blue-700 uppercase mb-2">Dias selecionados:</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(date, index) in selectedDates" :key="'tag-'+index">
                            <div class="bg-white border border-blue-300 text-blue-900 px-3 py-1 rounded-full flex items-center text-sm font-bold">
                                <span x-text="date.split('-').reverse().join('/')"></span>
                                <button type="button" @click="removeDate(index)" class="ml-2 text-red-500 hover:text-red-700">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Banner de erro de validação --}}
            <div x-show="Object.keys(formErrors).length > 0"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mb-4 flex items-start gap-3 bg-red-50 border border-red-300 text-red-700 rounded-xl px-4 py-3 text-sm">
                <i class="fas fa-exclamation-triangle mt-0.5 text-red-500 shrink-0"></i>
                <div>
                    <p class="font-bold">Corrija os erros antes de continuar:</p>
                    <ul class="list-disc list-inside mt-1 space-y-0.5">
                        <template x-for="msg in Object.values(formErrors)" :key="msg">
                            <li x-text="msg"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('events.index') }}" class="text-gray-500 font-semibold hover:text-gray-700">Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                    <i class="fas fa-check sm:hidden"></i>
                    <span class="hidden sm:inline">Criar Evento</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const element = document.getElementById('cpf_mask');
    if (element) IMask(element, { mask: '000.000.000-00' });
});

function imageCropper() {
    return {
        cropperOpen: false,
        preview: null,
        fileName: '',
        cropper: null,
        rawObjectUrl: null,

        openCropper(event) {
            const file = event.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('A imagem deve ter no máximo 2MB.');
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
            this.cropper.getCroppedCanvas({ width: 800, height: 800 }).toBlob((blob) => {
                const croppedFile = new File([blob], this.fileName, { type: 'image/jpeg' });
                const dt = new DataTransfer();
                dt.items.add(croppedFile);
                document.getElementById('croppedFileInput').files = dt.files;
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
