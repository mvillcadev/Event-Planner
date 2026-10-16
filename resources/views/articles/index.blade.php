@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-on-secondary-fixed-variant uppercase tracking-widest font-label">Gestión de Inventario</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-primary mt-1 font-headline">Artículos</h2>
        </div>
        <a href="{{ route('articles.create') }}"
           class="inline-flex items-center gap-2 px-5 py-3 bg-primary text-white rounded-xl font-headline font-bold text-sm shadow-md hover:bg-primary-container transition-all active:scale-95 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Nuevo Artículo
        </a>
    </div>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-secondary-container text-on-secondary-container text-sm rounded-xl px-4 py-3">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tarjetas de resumen rápido --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @php
            $totalArticulos  = $articulos->total();
            $disponibles     = \App\Models\Article::where('estado', 'disponible')->count();
            $mantenimiento   = \App\Models\Article::where('estado', 'mantenimiento')->count();
            $totalUnidades   = \App\Models\Article::sum('stock_total');
        @endphp
        <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-5 ring-1 ring-slate-100">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Total Tipos</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-primary font-headline">{{ $totalArticulos }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-5 ring-1 ring-slate-100">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Disponibles</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-primary font-headline">{{ $disponibles }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-5 ring-1 ring-slate-100">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Mantenimiento</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-error font-headline">{{ $mantenimiento }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-xl p-4 sm:p-5 ring-1 ring-slate-100">
            <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Total Unidades</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-primary font-headline">{{ number_format($totalUnidades) }}</p>
        </div>
    </div>

    {{-- Tabla de artículos --}}
    <div class="bg-surface-container-lowest rounded-2xl ring-1 ring-slate-100 overflow-hidden">
        {{-- Barra de búsqueda/filtros --}}
        <div class="flex flex-col sm:flex-row gap-3 p-4 sm:p-6 border-b border-surface-container-low">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-outline text-[20px]">search</span>
                </div>
                <input type="text" placeholder="Buscar artículo..."
                       class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border-transparent rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"/>
            </div>
            <select class="bg-surface-container-low border-transparent rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20">
                <option value="">Todos los estados</option>
                <option value="disponible">Disponible</option>
                <option value="mantenimiento">Mantenimiento</option>
            </select>
        </div>

        @if($articulos->isEmpty())
            <div class="text-center py-16 text-secondary">
                <span class="material-symbols-outlined text-6xl mb-4 block opacity-30">inventory_2</span>
                <p class="font-headline font-bold text-lg text-primary mb-1">Sin artículos registrados</p>
                <p class="text-sm mb-6">Comienza agregando el primer artículo al inventario.</p>
                <a href="{{ route('articles.create') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary-container transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Agregar primer artículo
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[640px]">
                    <thead>
                        <tr class="border-b border-surface-container-low bg-surface-container-low/50">
                            <th class="py-3 px-4 sm:px-6 font-label text-xs font-bold text-secondary uppercase tracking-wider">Artículo</th>
                            <th class="py-3 px-4 font-label text-xs font-bold text-secondary uppercase tracking-wider hidden sm:table-cell">Categoría</th>
                            <th class="py-3 px-4 font-label text-xs font-bold text-secondary uppercase tracking-wider">Stock</th>
                            <th class="py-3 px-4 font-label text-xs font-bold text-secondary uppercase tracking-wider hidden md:table-cell">Precio Alquiler</th>
                            <th class="py-3 px-4 font-label text-xs font-bold text-secondary uppercase tracking-wider">Estado</th>
                            <th class="py-3 px-4 sm:px-6 font-label text-xs font-bold text-secondary uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low">
                        @foreach($articulos as $articulo)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            {{-- Artículo --}}
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl overflow-hidden bg-surface-container-low flex-shrink-0 ring-1 ring-slate-100">
                                        @if($articulo->ruta_imagen)
                                            <img src="{{ $articulo->url_imagen }}" alt="{{ $articulo->nombre }}"
                                                 class="w-full h-full object-cover"/>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <span class="material-symbols-outlined text-2xl">image</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-on-surface text-sm">{{ $articulo->nombre }}</p>
                                        @if($articulo->descripcion)
                                            <p class="text-xs text-secondary truncate max-w-[160px]">{{ $articulo->descripcion }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            {{-- Categoría --}}
                            <td class="py-4 px-4 hidden sm:table-cell">
                                <span class="text-sm text-secondary">{{ $articulo->categoria->nombre ?? '—' }}</span>
                            </td>
                            {{-- Stock --}}
                            <td class="py-4 px-4">
                                <span class="text-sm font-semibold text-on-surface">{{ number_format($articulo->stock_total) }}</span>
                                <span class="text-xs text-secondary ml-1">uds</span>
                            </td>
                            {{-- Precio --}}
                            <td class="py-4 px-4 hidden md:table-cell">
                                <span class="text-sm font-semibold text-primary">Bs. {{ number_format($articulo->precio_alquiler, 2) }}</span>
                            </td>
                            {{-- Estado --}}
                            <td class="py-4 px-4">
                                @if($articulo->estado === 'disponible')
                                    <span class="px-2.5 py-1 bg-secondary-container text-on-secondary-container rounded-full text-[11px] font-bold uppercase whitespace-nowrap">Disponible</span>
                                @else
                                    <span class="px-2.5 py-1 bg-error-container text-on-error-container rounded-full text-[11px] font-bold uppercase whitespace-nowrap">Mantenimiento</span>
                                @endif
                            </td>
                            {{-- Acciones --}}
                            <td class="py-4 px-4 sm:px-6 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('articles.edit', $articulo) }}"
                                       class="p-2 text-outline hover:text-primary hover:bg-primary-fixed rounded-lg transition-all" title="Editar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('articles.destroy', $articulo) }}"
                                          onsubmit="return confirm('¿Eliminar este artículo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-outline hover:text-error hover:bg-error-container rounded-lg transition-all" title="Eliminar">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($articulos->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-surface-container-low">
                    {{ $articulos->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
