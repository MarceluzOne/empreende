<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-30 w-56 bg-white border-r border-gray-200 text-gray-700 transition duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col">

    {{-- Logo / Marca --}}
    <div class="px-5 py-5 border-b border-gray-100 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
            <span class="text-white font-bold text-sm">EV</span>
        </div>
        <div class="leading-tight">
            <p class="font-bold text-sm text-gray-900">Empreende</p>
            <p class="text-xs text-gray-400">Vitória de S. Antão</p>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

        {{-- GERAL --}}
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-2 pb-1 pt-2">Geral</p>

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-chart-bar w-4 text-center"></i>
            Dashboard
        </a>

        {{-- OPERAÇÕES --}}
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-2 pb-1 pt-4">Operações</p>

        <a href="{{ route('attendances.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('attendances.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-briefcase w-4 text-center"></i>
            Atendimentos
        </a>

        <a href="{{ route('bookings.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('bookings.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-calendar-alt w-4 text-center"></i>
            Reservas
        </a>

        <a href="{{ route('events.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('events.*') || request()->routeIs('speakers.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-star w-4 text-center"></i>
            Eventos
        </a>

        <a href="{{ route('services.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('services.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-user-tie w-4 text-center"></i>
            Prestadores
        </a>

        {{-- CADASTROS --}}
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-2 pb-1 pt-4">Cadastros</p>

        <a href="{{ route('job-vacancies.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('job-vacancies.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-clipboard-list w-4 text-center"></i>
            Vagas de Emprego
        </a>

        <a href="{{ route('job-seekers.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('job-seekers.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-user-graduate w-4 text-center"></i>
            Candidatos
        </a>

        {{-- SISTEMA --}}
        @if(auth()->user()->roles->contains('name', 'admin'))
        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider px-2 pb-1 pt-4">Sistema</p>

        <a href="{{ route('users.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-users-cog w-4 text-center"></i>
            Usuários
        </a>
        @endif

        <a href="{{ route('profile.edit') }}"
            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                {{ request()->routeIs('profile.*') ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
            <i class="fas fa-user-cog w-4 text-center"></i>
            Meu Perfil
        </a>

    </nav>

    {{-- Usuário logado + logout --}}
    <div class="px-4 py-4 border-t border-gray-100 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center shrink-0 text-white text-xs font-bold">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
            <p class="text-[10px] text-gray-400 uppercase tracking-wide">
                @if(auth()->user()->roles->contains('name', 'admin')) Administrador
                @elseif(auth()->user()->roles->contains('name', 'employee')) Funcionário
                @else Usuário @endif
            </p>
        </div>
        <button type="button" title="Sair"
            onclick="document.getElementById('modal-logout').style.display='flex'"
            class="text-gray-400 hover:text-red-500 transition">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</aside>

{{-- Modal de confirmação de logout --}}
<div id="modal-logout" style="display:none" class="fixed inset-0 z-50 items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm mx-4">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <i class="fas fa-sign-out-alt text-red-500"></i>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-800">Sair da conta</h2>
                <p class="text-sm text-gray-500">Tem certeza que deseja sair?</p>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="button"
                onclick="document.getElementById('modal-logout').style.display='none'"
                class="px-4 py-2 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                Cancelar
            </button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                    Sair
                </button>
            </form>
        </div>
    </div>
</div>
