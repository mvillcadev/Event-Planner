@extends('layouts.employee')

@section('title', 'Dashboard')

@section('content')

{{-- Alerta de retornos atrasados --}}
@if(isset($retornosAtrasados) && $retornosAtrasados->isNotEmpty())
<div class="px-4 sm:px-6 pt-6">
    <div class="bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-200/90 flex items-center justify-center text-amber-900 shrink-0">
                <span class="material-symbols-outlined">assignment_late</span>
            </div>
            <div>
                <p class="font-bold text-sm">⚠️ {{ $retornosAtrasados->count() }} reserva(s) con retorno pendiente</p>
                <p class="text-xs text-amber-800 mt-0.5">La fecha de devolución ya venció. Comunica al administrador para procesar los retornos.</p>
            </div>
        </div>
        <a href="{{ route('reservas.index') }}" class="px-4 py-2 bg-amber-900 text-white font-bold text-xs rounded-xl hover:bg-amber-950 transition-colors shrink-0">
            Ver Reservas
        </a>
    </div>
</div>
@endif

{{-- Barra de búsqueda y acción --}}
<section class="px-4 sm:px-6 py-6 sm:py-8">
    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-center justify-between">
        <div class="relative flex-1 sm:max-w-xl">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">search</span>
            <input id="buscador"
                   type="text"
                   placeholder="Buscar artículos por nombre..."
                   autocomplete="off"
                   class="w-full pl-12 pr-4 py-3.5 bg-white border-none rounded-2xl shadow-sm focus:ring-2 focus:ring-primary/20 transition-all text-sm placeholder:text-slate-400"/>
        </div>
        <a href="{{ route('reservas.create') }}"
           class="flex items-center justify-center gap-2 bg-primary text-white px-6 py-3.5 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary-container hover:scale-[1.02] active:scale-95 transition-all whitespace-nowrap">
            <span class="material-symbols-outlined">add</span>
            Nueva Reserva
        </a>
    </div>
</section>

{{-- Cuadrícula: catálogo + panel lateral --}}
<div class="px-4 sm:px-6 pb-8 sm:pb-12 flex flex-col xl:flex-row gap-6 xl:gap-8">

    {{-- ══ COLUMNA IZQUIERDA: Catálogo ══ --}}
    <div class="flex-1 space-y-5 sm:space-y-6 min-w-0">

        {{-- Pills de categorías --}}
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
            <button
                data-categoria=""
                class="pill-btn px-5 py-2 bg-primary text-on-primary rounded-full text-sm font-semibold whitespace-nowrap transition-colors">
                Todos
            </button>
            @foreach($categorias as $cat)
            <button
                data-categoria="{{ $cat->id }}"
                class="pill-btn px-5 py-2 bg-surface-container-low text-secondary hover:bg-surface-container-high rounded-full text-sm font-semibold whitespace-nowrap transition-colors">
                {{ $cat->nombre }}
            </button>
            @endforeach
        </div>

        {{-- Contador de resultados --}}
        <div class="flex items-center justify-between">
            <p class="text-xs text-secondary font-medium">
                Mostrando <span id="contador-articulos" class="font-bold text-on-surface">{{ $articulos->count() }}</span> artículos
            </p>
        </div>

        {{-- Grid de artículos --}}
        <div id="articulos-grid"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @include('employee.partials.articulos-grid', ['articulos' => $articulos])
        </div>

        {{-- Skeleton de carga --}}
        <div id="skeleton-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @for($i = 0; $i < 6; $i++)
            <div class="bg-white rounded-xl p-4 animate-pulse">
                <div class="aspect-square w-full rounded-lg bg-slate-100 mb-4"></div>
                <div class="h-4 bg-slate-100 rounded-lg mb-2 w-3/4"></div>
                <div class="h-3 bg-slate-100 rounded-lg w-1/2"></div>
            </div>
            @endfor
        </div>
    </div>

    {{-- ══ COLUMNA DERECHA: Panel lateral ══ --}}
    <aside class="w-full xl:w-72 2xl:w-80 space-y-5 sm:space-y-6 flex-shrink-0">

        {{-- Tarjeta de estado del inventario --}}
        @php
            $totalArticulos   = \App\Models\Article::count();
            $disponibles      = \App\Models\Article::where('estado', 'disponible')->count();
            $porcentaje       = $totalArticulos > 0 ? round(($disponibles / $totalArticulos) * 100) : 0;
        @endphp
        <div class="bg-primary text-white p-5 sm:p-6 rounded-2xl shadow-xl overflow-hidden relative">
            <div class="relative z-10">
                <h4 class="text-sm font-semibold opacity-80 mb-4 font-headline">Estado del Inventario</h4>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-3xl font-bold font-headline">{{ $porcentaje }}%</span>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-lg font-semibold">Disponible</span>
                </div>
                <div class="w-full bg-white/20 h-2 rounded-full mb-4">
                    <div class="bg-white h-full rounded-full transition-all duration-700"
                         style="width: {{ $porcentaje }}%"></div>
                </div>
                <div class="flex justify-between text-[11px] opacity-70">
                    <span>{{ $disponibles }} disponibles</span>
                    <span>{{ $totalArticulos - $disponibles }} en mantenimiento</span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        </div>

        {{-- Entregas del día --}}
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-5">
                <h4 class="text-sm font-bold text-slate-900 font-headline">Entregas para Hoy</h4>
                <span class="text-[10px] font-bold text-primary uppercase tracking-wider">
                    {{ now()->locale('es')->isoFormat('D MMM') }}
                </span>
            </div>

            @if($entregasHoy->isEmpty())
                <div class="text-center py-6 text-secondary">
                    <span class="material-symbols-outlined text-3xl mb-2 block opacity-40">event_available</span>
                    <p class="text-xs font-medium">Sin entregas programadas para hoy.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($entregasHoy as $entrega)
                    <div class="flex gap-3 group cursor-pointer">
                        <div class="flex flex-col items-center flex-shrink-0">
                            <span class="text-xs font-bold text-slate-900">
                                {{ $entrega->fecha_inicio->format('H:i') }}
                            </span>
                            @if(!$loop->last)
                            <div class="w-px flex-1 bg-slate-100 mt-1"></div>
                            @endif
                        </div>
                        <div class="pb-3 min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">
                                {{ $entrega->cliente->nombre_completo }}
                            </p>
                            <p class="text-[10px] text-slate-500 mt-0.5">
                                @if($entrega->estado === 'confirmada')
                                    <span class="text-green-600 font-semibold">Confirmada</span>
                                @else
                                    <span class="text-amber-500 font-semibold">Pendiente</span>
                                @endif
                                · Bs. {{ number_format($entrega->monto_total, 2) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            <a href="{{ route('reservas.index') }}"
               class="block text-center w-full mt-4 py-2.5 bg-slate-50 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition-colors">
                Ver Agenda Completa
            </a>
        </div>

        {{-- Acciones rápidas --}}
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('reservas.index') }}" class="flex flex-col items-center justify-center gap-2 p-4 bg-white rounded-xl shadow-sm border border-slate-100 hover:border-primary/40 hover:shadow-md transition-all">
                <span class="material-symbols-outlined text-blue-900 text-2xl">local_shipping</span>
                <span class="text-[10px] font-bold text-slate-700">Despachos</span>
            </a>
            <a href="{{ route('reservas.index') }}" class="flex flex-col items-center justify-center gap-2 p-4 bg-white rounded-xl shadow-sm border border-slate-100 hover:border-primary/40 hover:shadow-md transition-all">
                <span class="material-symbols-outlined text-blue-900 text-2xl">history</span>
                <span class="text-[10px] font-bold text-slate-700">Historial</span>
            </a>
        </div>

    </aside>
