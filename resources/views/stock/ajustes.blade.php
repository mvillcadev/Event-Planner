@extends('layouts.app')

@section('title', 'Ajustes de Inventario')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-8">

    <div>
        <h2 class="text-3xl font-extrabold text-primary tracking-tight font-headline">Ajuste de Inventario</h2>
        <p class="text-secondary mt-1 text-sm">Registro manual de entradas y bajas de stock con trazabilidad completa.</p>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 text-red-700 text-sm space-y-1">
        @foreach($errors->all() as $error)
        <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm">error</span>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        {{-- Formulario de ajuste --}}
        <div class="xl:col-span-5 bg-surface-container-lowest rounded-2xl p-6 shadow-sm space-y-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center text-on-primary-container">
                    <span class="material-symbols-outlined">tune</span>
                </div>
                <h3 class="text-lg font-bold text-primary">Nuevo Ajuste</h3>
            </div>

            <form method="POST" action="{{ route('stock.ajustes.store') }}" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary">Artículo *</label>
                    <select name="articulo_id" id="select-articulo"
                            onchange="mostrarStock(this)"
                            class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/20 @error('articulo_id') ring-2 ring-error @enderror">
                        <option value="">Seleccionar artículo...</option>
                        @foreach($articulos as $articulo)
                        <option value="{{ $articulo->id }}"
                                data-stock="{{ $articulo->stock_total }}"
                                {{ old('articulo_id') == $articulo->id ? 'selected' : '' }}>
                            {{ $articulo->nombre }} (Stock: {{ $articulo->stock_total }})
                        </option>
                        @endforeach
                    </select>
                    @error('articulo_id') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                {{-- Stock actual --}}
                <div id="stock-info" class="hidden bg-surface-container rounded-xl p-3 flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary">inventory_2</span>
                    <div>
                        <p class="text-xs text-secondary">Stock actual</p>
                        <p class="font-bold text-primary" id="stock-value">—</p>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary">Tipo de Ajuste *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="tipo" value="entrada"
                                   class="sr-only peer" {{ old('tipo', 'entrada') === 'entrada' ? 'checked' : '' }}>
                            <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-surface-container peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all">
                                <span class="material-symbols-outlined text-emerald-600 text-lg">add_circle</span>
                                <span class="text-sm font-bold text-primary">Entrada</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="tipo" value="baja"
                                   class="sr-only peer" {{ old('tipo') === 'baja' ? 'checked' : '' }}>
                            <div class="flex items-center gap-2 p-3 rounded-xl border-2 border-surface-container peer-checked:border-error peer-checked:bg-red-50 transition-all">
                                <span class="material-symbols-outlined text-error text-lg">remove_circle</span>
                                <span class="text-sm font-bold text-primary">Baja</span>
                            </div>
                        </label>
                    </div>
                    @error('tipo') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary">Cantidad *</label>
                    <input type="number" name="cantidad" min="1"
                           value="{{ old('cantidad', 1) }}"
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm font-bold outline-none focus:ring-2 focus:ring-primary/20 @error('cantidad') ring-2 ring-error @enderror">
                    @error('cantidad') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary">Motivo / Descripción *</label>
                    <input type="text" name="motivo"
                           value="{{ old('motivo') }}"
                           placeholder="Ej: Compra de artículos nuevos, Pérdida por deterioro..."
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary/20 @error('motivo') ring-2 ring-error @enderror">
                    @error('motivo') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full bg-primary text-on-primary py-3 rounded-xl font-bold text-sm hover:opacity-95 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Registrar Ajuste
                </button>
            </form>
        </div>

        {{-- Historial --}}
        <div class="xl:col-span-7 space-y-4">
            <h3 class="text-xl font-bold text-primary font-headline flex items-center gap-2">
                <span class="material-symbols-outlined">history</span>
                Historial de Ajustes
            </h3>

            <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low border-b border-outline-variant/10">
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Artículo</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Tipo</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden sm:table-cell">Cantidad</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden md:table-cell">Motivo</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden lg:table-cell">Usuario</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/5">
                            @forelse($historial as $ajuste)
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="px-5 py-3 text-sm font-semibold text-primary">
                                    {{ $ajuste->articulo->nombre ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    @if($ajuste->tipo === 'entrada')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        <span class="material-symbols-outlined text-[10px]">arrow_upward</span>
                                        Entrada
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700">
                                        <span class="material-symbols-outlined text-[10px]">arrow_downward</span>
                                        Baja
                                    </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 hidden sm:table-cell">
                                    <span class="font-bold {{ $ajuste->tipo === 'entrada' ? 'text-emerald-600' : 'text-error' }}">
                                        {{ $ajuste->tipo === 'entrada' ? '+' : '-' }}{{ $ajuste->cantidad }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-sm text-secondary hidden md:table-cell max-w-[200px] truncate">
                                    {{ $ajuste->motivo }}
                                </td>
                                <td class="px-5 py-3 text-xs text-secondary hidden lg:table-cell">
                                    {{ $ajuste->usuario->name ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-xs text-secondary">
                                    {{ $ajuste->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl block mb-2">history</span>
                                    No hay ajustes registrados aún.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($historial->hasPages())
                <div class="px-5 py-4 border-t border-outline-variant/10 bg-slate-50/50 flex items-center justify-between">
                    <p class="text-xs text-secondary">Mostrando {{ $historial->firstItem() }}–{{ $historial->lastItem() }} de {{ $historial->total() }}</p>
                    <div class="flex gap-2 text-xs">
                        @if(!$historial->onFirstPage())
                        <a href="{{ $historial->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-colors font-semibold text-secondary">Anterior</a>
                        @endif
                        @if($historial->hasMorePages())
                        <a href="{{ $historial->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-colors font-semibold text-secondary">Siguiente</a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function mostrarStock(sel) {
        const option  = sel.options[sel.selectedIndex];
        const stock   = option.dataset.stock;
        const infoBox = document.getElementById('stock-info');
        const val     = document.getElementById('stock-value');

        if (stock !== undefined && sel.value !== '') {
            infoBox.classList.remove('hidden');
            infoBox.classList.add('flex');
            val.textContent = stock + ' unidades';
        } else {
            infoBox.classList.add('hidden');
            infoBox.classList.remove('flex');
        }
    }
</script>
@endsection
