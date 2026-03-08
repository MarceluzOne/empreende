<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Empreende Vitória')</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-100 font-sans flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 lg:hidden" x-cloak>
    </div>

    @include('layouts.partials.sidebar')

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        @include('layouts.partials.header')

        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 flex flex-col">
            <div class="container mx-auto flex-grow">
                {{-- Exibição de erros de validação (crucial para ver o erro do CPF) --}}
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
                        <p class="font-bold">Ops! Verifique os dados:</p>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>

            <footer class="mt-8 py-4 text-center text-xs text-gray-400 border-t border-gray-200">
                &copy; {{ date('Y') }} Empreende Vitória - Vitória de Santo Antão/PE
            </footer>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/imask"></script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });

                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>