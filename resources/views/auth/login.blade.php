<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Iniciar Sesión - Vanessa Event Planner</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
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
                        "on-tertiary-fixed": "#390b00",
                        "on-primary-fixed": "#00174a",
                        "secondary": "#50606f",
                        "surface-variant": "#e0e3e5",
                        "on-surface": "#191c1e",
                        "inverse-surface": "#2d3133",
                        "secondary-fixed-dim": "#b8c8da",
                        "error": "#ba1a1a",
                        "tertiary": "#2d0700",
                        "on-surface-variant": "#444650",
                        "on-secondary-fixed-variant": "#394857",
                        "surface-container": "#eceef0",
                        "outline": "#757682",
                        "secondary-fixed": "#d4e4f6",
                        "primary-fixed-dim": "#b3c5ff",
                        "on-background": "#191c1e",
                        "primary-container": "#002366",
                        "on-tertiary-fixed-variant": "#783018",
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
                        "tertiary-fixed-dim": "#ffb59e"
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
        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-surface-bright font-body text-on-surface min-h-screen flex flex-col">

<main class="flex-grow flex items-center justify-center p-4 sm:p-6 relative overflow-hidden">
    <!-- Elementos decorativos de fondo -->
    <div class="absolute top-[-10%] right-[-5%] w-[60vw] h-[60vw] sm:w-[40vw] sm:h-[40vw] rounded-full bg-secondary-container/30 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-[-10%] left-[-5%] w-[50vw] h-[50vw] sm:w-[30vw] sm:h-[30vw] rounded-full bg-primary-container/10 blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-[440px] z-10">
        <!-- Identidad de marca -->
        <div class="text-center mb-6 sm:mb-10">
            <h1 class="font-headline text-2xl sm:text-3xl font-extrabold text-primary tracking-tight mb-2">Vanessa Event Planner</h1>
            <p class="font-body text-secondary text-xs sm:text-sm">Gestión de Eventos de Élite en Quillacollo</p>
        </div>

        <!-- Tarjeta de Login -->
        <div class="bg-surface-container-lowest rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-5 sm:p-8 md:p-10 border border-outline-variant/10">
            <div class="mb-6 sm:mb-8">
                <h2 class="font-headline text-lg sm:text-xl font-bold text-on-surface mb-1">Bienvenido de nuevo</h2>
                <p class="text-on-surface-variant text-xs sm:text-sm">Ingresa tus credenciales para acceder al panel.</p>
            </div>

            {{-- Alerta de error de autenticación --}}
            @if ($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-error-container text-on-error-container text-sm rounded-xl px-4 py-3">
                    <span class="material-symbols-outlined text-[18px] mt-0.5 flex-shrink-0">error</span>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5 sm:space-y-6">
                @csrf

                <!-- Selector de rol (visual) -->
                <div class="grid grid-cols-2 gap-2 p-1 bg-surface-container-low rounded-xl mb-4" id="role-selector">
                    <button
                        type="button"
                        data-role="admin"
                        class="role-btn active-role flex items-center justify-center gap-1.5 sm:gap-2 py-2 text-[10px] sm:text-xs font-semibold rounded-lg bg-surface-container-lowest text-primary shadow-sm border border-outline-variant/20 transition-all">
                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">admin_panel_settings</span>
                        <span class="truncate">Administrador</span>
                    </button>
                    <button
                        type="button"
                        data-role="employee"
                        class="role-btn flex items-center justify-center gap-1.5 sm:gap-2 py-2 text-[10px] sm:text-xs font-semibold rounded-lg text-on-secondary-container hover:bg-surface-container-lowest transition-all">
                        <span class="material-symbols-outlined text-[16px] sm:text-[18px]">badge</span>
                        <span class="truncate">Empleado</span>
                    </button>
                </div>

                <!-- Campo Email -->
                <div class="space-y-2">
                    <label class="font-label text-[10px] sm:text-xs font-bold text-on-surface-variant uppercase tracking-wider ml-1" for="email">
                        Correo Electrónico
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline text-[20px] group-focus-within:text-primary transition-colors">mail</span>
                        </div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="off"
                            placeholder="ej: usuario@vanessa.com"
                            class="w-full pl-11 pr-4 py-3 sm:py-3.5 bg-surface-container-low border-transparent rounded-xl text-on-surface font-body text-sm focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder:text-outline/60 @error('email') ring-2 ring-error/40 @enderror"/>
                    </div>
                </div>

                <!-- Campo Contraseña -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="font-label text-[10px] sm:text-xs font-bold text-on-surface-variant uppercase tracking-wider" for="password">
                            Contraseña
                        </label>
                        <a class="text-[10px] sm:text-[11px] font-semibold text-primary hover:underline transition-all" href="#">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline text-[20px] group-focus-within:text-primary transition-colors">lock</span>
                        </div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="w-full pl-11 pr-12 py-3 sm:py-3.5 bg-surface-container-low border-transparent rounded-xl text-on-surface font-body text-sm focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder:text-outline/60"/>
                        <button
                            type="button"
                            id="toggle-password"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-outline hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]" id="eye-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Botón Iniciar Sesión -->
                <button
                    type="submit"
                    class="w-full bg-primary text-on-primary font-headline font-bold py-3.5 sm:py-4 rounded-xl shadow-md hover:bg-primary-container hover:shadow-lg active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2 mt-2 sm:mt-4 text-sm sm:text-base">
                    <span>Iniciar Sesión</span>
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>

                <div class="relative flex items-center py-3 sm:py-4">
                    <div class="flex-grow border-t border-outline-variant/20"></div>
                    <span class="flex-shrink mx-4 text-[10px] font-bold text-outline uppercase tracking-widest">o</span>
                    <div class="flex-grow border-t border-outline-variant/20"></div>
                </div>

                <button
                    type="button"
                    class="w-full flex items-center justify-center gap-3 py-3.5 sm:py-4 border border-outline-variant/50 rounded-xl bg-surface-container-lowest text-on-surface font-headline font-bold hover:bg-surface-container-low active:scale-[0.98] transition-all duration-300 text-sm sm:text-base">
                    <svg height="20" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.66l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span>Continuar con Google</span>
                </button>
            </form>

            <div class="mt-8 sm:mt-10 pt-5 sm:pt-6 border-t border-outline-variant/10">
                <p class="text-center text-[11px] sm:text-xs text-on-surface-variant">
                    ¿Problemas para acceder?
                    <a class="text-primary font-bold hover:underline" href="#">Contactar Soporte Técnico</a>
                </p>
            </div>
        </div>

        <!-- Info contextual -->
        <div class="mt-6 sm:mt-8 flex flex-wrap justify-center gap-4 sm:gap-6 opacity-60">
            <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-medium text-secondary">
                <span class="material-symbols-outlined text-[16px]">verified_user</span>
                Conexión Segura
            </div>
            <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-medium text-secondary">
                <span class="material-symbols-outlined text-[16px]">cloud_done</span>
                Sincronización en Tiempo Real
            </div>
        </div>
    </div>
