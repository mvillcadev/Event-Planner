@php($layout = Auth::user()->rol === 'admin' ? 'layouts.app' : 'layouts.employee')
@extends($layout)

@section('title', 'Editar Cliente')

@section('content')
<div class="p-6 lg:p-8 max-w-4xl mx-auto space-y-8">

    {{-- Cabecera con retorno --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-secondary hover:text-primary mb-2 transition-colors">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Volver a la lista de clientes
            </a>
            <h2 class="text-3xl font-extrabold text-primary tracking-tight font-headline">Editar Cliente</h2>
            <p class="text-secondary mt-1 text-sm">Actualiza los datos de contacto y dirección de {{ $client->nombre_completo }}.</p>
        </div>
    </div>

    {{-- Formulario de edición --}}
    <div class="bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-sm">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary px-1">Nombre Completo *</label>
                    <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $client->nombre_completo) }}"
                           placeholder="Ej. María López"
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('nombre_completo') ring-2 ring-error @enderror">
                    @error('nombre_completo') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary px-1">CI / NIT</label>
                    <input type="text" name="ci_nit" value="{{ old('ci_nit', $client->ci_nit) }}"
                           placeholder="Ej. 1234567 CB"
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('ci_nit') ring-2 ring-error @enderror">
                    @error('ci_nit') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary px-1">Número de Celular *</label>
                    <input type="tel" name="telefono" value="{{ old('telefono', $client->telefono) }}"
                           placeholder="+591 70000000"
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('telefono') ring-2 ring-error @enderror">
                    @error('telefono') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary px-1">Correo Electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}"
                           placeholder="cliente@ejemplo.com"
                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('email') ring-2 ring-error @enderror">
                    @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-secondary px-1">Dirección de Entrega</label>
                <textarea name="direccion" rows="3" placeholder="Calle, número, zona y referencias..."
                          class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none resize-none">{{ old('direccion', $client->direccion) }}</textarea>
                @error('direccion') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('clients.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl text-sm hover:bg-slate-200 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-8 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow-md hover:bg-primary-container hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
