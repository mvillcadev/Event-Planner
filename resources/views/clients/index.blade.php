@php($layout = Auth::user()->rol === 'admin' ? 'layouts.app' : 'layouts.employee')
@extends($layout)

@section('title', 'Clientes')

@section('content')
<div class="p-6 lg:p-8 max-w-7xl mx-auto space-y-10">

    {{-- Cabecera --}}
    <section>
        <h2 class="text-3xl font-extrabold text-primary tracking-tight font-headline">Registro de Clientes</h2>
        <p class="text-secondary mt-1 text-sm">Gestiona la base de datos de clientes para eventos y alquileres.</p>
    </section>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-4 flex items-center gap-3">
        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Bento: Formulario + Stats --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

        {{-- Formulario --}}
        <div class="xl:col-span-7 bg-surface-container-lowest rounded-xl p-6 md:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center text-on-primary-container">
                    <span class="material-symbols-outlined">person_add</span>
                </div>
                <h3 class="text-xl font-bold text-primary">Nuevo Registro</h3>
            </div>

            <form method="POST" action="{{ route('clients.store') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-secondary px-1">Nombre Completo *</label>
                        <input type="text" name="nombre_completo" value="{{ old('nombre_completo') }}"
                               placeholder="Ej. María López"
                               class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('nombre_completo') ring-2 ring-error @enderror">
                        @error('nombre_completo') <p class="text-xs text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-secondary px-1">CI / NIT</label>
                        <input type="text" name="ci_nit" value="{{ old('ci_nit') }}"
                               placeholder="Ej. 1234567 CB"
                               class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('ci_nit') ring-2 ring-error @enderror">
                        @error('ci_nit') <p class="text-xs text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-secondary px-1">Número de Celular *</label>
                        <input type="tel" name="telefono" value="{{ old('telefono') }}"
                               placeholder="+591 70000000"
                               class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('telefono') ring-2 ring-error @enderror">
                        @error('telefono') <p class="text-xs text-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-secondary px-1">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="cliente@ejemplo.com"
                               class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none @error('email') ring-2 ring-error @enderror">
                        @error('email') <p class="text-xs text-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-secondary px-1">Dirección de Entrega</label>
                    <textarea name="direccion" rows="2" placeholder="Calle, número, zona y referencias..."
                              class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 outline-none resize-none">{{ old('direccion') }}</textarea>
                </div>
                <div class="pt-2">
                    <button type="submit"
                            class="px-8 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Registrar Cliente
                    </button>
                </div>
            </form>
        </div>

        {{-- Stats --}}
        <div class="xl:col-span-5 flex flex-col gap-5">
            <div class="bg-primary-container text-on-primary-container p-8 rounded-xl relative overflow-hidden flex-1 shadow-sm">
                <div class="relative z-10">
                    <h4 class="text-base font-bold opacity-80">Total Clientes</h4>
                    <div class="text-5xl font-extrabold mt-2">{{ $totalClientes }}</div>
                    <p class="text-sm mt-3 font-medium flex items-center gap-2 opacity-80">
                        <span class="material-symbols-outlined text-sm">groups</span>
                        Registrados en el sistema
                    </p>
                </div>
                <span class="material-symbols-outlined absolute -right-6 -bottom-6 text-[130px] opacity-10">groups</span>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-outline-variant/10">
                <h4 class="font-bold text-primary mb-3 text-sm">Acciones Rápidas</h4>
                <div class="space-y-2">
                    <a href="{{ route('reservas.create') }}"
                       class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-secondary text-lg">event_available</span>
                        <div>
                            <p class="text-sm font-bold text-primary">Nueva Reserva</p>
                            <p class="text-xs text-secondary">Crear reserva para un cliente</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-surface-container-low">
                        <span class="material-symbols-outlined text-secondary text-lg">search</span>
                        <div>
                            <p class="text-sm font-bold text-primary">Buscar cliente</p>
                            <p class="text-xs text-secondary">Usa la tabla inferior para filtrar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de clientes --}}
    <section class="space-y-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-2xl font-bold text-primary font-headline">Últimos Clientes Registrados</h3>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low border-b border-outline-variant/10">
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Cliente</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary">Contacto</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden md:table-cell">Dirección</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary hidden sm:table-cell">Reservas</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-secondary text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/5">
                        @forelse($clientes as $cliente)
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-secondary-fixed flex items-center justify-center font-bold text-on-secondary-fixed-variant text-xs shrink-0">
                                        {{ strtoupper(substr($cliente->nombre_completo, 0, 1)) }}{{ strtoupper(substr(explode(' ', $cliente->nombre_completo)[1] ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-primary text-sm">{{ $cliente->nombre_completo }}</p>
                                        <p class="text-xs text-secondary">ID: #{{ $cliente->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-primary">{{ $cliente->telefono }}</p>
                                @if($cliente->ci_nit)
                                <p class="text-xs text-secondary font-semibold">CI: {{ $cliente->ci_nit }}</p>
                                @endif
                                @if($cliente->email)
                                <p class="text-xs text-secondary">{{ $cliente->email }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-secondary hidden md:table-cell max-w-[200px] truncate">
                                {{ $cliente->direccion ?? '—' }}
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <span class="px-2 py-1 bg-secondary-fixed text-on-secondary-fixed-variant text-xs font-bold rounded-full">
                                    {{ $cliente->reservas_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('clients.show', $cliente) }}"
                                       title="Ver Historial"
                                       class="p-2 text-secondary hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">history</span>
                                    </a>
                                    <a href="{{ route('clients.edit', $cliente) }}"
                                       title="Editar Cliente"
                                       class="p-2 text-secondary hover:text-primary hover:bg-surface-container rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    @if(Auth::user()->rol === 'admin' && $cliente->reservas_count === 0)
                                    <form method="POST" action="{{ route('clients.destroy', $cliente) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar Cliente" class="p-2 text-secondary hover:text-error hover:bg-red-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                <span class="material-symbols-outlined text-4xl block mb-2">person_search</span>
                                No hay clientes registrados aún.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($clientes->hasPages())
            <div class="px-6 py-4 border-t border-outline-variant/10 bg-slate-50/50 flex items-center justify-between">
                <p class="text-xs text-secondary font-medium">
                    Mostrando {{ $clientes->firstItem() }}–{{ $clientes->lastItem() }} de {{ $clientes->total() }} clientes
                </p>
                <div class="flex gap-2">
                    @if($clientes->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 border border-outline-variant/20 cursor-not-allowed">
                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                    </span>
                    @else
                    <a href="{{ $clientes->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-secondary border border-outline-variant/20 hover:bg-white transition-colors">
                        <span class="material-symbols-outlined text-lg">chevron_left</span>
                    </a>
                    @endif

                    @if($clientes->hasMorePages())
                    <a href="{{ $clientes->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-secondary border border-outline-variant/20 hover:bg-white transition-colors">
                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                    </a>
                    @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 border border-outline-variant/20 cursor-not-allowed">
                        <span class="material-symbols-outlined text-lg">chevron_right</span>
                    </span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection
