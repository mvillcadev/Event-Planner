@extends('layouts.app')

@section('title', 'Detalle de Tarea: ' . $task->titulo)

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Detalle de Tarea</h1>
        <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Volver a Tareas
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Información de la tarea -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Información de la Tarea</h2>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Título</label>
                        <p class="text-sm text-gray-900">{{ $task->titulo }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <p class="text-sm text-gray-900">{{ $task->descripcion ?? 'No especificada' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                            <p class="text-sm text-gray-900">{{ $task->fecha_vencimiento->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                            <p class="text-sm text-gray-900">{{ $task->fecha_inicio ? $task->fecha_inicio->format('d/m/Y') : 'No especificada' }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha Estimada de Finalización</label>
                        <p class="text-sm text-gray-900">{{ $task->fecha_fin_estimada ? $task->fecha_fin_estimada->format('d/m/Y') : 'No especificada' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de asignación -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Asignación</h2>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asignado a</label>
                        <p class="text-sm text-gray-900">{{ $task->asignado ? $task->asignado->name : 'Sin asignar' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Creado por</label>
                        <p class="text-sm text-gray-900">{{ $task->creador ? $task->creador->name : 'Sistema' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Tarea</label>
                        <p class="text-sm text-gray-900">{{ $task->tipo_tarea === 'tarea' ? 'Tarea Normal' : ($task->tipo_tarea === 'hito' ? 'Hito' : ($task->tipo_tarea === 'reunion' ? 'Reunión' : 'Recordatorio')) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Prioridad</label>
                        @php
                            $prioridadColor = [
                                'urgente' => 'bg-red-100 text-red-800',
                                'alta' => 'bg-orange-100 text-orange-800',
                                'media' => 'bg-blue-100 text-blue-800',
                                'baja' => 'bg-green-100 text-green-800',
                            ];
                            $prioridadTexto = [
                                'urgente' => 'Urgente',
                                'alta' => 'Alta',
                                'media' => 'Media',
                                'baja' => 'Baja',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $prioridadColor[$task->prioridad] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $prioridadTexto[$task->prioridad] ?? $task->prioridad }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estado y dependencias -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Estado y Dependencias</h2>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado Actual</label>
                        @php
                            $estadoColor = [
                                'pendiente' => 'bg-yellow-100 text-yellow-800',
                                'en_progreso' => 'bg-blue-100 text-blue-800',
                                'completado' => 'bg-green-100 text-green-800',
                                'cancelado' => 'bg-gray-100 text-gray-800',
                            ];
                            $estadoTexto = [
                                'pendiente' => 'Pendiente',
                                'en_progreso' => 'En Progreso',
                                'completado' => 'Completado',
                                'cancelado' => 'Cancelado',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $estadoColor[$task->estado] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $estadoTexto[$task->estado] ?? $task->estado }}
                        </span>
                    </div>
                    @if($task->dependencia())
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dependencia de</label>
                            <p class="text-sm text-gray-900">
                                <a href="{{ route('tasks.show', $task->dependencia()) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $task->dependencia()->titulo }}
                                </a>
                            </p>
                        </div>
                    @endif
                    @if($task->dependientes()->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Depende de ({{ $task->dependientes()->count() }})</label>
                            <ul class="text-sm text-gray-900">
                                @foreach($task->dependientes() as $dependiente)
                                    <li>
                                        <a href="{{ route('tasks.show', $dependiente) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $dependiente->titulo }}
                                        </a>
                                        (@if($dependiente->puedeSerCompletada()) Pendiente @else Completado @endif)
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mensajes asociados -->
        <div class="bg-white rounded-lg shadow lg:col-span-2">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Mensajes Asociados ({{ $task->mensajes()->count() }})</h2>
            </div>
            <div class="px-6 py-4">
                @if($task->mensajes()->count() > 0)
                    <div class="space-y-3">
                        @foreach($task->mensajes()->latest()->take(10) as $mensaje)
                            <div class="border-l-4 {{ $mensaje->esUrgente() ? 'border-red-500 bg-red-50' : ($mensaje->leido ? 'border-green-500 bg-green-50' : 'border-yellow-500 bg-yellow-50') }} p-3 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-sm">{{ $mensaje->asunto }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">{{ $mensaje->contenido }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs {{ $mensaje->esUrgente() ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $mensaje->prioridad }} - {{ $mensaje->created_at->diffForHumans() }}
                                        </span>
                                        @if(!$mensaje->leido)
                                            <span class="block text-xs text-yellow-600 font-semibold">NO LEÍDO</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No hay mensajes asociados a esta tarea.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection