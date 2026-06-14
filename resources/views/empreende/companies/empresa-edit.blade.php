@extends('layouts.app')

@section('title', 'Editar empresa - Empreende Vitória')

@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('companies.show', app(App\Services\CompanyService::class)->uuidFor($empresa->cnpj)) }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-black text-gray-800 uppercase tracking-tight">Editar empresa</h2>
            </div>

            @if($errors->any())
                <div class="m-6 mb-0 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('companies.empresa.update', $empresa) }}" method="POST" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf @method('PUT')

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Razão social *</label>
                    <input type="text" name="razao_social" value="{{ old('razao_social', $empresa->razao_social) }}" required
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">CNPJ *</label>
                    <input type="text" name="cnpj" id="empresa_cnpj" value="{{ old('cnpj', $empresa->cnpj) }}" required
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Telefone</label>
                    <input type="text" name="telefone" value="{{ old('telefone', $empresa->telefone) }}"
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Cidade</label>
                    <input type="text" name="cidade" value="{{ old('cidade', $empresa->cidade) }}"
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Descrição</label>
                    <textarea name="descricao" rows="3"
                        class="w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('descricao', $empresa->descricao) }}</textarea>
                </div>

                <div class="md:col-span-2 flex justify-end gap-2">
                    <a href="{{ route('companies.show', app(App\Services\CompanyService::class)->uuidFor($empresa->cnpj)) }}"
                        class="px-5 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50">Cancelar</a>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/imask"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const el = document.getElementById('empresa_cnpj');
                if (el) IMask(el, { mask: '00.000.000/0000-00' });
            });
        </script>
    @endpush
@endsection
