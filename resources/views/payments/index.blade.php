@extends('layouts.app')

@section('title', 'Control de Pagos')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-8">

    <div>
        <h2 class="text-3xl font-extrabold text-primary tracking-tight font-headline">Control de Pagos</h2>
        <p class="text-secondary mt-1 text-sm">Gestiona los pagos y saldos de todas las reservas activas.</p>
    </div>

    {{-- Flash --}}
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
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Progreso Pago</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden sm:table-cell text-right">Saldo</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @forelse($reservas as $reserva)
                    @php
                        $totalPagado    = $reserva->pagos->sum('monto');
                        $saldo          = max(0, $reserva->monto_total - $totalPagado);
                        $porcentaje     = $reserva->monto_total > 0 ? min(100, round(($totalPagado / $reserva->monto_total) * 100)) : 0;
                        $estadoClases   = match($reserva->estado) {
                            'completada' => 'bg-emerald-50 text-emerald-700',
                            'confirmada' => 'bg-blue-50 text-blue-700',
                            default      => 'bg-amber-50 text-amber-700',
                        };
                    @endphp
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-primary text-sm">{{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}</p>
                            <p class="text-xs text-secondary">{{ $reserva->cliente->nombre_completo ?? '—' }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $estadoClases }} uppercase">{{ $reserva->estado }}</span>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-secondary">
                            {{ $reserva->fecha_inicio->format('d/m/Y') }} – {{ $reserva->fecha_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-surface-container-high rounded-full overflow-hidden min-w-[80px]">
                                    <div class="h-full bg-primary rounded-full transition-all" style="width: {{ $porcentaje }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-primary shrink-0">{{ $porcentaje }}%</span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Bs. {{ number_format($totalPagado, 2) }} de Bs. {{ number_format($reserva->monto_total, 2) }}</p>
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell text-right">
                            @if($saldo > 0)
                            <span class="text-sm font-bold text-error">Bs. {{ number_format($saldo, 2) }}</span>
                            @else
                            <span class="text-sm font-bold text-emerald-600">Pagado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('pagos.show', $reserva) }}"
                               class="inline-flex items-center gap-1 bg-primary text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-primary-container transition-all active:scale-95">
                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                                <span class="hidden sm:inline">Ver Detalle</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2">payments</span>
                            No hay reservas registradas.
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
