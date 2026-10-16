@php($layout = Auth::user()->rol === 'admin' ? 'layouts.app' : 'layouts.employee')
@extends($layout)

@section('title', 'Nueva Reserva')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto">

    {{-- Errores globales de stock --}}
    @if($errors->has('stock') || $errors->has('items'))
    <div class="mb-6 bg-error-container border border-error/20 rounded-xl p-4 flex items-start gap-3">
        <span class="material-symbols-outlined text-error mt-0.5">error</span>
        <div>
            <p class="font-bold text-error text-sm mb-1">No se puede confirmar la reserva</p>
            @foreach((array) $errors->get('stock') as $e)
                <p class="text-sm text-on-error-container">{{ $e }}</p>
            @endforeach
            @if($errors->has('items'))
                <p class="text-sm text-on-error-container">{{ $errors->first('items') }}</p>
            @endif
        </div>
    </div>
    @endif

    <form action="{{ route('reservas.store') }}" method="POST" id="reserva-form">
        @csrf
        <input type="hidden" name="items"       id="items-json" value="{{ old('items', '[]') }}">
        <input type="hidden" name="cliente_id"  id="input-cliente-id" value="{{ old('cliente_id') }}">

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

            {{-- ═══ COLUMNA IZQUIERDA ═══ --}}
            <div class="xl:col-span-8 space-y-6">

                {{-- Sección de fechas --}}
                <section class="bg-surface-container-lowest rounded-xl p-6 shadow-sm">
                    <h1 class="text-2xl font-bold text-primary font-headline mb-6">Nueva Reserva</h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wider">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio"
                                   value="{{ old('fecha_inicio') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('fecha_inicio') ring-2 ring-error @enderror">
                            @error('fecha_inicio') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wider">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="fecha_fin"
                                   value="{{ old('fecha_fin') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('fecha_fin') ring-2 ring-error @enderror">
                            @error('fecha_fin') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <button type="button" id="btn-verificar"
                                class="bg-primary text-white font-semibold rounded-xl px-6 py-3 hover:bg-primary-container transition-all active:scale-95 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">search</span>
                            Verificar Disponibilidad
                        </button>
                    </div>

                    {{-- Descripción del evento --}}
                    <div class="mt-4">
                        <label class="block text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wider">Nombre del Evento <span class="text-slate-400 normal-case font-normal">(opcional)</span></label>
                        <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                               placeholder="Ej. Boda: García & Rodríguez"
                               class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </section>

                {{-- Selección de cliente --}}
                <section class="bg-surface-container-low rounded-xl p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                        <h3 class="text-lg font-bold text-primary">Selección de Cliente</h3>
                        <button type="button" id="btn-nuevo-cliente"
                                class="text-primary font-semibold text-sm flex items-center gap-1 hover:underline">
                            <span class="material-symbols-outlined text-sm">add_circle</span>
                            + Nuevo Cliente
                        </button>
                    </div>

                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-lg">search</span>
                        <input type="text" id="buscar-cliente" autocomplete="off"
                               placeholder="Buscar por nombre, CI o teléfono..."
                               class="w-full pl-12 pr-4 py-3 bg-white border-none rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-primary/10 outline-none">
                        <div id="dropdown-clientes"
                             class="absolute z-30 w-full bg-white rounded-xl shadow-lg border border-outline-variant/20 mt-1 hidden max-h-52 overflow-y-auto">
                        </div>
                    </div>

                    {{-- Cliente seleccionado --}}
                    <div id="cliente-seleccionado" class="hidden mt-3 flex items-center gap-3 bg-white rounded-xl px-4 py-3 shadow-sm">
                        <div class="w-9 h-9 rounded-full bg-secondary-fixed flex items-center justify-center font-bold text-xs text-on-secondary-fixed-variant" id="cliente-avatar"></div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-primary" id="cliente-nombre"></p>
                            <p class="text-xs text-slate-500" id="cliente-telefono"></p>
                        </div>
                        <button type="button" id="btn-limpiar-cliente" class="text-slate-400 hover:text-error transition-colors">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>

                    @error('cliente_id')
                    <p class="text-xs text-error mt-2">{{ $message }}</p>
                    @enderror
                </section>

                {{-- Grilla de artículos --}}
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-primary">Artículos Disponibles</h3>
                        <div id="filtros-categorias" class="flex flex-wrap gap-2">
                            <button type="button"
                                    class="filtro-cat px-3 py-1 bg-primary text-white text-[10px] font-bold rounded-full uppercase active"
                                    data-cat="">Todos</button>
                            @foreach($categorias as $cat)
                            <button type="button"
                                    class="filtro-cat px-3 py-1 bg-surface-container-highest text-on-surface-variant text-[10px] font-bold rounded-full uppercase"
                                    data-cat="{{ $cat->id }}">{{ $cat->nombre }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Estado inicial --}}
                    <div id="estado-inicial" class="bg-surface-container-lowest rounded-xl p-12 text-center shadow-sm">
                        <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">calendar_search</span>
                        <p class="text-slate-500 font-medium">Selecciona las fechas y haz clic en <strong>"Verificar Disponibilidad"</strong></p>
                        <p class="text-slate-400 text-sm mt-1">para ver el stock disponible en ese periodo.</p>
                    </div>

                    {{-- Alerta stock insuficiente --}}
                    <div id="alerta-stock" class="hidden mb-4 bg-error-container border border-error/20 rounded-xl p-4 flex items-center gap-3">
                        <span class="material-symbols-outlined text-error">warning</span>
                        <p id="alerta-stock-texto" class="text-sm text-on-error-container font-semibold"></p>
                    </div>

                    {{-- Grilla dinámica --}}
                    <div id="articulos-grid" class="hidden">
                        <div id="articulos-lista" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"></div>
                        <p id="sin-resultados" class="hidden text-center text-slate-400 py-8">No hay artículos disponibles para estas fechas.</p>
                    </div>
                </section>
            </div>

            {{-- ═══ COLUMNA DERECHA: Resumen ═══ --}}
            <aside class="xl:col-span-4">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sticky top-24">
                    <div class="flex items-center gap-2 mb-5">
                        <span class="material-symbols-outlined text-primary">shopping_basket</span>
                        <h3 class="text-lg font-bold text-primary">Resumen de Reserva</h3>
                    </div>

                    {{-- Items del carrito --}}
                    <div id="carrito-items" class="space-y-3 mb-5 max-h-72 overflow-y-auto pr-1">
                        <p class="text-slate-400 text-sm text-center py-8">Añade artículos para comenzar</p>
                    </div>

                    {{-- Totales --}}
                    <div class="border-t border-slate-100 pt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span id="subtotal-display" class="font-semibold">Bs. 0.00</span>
                        </div>
                        <div class="flex justify-between items-center text-lg font-bold text-primary">
                            <span>Total a Pagar</span>
                            <span id="total-display">Bs. 0.00</span>
                        </div>

                        <div class="pt-3 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Monto de Adelanto</label>
                                <input type="number" name="adelanto" id="adelanto" step="0.01" min="0"
                                       value="{{ old('adelanto', 0) }}"
                                       class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Método de Pago</label>
                                <select name="metodo_pago" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none appearance-none">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia Bancaria</option>
                                    <option value="qr">Pago por QR</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" id="btn-confirmar"
                                class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:bg-primary-container shadow-lg shadow-primary/10 transition-all active:scale-95 mt-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Confirmar Reserva
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>

