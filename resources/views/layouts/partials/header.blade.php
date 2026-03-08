<header class="bg-white shadow-sm p-4 flex justify-between items-center">
    <div class="flex items-center">
        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        
        <img src="{{ asset('assets/logo-novo-1-400x96.png') }}" alt="Logo Empreende Vitória" class="h-6 md:h-8 mr-4">
        
        <h2 class="hidden sm:block text-xl font-bold text-gray-800 border-l-2 border-gray-200 pl-4">
            Painel de Gestão
        </h2>
    </div>

    <div class="flex items-center space-x-4">
        <span class="text-xs text-gray-400 italic hidden md:block">
            Vitória de Santo Antão - PE
        </span>
    </div>
</header>