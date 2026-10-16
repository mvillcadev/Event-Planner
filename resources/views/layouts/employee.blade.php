<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Empleado') - Vanessa Event Planner</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00113a",
                        "surface-bright": "#f7f9fb",
                        "secondary": "#50606f",
                        "surface-variant": "#e0e3e5",
                        "on-surface": "#191c1e",
                        "secondary-fixed-dim": "#b8c8da",
                        "error": "#ba1a1a",
                        "on-surface-variant": "#444650",
                        "on-secondary-fixed-variant": "#394857",
                        "surface-container": "#eceef0",
                        "outline": "#757682",
                        "secondary-fixed": "#d4e4f6",
                        "primary-fixed-dim": "#b3c5ff",
                        "on-background": "#191c1e",
                        "primary-container": "#002366",
                        "surface-container-lowest": "#ffffff",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "outline-variant": "#c5c6d2",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-container": "#758dd5",
                        "surface": "#f7f9fb",
                        "tertiary-fixed": "#ffdbd0",
                        "on-tertiary-fixed": "#390b00",
                        "surface-container-high": "#e6e8ea",
                        "surface-container-low": "#f2f4f6",
                        "inverse-on-surface": "#eff1f3",
                        "background": "#f7f9fb",
                        "on-primary": "#ffffff",
                        "on-primary-fixed-variant": "#2a4386",
                        "on-secondary-container": "#556474",
                        "surface-dim": "#d8dadc",
                        "primary-fixed": "#dbe1ff",
                        "on-secondary-fixed": "#0d1d2a",
                        "on-error-container": "#93000a",
                        "secondary-container": "#d1e1f4",
                        "tertiary-fixed-dim": "#ffb59e",
                    },
                    fontFamily: {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .nav-active .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        #emp-sidebar { transition: transform 0.3s ease; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-background text-on-background min-h-screen flex">

    {{-- Overlay móvil --}}
    <div id="emp-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

    {{-- ══════════ SIDEBAR ══════════ --}}
    <aside id="emp-sidebar"
           class="flex flex-col h-screen w-64 fixed left-0 top-0 border-r border-slate-200 bg-slate-50 py-6 z-50 -translate-x-full md:translate-x-0 transition-transform duration-300">

        <div class="px-6 mb-8">
            <h1 class="text-lg font-bold text-blue-900 font-headline">Vanessa</h1>
            <p class="text-xs text-secondary opacity-70">Gestión de Eventos</p>
        </div>

        <nav class="flex-1 space-y-1 px-3 overflow-y-auto">
            <a href="{{ route('employee.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm font-medium
                      {{ request()->routeIs('employee.dashboard') ? 'nav-active bg-blue-50 text-blue-900 border-r-4 border-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('reservas.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm font-medium
                      {{ request()->routeIs('reservas.index') ? 'nav-active bg-blue-50 text-blue-900 border-r-4 border-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                <span class="material-symbols-outlined">event_note</span>
                <span>Mis Reservas</span>
            </a>
            <a href="{{ route('reservas.create') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm font-medium
                      {{ request()->routeIs('reservas.create') ? 'nav-active bg-blue-50 text-blue-900 border-r-4 border-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                <span class="material-symbols-outlined">event_available</span>
                <span>Nueva Reserva</span>
            </a>
            <a href="{{ route('clients.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm font-medium
                      {{ request()->routeIs('clients.*') ? 'nav-active bg-blue-50 text-blue-900 border-r-4 border-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                <span class="material-symbols-outlined">group</span>
                <span>Clientes</span>
            </a>
            <a href="{{ route('articles.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-sm font-medium
                      {{ request()->routeIs('articles.*') ? 'nav-active bg-blue-50 text-blue-900 border-r-4 border-blue-900' : 'text-slate-600 hover:bg-slate-100' }}">
                <span class="material-symbols-outlined">inventory_2</span>
                <span>Inventario</span>
            </a>
        </nav>

        <div class="px-3 mt-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-600 hover:bg-red-50 hover:text-red-600 transition-all text-sm font-medium">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ══════════ CONTENIDO PRINCIPAL ══════════ --}}
    <main class="flex-1 md:ml-64 flex flex-col min-h-screen pb-20 md:pb-0">

        {{-- TopAppBar --}}
        <header class="bg-white/80 backdrop-blur-md sticky top-0 z-30 border-b border-slate-100 px-4 sm:px-6 py-3 flex justify-between items-center h-16">
            <div class="flex items-center gap-3">
                {{-- Hamburger móvil --}}
                <button id="emp-toggle" class="md:hidden p-2 text-primary hover:bg-slate-50 rounded-xl transition-colors">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <div>
                    <h2 class="text-base sm:text-xl font-bold text-blue-900 tracking-tight font-headline">
                        Hola, {{ auth()->user()->nombre }} 👋
                    </h2>
                    <p class="text-xs text-secondary hidden sm:block">Panel de Gestión Operativa</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <button class="p-2 text-slate-500 hover:bg-slate-50 rounded-full transition-colors">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold ring-2 ring-white ring-offset-1 flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                </div>
            </div>
        </header>

        {{-- Contenido de la página --}}
        @yield('content')

    </main>

    {{-- ══════════ NAVEGACIÓN INFERIOR MÓVIL ══════════ --}}
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white z-50 flex justify-around items-center px-2 pb-4 pt-2 border-t border-slate-100 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
        <a href="{{ route('employee.dashboard') }}"
           class="flex flex-col items-center justify-center px-3 py-1 rounded-xl transition-all
                  {{ request()->routeIs('employee.dashboard') ? 'text-blue-900 bg-blue-50' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-2xl">home</span>
            <span class="text-[10px] font-semibold mt-0.5">Inicio</span>
        </a>
        <a href="{{ route('reservas.create') }}"
           class="flex flex-col items-center justify-center px-3 py-1 rounded-xl transition-all
                  {{ request()->routeIs('reservas.create') ? 'text-blue-900 bg-blue-50' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-2xl">event_available</span>
            <span class="text-[10px] font-semibold mt-0.5">Reserva</span>
        </a>
        <a href="{{ route('clients.index') }}"
           class="flex flex-col items-center justify-center px-3 py-1 rounded-xl transition-all
                  {{ request()->routeIs('clients.*') ? 'text-blue-900 bg-blue-50' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-2xl">group</span>
            <span class="text-[10px] font-semibold mt-0.5">Clientes</span>
        </a>
        <a href="{{ route('articles.index') }}"
           class="flex flex-col items-center justify-center px-3 py-1 rounded-xl transition-all
                  {{ request()->routeIs('articles.*') ? 'text-blue-900 bg-blue-50' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-2xl">inventory_2</span>
            <span class="text-[10px] font-semibold mt-0.5">Inventario</span>
        </a>
    </nav>

    <script>
        const empSidebar  = document.getElementById('emp-sidebar');
        const empOverlay  = document.getElementById('emp-overlay');
        const empToggle   = document.getElementById('emp-toggle');

        empToggle.addEventListener('click', () => {
            empSidebar.classList.toggle('-translate-x-full');
            empOverlay.classList.toggle('hidden');
        });
        empOverlay.addEventListener('click', () => {
            empSidebar.classList.add('-translate-x-full');
            empOverlay.classList.add('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
