@extends('layouts.app')

@section('title', 'Palestrantes - Empreende Vitória')

@section('content')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Palestrantes</h1>
            <p class="text-sm text-gray-400 mt-0.5">Cadastro de palestrantes para eventos.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('events.index') }}" class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                Voltar a Eventos
            </a>
            <a href="{{ route('speakers.create') }}"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus text-xs sm:hidden"></i>
                <span class="hidden sm:inline">Novo Palestrante</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">E-mail</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Telefone</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Eventos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($speakers as $speaker)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $speaker->name }}</div>
                                @if($speaker->bio)
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $speaker->bio }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $speaker->email ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $speaker->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-center text-sm font-bold text-blue-700">{{ $speaker->events_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Nenhum palestrante cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $speakers->links() }}
        </div>
    </div>
</div>
@endsection
