@extends('layouts.app')

@section('title', 'Editar Artículo')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">

    {{-- Encabezado --}}
    <div class="mb-6 sm:mb-8 flex items-center gap-4">
        <a href="{{ route('articles.index') }}"
           class="p-2 text-secondary hover:text-primary hover:bg-surface-container-low rounded-xl transition-all">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-primary font-headline tracking-tight">Editar Artículo</h2>
            <p class="text-secondary font-body mt-1 text-sm sm:text-base">Modifica la información de <span class="font-semibold">{{ $article->nombre }}</span></p>
        </div>
    </div>

    {{-- Errores --}}
    @if($errors->any())
        <div class="mb-6 bg-error-container text-on-error-container rounded-xl px-4 py-3 flex items-start gap-3">
            <span class="material-symbols-outlined text-[18px] mt-0.5 flex-shrink-0">error</span>
            <ul class="text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('articles.update', $article) }}" enctype="multipart/form-data" class="space-y-6 sm:space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">

            {{-- Columna izquierda --}}
            <div class="lg:col-span-4 space-y-5 sm:space-y-6">

                {{-- Fotografía --}}
                <div class="bg-surface-container-lowest rounded-xl p-5 sm:p-6 shadow-sm ring-1 ring-slate-100">
                    <h3 class="text-sm font-semibold text-primary font-headline mb-4">Fotografía del Artículo</h3>

                    {{-- Imagen actual --}}
                    @if($article->ruta_imagen)
                        <div class="mb-4">
                            <img src="{{ $article->url_imagen }}" alt="{{ $article->nombre }}"
                                 class="w-full h-40 object-cover rounded-xl ring-1 ring-slate-100"/>
                            <p class="text-[11px] text-secondary mt-2 px-1">Imagen actual — sube una nueva para reemplazarla</p>
                        </div>
                    @endif

                    <div id="drop-zone"
                         class="border-2 border-dashed border-outline-variant rounded-xl p-6 flex flex-col items-center justify-center gap-3 bg-surface-container-low/50 hover:bg-surface-container-low transition-colors cursor-pointer group">
                        <div class="w-10 h-10 rounded-full bg-primary-fixed flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined">cloud_upload</span>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-bold text-on-surface">Click para cambiar imagen</p>
                            <p class="text-[10px] text-slate-400 mt-1">JPG, JPEG, PNG, WEBP — máx. 2MB</p>
                        </div>
                        <input id="imagen-input" name="imagen" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden"/>
                    </div>

                    {{-- Preview nueva imagen --}}
                    <div id="image-preview-container" class="mt-4 hidden">
                        <img id="image-preview" src="#" alt="Preview"
                             class="w-full h-40 object-cover rounded-xl ring-1 ring-slate-100"/>
                        <button type="button" id="remove-image"
                                class="mt-2 text-xs text-error hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">delete</span> Quitar selección
                        </button>
                    </div>
                </div>

                {{-- Estado --}}
                <div class="bg-surface-container-lowest rounded-xl p-5 sm:p-6 shadow-sm ring-1 ring-slate-100">
                    <label class="text-sm font-semibold text-primary font-headline block mb-3" for="estado">
                        Estado
                    </label>
                    <div class="relative">
                        <select id="estado" name="estado"
                                class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 appearance-none text-on-surface">
                            <option value="disponible" {{ old('estado', $article->estado) === 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="mantenimiento" {{ old('estado', $article->estado) === 'mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-sm">expand_more</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna derecha --}}
            <div class="lg:col-span-8 space-y-5 sm:space-y-6">

                {{-- Detalles --}}
                <div class="bg-surface-container-lowest rounded-xl p-5 sm:p-8 shadow-sm ring-1 ring-slate-100">
                    <div class="flex items-center gap-2 mb-5 sm:mb-6 text-primary">
                        <span class="material-symbols-outlined text-lg">description</span>
                        <h3 class="text-base font-bold font-headline">Detalles del Producto</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] sm:text-[12px] font-bold text-secondary mb-2 px-1 uppercase tracking-wider" for="nombre">
                                Nombre del Artículo <span class="text-error">*</span>
                            </label>
                            <input id="nombre" name="nombre" type="text"
                                   value="{{ old('nombre', $article->nombre) }}"
                                   class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('nombre') ring-2 ring-error/40 @enderror"/>
                        </div>

                        <div>
                            <label class="block text-[11px] sm:text-[12px] font-bold text-secondary mb-2 px-1 uppercase tracking-wider" for="categoria_id">
                                Categoría <span class="text-error">*</span>
                            </label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <select id="categoria_id" name="categoria_id"
                                            class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 appearance-none @error('categoria_id') ring-2 ring-error/40 @enderror">
                                        <option value="">Seleccionar categoría...</option>
                                        @foreach($categorias as $cat)
                                            <option value="{{ $cat->id }}" {{ old('categoria_id', $article->categoria_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-sm">unfold_more</span>
                                    </div>
                                </div>
                                @if(auth()->user()->rol === 'admin')
                                <button type="button" onclick="abrirModalCategoria()"
                                        title="Crear nueva categoría"
                                        class="flex-shrink-0 w-11 h-11 flex items-center justify-center bg-primary-fixed text-primary rounded-xl hover:bg-primary hover:text-white transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-[20px]">add</span>
                                </button>
                                @endif
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] sm:text-[12px] font-bold text-secondary mb-2 px-1 uppercase tracking-wider" for="descripcion">
                                Descripción breve
                            </label>
                            <textarea id="descripcion" name="descripcion" rows="3"
                                      class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none">{{ old('descripcion', $article->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Valores y Stock --}}
                <div class="bg-surface-container-lowest rounded-xl p-5 sm:p-8 shadow-sm ring-1 ring-slate-100">
                    <div class="flex items-center gap-2 mb-5 sm:mb-6 text-primary">
                        <span class="material-symbols-outlined text-lg">payments</span>
                        <h3 class="text-base font-bold font-headline">Valores y Stock</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                        <div>
                            <label class="block text-[11px] sm:text-[12px] font-bold text-secondary mb-2 px-1 uppercase tracking-wider" for="stock_total">
                                Stock Total <span class="text-error">*</span>
                            </label>
                            <div class="relative">
                                <input id="stock_total" name="stock_total" type="number" min="1"
                                       value="{{ old('stock_total', $article->stock_total) }}"
                                       class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 pr-12 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('stock_total') ring-2 ring-error/40 @enderror"/>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <span class="text-xs font-bold">UDS</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] sm:text-[12px] font-bold text-secondary mb-2 px-1 uppercase tracking-wider" for="precio_alquiler">
                                Precio Alquiler <span class="text-error">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-primary font-bold text-sm pointer-events-none">Bs.</span>
                                <input id="precio_alquiler" name="precio_alquiler" type="number" min="0" step="0.01"
                                       value="{{ old('precio_alquiler', $article->precio_alquiler) }}"
                                       class="w-full bg-surface-container-low border-transparent rounded-xl py-3 pl-12 pr-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all @error('precio_alquiler') ring-2 ring-error/40 @enderror"/>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-2">
                    <a href="{{ route('articles.index') }}"
                       class="px-6 sm:px-8 py-3 rounded-xl border-2 border-outline-variant text-secondary font-headline font-bold hover:bg-slate-100 transition-all active:scale-95 text-center text-sm sm:text-base">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 sm:px-8 py-3 rounded-xl bg-primary text-white font-headline font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:bg-primary-container transition-all active:scale-95 text-sm sm:text-base">
                        <span class="material-symbols-outlined text-xl">save</span>
                        Actualizar Artículo
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

{{-- Modal: Crear categoría rápida --}}
@if(auth()->user()->rol === 'admin')
<div id="modal-categoria" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarModalCategoria()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 sm:p-8 z-10">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-primary font-headline">Nueva Categoría</h3>
                <p class="text-xs text-secondary mt-0.5">Se agregará al selector automáticamente</p>
            </div>
            <button onclick="cerrarModalCategoria()" class="p-2 text-outline hover:text-primary hover:bg-surface-container-low rounded-xl transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div id="cat-error" class="hidden mb-4 bg-error-container text-on-error-container text-sm rounded-xl px-4 py-3 flex items-start gap-2">
            <span class="material-symbols-outlined text-[16px] mt-0.5 flex-shrink-0">error</span>
            <span id="cat-error-msg"></span>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-secondary uppercase tracking-wider mb-2 px-1">Nombre <span class="text-error">*</span></label>
                <input id="cat-nombre" type="text" placeholder="Ej. Mobiliario" autocomplete="off"
                       class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400"/>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-secondary uppercase tracking-wider mb-2 px-1">Descripción</label>
                <textarea id="cat-descripcion" rows="2" placeholder="Descripción breve..."
                          class="w-full bg-surface-container-low border-transparent rounded-xl py-3 px-4 text-on-surface text-sm focus:ring-2 focus:ring-primary/20 placeholder:text-slate-400 resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="cerrarModalCategoria()"
                        class="flex-1 py-3 rounded-xl border-2 border-outline-variant text-secondary font-headline font-bold hover:bg-slate-50 transition-all text-sm">Cancelar</button>
                <button type="button" id="btn-cat-guardar" onclick="guardarCategoria()"
                        class="flex-1 py-3 rounded-xl bg-primary text-white font-headline font-bold flex items-center justify-center gap-2 hover:bg-primary-container transition-all active:scale-95 text-sm">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span> Crear
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    const dropZone   = document.getElementById('drop-zone');
    const fileInput  = document.getElementById('imagen-input');
    const preview    = document.getElementById('image-preview');
    const previewBox = document.getElementById('image-preview-container');
    const removeBtn  = document.getElementById('remove-image');

    dropZone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) showPreview(fileInput.files[0]);
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('border-primary', 'bg-primary-fixed/20');
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('border-primary', 'bg-primary-fixed/20');
    });
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-primary', 'bg-primary-fixed/20');
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            showPreview(file);
        }
    });

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            previewBox.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    removeBtn.addEventListener('click', () => {
        fileInput.value = '';
        previewBox.classList.add('hidden');
    });

    // ── Modal categoría rápida ────────────────────────
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function abrirModalCategoria() {
        document.getElementById('cat-nombre').value = '';
        document.getElementById('cat-descripcion').value = '';
        document.getElementById('cat-error').classList.add('hidden');
        const m = document.getElementById('modal-categoria');
        m.classList.remove('hidden'); m.classList.add('flex');
        document.getElementById('cat-nombre').focus();
    }

    function cerrarModalCategoria() {
        const m = document.getElementById('modal-categoria');
        m.classList.add('hidden'); m.classList.remove('flex');
    }

    async function guardarCategoria() {
        const nombre = document.getElementById('cat-nombre').value.trim();
        const desc   = document.getElementById('cat-descripcion').value.trim();
        const errEl  = document.getElementById('cat-error');
        const btn    = document.getElementById('btn-cat-guardar');

        errEl.classList.add('hidden');
        if (!nombre) {
            document.getElementById('cat-error-msg').textContent = 'El nombre es obligatorio.';
            errEl.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">progress_activity</span> Creando...';

        try {
            const res  = await fetch('{{ route("categories.store") }}', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body:    JSON.stringify({ nombre, descripcion: desc }),
            });
            const data = await res.json();

            if (!res.ok) {
                const msg = data.errors ? Object.values(data.errors).flat().join(' ') : 'Error al crear.';
                document.getElementById('cat-error-msg').textContent = msg;
                errEl.classList.remove('hidden');
            } else {
                const select = document.getElementById('categoria_id');
                const option = new Option(data.categoria.nombre, data.categoria.id, true, true);
                select.add(option);
                cerrarModalCategoria();
            }
        } catch {
            document.getElementById('cat-error-msg').textContent = 'Error de conexión.';
            errEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check_circle</span> Crear';
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrarModalCategoria();
    });
</script>
@endpush
