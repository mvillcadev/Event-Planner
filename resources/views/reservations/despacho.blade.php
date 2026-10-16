@php($layout = Auth::user()->rol === 'admin' ? 'layouts.app' : 'layouts.employee')
@extends($layout)

@section('title', 'Hoja de Ruta - ' . ($reserva->descripcion ?? 'Reserva #' . $reserva->id))

@section('content')
<div class="p-6 lg:p-8 max-w-4xl mx-auto space-y-8">

    {{-- Cabecera --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('reservas.index') }}"
           class="p-2 rounded-xl hover:bg-surface-container transition-colors text-secondary">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-primary font-headline">Hoja de Ruta / Despacho</h2>
            <p class="text-secondary text-sm">Lista de carga para el equipo de logística</p>
        </div>
        <div class="ml-auto flex items-center gap-3">
            <a href="{{ route('reservas.comprobante', $reserva) }}"
               class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-semibold text-sm hover:bg-primary-container transition-all active:scale-95 shadow-sm">
                <span class="material-symbols-outlined text-sm">download</span>
                Descargar PDF
            </a>
            @if(Auth::user()->rol === 'admin')
            <a href="{{ route('retornos.create', $reserva) }}"
               class="flex items-center gap-2 px-4 py-2 bg-surface-container text-primary rounded-xl font-semibold text-sm hover:bg-surface-container-high transition-all active:scale-95">
                <span class="material-symbols-outlined text-sm">assignment_return</span>
                Procesar Retorno
            </a>
            @endif
        </div>
    </div>

    {{-- Datos del evento --}}
    <div class="bg-primary text-white rounded-2xl p-6 shadow-xl relative overflow-hidden">
        <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl opacity-10">local_shipping</span>
        <div class="relative z-10">
            <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">Evento</p>
            <h3 class="text-2xl font-extrabold">{{ $reserva->descripcion ?? 'Reserva #' . $reserva->id }}</h3>
            <div class="mt-3 flex flex-wrap gap-6 text-sm">
                <div>
                    <p class="text-blue-200 text-xs">Cliente</p>
                    <p class="font-bold">{{ $reserva->cliente->nombre_completo ?? '—' }}</p>
                    <p class="text-blue-200 text-xs mt-0.5">{{ $reserva->cliente->telefono ?? '' }}</p>
                </div>
                <div>
                    <p class="text-blue-200 text-xs">Período</p>
                    <p class="font-bold">{{ $reserva->fecha_inicio->format('d/m/Y') }} – {{ $reserva->fecha_fin->format('d/m/Y') }}</p>
                    <p class="text-blue-200 text-xs mt-0.5">{{ $reserva->fecha_inicio->diffInDays($reserva->fecha_fin) + 1 }} días</p>
                </div>
                @if($reserva->cliente->direccion)
                <div>
                    <p class="text-blue-200 text-xs">Dirección de Entrega</p>
                    <p class="font-bold">{{ $reserva->cliente->direccion }}</p>
                </div>
                @endif
                <div>
                    <p class="text-blue-200 text-xs">Responsable</p>
                    <p class="font-bold">{{ $reserva->usuario->name ?? Auth::user()->name }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de artículos --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-surface-container flex items-center justify-between">
            <h3 class="font-bold text-primary flex items-center gap-2">
                <span class="material-symbols-outlined">inventory_2</span>
                Artículos a Cargar
            </h3>
            <span class="text-xs text-secondary font-semibold bg-surface-container px-3 py-1 rounded-full">
                {{ $reserva->detalles->sum('cantidad') }} unidades totales
            </span>
        </div>

        <div class="divide-y divide-surface-container">
            @foreach($reserva->detalles as $detalle)
            @php
                $articulo = $detalle->articulo;
                $categoria = $articulo?->categoria?->nombre ?? 'Sin categoría';
            @endphp
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 transition-colors">

                {{-- Checkbox para marcar como cargado (solo visual) --}}
                <div class="w-6 h-6 rounded border-2 border-outline-variant flex-shrink-0 flex items-center justify-center cursor-pointer hover:bg-primary/10 transition-colors check-box"
                     onclick="toggleCheck(this)">
                </div>

                {{-- Imagen --}}
                <div class="w-12 h-12 rounded-lg bg-surface-container overflow-hidden flex-shrink-0">
                    @if($articulo?->url_imagen)
                    <img src="{{ $articulo->url_imagen }}" alt="{{ $articulo->nombre }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-secondary text-sm">inventory_2</span>
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-primary truncate check-text">{{ $articulo->nombre ?? '—' }}</p>
                    <p class="text-xs text-secondary">{{ $categoria }}</p>
                </div>

                {{-- Cantidad --}}
                <div class="text-right shrink-0">
                    <div class="text-2xl font-extrabold text-primary leading-none">{{ $detalle->cantidad }}</div>
                    <div class="text-[10px] text-secondary font-semibold">UNIDADES</div>
                </div>

                {{-- Estado de carga --}}
                <div class="w-24 shrink-0 text-right">
                    <span class="cargo-badge px-2 py-1 rounded-full text-[10px] font-bold bg-surface-container text-secondary">
                        Pendiente
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Footer con total --}}
        <div class="px-6 py-4 bg-surface-container-low flex justify-between items-center">
            <p class="text-sm text-secondary">
                <span id="cargados" class="font-bold text-primary">0</span>
                de {{ $reserva->detalles->count() }} artículos marcados
            </p>
            <div class="text-right">
                <p class="text-xs text-secondary">Valor del despacho</p>
                <p class="font-bold text-primary">Bs. {{ number_format($reserva->monto_total, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Notas de logística --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
        <div class="flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-600 mt-0.5">warning</span>
            <div class="text-sm text-amber-800">
                <p class="font-bold mb-1">Instrucciones de Despacho</p>
                <ul class="space-y-1 text-amber-700 list-disc list-inside">
                    <li>Verificar el estado de cada artículo antes de cargar</li>
                    <li>Confirmar que las cantidades coinciden con este documento</li>
                    <li>Fotografiar el equipo al momento de la entrega al cliente</li>
                    <li>Obtener firma del cliente al recibir el equipo</li>
                    <li>Fecha de devolución: <strong>{{ $reserva->fecha_fin->format('d/m/Y') }}</strong></li>
                </ul>
            </div>
        </div>
    </div>

</div>

<script>
    let totalItems = {{ $reserva->detalles->count() }};
    let cargados   = 0;

    function toggleCheck(box) {
        const row   = box.closest('.flex');
        const badge = row.querySelector('.cargo-badge');
        const text  = row.querySelector('.check-text');

        box.classList.toggle('checked');

        if (box.classList.contains('checked')) {
            box.innerHTML = '<span class="material-symbols-outlined text-sm text-primary" style="font-size:16px">check</span>';
            box.classList.add('border-primary', 'bg-primary/10');
            badge.className = 'cargo-badge px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700';
            badge.textContent = 'Cargado';
            text.classList.add('line-through', 'text-slate-400');
            cargados++;
        } else {
            box.innerHTML = '';
            box.classList.remove('border-primary', 'bg-primary/10');
            badge.className = 'cargo-badge px-2 py-1 rounded-full text-[10px] font-bold bg-surface-container text-secondary';
            badge.textContent = 'Pendiente';
            text.classList.remove('line-through', 'text-slate-400');
            cargados--;
        }

        document.getElementById('cargados').textContent = cargados;
    }
</script>
@endsection
