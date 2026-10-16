@forelse($articulos as $articulo)
<div class="group bg-white rounded-xl p-4 shadow-sm hover:shadow-lg transition-all border border-transparent hover:border-slate-100 flex flex-col">

    {{-- Imagen --}}
    <div class="aspect-square w-full rounded-lg overflow-hidden bg-slate-100 mb-4 relative">
        @if($articulo->ruta_imagen)
            <img src="{{ $articulo->url_imagen }}"
                 alt="{{ $articulo->nombre }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                 loading="lazy"/>
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 gap-2">
                <span class="material-symbols-outlined text-5xl">image_not_supported</span>
                <span class="text-xs">Sin imagen</span>
            </div>
        @endif

        {{-- Badge de estado --}}
        <div class="absolute top-2 right-2 px-2 py-1 bg-white/90 backdrop-blur rounded-lg flex items-center gap-1.5 shadow-sm">
            @if($articulo->estado === 'disponible')
                <span class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0"></span>
                <span class="text-[10px] font-bold text-slate-700 uppercase">Disponible</span>
            @else
                <span class="w-2 h-2 bg-orange-400 rounded-full flex-shrink-0"></span>
                <span class="text-[10px] font-bold text-slate-700 uppercase">Mantenimiento</span>
            @endif
        </div>

        {{-- Badge de categoría --}}
        @if($articulo->categoria)
        <div class="absolute bottom-2 left-2 px-2 py-0.5 bg-primary/80 backdrop-blur rounded-lg">
            <span class="text-[9px] font-bold text-white uppercase tracking-wide">{{ $articulo->categoria->nombre }}</span>
        </div>
        @endif
    </div>

    {{-- Nombre --}}
    <h3 class="text-slate-900 font-bold text-sm sm:text-base mb-1 line-clamp-2 font-headline">
        {{ $articulo->nombre }}
    </h3>

    @if($articulo->descripcion)
        <p class="text-xs text-secondary mb-2 line-clamp-1">{{ $articulo->descripcion }}</p>
    @endif

    {{-- Footer de la tarjeta --}}
    <div class="flex justify-between items-end mt-auto pt-2">
        <div>
            <p class="text-xs text-secondary font-medium">
                {{ number_format($articulo->stock_total) }} en stock
            </p>
            <p class="text-primary font-bold mt-1 text-sm">
                {{ number_format($articulo->precio_alquiler, 2) }} Bs
                <span class="text-[10px] text-secondary font-normal">/ unidad</span>
            </p>
        </div>
        {{-- Botón "Añadir a Reserva" — no funcional en esta iteración --}}
        <button
            title="Añadir a Reserva (próximamente)"
            class="p-2 bg-slate-50 text-primary rounded-lg hover:bg-primary hover:text-white transition-colors cursor-not-allowed opacity-70"
            disabled>
            <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
        </button>
    </div>
</div>
@empty
<div class="col-span-full text-center py-16 text-secondary">
    <span class="material-symbols-outlined text-6xl mb-3 block opacity-30">inventory_2</span>
    <p class="font-headline font-bold text-lg text-primary mb-1">Sin artículos en esta categoría</p>
    <p class="text-sm">Prueba seleccionando otra categoría.</p>
</div>
@endforelse
