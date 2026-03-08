<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Empreende Vitória')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/50 lg:hidden" x-cloak></div>

    @include('layouts.partials.sidebar')

    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        
        @include('layouts.partials.header')

        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8">
            <div class="container mx-auto">
                @yield('content')
            </div>
            
            <footer class="mt-8 py-4 text-center text-xs text-gray-400 border-t border-gray-200">
                &copy; {{ date('Y') }} Empreende Vitória - Vitória de Santo Antão/PE
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>