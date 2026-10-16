@extends('layouts.app')

@section('title', 'Editar Tarea: ' . $task->titulo)

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Editar Tarea</h1>
        <a href="{{ route('tasks.show', $task) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Ver Tarea
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="titulo" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" id="titulo" name="titulo" value="{{ old('titulo', $task->titulo) }}" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('descripcion', $task->descripcion) }}</textarea>
                </div>

                <div>
                    <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $task->fecha_vencimiento->format('Y-m-d')) }}" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', $task->fecha_inicio ? $task->fecha_inicio->format('Y-m-d') : '') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="fecha_fin_estimada" class="block text-sm font-medium text-gray-700">Fecha Estimada de Finalización</label>
                    <input type="date" id="fecha_fin_estimada" name="fecha_fin_estimada" value="{{ old('fecha_fin_estimada', $task->fecha_fin_estimada ? $task->fecha_fin_estimada->format('Y-m-d') : '') }}" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="prioridad" class="block text-sm font-medium text-gray-700">Prioridad</label>
                    <select id="prioridad" name="prioridad" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="baja" {{ old('prioridad', $task->prioridad) === 'baja' ? 'selected' : '' }}>Baja</option>
                        <option value="media" {{ old('prioridad', $task->prioridad) === 'media' ? 'selected' : '' }}>Media</option>
                        <option value="alta" {{ old('prioridad', $task->prioridad) === 'alta' ? 'selected' : '' }}>Alta</option>
                        <option value="urgente" {{ old('prioridad', $task->prioridad) === 'urgente' ? 'selected' : '' }}>Urgente</option>
                    </select>
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                    <select id="estado" name="estado" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="pendiente" {{ old('estado', $task->estado) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_progreso" {{ old('estado', $task->estado) === 'en_progreso' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completado" {{ old('estado', $task->estado) === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ old('estado', $task->estado) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div>
                    <label for="tipo_tarea" class="block text-sm font-medium text-gray-700">Tipo de Tarea</label>
                    <select id="tipo_tarea" name="tipo_tarea" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="tarea" {{ old('tipo_tarea', $task->tipo_tarea) === 'tarea' ? 'selected' : '' }}>Tarea Normal</option>
                        <option value="hito" {{ old('tipo_tarea', $task->tipo_tarea) === 'hito' ? 'selected' : '' }}>Hito</option>
                        <option value="reunion" {{ old('tipo_tarea', $task->tipo_tarea) === 'reunion' ? 'selected' : '' }}>Reunión</option>
                        <option value="recordatorio" {{ old('tipo_tarea', $task->tipo_tarea) === 'recordatorio' ? 'selected' : '' }}>Recordatorio</option>
                    </select>
                </div>

                <div>
                    <label for="asignado_a" class="block text-sm font-medium text-gray-700">Asignar a</label>
                    <select id="asignado_a" name="asignado_a" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">No asignar</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id }}" {{ old('asignado_a', $task->asignado_a) === $empleado->id ? 'selected' : '' }}>
                                {{ $empleado->name }} ({{ $empleado->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="dependencia_tarea_id" class="block text-sm font-medium text-gray-700">Dependencia de (opcional)</label>
                    <select id="dependencia_tarea_id" name="dependencia_tarea_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Ninguna</option>
                        @foreach($tareasPadre as $tareaPadre)
                            <option value="{{ $tareaPadre->id }}" {{ old('dependencia_tarea_id', $task->dependencia_tarea_id) === $tareaPadre->id ? 'selected' : '' }}>
                                {{ $tareaPadre->titulo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="window.location.href='{{ route('tasks.show', $task) }}'" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                    Cancelar
                </button>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                    Actualizar Tarea
                </button>
            </div>
        </form>
    </div>
</div>
@endsection