{{-- ═══ MODAL: Cantidad de artículo ═══ --}}
<div id="modal-cantidad" class="hidden fixed inset-0 bg-primary/20 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-primary" id="modal-articulo-nombre">Artículo</h3>
            <button type="button" onclick="cerrarModalCantidad()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <p class="text-sm text-slate-500 mb-1">Disponible: <span id="modal-disponible" class="font-bold text-primary"></span> unidades</p>
        <p class="text-sm text-slate-500 mb-4">Precio: <span id="modal-precio" class="font-bold text-primary"></span></p>
        <div>
            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">Cantidad</label>
            <input type="number" id="modal-cantidad-input" min="1" value="1"
                   class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
            <p id="modal-error" class="text-xs text-error mt-1 hidden"></p>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="button" onclick="cerrarModalCantidad()" class="flex-1 py-3 border border-slate-200 rounded-xl text-slate-600 font-bold hover:bg-slate-50">Cancelar</button>
            <button type="button" onclick="confirmarAnadir()" class="flex-1 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-container transition-all">Añadir</button>
        </div>
    </div>
</div>

{{-- ═══ MODAL: Nuevo Cliente ═══ --}}
<div id="modal-nuevo-cliente" class="hidden fixed inset-0 bg-primary/20 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-primary">Registrar Nuevo Cliente</h3>
            <button type="button" onclick="cerrarModalCliente()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="modal-cliente-error" class="hidden mb-4 bg-error-container text-on-error-container rounded-xl p-3 text-sm"></div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Nombre Completo *</label>
                <input type="text" id="nc-nombre" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="Nombre completo del cliente">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Teléfono *</label>
                    <input type="text" id="nc-telefono" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="70000000">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Email</label>
                    <input type="email" id="nc-email" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="opcional">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Dirección</label>
                <input type="text" id="nc-direccion" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="opcional">
            </div>
        </div>
        <div class="mt-8 flex gap-3">
            <button type="button" onclick="cerrarModalCliente()" class="flex-1 py-3 border border-slate-200 rounded-xl text-slate-600 font-bold hover:bg-slate-50">Cancelar</button>
            <button type="button" onclick="guardarNuevoCliente()" id="btn-guardar-cliente" class="flex-1 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary-container transition-all">Guardar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ═══════════════════════════════════════
