@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Bem-vindo, {{ auth()->user()->name }}! Aqui está o resumo do dia.</p>
</div>

{{-- Cards de resumo --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Atendimentos hoje --}}
    <a href="{{ route('attendances.index') }}"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition group">
        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0 group-hover:bg-blue-200 transition">
            <i class="fas fa-briefcase text-blue-700 text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $todayAttendances->count() }}</p>
            <p class="text-sm text-gray-500">Atendimentos hoje</p>
        </div>
    </a>

    {{-- Prestadores pendentes --}}
    <a href="{{ route('services.index') }}"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition group">
        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 group-hover:bg-amber-200 transition">
            <i class="fas fa-user-clock text-amber-600 text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $pendingProviders }}</p>
            <p class="text-sm text-gray-500">Prestadores pendentes</p>
        </div>
    </a>

    {{-- Agendamentos hoje --}}
    <a href="{{ route('bookings.index') }}"
        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition group">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center shrink-0 group-hover:bg-green-200 transition">
            <i class="fas fa-calendar-day text-green-700 text-lg"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $todayBookingsCount }}</p>
            <p class="text-sm text-gray-500">Agendamentos hoje</p>
        </div>
    </a>

</div>

{{-- Lista de atendimentos do dia --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <i class="fas fa-calendar-check text-blue-700"></i>
            <h2 class="font-semibold text-gray-800">Atendimentos de Hoje</h2>
            <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2 py-0.5 rounded-full">
                {{ \Carbon\Carbon::today()->format('d/m/Y') }}
            </span>
        </div>
        <a href="{{ route('attendances.index') }}"
            class="text-sm text-blue-700 hover:text-blue-900 font-semibold transition flex items-center gap-1">
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
                    'completed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    'in_progress' => 'bg-blue-100 text-blue-700',
                    default => 'bg-yellow-100 text-yellow-700',
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
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center shrink-0 text-blue-700 font-bold text-sm">
                    {{ strtoupper(substr($attendance->customer_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 truncate">{{ $attendance->customer_name }}</p>
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
