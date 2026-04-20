<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Senha - Empreende Vitória</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">

        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/logo-novo-1-400x96.png') }}" alt="Logo Empreende Vitória" class="h-16 w-auto">
        </div>

        <h2 class="text-xl font-bold mb-2 text-center text-gray-800">Redefinir Senha</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Escolha uma nova senha para sua conta.</p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-700 text-sm rounded-lg">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" x-data="{ show: false }">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $email) }}"
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 @error('email') border-red-400 @enderror"
                    required readonly>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nova Senha</label>
                <div class="relative flex items-center">
                    <input :type="show ? 'text' : 'password'" name="password"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10 @error('password') border-red-400 @enderror"
                        required autofocus>
                    <button type="button" @click="show = !show"
                        class="absolute right-3 text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Mínimo de 8 caracteres.</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Confirmar Nova Senha</label>
                <div class="relative flex items-center">
                    <input :type="show ? 'text' : 'password'" name="password_confirmation"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                        required>
                    <button type="button" @click="show = !show"
                        class="absolute right-3 text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition duration-300">
                Redefinir Senha
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">
                Voltar para o Login
            </a>
        </div>
    </div>
</body>
</html>
