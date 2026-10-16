<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Vanessa Event Planner') - Panel de Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00113a",
                        "surface-bright": "#f7f9fb",
                        "on-secondary": "#ffffff",
                        "secondary": "#50606f",
                        "surface-variant": "#e0e3e5",
                        "on-surface": "#191c1e",
                        "inverse-surface": "#2d3133",
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
                        "on-tertiary": "#ffffff",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-container": "#758dd5",
                        "surface": "#f7f9fb",
                        "tertiary-fixed": "#ffdbd0",
                        "tertiary-container": "#501300",
                        "on-tertiary-container": "#d37758",
                        "inverse-primary": "#b3c5ff",
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
                        "surface-tint": "#435b9f",
                        "on-error-container": "#93000a",
                        "secondary-container": "#d1e1f4",
                        "tertiary-fixed-dim": "#ffb59e",
                        "on-tertiary-fixed": "#390b00",
                        "tertiary": "#2d0700",
                        "on-tertiary-fixed-variant": "#783018",
                        "on-primary-fixed": "#00174a",
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
        body { font-family: 'Inter', sans-serif; background-color: #f7f9fb; }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .nav-icon-filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        #sidebar { transition: transform 0.3s ease; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @stack('styles')
</head>
<body class="text-on-surface antialiased overflow-x-hidden bg-surface-bright">

{{-- Overlay para móvil --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

{{-- SideNavBar --}}
<aside id="sidebar" class="h-screen w-64 fixed left-0 top-0 border-r border-slate-200 bg-slate-50 flex flex-col p-4 gap-2 z-50 -translate-x-full lg:translate-x-0">
    <div class="mb-6 px-4 py-2">
        <h1 class="text-lg font-bold text-blue-900 font-headline">Vanessa Events</h1>
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Gestión Elite</p>
    </div>

    <nav class="flex flex-col gap-0.5 flex-1 overflow-y-auto scrollbar-hide">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('dashboard') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'nav-icon-filled' : '' }}">dashboard</span>
            <span>Dashboard</span>
        </a>

        {{-- Inventario --}}
        <a href="{{ route('articles.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('articles.*') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('articles.*') ? 'nav-icon-filled' : '' }}">inventory_2</span>
            <span>Inventario</span>
        </a>

        {{-- Nueva Reserva --}}
        <a href="{{ route('reservas.create') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('reservas.create') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('reservas.create') ? 'nav-icon-filled' : '' }}">event_available</span>
            <span>Nueva Reserva</span>
        </a>

        {{-- Clientes --}}
        <a href="{{ route('clients.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('clients.*') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('clients.*') ? 'nav-icon-filled' : '' }}">group</span>
            <span>Clientes</span>
        </a>

        {{-- Categorías (solo admin) --}}
        @if(auth()->user()->rol === 'admin')
        <a href="{{ route('categories.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('categories.*') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('categories.*') ? 'nav-icon-filled' : '' }}">category</span>
            <span>Categorías</span>
        </a>

        {{-- Reservas --}}
        <a href="{{ route('reservas.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('reservas.index') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('reservas.index') ? 'nav-icon-filled' : '' }}">event_note</span>
            <span>Reservas</span>
        </a>

        {{-- Control de Pagos (solo admin) --}}
        <a href="{{ route('pagos.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('pagos.*') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('pagos.*') ? 'nav-icon-filled' : '' }}">payments</span>
            <span>Control Pagos</span>
        </a>

        {{-- Reporte Disponibilidad (solo admin) --}}
        <a href="{{ route('disponibilidad.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('disponibilidad.*') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('disponibilidad.*') ? 'nav-icon-filled' : '' }}">calendar_view_day</span>
            <span>Disponibilidad</span>
        </a>

        {{-- Retornos (solo admin) --}}
        <a href="{{ route('retornos.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('retornos.*') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('retornos.*') ? 'nav-icon-filled' : '' }}">assignment_return</span>
            <span>Retornos</span>
        </a>

        {{-- Ajustes de Stock (solo admin) --}}
        <a href="{{ route('stock.ajustes') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200
                  {{ request()->routeIs('stock.*') ? 'bg-white text-blue-950 shadow-sm translate-x-1' : 'text-slate-500 hover:bg-white hover:text-blue-900' }}">
            <span class="material-symbols-outlined {{ request()->routeIs('stock.*') ? 'nav-icon-filled' : '' }}">tune</span>
            <span>Ajustes Stock</span>
        </a>
        @endif
    </nav>

    {{-- Botón Nuevo Evento --}}
    <a href="{{ route('reservas.create') }}" class="mx-2 bg-primary text-white py-3 px-4 rounded-xl flex items-center justify-center gap-2 font-headline font-bold text-sm shadow-md hover:opacity-90 transition-all active:scale-95">
        <span class="material-symbols-outlined text-sm">add</span>
        Nueva Reserva
    </a>

    {{-- Zona inferior --}}
    <div class="mt-2 flex flex-col gap-1 border-t border-slate-200 pt-3">
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:bg-white hover:text-blue-900 transition-all duration-200">
            <span class="material-symbols-outlined">settings</span>
            <span>Configuración</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 hover:text-error hover:bg-red-50 transition-all duration-200">
                <span class="material-symbols-outlined">logout</span>
                <span>Cerrar Sesión</span>
            </button>
        </form>
    </div>
</aside>

{{-- Main Content Wrapper --}}
<main class="lg:ml-64 min-h-screen flex flex-col">

    {{-- TopNavBar --}}
    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100 sticky top-0 z-40 flex justify-between items-center w-full px-4 sm:px-6 lg:px-8 py-3 h-16">
        <div class="flex items-center gap-3">
            {{-- Hamburger (solo móvil) --}}
            <button id="sidebar-toggle" class="lg:hidden p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <span class="text-base sm:text-xl font-bold text-blue-950 tracking-tight font-headline truncate">Vanessa Event Planner</span>
        </div>

        <div class="flex items-center gap-2 sm:gap-4">
            {{-- Buscador (oculto en móvil muy pequeño) --}}
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-low">
                <span class="material-symbols-outlined text-outline text-lg">search</span>
                <input class="bg-transparent border-none focus:ring-0 text-sm text-on-surface-variant w-28 md:w-48" placeholder="Buscar..." type="text"/>
            </div>

            {{-- Notificaciones --}}
            <button class="p-2 text-slate-500 hover:bg-slate-50 rounded-full transition-colors relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full border-2 border-white"></span>
            </button>

            {{-- Perfil --}}
            <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold ring-2 ring-white ring-offset-1">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                </div>
                <span class="hidden sm:block text-sm font-semibold text-blue-900 font-headline">{{ auth()->user()->nombre }}</span>
            </div>
        </div>
    </header>

    {{-- Contenido de la página --}}
    <div class="flex-1">
        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="w-full py-5 sm:py-6 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 gap-3 text-xs text-slate-400 font-label">
        <p>© 2025 Vanessa Event Planner. Quillacollo, Bolivia.</p>
        <div class="flex gap-4 sm:gap-6">
            <a class="hover:text-primary transition-colors" href="#">Soporte</a>
            <a class="hover:text-primary transition-colors" href="#">Términos</a>
            <a class="hover:text-primary transition-colors" href="#">Privacidad</a>
        </div>
    </footer>
</main>

{{-- FAB móvil --}}
<button class="fixed bottom-6 right-6 w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center shadow-xl hover:scale-105 transition-transform lg:hidden z-30">
    <span class="material-symbols-outlined">add</span>
</button>

<script>
    const sidebar        = document.getElementById('sidebar');
    const overlay        = document.getElementById('sidebar-overlay');
    const toggleBtn      = document.getElementById('sidebar-toggle');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
    });

    overlay.addEventListener('click', closeSidebar);
</script>

@stack('scripts')
</body>
</html>
