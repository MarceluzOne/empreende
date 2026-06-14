@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Cabeçalho --}}
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Olá, {{ explode(' ', auth()->user()->name)[0] }}</h1>
        <p class="text-sm text-gray-400 mt-0.5">Visão geral do sistema — Vitória de Santo Antão/PE</p>
    </div>
    <a href="{{ route('attendances.create') }}"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        <i class="fas fa-plus text-xs sm:hidden"></i>
        <span class="hidden sm:inline">Novo atendimento</span>
    </a>
</div>

{{-- Cards de resumo --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Atendimentos hoje --}}
    <a href="{{ route('attendances.index') }}"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-1 bg-blue-500 rounded-t-xl"></div>
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Atendimentos hoje</p>
            <span class="text-[11px] font-semibold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">hoje</span>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $todayAttendances->count() }}</p>
        <p class="text-xs text-gray-400 mt-1">registros no dia atual</p>
    </a>

    {{-- Agendamentos hoje --}}
    <a href="{{ route('bookings.index') }}"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-1 bg-green-500 rounded-t-xl"></div>
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Reservas hoje</p>
            <span class="text-[11px] font-semibold bg-green-50 text-green-600 px-2 py-0.5 rounded-full">hoje</span>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $todayBookingsCount }}</p>
        <p class="text-xs text-gray-400 mt-1">agendamentos do dia</p>
    </a>

    {{-- Vagas ativas --}}
    <a href="{{ route('job-vacancies.index') }}"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition group overflow-hidden relative">
        <div class="absolute top-0 left-0 right-0 h-1 bg-purple-500 rounded-t-xl"></div>
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Vagas ativas</p>
            <span class="text-[11px] font-semibold bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full">total</span>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ \App\Models\JobVacancy::where('status', 'active')->count() }}</p>
        <p class="text-xs text-gray-400 mt-1">vagas abertas no momento</p>
    </a>

</div>

{{-- Lista de atendimentos do dia --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <i class="fas fa-calendar-check text-blue-600 text-sm"></i>
            <h2 class="font-semibold text-gray-800 text-sm">Atendimentos de Hoje</h2>
            <span class="text-xs bg-blue-50 text-blue-600 font-semibold px-2 py-0.5 rounded-full">
                {{ \Carbon\Carbon::today()->format('d/m/Y') }}
            </span>
        </div>
        <a href="{{ route('attendances.index') }}"
            class="text-sm text-blue-600 hover:text-blue-800 font-semibold transition flex items-center gap-1">
            Ver todos <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>

    @if($todayAttendances->isEmpty())
        <div class="px-6 py-12 text-center text-gray-400">
            <i class="fas fa-calendar-times text-3xl mb-3 block"></i>
            Nenhum atendimento agendado para hoje.
        </div>
    @else
        <div class="divide-y divide-gray-50">
            @foreach($todayAttendances as $attendance)
            @php
                $statusColor = match($attendance->status) {
                    'completed'   => 'bg-green-100 text-green-700',
                    'cancelled'   => 'bg-red-100 text-red-700',
                    'in_progress' => 'bg-blue-100 text-blue-700',
                    default       => 'bg-yellow-100 text-yellow-700',
                };
                $statusLabel = match($attendance->status) {
                    'completed'   => 'Concluído',
                    'cancelled'   => 'Cancelado',
                    'in_progress' => 'Em andamento',
                    'scheduled'   => 'Agendado',
                    default       => ucfirst($attendance->status),
                };
            @endphp
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 transition">
                <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center shrink-0 text-blue-600 font-bold text-sm">
                    {{ strtoupper(substr($attendance->customer_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $attendance->customer_name }}</p>
                    <p class="text-xs text-gray-400">{{ $attendance->service_type }}</p>
                </div>
                <div class="text-right shrink-0">
                    @if($attendance->scheduled_at)
                        <p class="text-sm font-semibold text-gray-700">{{ $attendance->scheduled_at->format('H:i') }}</p>
                    @endif
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
