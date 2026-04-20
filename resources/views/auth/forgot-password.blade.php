<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Conta - Empreende Vitória</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">

        <div class="flex justify-center mb-4">
            <img src="{{ asset('assets/logo-novo-1-400x96.png') }}" alt="Logo Empreende Vitória" class="h-16 w-auto">
        </div>

        <h2 class="text-xl font-bold mb-2 text-center text-gray-800">Recuperar Conta</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Informe seu e-mail e enviaremos um link para redefinir sua senha.</p>

        @if(session('status'))
            <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
                <i class="fas fa-check-circle text-green-500"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-700 text-sm rounded-lg">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror"
                    placeholder="seu@email.com" required autofocus>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition duration-300">
                Enviar Link de Recuperação
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
