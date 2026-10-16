@php($layout = Auth::user()->rol === 'admin' ? 'layouts.app' : 'layouts.employee')
@extends($layout)

@section('title', 'Reservas')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-8">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-primary tracking-tight font-headline">Reservas</h2>
            <p class="text-secondary mt-1 text-sm">Listado de todas las reservas registradas en el sistema.</p>
        </div>
        <a href="{{ route('reservas.create') }}"
           class="flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-semibold text-sm shadow-md hover:bg-primary-container transition-all active:scale-95">
            <span class="material-symbols-outlined text-sm">add</span>
            Nueva Reserva
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/10">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Evento / Cliente</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden md:table-cell">Fechas</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden sm:table-cell">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @forelse($reservas as $reserva)
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-primary text-sm">{{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}</p>
                            <p class="text-xs text-secondary">{{ $reserva->cliente->nombre_completo ?? '—' }}</p>
                            <p class="text-xs text-secondary font-semibold mt-1">Bs. {{ number_format($reserva->monto_total, 2) }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-secondary">
                            {{ $reserva->fecha_inicio->format('d/m/Y') }} – {{ $reserva->fecha_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            @php
                                $badgeStyles = [
                                    'pendiente'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    'confirmada' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'en_curso'   => 'bg-blue-50 text-blue-700 border border-blue-200',
                                    'finalizada' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                    'cancelada'  => 'bg-red-50 text-red-700 border border-red-200',
                                ];
                                $labelNames = [
                                    'pendiente'  => 'Pendiente',
                                    'confirmada' => 'Confirmada',
                                    'en_curso'   => 'En Curso',
                                    'finalizada' => 'Finalizada',
                                    'cancelada'  => 'Cancelada',
                                ];
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $badgeStyles[$reserva->estado] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $labelNames[$reserva->estado] ?? $reserva->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Hoja de despacho --}}
                                <a href="{{ route('reservas.despacho', $reserva) }}"
                                   title="Hoja de Despacho"
                                   class="p-2 text-secondary hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">local_shipping</span>
                                </a>

                                {{-- Despachar (Confirmada -> En Curso) --}}
                                @if($reserva->estado === 'confirmada')
                                <form method="POST" action="{{ route('reservas.despachar', $reserva) }}" onsubmit="return confirm('¿Confirmar salida de artículos de depósito para este evento?');" class="inline">
                                    @csrf
                                    <button type="submit" title="Despachar Artículos (Marcar En Curso)" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">outbox</span>
                                    </button>
                                </form>
                                @endif

                                {{-- Descargar comprobante PDF --}}
                                <a href="{{ route('reservas.comprobante', $reserva) }}"
                                   title="Descargar Comprobante PDF"
                                   class="p-2 text-secondary hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">download</span>
                                </a>

                                @if(Auth::user()->rol === 'admin')
                                {{-- Pagos --}}
                                <a href="{{ route('pagos.show', $reserva) }}"
                                   title="Control de Pagos"
                                   class="p-2 text-secondary hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">payments</span>
                                </a>

                                {{-- Retorno (solo si está confirmada o en curso) --}}
                                @if(in_array($reserva->estado, ['confirmada', 'en_curso']))
                                <a href="{{ route('retornos.create', $reserva) }}"
                                   title="Procesar Retorno de Alquiler"
                                   class="p-2 text-secondary hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-lg">assignment_return</span>
                                </a>
                                @endif
                                @endif

                                {{-- Cancelar (solo si pendiente o confirmada) --}}
                                @if(in_array($reserva->estado, ['pendiente', 'confirmada']))
                                <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}" onsubmit="return confirm('¿Estás seguro de cancelar esta reserva? El stock quedará inmediatamente liberado.');" class="inline">
                                    @csrf
                                    <button type="submit" title="Cancelar Reserva" class="p-2 text-secondary hover:text-error hover:bg-red-50 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">cancel</span>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-12 text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2">event_busy</span>
                            No hay reservas registradas aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservas->hasPages())
        <div class="px-6 py-4 border-t border-outline-variant/10 bg-slate-50/50 flex items-center justify-between">
            <p class="text-xs text-secondary">Mostrando {{ $reservas->firstItem() }}–{{ $reservas->lastItem() }} de {{ $reservas->total() }}</p>
            <div class="flex gap-2 text-xs">
                @if(!$reservas->onFirstPage())
                <a href="{{ $reservas->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-colors font-semibold text-secondary">Anterior</a>
                @endif
                @if($reservas->hasMorePages())
                <a href="{{ $reservas->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-colors font-semibold text-secondary">Siguiente</a>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
