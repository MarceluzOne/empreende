@extends('layouts.app')

@section('title', 'Editar Evento - Empreende Vitória')

@section('content')
@php
    $allDates    = $event->allDates();
    $preDate     = $allDates[0] ?? $event->date->format('Y-m-d');
    $preStart    = substr($event->start_time, 0, 5);
    $preEnd      = $event->endTime();
    $initType    = old('type', $event->type ?? 'single');
    $initDates   = $event->type === 'alternated' ? $allDates : [];
    $initStart   = old('start_date',      $event->type === 'consecutive' ? ($allDates[0] ?? '') : '');
    $initEnd     = old('end_date_period', $event->type === 'consecutive' ? (end($allDates) ?: '') : '');
    $initSingle  = old('single_date',     $event->type === 'single' ? $preDate : '');
@endphp

<div class="max-w-4xl mx-auto" x-data="{
        type: '{{ $initType }}',
        startTime: '{{ old('start_time', $preStart) }}',
        endTime: '{{ old('end_time', $preEnd) }}',
        consecutiveStep: 'start',
        singleDate: '{{ $initSingle }}',
        startDate: '{{ $initStart }}',
        endDatePeriod: '{{ $initEnd }}',
        selectedDates: {{ json_encode($initDates) }},
        durationMinutes: 0,
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
            this.startTime = e.detail.start;
            this.endTime   = e.detail.end;
            const [sh, sm] = this.startTime.split(':').map(Number);
            const [eh, em] = this.endTime.split(':').map(Number);
            this.durationMinutes = (eh * 60 + em) - (sh * 60 + sm);
        },
        init() {
            if (this.selectedDates.length > 0) {
                this.$nextTick(() => this.$dispatch('dates-updated', { dates: this.selectedDates }));
            }
            const [sh, sm] = this.startTime.split(':').map(Number);
            const [eh, em] = this.endTime.split(':').map(Number);
            this.durationMinutes = (eh * 60 + em) - (sh * 60 + sm);
        }
    }"
    @date-selected.window="onDateSelected($event)"
    @time-selected.window="onTimeSelected($event)">

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Editar Evento</h2>
            <p class="text-gray-600 italic text-sm">{{ $event->title }}</p>
        </div>
        <a href="{{ route('events.show', $event) }}" class="text-blue-900 font-bold hover:underline text-sm">Voltar</a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')

            {{-- Título, Palestrante e Capacidade --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Título do Evento *</label>
                    <input type="text" name="title" value="{{ old('title', $event->title) }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('title') border-red-500 @enderror" required>
                    @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2" x-data="imageCropper()">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Imagem do Evento</label>

                    <input type="file" id="croppedFileInput" name="image" accept="image/*" class="hidden">

                    <div class="flex items-center gap-4">
                        <button type="button" @click="$refs.rawInput.click()"
                            class="px-5 py-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-sm font-semibold text-blue-700 transition">
                            <i class="fas fa-image mr-2"></i>Selecionar Imagem
                        </button>
                        <span x-show="!preview" class="text-xs text-gray-400">
                            @if($event->image_path)
                                <img src="{{ $event->image_url }}" alt="Imagem atual" class="w-12 h-12 rounded-lg object-cover border border-gray-200 shadow-sm inline-block mr-2">
                                <span class="text-gray-500">Imagem atual — selecione para substituir</span>
                            @else
                                Nenhuma imagem selecionada
                            @endif
                        </span>
                        <div x-show="preview" class="flex items-center gap-3">
                            <img :src="preview" class="w-16 h-16 rounded-lg object-cover border border-gray-200 shadow-sm">
                            <div>
                                <p class="text-xs font-semibold text-gray-700" x-text="fileName"></p>
                                <button type="button" @click="$refs.rawInput.click()" class="text-xs text-blue-600 hover:underline">Trocar imagem</button>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">JPG, PNG ou WebP — máx. 2MB — formato quadrado (1:1)</p>

                    <input type="file" x-ref="rawInput" accept="image/*" class="hidden" @change="openCropper($event)">

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
                    <select name="speaker_id"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white @error('speaker_id') border-red-500 @enderror" required>
                        @foreach($speakers as $speaker)
                            <option value="{{ $speaker->id }}" {{ old('speaker_id', $event->speaker_id) == $speaker->id ? 'selected' : '' }}>
                                {{ $speaker->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('speaker_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Capacidade Máxima *</label>
                    <input type="number" name="max_capacity" value="{{ old('max_capacity', $event->max_capacity) }}"
                        min="{{ $event->participants()->count() }}"
                        class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none @error('max_capacity') border-red-500 @enderror" required>
                    @if($event->participants()->count() > 0)
                        <p class="text-xs text-gray-400 mt-1">Mínimo: {{ $event->participants()->count() }} (já inscritos)</p>
                    @endif
                    @error('max_capacity') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="border-gray-100 mb-5">

            {{-- Tipo --}}
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
                <input type="hidden" name="resource_type" value="auditorio">
                @include('bookings.partials._calendar', [
                    'preselectedDate'  => $preDate,
                    'preselectedStart' => $preStart,
                    'preselectedEnd'   => $preEnd,
                ])
            </div>

            {{-- Hidden inputs --}}
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

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800 mb-5">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Alterar datas ou horário recriará as reservas do Auditório. Se houver conflito em algum dia, você será avisado.
            </div>

            <div class="flex items-center justify-end space-x-4 border-t pt-6">
                <a href="{{ route('events.show', $event) }}" class="text-gray-500 font-semibold hover:text-gray-700">Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-10 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg">
                    Salvar Alterações
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
