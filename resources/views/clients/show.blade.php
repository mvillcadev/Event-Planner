@php($layout = Auth::user()->rol === 'admin' ? 'layouts.app' : 'layouts.employee')
@extends($layout)

@section('title', 'Historial - ' . $client->nombre_completo)

@section('content')
<div class="p-6 lg:p-8 max-w-5xl mx-auto space-y-8">

    <div class="flex items-center gap-4">
        <a href="{{ route('clients.index') }}"
           class="p-2 rounded-xl hover:bg-surface-container transition-colors text-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-primary font-headline">{{ $client->nombre_completo }}</h2>
            <p class="text-secondary text-sm">{{ $client->telefono }}{{ $client->email ? ' · ' . $client->email : '' }}</p>
        </div>
    </div>

    @if($client->direccion)
    <div class="flex items-center gap-2 text-sm text-secondary bg-surface-container-low rounded-xl px-4 py-3 w-fit">
        <span class="material-symbols-outlined text-sm">location_on</span>
        {{ $client->direccion }}
    </div>
    @endif

    <section>
        <h3 class="text-lg font-bold text-primary mb-4 font-headline">Historial de Reservas</h3>

        @if($reservas->isEmpty())
        <div class="bg-surface-container-lowest rounded-xl p-12 text-center shadow-sm">
            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">event_busy</span>
            <p class="text-slate-400">Este cliente no tiene reservas registradas.</p>
        </div>
        @else
        <div class="space-y-4">
            @foreach($reservas as $reserva)
            @php
                $totalPagado  = $reserva->pagos->sum('monto');
                $saldo        = max(0, $reserva->monto_total - $totalPagado);
                $estadoClases = match($reserva->estado) {
                    'completada' => 'bg-emerald-50 text-emerald-700',
                    'confirmada' => 'bg-blue-50 text-blue-700',
                    default      => 'bg-amber-50 text-amber-700',
                };
            @endphp
            <div class="bg-surface-container-lowest rounded-xl p-5 shadow-sm flex flex-col sm:flex-row justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="font-bold text-primary text-sm">{{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}</p>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $estadoClases }} uppercase">{{ $reserva->estado }}</span>
                    </div>
                    <p class="text-xs text-secondary">
                        {{ $reserva->fecha_inicio->format('d/m/Y') }} – {{ $reserva->fecha_fin->format('d/m/Y') }}
                    </p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($reserva->detalles as $d)
                        <span class="text-[10px] bg-surface-container px-2 py-0.5 rounded-full text-secondary">{{ $d->articulo->nombre ?? '—' }} ×{{ $d->cantidad }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-lg font-bold text-primary">Bs. {{ number_format($reserva->monto_total, 2) }}</p>
                    @if($saldo > 0)
                    <p class="text-xs text-error">Saldo: Bs. {{ number_format($saldo, 2) }}</p>
                    @else
                    <p class="text-xs text-emerald-600 font-semibold">Pagado completo</p>
                    @endif
                    @if(Auth::user()->rol === 'admin')
                    <a href="{{ route('pagos.show', $reserva) }}"
                       class="inline-flex items-center gap-1 mt-2 text-xs text-primary font-bold hover:underline">
                        <span class="material-symbols-outlined text-sm">payments</span>
                        Ver pagos
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($reservas->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $reservas->links() }}
        </div>
        @endif
        @endif
    </section>
</div>
@endsection
