@extends('layouts.app')

@section('title', 'Control de Pagos - ' . ($reserva->descripcion ?? 'Reserva #' . $reserva->id))

@section('content')
<div class="p-6 lg:p-10 max-w-7xl mx-auto space-y-8">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- ═══ CABECERA: Detalle del evento + Totalizador ═══ --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Detalle del evento --}}
        <div class="md:col-span-2 bg-surface-container-lowest rounded-xl p-6 shadow-sm flex flex-col gap-4">
            <div class="flex justify-between items-start flex-wrap gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-primary font-headline">
                        {{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}
                    </h2>
                    <p class="text-secondary text-sm flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-sm">calendar_month</span>
                        {{ $reserva->fecha_inicio->format('d M, Y') }} – {{ $reserva->fecha_fin->format('d M, Y') }}
                        &nbsp;·&nbsp; {{ $reserva->cliente->nombre_completo ?? '—' }}
                    </p>
                </div>
                @php
                    $estadoClases = match($reserva->estado) {
                        'completada' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'confirmada' => 'bg-blue-50 text-blue-700 border-blue-200',
                        default      => 'bg-amber-50 text-amber-700 border-amber-200',
                    };
                    $estadoLabel = match($reserva->estado) {
                        'completada' => 'Pagado Completo',
                        'confirmada' => 'En Proceso',
                        default      => ucfirst($reserva->estado),
                    };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $estadoClases }} uppercase">{{ $estadoLabel }}</span>
            </div>

            {{-- Artículos de la reserva --}}
            @if($reserva->detalles->isNotEmpty())
            <div class="text-xs text-secondary space-y-1 border-t border-slate-100 pt-3">
                @foreach($reserva->detalles as $detalle)
                <div class="flex justify-between">
                    <span>{{ $detalle->articulo->nombre ?? '—' }} × {{ $detalle->cantidad }}</span>
                    <span class="font-semibold">Bs. {{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Barra de progreso --}}
            <div class="mt-2">
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-semibold text-primary">Estado del Pago</span>
                    <span class="font-bold text-primary">{{ $porcentajePagado }}%</span>
                </div>
                <div class="w-full h-3 bg-surface-container-high rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full transition-all duration-700"
                         style="width: {{ $porcentajePagado }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-1.5 italic">
                    Saldo pendiente: Bs. {{ number_format($saldoPendiente, 2) }} de Bs. {{ number_format($reserva->monto_total, 2) }}
                </p>
            </div>
        </div>

        {{-- Totalizador --}}
        <div class="bg-primary text-white rounded-xl p-6 shadow-xl flex flex-col justify-center relative overflow-hidden">
            <span class="material-symbols-outlined absolute -right-4 -top-4 text-9xl opacity-10">payments</span>
            <p class="text-primary-fixed text-sm font-medium mb-1">Monto Total</p>
            <h3 class="text-4xl font-extrabold mb-4 tracking-tight">Bs. {{ number_format($reserva->monto_total, 2) }}</h3>
            <div class="flex flex-col gap-2 border-t border-white/10 pt-4">
                <div class="flex justify-between text-sm">
                    <span class="opacity-70">Pagado</span>
                    <span class="font-bold">Bs. {{ number_format($totalPagado, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="opacity-70">Saldo</span>
                    <span class="font-bold {{ $saldoPendiente > 0 ? 'text-tertiary-fixed' : '' }}">
                        Bs. {{ number_format($saldoPendiente, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ CUERPO: Historial + Formulario ═══ --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-8">

        {{-- Historial de abonos --}}
        <div class="xl:col-span-2 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h3 class="text-xl font-bold text-primary flex items-center gap-2 font-headline">
                    <span class="material-symbols-outlined">receipt_long</span>
                    Historial de Abonos
                </h3>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('reservas.despacho', $reserva) }}"
                       class="inline-flex items-center gap-1 px-3 py-2 bg-surface-container text-primary text-xs font-bold rounded-lg hover:bg-surface-container-high transition-all">
                        <span class="material-symbols-outlined text-sm">local_shipping</span>
                        Hoja de Despacho
                    </a>
                    <a href="{{ route('reservas.comprobante', $reserva) }}"
                       class="inline-flex items-center gap-1 px-3 py-2 bg-surface-container text-primary text-xs font-bold rounded-lg hover:bg-surface-container-high transition-all">
                        <span class="material-symbols-outlined text-sm">download</span>
                        Descargar PDF
                    </a>
                    @if($reserva->estado !== 'finalizada')
                    <a href="{{ route('retornos.create', $reserva) }}"
                       class="inline-flex items-center gap-1 px-3 py-2 bg-red-50 text-error text-xs font-bold rounded-lg hover:bg-red-100 transition-all">
                        <span class="material-symbols-outlined text-sm">assignment_return</span>
                        Procesar Retorno
                    </a>
                    @endif
                    <a href="{{ route('pagos.index') }}" class="text-primary text-sm font-semibold hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Volver
                    </a>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-on-surface-variant text-xs font-bold uppercase tracking-widest">
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Método</th>
                            <th class="px-6 py-4 hidden sm:table-cell">Nota</th>
                            <th class="px-6 py-4 text-right">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        @forelse($reserva->pagos as $pago)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-4 text-sm font-medium">
                                {{ $pago->fecha_pago->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="material-symbols-outlined text-slate-400 text-lg">
                                        {{ $pago->metodo === 'transferencia' ? 'account_balance' : ($pago->metodo === 'qr' ? 'qr_code_2' : 'payments') }}
                                    </span>
                                    {{ ucfirst($pago->metodo) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-secondary hidden sm:table-cell">
                                {{ $pago->nota ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-right text-primary">
                                Bs. {{ number_format($pago->monto, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-slate-400 text-sm">
                                No hay pagos registrados aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cards de contexto --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-secondary-container/30 p-4 rounded-xl flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-primary shadow-sm shrink-0">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">calendar_today</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase text-secondary opacity-70">Fecha del Evento</p>
                        <p class="text-sm font-bold text-primary">{{ $reserva->fecha_inicio->format('d M, Y') }}</p>
                    </div>
                </div>
                <div class="bg-surface-container-low p-4 rounded-xl flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-secondary shadow-sm shrink-0">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase text-secondary opacity-70">Cliente</p>
                        <p class="text-sm font-bold text-primary">{{ $reserva->cliente->nombre_completo ?? '—' }}</p>
                        <p class="text-xs text-secondary">{{ $reserva->cliente->telefono ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel lateral: abono + liquidar --}}
        <div class="space-y-5">

            {{-- Formulario nuevo abono --}}
            @if($saldoPendiente > 0)
            <div class="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/10">
                <h4 class="text-lg font-bold text-primary mb-4 font-headline">Registrar Nuevo Abono</h4>
                <form method="POST" action="{{ route('pagos.store', $reserva) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Monto a abonar (Bs.)</label>
                        <input type="number" name="monto" step="0.01" min="0.01" max="{{ $saldoPendiente }}"
                               value="{{ old('monto') }}"
                               placeholder="0.00"
                               class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20 p-3 font-bold text-primary outline-none @error('monto') ring-2 ring-error @enderror">
                        @error('monto') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-slate-400 mt-1">Saldo restante: Bs. {{ number_format($saldoPendiente, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Método de Pago</label>
                        <select name="metodo" class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20 p-3 text-sm outline-none appearance-none">
                            <option value="efectivo">Efectivo</option>
                            <option value="qr">Transferencia QR</option>
                            <option value="transferencia">Depósito Bancario</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Nota / Referencia</label>
                        <input type="text" name="nota" value="{{ old('nota') }}"
                               placeholder="Ej: Comprobante 4591"
                               class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary/20 p-3 text-sm outline-none">
                    </div>
                    <button type="submit"
                            class="w-full bg-secondary text-white py-3 rounded-xl font-bold text-sm hover:opacity-95 transition-all active:scale-95">
                        Confirmar Abono
                    </button>
                </form>
            </div>
            @endif

            {{-- Liquidar saldo --}}
            @if($saldoPendiente > 0)
            <div class="bg-white rounded-xl p-6 shadow-md border-2 border-primary ring-4 ring-primary/5 flex flex-col items-center text-center">
                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                    <span class="material-symbols-outlined text-2xl">task_alt</span>
                </div>
                <h4 class="text-lg font-extrabold text-primary mb-2">Liquidación Final</h4>
                <p class="text-sm text-slate-500 mb-5">
                    Procesa el pago restante de <span class="font-bold text-primary">Bs. {{ number_format($saldoPendiente, 2) }}</span> para cerrar este folio.
                </p>
                <form method="POST" action="{{ route('pagos.liquidar', $reserva) }}" class="w-full"
                      onsubmit="return confirm('¿Confirmar liquidación total de Bs. {{ number_format($saldoPendiente, 2) }}?')">
                    @csrf
                    <button type="submit"
                            class="group w-full bg-primary text-white py-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 transition-all shadow-lg">
                        Liquidar Saldo y Finalizar Reserva
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </form>
                <p class="mt-3 text-[10px] text-slate-400">Esta acción cambiará el estado a "Totalmente Pagado".</p>
            </div>
            @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-6 flex flex-col items-center text-center">
                <span class="material-symbols-outlined text-4xl text-emerald-600 mb-3" style="font-variation-settings:'FILL' 1">check_circle</span>
                <h4 class="font-bold text-emerald-800 text-lg">¡Pagado Completo!</h4>
                <p class="text-sm text-emerald-600 mt-1">Esta reserva está totalmente liquidada.</p>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection
