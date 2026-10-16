@extends('layouts.app')

@section('title', 'Reporte de Disponibilidad')

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c5c6d2; border-radius: 10px; }
    .tooltip-container { position: relative; }
    .tooltip-container:hover .tooltip-box { display: block; }
    .tooltip-box { display: none; position: absolute; z-index: 50; bottom: 100%; left: 50%; transform: translateX(-50%); white-space: nowrap; background: #00113a; color: white; font-size: 10px; font-weight: 600; padding: 4px 8px; border-radius: 6px; margin-bottom: 4px; pointer-events: none; }
</style>
@endpush

@section('content')
<div class="p-6 lg:p-8 max-w-full mx-auto space-y-8">

    {{-- Cabecera + Filtros --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-1">
            <p class="text-xs text-secondary font-bold uppercase tracking-wider">Planificación Mensual</p>
            <h3 class="text-3xl font-bold text-primary font-headline">
                {{ ucfirst(\Carbon\Carbon::create($anio, $mes, 1)->locale('es')->isoFormat('MMMM YYYY')) }}
            </h3>
            <p class="text-secondary text-sm">Visualización de ocupación del inventario de eventos.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Navegación de mes --}}
            <div class="flex items-center gap-2 bg-surface-container rounded-xl p-1">
                <a href="{{ route('disponibilidad.index', ['mes' => $mesPrevio->month, 'anio' => $mesPrevio->year]) }}"
                   class="p-2 hover:bg-white rounded-lg transition-colors text-secondary hover:text-primary">
                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                </a>
                <span class="text-sm font-bold text-primary px-2">Hoy</span>
                <a href="{{ route('disponibilidad.index', ['mes' => $mesSiguiente->month, 'anio' => $mesSiguiente->year]) }}"
                   class="p-2 hover:bg-white rounded-lg transition-colors text-secondary hover:text-primary">
                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                </a>
            </div>
            <a href="{{ route('reservas.create') }}"
               class="flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-semibold shadow-md hover:bg-primary-container transition-all active:scale-95 text-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                Nueva Reserva
            </a>
        </div>
    </div>

    {{-- Componente Gantt/Calendario --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden">

        {{-- Leyenda --}}
        <div class="px-6 py-4 border-b border-surface-container flex items-center gap-6 overflow-x-auto">
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-3.5 h-3.5 rounded bg-emerald-500"></div>
                <span class="text-xs font-semibold text-secondary">Disponible (100% libre)</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-3.5 h-3.5 rounded bg-blue-600"></div>
                <span class="text-xs font-semibold text-secondary">Parcialmente Reservado</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-3.5 h-3.5 rounded bg-rose-500"></div>
                <span class="text-xs font-semibold text-secondary">Agotado (0u libres)</span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-3.5 h-3.5 rounded bg-amber-400"></div>
                <span class="text-xs font-semibold text-secondary">Mantenimiento</span>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full border-collapse" style="min-width: {{ 256 + ($diasDelMes * 44) }}px">
                <thead>
                    <tr class="bg-surface-container-low">
                        <th class="sticky left-0 z-20 bg-surface-container-low px-4 py-4 text-left text-xs font-bold text-primary uppercase tracking-widest border-r border-outline-variant/10 w-64 min-w-[256px]">
                            Artículo / Stock Total
                        </th>
                        @for($d = 1; $d <= $diasDelMes; $d++)
                        @php
                            $fecha = \Carbon\Carbon::create($anio, $mes, $d);
                            $esHoy = $fecha->isToday();
                            $esFinde = $fecha->isWeekend();
                        @endphp
                        <th class="px-1 py-4 text-center text-[10px] font-bold border-r border-outline-variant/10 w-11
                            {{ $esHoy ? 'ring-2 ring-inset ring-primary text-primary font-black' : ($esFinde ? 'bg-primary/5 text-primary' : 'text-secondary') }}">
                            {{ str_pad($d, 2, '0', STR_PAD_LEFT) }}
                            <br>
                            <span class="text-[9px] opacity-60">{{ $fecha->locale('es')->isoFormat('dd') }}</span>
                        </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @forelse($articulos as $articulo)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        {{-- Nombre del artículo --}}
                        <td class="sticky left-0 z-10 bg-white group-hover:bg-slate-50 px-4 py-3 border-r border-outline-variant/10 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-secondary text-sm">inventory_2</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-primary truncate max-w-[140px]">{{ $articulo->nombre }}</p>
                                    <p class="text-[10px] text-secondary font-medium">Stock Total: <strong class="text-slate-800">{{ $articulo->stock_total }} u.</strong></p>
                                </div>
                            </div>
                        </td>

                        {{-- Celdas cuantitativas de días --}}
                        @for($d = 1; $d <= $diasDelMes; $d++)
                        @php
                            $diaData         = $calendario[$articulo->id][$d] ?? null;
                            $esHoy           = \Carbon\Carbon::create($anio, $mes, $d)->isToday();
                            $reservado       = $diaData['reservado'] ?? 0;
                            $disponible      = $diaData['disponible'] ?? $articulo->stock_total;
                            $porcentaje      = $diaData['porcentaje'] ?? 0;
                            $esMantenimiento = $diaData['mantenimiento'] ?? false;
                            $reservas        = $diaData['reservas'] ?? [];
                        @endphp
                        <td class="p-0.5 border-r border-outline-variant/5 {{ $esHoy ? 'bg-primary/5' : '' }}">
                            @if($esMantenimiento)
                            <div class="tooltip-container h-8 mx-0.5">
                                <div class="h-full bg-amber-400 text-amber-950 font-bold text-[9px] rounded flex items-center justify-center px-1 cursor-pointer shadow-sm">
                                    MANT
                                </div>
                                <div class="tooltip-box">
                                    <p class="font-bold text-xs">{{ $articulo->nombre }}</p>
                                    <p class="text-xs text-amber-400">Artículo en mantenimiento técnico</p>
                                </div>
                            </div>
                            @elseif($reservado > 0)
                            <div class="tooltip-container h-8 mx-0.5">
                                <div class="h-full {{ $disponible === 0 ? 'bg-rose-500 text-white' : 'bg-blue-600 text-white' }} rounded flex flex-col justify-center items-center px-0.5 cursor-pointer hover:brightness-110 transition-all text-center shadow-sm">
                                    <span class="text-[9px] font-black leading-none">{{ $disponible }}</span>
                                    <span class="text-[7px] uppercase font-bold tracking-tighter opacity-80 leading-none mt-0.5">{{ $disponible === 0 ? 'Agot' : 'lib' }}</span>
                                </div>
                                <div class="tooltip-box">
                                    <p class="font-bold text-xs text-white">{{ $articulo->nombre }} · Día {{ $d }}</p>
                                    <div class="mt-1 space-y-0.5 text-[11px]">
                                        <p class="text-slate-300">Reservados: <strong class="text-white">{{ $reservado }} u.</strong> de {{ $articulo->stock_total }} u. ({{ $porcentaje }}%)</p>
                                        <p class="{{ $disponible === 0 ? 'text-rose-400' : 'text-emerald-400' }} font-bold">Disponibles restantes: {{ $disponible }} u.</p>
                                    </div>
                                    @if(!empty($reservas))
                                    <div class="mt-2 pt-1.5 border-t border-slate-700 space-y-1 text-[10px] text-left">
                                        <p class="font-semibold text-slate-300 uppercase tracking-wider text-[9px]">Eventos en este día:</p>
                                        @foreach($reservas as $rc)
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="truncate text-slate-200">• {{ $rc['evento'] }}</span>
                                            <span class="font-bold text-amber-300 shrink-0">{{ $rc['cantidad'] }} u.</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <div class="tooltip-container h-8 mx-0.5 group/cell">
                                <div class="h-full rounded bg-emerald-50/70 hover:bg-emerald-100 flex items-center justify-center text-[9px] font-medium text-emerald-700/80 transition-colors cursor-pointer">
                                    {{ $articulo->stock_total }}
                                </div>
                                <div class="tooltip-box">
                                    <p class="font-bold text-xs text-white">{{ $articulo->nombre }} · Día {{ $d }}</p>
                                    <p class="text-xs text-emerald-400 font-semibold mt-1">100% Libre: {{ $articulo->stock_total }} unidades disponibles</p>
                                </div>
                            </div>
                            @endif
                        </td>
                        @endfor
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $diasDelMes + 1 }}" class="text-center py-12 text-slate-400">
                            No hay artículos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($articulos->hasPages())
        <div class="px-6 py-4 bg-surface-container-low/30 flex justify-between items-center text-xs font-medium text-secondary">
            <p>Mostrando {{ $articulos->firstItem() }}–{{ $articulos->lastItem() }} de {{ $articulos->total() }} artículos</p>
            <div class="flex gap-2">
                @if(!$articulos->onFirstPage())
                <a href="{{ $articulos->previousPageUrl() }}&mes={{ $mes }}&anio={{ $anio }}"
                   class="px-3 py-1 bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-colors">Anterior</a>
                @endif
                @if($articulos->hasMorePages())
                <a href="{{ $articulos->nextPageUrl() }}&mes={{ $mes }}&anio={{ $anio }}"
                   class="px-3 py-1 bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-colors">Siguiente</a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Estadísticas --}}
    @php
        $sumaOcupacion = 0;
        $totalCeldasValidas = 0;
        foreach($calendario as $dias) {
            foreach($dias as $dData) {
                if(is_array($dData)) {
                    $sumaOcupacion += $dData['porcentaje'];
                    $totalCeldasValidas++;
                }
            }
        }
        $ocupacion = $totalCeldasValidas > 0 ? round($sumaOcupacion / $totalCeldasValidas) : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-primary text-white p-6 rounded-2xl shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-blue-200 text-sm font-semibold uppercase tracking-wider">Ocupación</p>
                <h4 class="text-4xl font-bold mt-2">{{ $ocupacion }}%</h4>
                <p class="text-blue-200 text-xs mt-3">Artículos reservados este mes</p>
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl opacity-10 rotate-12">query_stats</span>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant/10">
            <p class="text-secondary text-sm font-semibold uppercase tracking-wider">Artículos en Inventario</p>
            <h4 class="text-4xl font-bold text-primary mt-2">{{ $articulos->total() }}</h4>
            <p class="text-secondary text-xs mt-3">Total registrados en el sistema</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant/10">
            <p class="text-secondary text-sm font-semibold uppercase tracking-wider">Período Visualizado</p>
            <h4 class="text-xl font-bold text-primary mt-2">
                {{ ucfirst(\Carbon\Carbon::create($anio, $mes, 1)->locale('es')->isoFormat('MMMM')) }}
            </h4>
            <p class="text-secondary text-xs mt-2">{{ $diasDelMes }} días · Año {{ $anio }}</p>
            <a href="{{ route('disponibilidad.index') }}"
               class="inline-block mt-3 text-xs font-semibold text-primary hover:underline">
                Ir a mes actual →
            </a>
        </div>
    </div>
</div>
@endsection
