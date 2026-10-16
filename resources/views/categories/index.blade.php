@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-on-secondary-fixed-variant uppercase tracking-widest font-label">Gestión de Inventario</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-primary mt-1 font-headline">Categorías</h2>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-3 bg-primary text-white rounded-xl font-headline font-bold text-sm shadow-md hover:bg-primary-container transition-all active:scale-95 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Nueva Categoría
        </button>
    </div>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-secondary-container text-on-secondary-container text-sm rounded-xl px-4 py-3">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-error-container text-on-error-container text-sm rounded-xl px-4 py-3">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabla --}}
    <div class="bg-surface-container-lowest rounded-2xl ring-1 ring-slate-100 overflow-hidden">

        @if($categorias->isEmpty())
            <div class="text-center py-16 text-secondary">
                <span class="material-symbols-outlined text-6xl mb-4 block opacity-30">category</span>
                <p class="font-headline font-bold text-lg text-primary mb-1">Sin categorías registradas</p>
                <p class="text-sm mb-6">Crea la primera categoría para organizar el inventario.</p>
                <button onclick="abrirModalCrear()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary-container transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Crear primera categoría
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[480px]">
                    <thead>
                        <tr class="border-b border-surface-container-low bg-surface-container-low/50">
                            <th class="py-3 px-4 sm:px-6 font-label text-xs font-bold text-secondary uppercase tracking-wider">#</th>
                            <th class="py-3 px-4 font-label text-xs font-bold text-secondary uppercase tracking-wider">Nombre</th>
                            <th class="py-3 px-4 font-label text-xs font-bold text-secondary uppercase tracking-wider hidden sm:table-cell">Descripción</th>
                            <th class="py-3 px-4 font-label text-xs font-bold text-secondary uppercase tracking-wider text-center">Artículos</th>
                            <th class="py-3 px-4 sm:px-6 font-label text-xs font-bold text-secondary uppercase tracking-wider text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low">
                        @foreach($categorias as $categoria)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="py-4 px-4 sm:px-6 text-xs text-secondary font-mono">{{ $categoria->id }}</td>
                            <td class="py-4 px-4">
                                <span class="font-semibold text-on-surface text-sm">{{ $categoria->nombre }}</span>
                            </td>
                            <td class="py-4 px-4 hidden sm:table-cell">
                                <span class="text-sm text-secondary line-clamp-1">{{ $categoria->descripcion ?? '—' }}</span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold">
                                    {{ $categoria->articulos_count }}
                                </span>
                            </td>
                            <td class="py-4 px-4 sm:px-6 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- Editar --}}
                                    <button
                                        onclick="abrirModalEditar({{ $categoria->id }}, '{{ addslashes($categoria->nombre) }}', '{{ addslashes($categoria->descripcion ?? '') }}')"
                                        class="p-2 text-outline hover:text-primary hover:bg-primary-fixed rounded-lg transition-all"
                                        title="Editar">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    {{-- Eliminar --}}
                                    @if($categoria->articulos_count === 0)
                                        <form method="POST" action="{{ route('categories.destroy', $categoria) }}"
                                              onsubmit="return confirm('¿Eliminar la categoría «{{ $categoria->nombre }}»?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 text-outline hover:text-error hover:bg-error-container rounded-lg transition-all"
                                                    title="Eliminar">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @else
                                        <button disabled
                                                title="Tiene artículos asociados"
                                                class="p-2 text-slate-300 cursor-not-allowed rounded-lg">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL: CREAR CATEGORÍA
════════════════════════════════════════════════ --}}
<div id="modal-crear" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarModalCrear()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 sm:p-8 z-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-primary font-headline">Nueva Categoría</h3>
                <p class="text-sm text-secondary mt-0.5">Completa los campos requeridos</p>
            </div>
            <button onclick="cerrarModalCrear()" class="p-2 text-outline hover:text-primary hover:bg-surface-container-low rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="crear-error" class="hidden mb-4 bg-error-container text-on-error-container text-sm rounded-xl px-4 py-3 flex items-start gap-2">
            <span class="material-symbols-outlined text-[16px] mt-0.5 flex-shrink-0">error</span>
            <span id="crear-error-msg"></span>
        </div>

        <form id="form-crear" class="space-y-4" onsubmit="submitCrear(event)">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-secondary uppercase tracking-wider mb-2 px-1">
                    Nombre <span class="text-error">*</span>
                </label>
                <input id="crear-nombre" type="text" placeholder="Ej. Mobiliario"
                       class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder:text-slate-400"
                       autocomplete="off"/>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-secondary uppercase tracking-wider mb-2 px-1">
                    Descripción
                </label>
                <textarea id="crear-descripcion" rows="3" placeholder="Descripción breve de la categoría..."
                          class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all placeholder:text-slate-400 resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="cerrarModalCrear()"
                        class="flex-1 py-3 rounded-xl border-2 border-outline-variant text-secondary font-headline font-bold hover:bg-slate-50 transition-all text-sm">
                    Cancelar
                </button>
                <button type="submit" id="btn-crear"
                        class="flex-1 py-3 rounded-xl bg-primary text-white font-headline font-bold flex items-center justify-center gap-2 hover:bg-primary-container transition-all active:scale-95 text-sm">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL: EDITAR CATEGORÍA
