@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-8 sm:space-y-12">

    {{-- Alerta de retornos pendientes --}}
    @if(isset($retornosPendientes) && $retornosPendientes->isNotEmpty())
    <div class="bg-amber-50 border border-amber-300 text-amber-900 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-200/90 flex items-center justify-center text-amber-900 shrink-0">
                <span class="material-symbols-outlined text-2xl">assignment_late</span>
            </div>
            <div>
                <p class="font-bold text-sm">Alerta Logística: {{ $retornosPendientes->count() }} reserva(s) con fecha cumplida pendientes de retorno</p>
                <p class="text-xs text-amber-800 mt-0.5">Realiza la recepción de activos en depósito y audita posibles roturas o piezas faltantes.</p>
            </div>
        </div>
        <a href="{{ route('retornos.index') }}" class="px-5 py-2.5 bg-amber-900 text-white font-bold text-xs rounded-xl hover:bg-amber-950 transition-colors shrink-0 shadow-sm">
            Auditar Retornos
        </a>
    </div>
    @endif

    {{-- Encabezado del resumen --}}
    <section>
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6 sm:mb-8">
            <div>
                <span class="text-xs sm:text-sm font-bold text-on-secondary-fixed-variant uppercase tracking-widest font-label">Dashboard Global</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-primary mt-1 font-headline">Resumen Ejecutivo</h2>
            </div>
            <div class="text-xs sm:text-sm text-secondary font-body">
                Actualizado: {{ now()->locale('es')->isoFormat('D MMM YYYY, HH:mm') }}
            </div>
        </div>

        {{-- Tarjetas de resumen (4 Columnas) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">

            {{-- Artículos en Almacén --}}
            <div class="bg-surface-container-lowest p-6 rounded-2xl relative overflow-hidden group shadow-sm border border-slate-100">
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-secondary-container flex items-center justify-center text-on-secondary-container flex-shrink-0">
                            <span class="material-symbols-outlined">warehouse</span>
                        </div>
                        <span class="font-label text-xs font-semibold text-secondary uppercase tracking-wider">Total en Almacén</span>
                    </div>
                    <div>
                        <span class="text-3xl font-extrabold text-primary font-headline">{{ number_format($totalStock) }}</span>
                        <span class="text-xs font-medium text-secondary block mt-0.5">unidades registradas</span>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center text-xs text-primary font-bold">
                        <a href="{{ route('articles.index') }}" class="hover:underline flex items-center gap-1">
                            <span>Ver catálogo</span>
                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Reservas Activas --}}
            <div class="bg-primary p-6 rounded-2xl relative overflow-hidden group shadow-xl shadow-primary/20 text-white">
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white flex-shrink-0">
                            <span class="material-symbols-outlined">event_available</span>
                        </div>
                        <span class="font-label text-xs font-semibold text-primary-fixed-dim uppercase tracking-wider">Reservas Activas</span>
                    </div>
                    <div>
                        <span class="text-3xl font-extrabold text-white font-headline">{{ $reservasActivas }}</span>
                        <span class="text-xs font-medium text-primary-fixed-dim block mt-0.5">en agenda y en curso</span>
                    </div>
                    <div class="pt-3 border-t border-white/10 flex items-center text-xs font-bold">
                        <a href="{{ route('reservas.index') }}" class="text-white hover:underline flex items-center gap-1">
                            <span>Ver reservas</span>
                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Ingresos del Mes (Bs.) --}}
            <div class="bg-surface-container-lowest p-6 rounded-2xl relative overflow-hidden group shadow-sm border border-slate-100">
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-800 flex-shrink-0">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                        <span class="font-label text-xs font-semibold text-secondary uppercase tracking-wider">Ingresos {{ ucfirst(now()->locale('es')->isoFormat('MMM')) }}</span>
                    </div>
                    <div>
                        <span class="text-2xl sm:text-3xl font-extrabold text-emerald-700 font-headline">Bs. {{ number_format($ingresosMes ?? 0, 2) }}</span>
                        <span class="text-xs font-medium text-secondary block mt-0.5">cobrados este mes</span>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center text-xs text-primary font-bold">
                        <a href="{{ route('pagos.index') }}" class="hover:underline flex items-center gap-1">
                            <span>Control financiero</span>
                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- En Mantenimiento --}}
            <div class="bg-surface-container-lowest p-6 rounded-2xl relative overflow-hidden group shadow-sm border border-slate-100">
                <div class="flex flex-col gap-4 relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-800 flex-shrink-0">
                            <span class="material-symbols-outlined">build_circle</span>
                        </div>
                        <span class="font-label text-xs font-semibold text-secondary uppercase tracking-wider">Mantenimiento</span>
                    </div>
                    <div>
                        <span class="text-3xl font-extrabold text-primary font-headline">{{ $enMantenimiento }}</span>
                        <span class="text-xs font-medium text-secondary block mt-0.5">artículos no disponibles</span>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center text-xs {{ $enMantenimiento > 0 ? 'text-amber-700 font-bold' : 'text-slate-400' }}">
                        <span>{{ $enMantenimiento > 0 ? 'Requiere revisión' : 'Todo operativo' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Tabla: Próximas Reservas --}}
    <section class="bg-surface-container-lowest rounded-2xl p-4 sm:p-6 lg:p-8 shadow-sm border border-slate-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
            <div>
                <h3 class="text-lg sm:text-xl font-bold text-primary font-headline">Próximos Eventos en Agenda</h3>
                <p class="text-xs text-secondary mt-0.5">Reservas pendientes, confirmadas y en curso</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reservas.create') }}" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-xs font-semibold hover:bg-primary-container transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-sm">add</span>
                    <span>Nueva Reserva</span>
                </a>
            </div>
        </div>

        @if($proximasReservas->isEmpty())
            <div class="text-center py-12 text-secondary">
                <span class="material-symbols-outlined text-5xl mb-3 block opacity-40">event_busy</span>
                <p class="font-semibold">No hay reservas próximas registradas.</p>
            </div>
        @else
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-surface-container-low">
                            <th class="pb-4 pt-2 px-4 sm:px-0 font-label text-xs font-bold text-secondary uppercase tracking-wider">Fecha Inicio</th>
                            <th class="pb-4 pt-2 font-label text-xs font-bold text-secondary uppercase tracking-wider">Cliente</th>
                            <th class="pb-4 pt-2 font-label text-xs font-bold text-secondary uppercase tracking-wider hidden md:table-cell">Monto Total</th>
                            <th class="pb-4 pt-2 font-label text-xs font-bold text-secondary uppercase tracking-wider">Estado</th>
                            <th class="pb-4 pt-2 font-label text-xs font-bold text-secondary uppercase tracking-wider text-right pr-4 sm:pr-0">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="font-body text-sm divide-y divide-surface-container-low">
                        @foreach ($proximasReservas as $reserva)
                        @php
                            $iniciales = collect(explode(' ', $reserva->cliente->nombre_completo))
                                ->take(2)->map(fn($p) => strtoupper(substr($p,0,1)))->join('');
                            $colores = ['bg-secondary-fixed', 'bg-primary-fixed', 'bg-tertiary-fixed', 'bg-secondary-fixed-dim'];
                            $color   = $colores[$loop->index % count($colores)];
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-4 sm:px-0 font-semibold text-primary whitespace-nowrap">
                                {{ $reserva->fecha_inicio->locale('es')->isoFormat('D MMM, YYYY') }}
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $color }} flex items-center justify-center text-on-secondary-fixed text-[10px] font-bold flex-shrink-0">
                                        {{ $iniciales }}
                                    </div>
                                    <span class="truncate max-w-[140px]">{{ $reserva->cliente->nombre_completo }}</span>
                                </div>
                            </td>
                            <td class="py-4 text-secondary hidden md:table-cell">
                                Bs. {{ number_format($reserva->monto_total, 2) }}
                            </td>
                            <td class="py-4">
                                @if($reserva->estado === 'confirmada')
                                    <span class="px-3 py-1 bg-secondary-container text-on-secondary-container rounded-full text-[11px] font-bold uppercase">Confirmada</span>
                                @elseif($reserva->estado === 'pendiente')
                                    <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant rounded-full text-[11px] font-bold uppercase">Pendiente</span>
                                @else
                                    <span class="px-3 py-1 bg-error-container text-on-error-container rounded-full text-[11px] font-bold uppercase">{{ ucfirst($reserva->estado) }}</span>
                                @endif
                            </td>
                            <td class="py-4 text-right pr-4 sm:pr-0">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('pagos.show', $reserva) }}"
                                       title="Control de Pagos"
                                       class="p-1.5 text-secondary hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">payments</span>
                                    </a>
                                    <a href="{{ route('reservas.despacho', $reserva) }}"
                                       title="Hoja de Despacho"
                                       class="p-1.5 text-secondary hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">local_shipping</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

</div>
@endsection
