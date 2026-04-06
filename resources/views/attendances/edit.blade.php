@extends('layouts.app')

@section('title', 'Editar Atendimento - Empreende Vitória')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ 
    isScheduled: {{ $isScheduled ? 'true' : 'false' }},
    status: '{{ $attendance->status }}'
}">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">
                <i class="fas fa-edit text-blue-900 mr-2"></i> Editar Registro
            </h2>
            <p class="text-gray-500 italic text-sm">Atualize as informações do atendimento de {{ $attendance->customer_name }}.</p>
        </div>
        <a href="{{ route('attendances.index') }}" class="text-gray-400 hover:text-gray-600 transition font-bold text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Voltar
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <form action="{{ route('attendances.update', $attendance->id) }}" method="POST" class="p-8 md:p-10">
            @csrf
            @method('PUT')
            
            {{-- Campo oculto para o Alpine enviar o estado correto --}}
            <input type="hidden" name="is_scheduled" :value="isScheduled">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                {{-- Nome --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nome do Cidadão *</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $attendance->customer_name) }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800" required>
                </div>

                {{-- CPF --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">CPF</label>
                    <input type="text" name="customer_cpf" id="cpf_mask" value="{{ old('customer_cpf', $attendance->customer_cpf) }}"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800">
                </div>

                {{-- Serviço --}}
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Serviço</label>
                    <select name="service_type" class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-700">
                        @foreach($services as $service)
                            <option value="{{ $service }}" {{ (old('service_type', $attendance->service_type) == $service) ? 'selected' : '' }}>
                                {{ $service }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Seletor de Momento (Tabs) --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Momento do Atendimento</label>
                    <div class="flex bg-gray-100 p-1 rounded-2xl w-full">
                        <button type="button" @click="isScheduled = false"
                            :class="!isScheduled ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-6 py-2 rounded-xl font-bold text-sm transition-all">
                            Realizado Agora
                        </button>
                        <button type="button" @click="isScheduled = true"
                            :class="isScheduled ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                            class="flex-1 px-6 py-2 rounded-xl font-bold text-sm transition-all">
                            Agendar Retorno
                        </button>
                    </div>
                </div>

                {{-- Campos de Data/Hora --}}
                <template x-if="isScheduled">
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 animate-fade-in-down">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nova Data *</label>
                            <input type="date" name="scheduled_date" 
                                value="{{ old('scheduled_date', $attendance->scheduled_at ? $attendance->scheduled_at->format('Y-m-d') : date('Y-m-d')) }}"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-blue-200 focus:border-blue-900 rounded-2xl outline-none font-bold text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Novo Horário *</label>
                            <input type="time" name="scheduled_time" 
                                value="{{ old('scheduled_time', $attendance->scheduled_at ? $attendance->scheduled_at->format('H:i') : '08:00') }}"
                                class="w-full px-5 py-4 bg-gray-50 border-2 border-blue-200 focus:border-blue-900 rounded-2xl outline-none font-bold text-gray-800">
                        </div>
                    </div>
                </template>

                {{-- Descrição --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Relato do Atendimento *</label>
                    <textarea name="description" rows="4"
                        class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 rounded-2xl outline-none font-semibold text-gray-800" required>{{ old('description', $attendance->description) }}</textarea>
                </div>

                {{-- Status Manual (Caso queira mudar de 'scheduled' para 'completed' na mão) --}}
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Alterar Status</label>
                    <select name="status" class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 rounded-2xl outline-none font-bold text-gray-700">
                        <option value="scheduled" {{ $attendance->status == 'scheduled' ? 'selected' : '' }}>Agendado</option>
                        <option value="completed" {{ $attendance->status == 'completed' ? 'selected' : '' }}>Concluído</option>
                        <option value="pending" {{ $attendance->status == 'pending' ? 'selected' : '' }}>Pendente</option>
                    </select>
                </div>
            </div>

            <div class="mt-10 flex gap-4">
                <button type="submit"
                        class="w-full md:3/4 bg-blue-900 text-white py-5 rounded-2xl font-semibold uppercase tracking-widest shadow-2xl active:scale-95 flex items-center self-end justify-center">
                        <span>Salvar Alterações</span>
                        <i class="fas hidden md:flex fa-arrow-right ml-2"></i>
                    </button>
            </div>
        </form>
    </div>
</div>
@endsection