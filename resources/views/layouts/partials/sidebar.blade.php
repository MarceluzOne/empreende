<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-blue-900 text-white transition duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-xl">
    
    <div class="p-6 border-b border-blue-800 flex flex-col items-center">
        <div class="w-20 h-20 rounded-full bg-gray-300 border-2 border-white overflow-hidden mb-3 flex items-center justify-center">
            <img src="{{ asset('assets/Brasão_vitoria.png') }}" 
                 alt="Brasão Vitória" 
                 class="w-full h-full object-cover">
        </div>

        <p class="font-bold text-sm text-center leading-tight">{{ auth()->user()->name }}</p>
        
        <span class="text-xs text-blue-300 mt-1">
            @if(auth()->user()->roles->contains('name', 'admin'))
                Administrador
            @elseif(auth()->user()->roles->contains('name', 'employee'))
                Funcionário
            @else
                Usuário
            @endif
        </span>
    </div>

    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto custom-scrollbar">
        <a href="{{ route('dashboard') }}" 
           class="flex items-center p-2 text-blue-100 hover:bg-blue-800 rounded transition group">
            <span class="mr-3 group-hover:scale-110 transition">🏠</span> Dashboard
        </a>

        <a href="#" class="flex items-center p-2 text-blue-100 hover:bg-blue-800 rounded transition group">
            <span class="mr-3 group-hover:scale-110 transition">📅</span> Cadastro de vagas
        </a>

        <a href="#" class="flex items-center p-2 text-blue-100 hover:bg-blue-800 rounded transition group">
            <span class="mr-3 group-hover:scale-110 transition">📅</span> Agendamentos
        </a>
    </nav>

    <div class="p-4 border-t border-blue-800">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center p-2 bg-red-600 hover:bg-red-700 rounded text-sm font-bold transition">
                Sair do Sistema
            </button>
        </form>
    </div>
</aside>