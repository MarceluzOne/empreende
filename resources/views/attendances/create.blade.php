@extends('layouts.app')

@section('title', 'Novo Atendimento - Empreende Vitória')

@section('content')
    <div class="max-w-4xl mx-auto" x-data="{ 
            isScheduled: false, 
            status: 'completed' 
        }">
        {{-- Cabeçalho --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tighter">
                    Registrar Atendimento
                </h2>
                <p class="text-gray-500 italic text-sm">Cadastre o fluxo de cidadãos na recepção de Vitória de Santo Antão.
                </p>
            </div>
            <a href="{{ route('attendances.index') }}"
                class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl font-bold hover:bg-gray-200 transition text-sm">
                
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <form action="{{ route('attendances.store') }}" method="POST" class="p-8 md:p-10">
                @csrf
                <input type="hidden" name="is_scheduled" :value="isScheduled">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Nome do Cidadão --}}
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nome do
                            Cidadão / Empreendedor *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                            class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800 @error('customer_name') border-red-500 @enderror"
                            placeholder="Nome completo" required>
                        @error('customer_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- CPF --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">CPF
                            (Opcional)</label>
                        <input type="text" name="customer_cpf" id="cpf_mask" value="{{ old('customer_cpf') }}"
                            class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800"
                            placeholder="000.000.000-00">
                    </div>

                    {{-- Telefone (Opcional) --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Telefone
                            (Opcional)</label>
                        <input type="text" name="customer_phone" id="phone_mask" value="{{ old('customer_phone') }}"
                            class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800"
                            placeholder="(00) 00000-0000">
                    </div>

                    {{-- Tipo de Serviço --}}
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tipo de
                            Serviço *</label>
                        <select name="service_type"
                            class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-700"
                            required>
                            <option value="" disabled selected>Selecione...</option>
                            <option value="Formalização MEI">Formalização MEI</option>
                            <option value="Emissão de Guia">Emissão de Guia (DAS)</option>
                            <option value="Declaração Anual">Declaração Anual (DASN)</option>
                            <option value="Consultoria">Consultoria Geral</option>
                            <option value="Outros">Outros Assuntos</option>
                        </select>
                    </div>

                    {{-- Toggle: Atendimento Agora ou Agendado --}}
                    {{-- Seletor de Tipo de Atendimento --}}
                    <div class="md:col-span-2 mb-4">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Momento do
                            Atendimento</label>
                        <div class="flex bg-gray-100 p-1 rounded-2xl w-full">
                            {{-- Opção: Agora --}}
                            <button type="button" @click="isScheduled = false"
                                :class="!isScheduled ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1 px-6 py-2 rounded-xl font-bold text-sm transition-all flex items-center justify-center">
                                <input type="radio" :value="false" x-model="isScheduled" class="hidden">
                                Realizado Agora
                            </button>

                            {{-- Opção: Agendar --}}
                            <button type="button" @click="isScheduled = true"
                                :class="isScheduled ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                                class="flex-1  px-6 py-2 rounded-xl font-bold text-sm transition-all flex items-center justify-center">
                                <input type="radio" :value="true" x-model="isScheduled" class="hidden">
                                Agendar Retorno
                            </button>
                        </div>
                    </div>

                    {{-- Campos de Data/Hora (Aparecem se for Agendado) --}}
                    <template x-if="isScheduled">
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8 animate-fade-in-down">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Data
                                    do Agendamento *</label>
                                <input type="date" name="scheduled_date" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                    class="w-full px-5 py-4 bg-gray-50 border-2 border-blue-200 focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Horário
                                    *</label>
                                <input type="time" name="scheduled_time" value="08:00"
                                    class="w-full px-5 py-4 bg-gray-50 border-2 border-blue-200 focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-bold text-gray-800">
                            </div>
                        </div>
                    </template>

                    {{-- Descrição --}}
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Relato /
                            Observações *</label>
                        <textarea name="description" rows="4"
                            class="w-full px-5 py-4 bg-gray-50 border-2 border-transparent focus:border-blue-900 focus:bg-white rounded-2xl outline-none transition-all font-semibold text-gray-800 placeholder:italic"
                            placeholder="O que foi resolvido ou o que precisa ser feito?"
                            required>{{ old('description') }}</textarea>
                    </div>
                </div>

                {{-- Botão de Ação --}}
                <div class="mt-10">
                    <button type="submit"
                        class="w-full md:3/4 bg-blue-600 text-white py-5 rounded-2xl font-semibold uppercase tracking-widest shadow-2xl active:scale-95 flex items-center self-end justify-center">
                        <i class="fas fa-check sm:hidden"></i>
                        <span class="hidden sm:inline" x-text="isScheduled ? 'Agendar Atendimento' : 'Finalizar Atendimento Agora'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/imask"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Máscara de CPF
                const cpfElement = document.getElementById('cpf_mask');
                if (cpfElement) {
                    IMask(cpfElement, { mask: '000.000.000-00' });
                }
                // Máscara de Telefone
                const phoneElement = document.getElementById('phone_mask');
                if (phoneElement) {
                    IMask(phoneElement, {
                        mask: [
                            { mask: '(00) 00000-0000' },
                            { mask: '(00) 0000-0000' },
                            { mask: '+00 (00) 00000-0000' }
                        ]
                    });
                }
            });
        </script>
        <style>
            .animate-fade-in-down {
                animation: fadeInDown 0.3s ease-out;
            }

            @keyframes fadeInDown {
                0% {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush
@endsection