// Estado global
// ═══════════════════════════════════════
const RUTA_VERIFICAR = '{{ route('reservas.verificar') }}';
const RUTA_BUSCAR    = '{{ route('clients.buscar') }}';
const RUTA_CLIENTES  = '{{ route('clients.store') }}';
const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').content;

let carrito             = JSON.parse(document.getElementById('items-json').value || '[]');
let articulosMap        = {};    // id → articulo
let articulosFiltrados  = [];   // lista actual tras filtro de categoría
let categoriaActiva     = '';
let articuloParaAnadir  = null; // el artículo pendiente en el modal

// ═══════════════════════════════════════
// Verificar disponibilidad
// ═══════════════════════════════════════
document.getElementById('btn-verificar').addEventListener('click', async () => {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin    = document.getElementById('fecha_fin').value;

    if (!fechaInicio || !fechaFin) {
        mostrarAlertaStock('Selecciona las fechas de inicio y fin primero.');
        return;
    }

    const btn = document.getElementById('btn-verificar');
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Verificando...';

    try {
        const params = new URLSearchParams({ fecha_inicio: fechaInicio, fecha_fin: fechaFin });
        const res    = await fetch(`${RUTA_VERIFICAR}?${params}`);
        const data   = await res.json();

        articulosMap = {};
        data.articulos.forEach(a => articulosMap[a.id] = a);
        articulosFiltrados = data.articulos;

        ocultarAlertaStock();
        renderGrilla(data.articulos);

        document.getElementById('estado-inicial').classList.add('hidden');
        document.getElementById('articulos-grid').classList.remove('hidden');
    } catch (e) {
        mostrarAlertaStock('Error al verificar disponibilidad. Intenta de nuevo.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined text-sm">search</span> Verificar Disponibilidad';
    }
});

// ═══════════════════════════════════════
// Filtros de categoría
// ═══════════════════════════════════════
document.querySelectorAll('.filtro-cat').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filtro-cat').forEach(b => {
            b.classList.remove('bg-primary', 'text-white');
            b.classList.add('bg-surface-container-highest', 'text-on-surface-variant');
        });
        btn.classList.add('bg-primary', 'text-white');
        btn.classList.remove('bg-surface-container-highest', 'text-on-surface-variant');

        categoriaActiva = btn.dataset.cat;

        const filtrados = categoriaActiva
            ? Object.values(articulosMap).filter(a => String(a.categoria_id) === categoriaActiva || true)
            : Object.values(articulosMap);

        // Filtro real por categoría usando el nombre ya que la API devuelve categoria string
        const resultado = categoriaActiva
            ? Object.values(articulosMap).filter(a => String(a.categoria_id) === categoriaActiva)
            : Object.values(articulosMap);

        renderGrilla(Object.values(articulosMap).filter(a =>
            !categoriaActiva || String(a.categoria_id) === categoriaActiva
        ));
    });
});

