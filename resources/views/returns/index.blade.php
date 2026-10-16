@extends('layouts.app')

@section('title', 'Retornos y Control de Siniestros')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-8">

    <div>
        <h2 class="text-3xl font-extrabold text-primary tracking-tight font-headline">Retornos y Control de Siniestros</h2>
        <p class="text-secondary mt-1 text-sm">Gestiona la devolución de equipos y el registro de daños post-evento.</p>
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
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden sm:table-cell">Artículos</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @forelse($reservas as $reserva)
                    @php
                        $estadoClases = match($reserva->estado) {
                            'completada' => 'bg-blue-50 text-blue-700',
                            'confirmada' => 'bg-amber-50 text-amber-700',
                            'finalizada' => 'bg-emerald-50 text-emerald-700',
                            default      => 'bg-slate-50 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-primary text-sm">{{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}</p>
                            <p class="text-xs text-secondary">{{ $reserva->cliente->nombre_completo ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-secondary">
                            {{ $reserva->fecha_inicio->format('d/m/Y') }} – {{ $reserva->fecha_fin->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 hidden sm:table-cell">
                            <div class="flex flex-wrap gap-1">
                                @foreach($reserva->detalles->take(3) as $d)
                                <span class="text-[10px] bg-surface-container px-2 py-0.5 rounded-full text-secondary">
                                    {{ $d->articulo->nombre ?? '—' }} ×{{ $d->cantidad }}
                                </span>
                                @endforeach
                                @if($reserva->detalles->count() > 3)
                                <span class="text-[10px] text-secondary">+{{ $reserva->detalles->count() - 3 }} más</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $estadoClases }} uppercase">
                                {{ $reserva->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($reserva->estado !== 'finalizada')
                            <a href="{{ route('retornos.create', $reserva) }}"
                               class="inline-flex items-center gap-1 bg-primary text-white text-xs font-bold px-3 py-2 rounded-lg hover:bg-primary-container transition-all active:scale-95">
                                <span class="material-symbols-outlined text-sm">assignment_return</span>
                                <span class="hidden sm:inline">Procesar Retorno</span>
                            </a>
                            @else
                            <span class="text-xs text-emerald-600 font-semibold flex items-center gap-1 justify-end">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Finalizada
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-slate-400">
                            <span class="material-symbols-outlined text-4xl block mb-2">assignment_return</span>
                            No hay reservas para procesar retorno.
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
