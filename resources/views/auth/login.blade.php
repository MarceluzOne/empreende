<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Empreende Vitoria</title>
    
    {{-- 1. Scripts e Estilos Necessários --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js para a lógica do olho --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- FontAwesome para os ícones --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/logo-novo-1-400x96.png') }}" alt="Logo Empreende Vitória" class="h-16 w-auto">
        </div>
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Empreende Vitória</h2>

        @if ($errors->any())
            <div class="mb-4 p-2 bg-red-100 text-red-600 text-sm rounded">
                <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                <input type="email" name="email" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            {{-- 2. Campo de Senha com Alpine.js --}}
            <div class="mb-6" x-data="{ show: false }">
                <label class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
                <div class="relative flex items-center">
                    {{-- Input --}}
                    <input :type="show ? 'text' : 'password'" 
                           name="password" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10" 
                           required>

                    {{-- Botão do Olho --}}
                    <button type="button" 
                            @click="show = !show" 
                            class="absolute right-3 focus:outline-none text-gray-400 hover:text-blue-600 transition-colors">
                        {{-- Ícone dinâmico --}}
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                    onclick="this.disabled=true; this.form.submit();"
                    class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                Entrar
            </button>

            <div class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">
                    Esqueci minha senha
                </a>
            </div>
        </form>

        @if(session('status'))
            <div class="mt-4 flex items-center gap-2 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
                <i class="fas fa-check-circle text-green-500"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif
    </div>
</body>
</html>