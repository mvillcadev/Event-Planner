@extends('layouts.app')

@section('title', 'Retorno y Control de Siniestros - ' . ($reserva->descripcion ?? 'Reserva #' . $reserva->id))

@section('content')
<div class="p-8 max-w-6xl mx-auto">

    {{-- Cabecera --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex text-xs font-medium text-secondary mb-2 gap-2">
                <a href="{{ route('retornos.index') }}" class="hover:text-primary transition-colors">Almacén</a>
                <span>/</span>
                <span class="text-primary">Inspección Post-Evento</span>
            </nav>
            <h1 class="text-3xl font-bold text-primary tracking-tight font-headline">Retorno de Materiales</h1>
            <p class="text-secondary mt-1">
                Evento: <strong>{{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}</strong>
                · {{ $reserva->cliente->nombre_completo ?? '—' }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('retornos.index') }}"
               class="px-5 py-2.5 rounded-xl border border-outline-variant text-primary font-semibold hover:bg-surface-container transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                Volver
            </a>
        </div>
    </div>

    {{-- Alerta informativa --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 mb-6">
        <span class="material-symbols-outlined text-amber-600 mt-0.5">info</span>
        <p class="text-sm text-amber-800">
            Registra el estado de cada artículo devuelto. Los artículos dañados descontarán del inventario permanentemente
            y el costo de reposición se sumará al monto de la reserva.
            <strong class="block mt-1">Esta acción cambiará el estado de la reserva a "Finalizada" y no puede deshacerse.</strong>
        </p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-red-700 text-sm space-y-1 mb-6">
        @foreach($errors->all() as $error)
        <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    {{-- Tarjetas de resumen --}}
    @php $totalItems = $reserva->detalles->sum('cantidad'); @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-slate-50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-secondary font-medium text-sm">Items Totales</span>
                <span class="material-symbols-outlined text-primary">inventory</span>
            </div>
            <p class="text-2xl font-bold text-primary">{{ $totalItems }}</p>
            <p class="text-xs text-secondary mt-2">Distribuidos en {{ $reserva->detalles->count() }} artículos</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-slate-50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-secondary font-medium text-sm">Reporte de Daños</span>
                <span class="material-symbols-outlined text-error">warning</span>
            </div>
            <p class="text-2xl font-bold text-error" id="card-danos-count">0 Items</p>
            <p class="text-xs text-secondary mt-2">Requieren reposición o reparación</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-slate-50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-secondary font-medium text-sm">Costo de Siniestro</span>
                <span class="material-symbols-outlined text-primary">payments</span>
            </div>
            <p class="text-2xl font-bold text-primary" id="card-costo-total">Bs. 0.00</p>
            <p class="text-xs text-secondary mt-2">Monto pendiente de cobro al cliente</p>
        </div>
    </div>

    {{-- Formulario --}}
    <form method="POST" action="{{ route('retornos.store', $reserva) }}">
        @csrf

        {{-- Tabla de inspección --}}
        <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-slate-50">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-primary">Formulario de Inspección de Inventario</h3>
                <span class="px-3 py-1 bg-secondary-container text-on-secondary-container rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('d/m/Y') }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-secondary text-xs font-bold uppercase tracking-wider">
                            <th class="px-6 py-4">Artículo</th>
                            <th class="px-6 py-4 text-center">Enviado</th>
                            <th class="px-6 py-4 text-center">Cantidad OK</th>
                            <th class="px-6 py-4 text-center">Dañado / Perdido</th>
                            <th class="px-6 py-4">Observaciones del Daño</th>
                            <th class="px-6 py-4 text-right">Reposición (Bs.)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($reserva->detalles as $index => $detalle)
                        @php
                            $articulo   = $detalle->articulo;
                            $yaHayDano  = $detalle->reportesDanos->isNotEmpty();
                            $oldDanada  = (int) old('danos.' . $index . '.cantidad_danada', 0);
                            $isDamaged  = $oldDanada > 0 || $yaHayDano;
                        @endphp

                        <input type="hidden" name="danos[{{ $index }}][detalle_reserva_id]" value="{{ $detalle->id }}">
                        <input type="hidden" name="danos[{{ $index }}][articulo_id]"        value="{{ $detalle->articulo_id }}">

                        <tr class="{{ $isDamaged ? 'bg-red-50/50 hover:bg-red-50' : 'hover:bg-slate-50' }} transition-colors"
                            id="row-{{ $index }}">

                            {{-- Artículo --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg {{ $isDamaged ? 'bg-red-100' : 'bg-slate-100' }} overflow-hidden flex-shrink-0 flex items-center justify-center"
                                         id="thumb-{{ $index }}">
                                        @if($articulo && $articulo->ruta_imagen)
                                            <img src="{{ asset('storage/' . $articulo->ruta_imagen) }}"
                                                 alt="{{ $articulo->nombre }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-secondary text-sm">inventory_2</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-primary">{{ $articulo->nombre ?? '—' }}</p>
                                        <p class="text-xs text-secondary">{{ $articulo->categoria->nombre ?? '' }}</p>
                                        @if($yaHayDano)
                                        <span class="text-[10px] bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">Ya tiene reporte</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Enviado --}}
                            <td class="px-6 py-4 text-center font-medium">{{ $detalle->cantidad }}</td>

                            {{-- Cantidad OK --}}
                            <td class="px-6 py-4 text-center">
                                <input type="number" readonly
                                       id="ok-{{ $index }}"
                                       value="{{ $detalle->cantidad - $oldDanada }}"
                                       class="w-20 rounded-lg text-center text-sm {{ $isDamaged ? 'bg-white border border-red-100 text-red-700 font-bold' : 'bg-surface-container-low border-none' }}">
                            </td>

                            {{-- Dañado / Perdido --}}
                            <td class="px-6 py-4 text-center">
                                <input type="number"
                                       name="danos[{{ $index }}][cantidad_danada]"
                                       id="danada-{{ $index }}"
                                       min="0" max="{{ $detalle->cantidad }}"
                                       value="{{ $oldDanada }}"
                                       onchange="actualizarFila({{ $index }}, {{ $detalle->cantidad }})"
                                       class="w-20 rounded-lg text-center text-sm {{ $isDamaged ? 'bg-white border border-red-200 text-red-700 font-bold' : 'bg-surface-container-low border-none' }} focus:ring-error focus:border-error">
                            </td>

                            {{-- Observaciones --}}
                            <td class="px-6 py-4">
                                <input type="text"
                                       name="danos[{{ $index }}][descripcion]"
                                       id="desc-{{ $index }}"
                                       value="{{ old('danos.' . $index . '.descripcion') }}"
                                       placeholder="Sin observaciones"
                                       class="w-full rounded-lg text-sm px-3 py-1 {{ $isDamaged ? 'bg-white/50 border border-red-100 text-red-700 focus:ring-error' : 'bg-transparent border-none italic text-slate-400 placeholder-slate-300 focus:ring-0' }}">
                            </td>

                            {{-- Reposición --}}
                            <td class="px-6 py-4 text-right">
                                <input type="number" step="0.01" min="0"
                                       name="danos[{{ $index }}][costo_reposicion]"
                                       id="costo-{{ $index }}"
                                       value="{{ old('danos.' . $index . '.costo_reposicion', 0) }}"
                                       onchange="actualizarTotales()"
                                       class="w-28 rounded-lg text-right text-sm px-3 py-1 {{ $isDamaged ? 'bg-white border border-red-100 text-red-700 font-bold' : 'bg-surface-container-low border-none' }} focus:ring-primary">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer de la tabla --}}
            <div class="p-6 bg-surface-container-low/50 border-t border-slate-100">
                <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="flex items-center gap-4 text-sm text-secondary">
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-white border border-slate-300 inline-block"></span>
                            Buen Estado
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-100 inline-block"></span>
                            Dañado / Perdido
                        </span>
                    </div>
                    <div class="text-right space-y-1">
                        <div class="flex items-center justify-end gap-10">
                            <span class="text-secondary font-medium text-sm">Monto original de reserva:</span>
                            <span class="font-semibold text-secondary">Bs. {{ number_format($reserva->monto_total, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-end gap-10">
                            <span class="text-secondary font-medium text-sm">Subtotal daños:</span>
                            <span class="text-xl font-bold text-error" id="total-danos">Bs. 0.00</span>
                        </div>
                        <div class="flex items-center justify-end gap-10 pt-1 border-t border-slate-200">
                            <span class="text-secondary font-semibold">Nuevo total estimado:</span>
                            <span class="text-xl font-bold text-primary" id="nuevo-total">Bs. {{ number_format($reserva->monto_total, 2) }}</span>
                        </div>
                        <p class="text-xs text-secondary">El costo se agregará al saldo pendiente del cliente.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones de acción --}}
        <div class="mt-8 flex justify-end gap-4">
            <a href="{{ route('retornos.index') }}"
               class="px-8 py-3 rounded-xl border border-primary text-primary font-bold hover:bg-slate-50 transition-all">
                Cancelar
            </a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl bg-primary text-on-primary font-bold shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2"
                    onclick="return confirm('¿Confirmar el retorno? Esta acción finalizará la reserva y es irreversible.')">
                <span class="material-symbols-outlined">save_as</span>
                Procesar Devolución y Ajustar Stock
            </button>
        </div>
    </form>
</div>

<script>
    const montoOriginal = {{ $reserva->monto_total }};

    function actualizarFila(index, total) {
        const danada      = parseInt(document.getElementById('danada-' + index).value) || 0;
        const ok          = Math.max(0, total - danada);
        const row         = document.getElementById('row-' + index);
        const okInput     = document.getElementById('ok-' + index);
        const danadaInput = document.getElementById('danada-' + index);
        const descInput   = document.getElementById('desc-' + index);
        const costoInput  = document.getElementById('costo-' + index);
        const thumb       = document.getElementById('thumb-' + index);

        okInput.value = ok;

        if (danada > 0) {
            row.className        = 'bg-red-50/50 hover:bg-red-50 transition-colors';
            thumb.className      = 'w-10 h-10 rounded-lg bg-red-100 overflow-hidden flex-shrink-0 flex items-center justify-center';
            okInput.className    = 'w-20 rounded-lg text-center text-sm bg-white border border-red-100 text-red-700 font-bold';
            danadaInput.className = 'w-20 rounded-lg text-center text-sm bg-white border border-red-200 text-red-700 font-bold focus:ring-error focus:border-error';
            descInput.className  = 'w-full rounded-lg text-sm px-3 py-1 bg-white/50 border border-red-100 text-red-700 focus:ring-error';
            costoInput.className = 'w-28 rounded-lg text-right text-sm px-3 py-1 bg-white border border-red-100 text-red-700 font-bold focus:ring-primary';
        } else {
            row.className        = 'hover:bg-slate-50 transition-colors';
            thumb.className      = 'w-10 h-10 rounded-lg bg-slate-100 overflow-hidden flex-shrink-0 flex items-center justify-center';
            okInput.className    = 'w-20 rounded-lg text-center text-sm bg-surface-container-low border-none';
            danadaInput.className = 'w-20 rounded-lg text-center text-sm bg-surface-container-low border-none focus:ring-error focus:border-error';
            descInput.className  = 'w-full rounded-lg text-sm px-3 py-1 bg-transparent border-none italic text-slate-400 placeholder-slate-300 focus:ring-0';
            costoInput.className = 'w-28 rounded-lg text-right text-sm px-3 py-1 bg-surface-container-low border-none focus:ring-primary';
        }

        actualizarTotales();
    }

    function actualizarTotales() {
        let totalDanos   = 0;
        let itemsDanados = 0;

        document.querySelectorAll('[id^="costo-"]').forEach(input => {
            totalDanos += parseFloat(input.value) || 0;
        });

        document.querySelectorAll('[id^="danada-"]').forEach(input => {
            if ((parseInt(input.value) || 0) > 0) itemsDanados++;
        });

        document.getElementById('total-danos').textContent    = 'Bs. ' + totalDanos.toFixed(2);
        document.getElementById('nuevo-total').textContent    = 'Bs. ' + (montoOriginal + totalDanos).toFixed(2);
        document.getElementById('card-costo-total').textContent = 'Bs. ' + totalDanos.toFixed(2);
        document.getElementById('card-danos-count').textContent = itemsDanados + ' Items';
    }
</script>
@endsection