function renderGrilla(articulos) {
    const lista       = document.getElementById('articulos-lista');
    const sinResult   = document.getElementById('sin-resultados');

    if (articulos.length === 0) {
        lista.innerHTML = '';
        sinResult.classList.remove('hidden');
        return;
    }
    sinResult.classList.add('hidden');

    lista.innerHTML = articulos.map(a => {
        const enCarrito   = carrito.find(i => i.articulo_id === a.id);
        const cantCarrito = enCarrito ? enCarrito.cantidad : 0;

        return `
        <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all ${a.agotado ? 'opacity-60' : ''}">
            <div class="aspect-square bg-slate-200 relative">
                <img src="${a.url_imagen}" alt="${a.nombre}" class="w-full h-full object-cover">
                <span class="absolute top-2 right-2 px-2 py-1 rounded-lg text-[10px] font-bold ${a.disponible > 5 ? 'bg-white/90 text-primary' : a.disponible > 0 ? 'bg-amber-100/90 text-amber-700' : 'bg-error-container text-error'}">
                    ${a.agotado ? 'AGOTADO' : `DISP: ${a.disponible}`}
                </span>
                ${cantCarrito > 0 ? `<span class="absolute top-2 left-2 bg-primary text-white text-[10px] font-bold px-2 py-1 rounded-lg">${cantCarrito} en carrito</span>` : ''}
            </div>
            <div class="p-4">
                <p class="text-[10px] font-semibold text-secondary uppercase mb-0.5">${a.categoria}</p>
                <h4 class="font-bold text-sm text-primary mb-1">${a.nombre}</h4>
                <p class="text-xs text-slate-500 mb-3">Bs. ${Number(a.precio).toFixed(2)}/u</p>
                <button type="button" onclick="abrirModalCantidad(${a.id})"
                        ${a.agotado ? 'disabled' : ''}
                        class="w-full py-2 text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1
                               ${a.agotado ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-surface-container-low text-primary hover:bg-primary hover:text-white'}">
                    <span class="material-symbols-outlined text-sm">add</span>
                    ${a.agotado ? 'Sin stock' : 'Añadir'}
                </button>
            </div>
        </div>`;
    }).join('');
}

// ═══════════════════════════════════════
// Modal de cantidad
// ═══════════════════════════════════════
function abrirModalCantidad(articuloId) {
    const a = articulosMap[articuloId];
    if (!a || a.agotado) return;

    articuloParaAnadir = a;

    const enCarrito = carrito.find(i => i.articulo_id === a.id);
    const disponibleRestante = a.disponible - (enCarrito ? enCarrito.cantidad : 0);

    document.getElementById('modal-articulo-nombre').textContent = a.nombre;
    document.getElementById('modal-disponible').textContent      = disponibleRestante;
    document.getElementById('modal-precio').textContent          = `Bs. ${Number(a.precio).toFixed(2)}/u`;
    document.getElementById('modal-cantidad-input').value        = 1;
    document.getElementById('modal-cantidad-input').max          = disponibleRestante;
    document.getElementById('modal-error').classList.add('hidden');

    document.getElementById('modal-cantidad').classList.remove('hidden');
}

function cerrarModalCantidad() {
    document.getElementById('modal-cantidad').classList.add('hidden');
    articuloParaAnadir = null;
}

function confirmarAnadir() {
    const a          = articuloParaAnadir;
    const cantInput  = parseInt(document.getElementById('modal-cantidad-input').value);
    const errorEl    = document.getElementById('modal-error');

    if (!a || isNaN(cantInput) || cantInput < 1) {
        errorEl.textContent = 'Ingresa una cantidad válida.';
        errorEl.classList.remove('hidden');
        return;
    }

    const enCarrito         = carrito.find(i => i.articulo_id === a.id);
    const cantidadActual    = enCarrito ? enCarrito.cantidad : 0;
    const nuevaCantidad     = cantidadActual + cantInput;

    if (nuevaCantidad > a.disponible) {
        errorEl.textContent = `Stock insuficiente. Disponibles: ${a.disponible - cantidadActual} más.`;
        errorEl.classList.remove('hidden');
        return;
    }

    if (enCarrito) {
        enCarrito.cantidad = nuevaCantidad;
    } else {
        carrito.push({
            articulo_id:      a.id,
            nombre:           a.nombre,
            cantidad:         cantInput,
            precio_unitario:  Number(a.precio),
        });
    }

    renderCarrito();
    renderGrilla(Object.values(articulosMap));
    cerrarModalCantidad();
}

