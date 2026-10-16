@extends('layouts.app')

@section('title', 'Gestión de Tareas')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestión de Tareas</h1>
        <div class="flex space-x-2">
            <a href="{{ route('tasks.estadisticas') }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">
                Estadísticas
            </a>
            <a href="{{ route('tasks.verificarDependencias') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
                Verificar Dependencias
            </a>
            <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Nueva Tarea
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold">Total Tareas</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $tareasPendientes + $tareasEnProgreso + $tareasCompletadas }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold">Pendientes</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $tareasPendientes }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold">En Progreso</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $tareasEnProgreso }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold">Completadas</h3>
            <p class="text-3xl font-bold text-green-600">{{ $tareasCompletadas }}</p>
        </div>
    </div>

    <!-- Lista de tareas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignado a</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tareas as $tarea)
                <tr class="hover:bg-gray-50 {{ $tarea->estaVencida() ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $tarea->titulo }}</div>
                        @if($tarea->esHito())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                Hito
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $tarea->asignado ? $tarea->asignado->name : 'Sin asignar' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $estadoColor[$tarea->estado] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $estadoTexto[$tarea->estado] ?? $tarea->estado }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $prioridadColor = [
                                'urgente' => 'bg-red-100 text-red-800',
                                'alta' => 'bg-orange-100 text-orange-800',
                                'media' => 'bg-blue-100 text-blue-800',
                                'baja' => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $prioridadColor[$tarea->prioridad] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($tarea->prioridad) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $tarea->fecha_vencimiento->format('d/m/Y') }}</div>
                        @if($tarea->estaVencida())
                            <span class="text-xs text-red-600 font-semibold">VENCIDA</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('tasks.show', $tarea) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</a>
                        @if($tarea->puedeSerEditada())
                            <a href="{{ route('tasks.edit', $tarea) }}" class="text-blue-600 hover:text-blue-900 mr-2">Editar</a>
                        @endif
                        @if($tarea->estado === 'pendiente')
                            <form method="POST" action="{{ route('tasks.iniciar', $tarea) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Iniciar</button>
                            </form>
                        @elseif($tarea->estado === 'en_progreso')
                            <form method="POST" action="{{ route('tasks.completar', $tarea) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-purple-600 hover:text-purple-900 mr-2">Completar</button>
                            </form>
                        @endif
                        @if($tarea->puedeSerEditada())
                            <form method="POST" action="{{ route('tasks.cancelar', $tarea) }}" class="inline">
                                @csrf
                                @method('POST')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro?')">Cancelar</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tareas->links() }}
    </div>
</div>
@endsection