</div>

@endsection

@push('scripts')
<script>
    const csrfToken     = document.querySelector('meta[name="csrf-token"]').content;
    const grid          = document.getElementById('articulos-grid');
    const skeleton      = document.getElementById('skeleton-grid');
    const contador      = document.getElementById('contador-articulos');
    const buscador      = document.getElementById('buscador');
    const pills         = document.querySelectorAll('.pill-btn');
    let   categoriaActiva = '';
    let   busquedaTimer   = null;

    // ── Activar pill ─────────────────────────────────
    function activarPill(btn) {
        pills.forEach(p => {
            p.classList.remove('bg-primary', 'text-on-primary');
            p.classList.add('bg-surface-container-low', 'text-secondary');
        });
        btn.classList.remove('bg-surface-container-low', 'text-secondary');
        btn.classList.add('bg-primary', 'text-on-primary');
    }

    // ── Cargar artículos vía AJAX ─────────────────────
    async function cargarArticulos(categoriaId = '', busqueda = '') {
        grid.classList.add('hidden');
        skeleton.classList.remove('hidden');

        try {
            const params = new URLSearchParams();
            if (categoriaId) params.append('categoria_id', categoriaId);
            if (busqueda)    params.append('busqueda', busqueda);

            const res  = await fetch(`{{ route('employee.catalogo') }}?${params}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();

            grid.innerHTML = data.html;
            contador.textContent = data.total;
        } catch (err) {
            grid.innerHTML = '<p class="col-span-full text-center text-secondary py-8 text-sm">Error al cargar los artículos.</p>';
        } finally {
            skeleton.classList.add('hidden');
            grid.classList.remove('hidden');
        }
    }

    // ── Eventos pills ─────────────────────────────────
    pills.forEach(btn => {
        btn.addEventListener('click', () => {
            categoriaActiva = btn.dataset.categoria;
            activarPill(btn);
            cargarArticulos(categoriaActiva, buscador.value.trim());
        });
    });

    // ── Búsqueda con debounce ─────────────────────────
    buscador.addEventListener('input', () => {
        clearTimeout(busquedaTimer);
        busquedaTimer = setTimeout(() => {
            cargarArticulos(categoriaActiva, buscador.value.trim());
        }, 350);
    });
</script>
@endpush