</main>

<footer class="w-full py-5 sm:py-8 mt-auto flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 md:px-12 gap-3 sm:gap-4 border-t border-slate-100/50">
    <p class="font-inter text-[10px] sm:text-[11px] text-slate-400 text-center sm:text-left">
        © 2025 Vanessa Event Planner. Quillacollo, Bolivia.
    </p>
    <div class="flex gap-4 sm:gap-6">
        <a class="font-inter text-[10px] sm:text-[11px] text-slate-400 hover:text-primary transition-opacity" href="#">Soporte</a>
        <a class="font-inter text-[10px] sm:text-[11px] text-slate-400 hover:text-primary transition-opacity" href="#">Términos</a>
        <a class="font-inter text-[10px] sm:text-[11px] text-slate-400 hover:text-primary transition-opacity" href="#">Privacidad</a>
    </div>
</footer>

<script>
    // Toggle visibilidad de contraseña
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
    });

    // Selector de rol (visual, sin efecto en la autenticación)
    const roleBtns = document.querySelectorAll('.role-btn');
    roleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            roleBtns.forEach(b => {
                b.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm', 'border', 'border-outline-variant/20');
                b.classList.add('text-on-secondary-container');
            });
            btn.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm', 'border', 'border-outline-variant/20');
            btn.classList.remove('text-on-secondary-container');
        });
    });
</script>
</body>
</html>