// ═══════════════════════════════════════
// Carrito
// ═══════════════════════════════════════
function renderCarrito() {
    const lista   = document.getElementById('carrito-items');
    const total   = carrito.reduce((s, i) => s + i.cantidad * i.precio_unitario, 0);

    if (carrito.length === 0) {
        lista.innerHTML = '<p class="text-slate-400 text-sm text-center py-8">Añade artículos para comenzar</p>';
    } else {
        lista.innerHTML = carrito.map((item, idx) => `
            <div class="flex justify-between items-start gap-3 pb-3 border-b border-slate-100 last:border-0">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-on-surface truncate">${item.nombre}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <button type="button" onclick="cambiarCantidad(${idx}, -1)"
                                class="w-6 h-6 rounded-full bg-surface-container text-secondary hover:bg-primary hover:text-white text-xs font-bold flex items-center justify-center">−</button>
                        <span class="text-sm font-bold w-5 text-center">${item.cantidad}</span>
                        <button type="button" onclick="cambiarCantidad(${idx}, 1)"
                                class="w-6 h-6 rounded-full bg-surface-container text-secondary hover:bg-primary hover:text-white text-xs font-bold flex items-center justify-center">+</button>
                        <span class="text-xs text-slate-500">× Bs. ${item.precio_unitario.toFixed(2)}</span>
                    </div>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-primary">Bs. ${(item.cantidad * item.precio_unitario).toFixed(2)}</p>
                    <button type="button" onclick="eliminarItem(${idx})" class="text-[10px] text-error hover:underline">Eliminar</button>
                </div>
            </div>
        `).join('');
    }

    document.getElementById('subtotal-display').textContent = `Bs. ${total.toFixed(2)}`;
    document.getElementById('total-display').textContent    = `Bs. ${total.toFixed(2)}`;
    document.getElementById('items-json').value             = JSON.stringify(carrito);
}

function cambiarCantidad(idx, delta) {
    const item      = carrito[idx];
    const articulo  = articulosMap[item.articulo_id];
    const nueva     = item.cantidad + delta;

    if (nueva < 1) return;
    if (articulo && nueva > articulo.disponible) {
        mostrarAlertaStock(`Stock insuficiente para "${item.nombre}": máximo ${articulo.disponible}.`);
        return;
    }

    item.cantidad = nueva;
    renderCarrito();
    if (Object.keys(articulosMap).length) renderGrilla(Object.values(articulosMap));
}

function eliminarItem(idx) {
    carrito.splice(idx, 1);
    renderCarrito();
    if (Object.keys(articulosMap).length) renderGrilla(Object.values(articulosMap));
}

// ═══════════════════════════════════════
// Búsqueda de cliente
// ═══════════════════════════════════════
let buscarTimeout = null;

document.getElementById('buscar-cliente').addEventListener('input', function () {
    clearTimeout(buscarTimeout);
    const q = this.value.trim();
    if (q.length < 2) {
        cerrarDropdown();
        return;
    }
    buscarTimeout = setTimeout(() => buscarClientes(q), 300);
});

async function buscarClientes(q) {
    const res      = await fetch(`${RUTA_BUSCAR}?q=${encodeURIComponent(q)}`);
    const clientes = await res.json();
    renderDropdown(clientes);
}

function renderDropdown(clientes) {
    const dropdown = document.getElementById('dropdown-clientes');
    if (clientes.length === 0) {
        dropdown.innerHTML = '<p class="text-sm text-slate-400 px-4 py-3">Sin resultados</p>';
    } else {
        dropdown.innerHTML = clientes.map(c => `
            <button type="button" onclick="seleccionarCliente(${c.id}, '${escHtml(c.nombre_completo)}', '${escHtml(c.telefono)}')"
                    class="w-full text-left px-4 py-3 hover:bg-surface-container-low transition-colors border-b border-slate-50 last:border-0">
                <p class="text-sm font-semibold text-primary">${c.nombre_completo}</p>
                <p class="text-xs text-slate-500">${c.telefono}${c.email ? ' · ' + c.email : ''}</p>
            </button>
        `).join('');
    }
    dropdown.classList.remove('hidden');
}

