@extends('layouts.app')

@section('title', ($name ?? 'Cidadão') . ' - Empreende Vitória')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Voltar --}}
        <a href="{{ route('citizens.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <i class="fas fa-arrow-left"></i> Voltar para cidadãos
        </a>

        {{-- Cabeçalho --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-white text-xl font-black shrink-0">
                {{ strtoupper(mb_substr($name ?? '?', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-800">{{ $name ?? '—' }}</h2>
                <p class="text-gray-500 font-mono text-sm">CPF: {{ $cpf_formatted }}</p>
                @if(!empty($cnpjs) && count($cnpjs))
                    <p class="text-gray-400 font-mono text-xs mt-0.5">
                        CNPJ: {{ $cnpjs->implode(' · ') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Perfil de candidato --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-user-graduate text-indigo-500 mr-1"></i> Candidato
                </h3>
                @if($candidato)
                    <a href="{{ route('job-seekers.curriculo', $candidato->id) }}" target="_blank"
                        class="text-blue-600 hover:text-blue-800 text-sm font-bold">
                        <i class="fas fa-file-pdf mr-1"></i> Ver currículo (PDF)
                    </a>
                @endif
            </div>

            @if($candidato)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Área de interesse</span>
                        {{ $candidato->interest_area ?? $candidato->job_function ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Cidade / UF</span>
                        {{ trim(($candidato->city ?? '') . ($candidato->state ? ' / ' . $candidato->state : '')) ?: '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Telefone</span>
                        {{ $candidato->formatted_phone ?? $candidato->phone ?? '—' }}
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">E-mail</span>
                        {{ $candidato->email ?? optional($candidato->user)->email ?? '—' }}
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Esta pessoa não possui currículo de candidato cadastrado.</p>
            @endif
        </div>

        {{-- Histórico de atendimentos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-briefcase text-blue-500 mr-1"></i>
                    Atendimentos ({{ $attendances->count() }})
                </h3>
            </div>

            @if($attendances->isEmpty())
                <p class="px-6 py-8 text-sm text-gray-400 italic text-center">Nenhum atendimento registrado para este CPF.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-left">
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Serviço</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">CNPJ</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Situação</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Atendente</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($attendances as $a)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $a->service_type }}</div>
                                    @if($a->description)
                                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ $a->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                    {{ $a->customer_cnpj_formatted ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $a->scheduled_at ? $a->scheduled_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($a->status === 'completed')
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-50 text-green-700">Concluído</span>
                                    @else
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-orange-50 text-orange-700">Agendado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ optional($a->user)->name ?? 'Agendamento pelo site' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