════════════════════════════════════════════════ --}}
<div id="modal-editar" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarModalEditar()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 sm:p-8 z-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-primary font-headline">Editar Categoría</h3>
                <p class="text-sm text-secondary mt-0.5">Modifica los campos necesarios</p>
            </div>
            <button onclick="cerrarModalEditar()" class="p-2 text-outline hover:text-primary hover:bg-surface-container-low rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="editar-error" class="hidden mb-4 bg-error-container text-on-error-container text-sm rounded-xl px-4 py-3 flex items-start gap-2">
            <span class="material-symbols-outlined text-[16px] mt-0.5 flex-shrink-0">error</span>
            <span id="editar-error-msg"></span>
        </div>

        <form id="form-editar" class="space-y-4" onsubmit="submitEditar(event)">
            <input type="hidden" id="editar-id"/>
            <div>
                <label class="block text-[11px] font-bold text-secondary uppercase tracking-wider mb-2 px-1">
                    Nombre <span class="text-error">*</span>
                </label>
                <input id="editar-nombre" type="text"
                       class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                       autocomplete="off"/>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-secondary uppercase tracking-wider mb-2 px-1">
                    Descripción
                </label>
                <textarea id="editar-descripcion" rows="3"
                          class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="cerrarModalEditar()"
                        class="flex-1 py-3 rounded-xl border-2 border-outline-variant text-secondary font-headline font-bold hover:bg-slate-50 transition-all text-sm">
                    Cancelar
                </button>
                <button type="submit" id="btn-editar"
                        class="flex-1 py-3 rounded-xl bg-primary text-white font-headline font-bold flex items-center justify-center gap-2 hover:bg-primary-container transition-all active:scale-95 text-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // ── Helpers de modal ─────────────────────────────
    function abrirModal(id)  { const m = document.getElementById(id); m.classList.remove('hidden'); m.classList.add('flex'); }
    function cerrarModal(id) { const m = document.getElementById(id); m.classList.add('hidden'); m.classList.remove('flex'); }

    function abrirModalCrear()  { limpiarError('crear'); document.getElementById('crear-nombre').value = ''; document.getElementById('crear-descripcion').value = ''; abrirModal('modal-crear'); document.getElementById('crear-nombre').focus(); }
    function cerrarModalCrear() { cerrarModal('modal-crear'); }

    function abrirModalEditar(id, nombre, descripcion) {
        limpiarError('editar');
        document.getElementById('editar-id').value       = id;
        document.getElementById('editar-nombre').value   = nombre;
        document.getElementById('editar-descripcion').value = descripcion;
        abrirModal('modal-editar');
        document.getElementById('editar-nombre').focus();
    }
    function cerrarModalEditar() { cerrarModal('modal-editar'); }

    function mostrarError(prefijo, msg) {
        const el = document.getElementById(prefijo + '-error');
        document.getElementById(prefijo + '-error-msg').textContent = msg;
        el.classList.remove('hidden');
    }
    function limpiarError(prefijo) {
        document.getElementById(prefijo + '-error').classList.add('hidden');
    }

    // ── Crear categoría (AJAX) ───────────────────────
    async function submitCrear(e) {
        e.preventDefault();
        limpiarError('crear');
        const btn    = document.getElementById('btn-crear');
        const nombre = document.getElementById('crear-nombre').value.trim();
        const desc   = document.getElementById('crear-descripcion').value.trim();

        if (!nombre) { mostrarError('crear', 'El nombre es obligatorio.'); return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span> Guardando...';

        try {
            const res  = await fetch('{{ route("categories.store") }}', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    JSON.stringify({ nombre, descripcion: desc }),
            });
            const data = await res.json();

            if (!res.ok) {
                const errores = data.errors ? Object.values(data.errors).flat().join(' ') : 'Error al guardar.';
                mostrarError('crear', errores);
            } else {
                cerrarModalCrear();
                window.location.reload();
            }
        } catch (err) {
            mostrarError('crear', 'Error de conexión. Intenta de nuevo.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check_circle</span> Guardar';
        }
    }

    // ── Editar categoría (AJAX) ──────────────────────
    async function submitEditar(e) {
        e.preventDefault();
        limpiarError('editar');
        const id     = document.getElementById('editar-id').value;
        const btn    = document.getElementById('btn-editar');
        const nombre = document.getElementById('editar-nombre').value.trim();
        const desc   = document.getElementById('editar-descripcion').value.trim();

        if (!nombre) { mostrarError('editar', 'El nombre es obligatorio.'); return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span> Actualizando...';

        try {
            const res  = await fetch(`/categories/${id}`, {
                method:  'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    JSON.stringify({ nombre, descripcion: desc }),
            });
            const data = await res.json();

            if (!res.ok) {
                const errores = data.errors ? Object.values(data.errors).flat().join(' ') : 'Error al actualizar.';
                mostrarError('editar', errores);
            } else {
                cerrarModalEditar();
                window.location.reload();
            }
        } catch (err) {
            mostrarError('editar', 'Error de conexión. Intenta de nuevo.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">save</span> Actualizar';
        }
    }

    // Cerrar modales con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { cerrarModalCrear(); cerrarModalEditar(); }
    });
</script>
@endpush
