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
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($speakers as $speaker)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 shrink-0 overflow-hidden flex items-center justify-center">
                                        @if($speaker->photoUrl())
                                            <img src="{{ $speaker->photoUrl() }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-user text-gray-300"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $speaker->name }}</div>
                                        @if($speaker->bio)
                                            <div class="text-xs text-gray-500 truncate max-w-xs">{{ $speaker->bio }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $speaker->email ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $speaker->phone ?? '—' }}</td>
                            <td class="px-6 py-4 text-center text-sm font-bold text-blue-700">{{ $speaker->events_count }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('speakers.edit', $speaker) }}" class="text-yellow-600 hover:text-yellow-900 transition" title="Editar">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </a>
                                    <button type="button"
                                        onclick="confirmDelete({{ $speaker->id }}, '{{ addslashes($speaker->name) }}')"
                                        class="text-red-600 hover:text-red-900 transition" title="Excluir">
                                        <i class="fas fa-trash-alt fa-lg"></i>
                                    </button>
                                    <form id="delete-form-{{ $speaker->id }}" action="{{ route('speakers.destroy', $speaker) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Nenhum palestrante cadastrado.</td>
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

{{-- Modal de confirmação de exclusão --}}
<div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full mx-4">
        <div class="flex items-center justify-center w-14 h-14 rounded-full bg-red-100 mx-auto mb-4">
            <i class="fas fa-trash-alt text-red-500 text-xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 text-center mb-1">Excluir Palestrante</h3>
        <p class="text-sm text-gray-500 text-center mb-6">Tem certeza que deseja excluir <span id="delete-speaker-name" class="font-semibold text-gray-700"></span>? Esta ação não pode ser desfeita.</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 border border-gray-300 text-gray-700 font-semibold py-2 rounded-lg hover:bg-gray-50 transition">Cancelar</button>
            <button onclick="submitDelete()" class="flex-1 bg-red-600 text-white font-semibold py-2 rounded-lg hover:bg-red-700 transition">Excluir</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let _deleteId = null;
function confirmDelete(id, name) {
    _deleteId = id;
    document.getElementById('delete-speaker-name').textContent = name;
    const m = document.getElementById('delete-modal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
function closeDeleteModal() {
    _deleteId = null;
    const m = document.getElementById('delete-modal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}
function submitDelete() {
    if (_deleteId) document.getElementById('delete-form-' + _deleteId).submit();
}
</script>
@endpush
@endsection