function seleccionarCliente(id, nombre, telefono) {
    document.getElementById('input-cliente-id').value = id;

    const initiales = nombre.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
    document.getElementById('cliente-avatar').textContent   = initiales;
    document.getElementById('cliente-nombre').textContent   = nombre;
    document.getElementById('cliente-telefono').textContent = telefono;
    document.getElementById('cliente-seleccionado').classList.remove('hidden');
    document.getElementById('buscar-cliente').value = '';
    cerrarDropdown();
}

document.getElementById('btn-limpiar-cliente').addEventListener('click', () => {
    document.getElementById('input-cliente-id').value = '';
    document.getElementById('cliente-seleccionado').classList.add('hidden');
});

function cerrarDropdown() {
    document.getElementById('dropdown-clientes').classList.add('hidden');
}

document.addEventListener('click', e => {
    if (!e.target.closest('#buscar-cliente') && !e.target.closest('#dropdown-clientes')) {
        cerrarDropdown();
    }
});

// ═══════════════════════════════════════
// Modal nuevo cliente
// ═══════════════════════════════════════
document.getElementById('btn-nuevo-cliente').addEventListener('click', () => {
    document.getElementById('modal-nuevo-cliente').classList.remove('hidden');
});

function cerrarModalCliente() {
    document.getElementById('modal-nuevo-cliente').classList.add('hidden');
    ['nc-nombre','nc-telefono','nc-email','nc-direccion'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('modal-cliente-error').classList.add('hidden');
}

async function guardarNuevoCliente() {
    const nombre   = document.getElementById('nc-nombre').value.trim();
    const telefono = document.getElementById('nc-telefono').value.trim();
    const email    = document.getElementById('nc-email').value.trim();
    const direccion= document.getElementById('nc-direccion').value.trim();
    const errEl    = document.getElementById('modal-cliente-error');

    if (!nombre || !telefono) {
        errEl.textContent = 'El nombre y el teléfono son obligatorios.';
        errEl.classList.remove('hidden');
        return;
    }

    const btn = document.getElementById('btn-guardar-cliente');
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    try {
        const res  = await fetch(RUTA_CLIENTES, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({ nombre_completo: nombre, telefono, email, direccion }),
        });
        const data = await res.json();

        if (data.success) {
            seleccionarCliente(data.cliente.id, data.cliente.nombre_completo, data.cliente.telefono);
            cerrarModalCliente();
        } else {
            errEl.textContent = 'Error al guardar. Verifica los datos.';
            errEl.classList.remove('hidden');
        }
    } catch {
        errEl.textContent = 'Error de conexión. Intenta de nuevo.';
        errEl.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Guardar';
    }
}

// ═══════════════════════════════════════
// Alertas
// ═══════════════════════════════════════
function mostrarAlertaStock(msg) {
    const el = document.getElementById('alerta-stock');
    document.getElementById('alerta-stock-texto').textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 4000);
}

function ocultarAlertaStock() {
    document.getElementById('alerta-stock').classList.add('hidden');
}

// ═══════════════════════════════════════
// Validación antes de enviar
// ═══════════════════════════════════════
document.getElementById('reserva-form').addEventListener('submit', function (e) {
    const clienteId   = document.getElementById('input-cliente-id').value;
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin    = document.getElementById('fecha_fin').value;
    const items       = JSON.parse(document.getElementById('items-json').value || '[]');

    if (!clienteId) {
        e.preventDefault();
        mostrarAlertaStock('Debes seleccionar un cliente antes de confirmar.');
        return;
    }
    if (!fechaInicio || !fechaFin) {
        e.preventDefault();
        mostrarAlertaStock('Las fechas de inicio y fin son obligatorias.');
        return;
    }
    if (items.length === 0) {
        e.preventDefault();
        mostrarAlertaStock('Añade al menos un artículo al carrito.');
        return;
    }
});

// ═══════════════════════════════════════
// Utilidades
// ═══════════════════════════════════════
function escHtml(str) {
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// Restaurar carrito si hay old input
if (carrito.length > 0) {
    renderCarrito();
}
</script>
@endpush
@endsection
