@extends('layouts.app')

@section('title', 'Estadísticas de Tareas')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Estadísticas de Tareas</h1>
        <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Volver a Tareas
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Tarjetas de resumen -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total de Tareas</h3>
            <p class="text-4xl font-bold text-blue-600">{{ $estadisticas['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Pendientes</h3>
            <p class="text-4xl font-bold text-yellow-600">{{ $estadisticas['pendientes'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">En Progreso</h3>
            <p class="text-4xl font-bold text-blue-600">{{ $estadisticas['en_progreso'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Completadas</h3>
            <p class="text-4xl font-bold text-green-600">{{ $estadisticas['completados'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Vencidas</h3>
            <p class="text-4xl font-bold text-red-600">{{ $estadisticas['vencidos'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de distribución por prioridad -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Tareas por Prioridad</h2>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    @foreach($estadisticas['por_prioridad'] as $prioridad => $cantidad)
                        @php
                            $colorClase = [
                                'urgente' => 'bg-red-500',
                                'alta' => 'bg-orange-500',
                                'media' => 'bg-blue-500',
                                'baja' => 'bg-green-500',
                            ];
                            $porcentaje = $estadisticas['total'] > 0 ? ($cantidad / $estadisticas['total']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($prioridad) }}</span>
                                <span class="text-sm text-gray-600">{{ $cantidad }} ({{ number_format($porcentaje, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colorClase[$prioridad] ?? 'bg-gray-500' }} h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Gráfico de distribución por tipo de tarea -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Tareas por Tipo</h2>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">
                    @foreach($estadisticas['por_tipo'] as $tipo => $cantidad)
                        @php
                            $colorClase = [
                                'tarea' => 'bg-indigo-500',
                                'hito' => 'bg-purple-500',
                                'reunion' => 'bg-pink-500',
                                'recordatorio' => 'bg-cyan-500',
                            ];
                            $porcentaje = $estadisticas['total'] > 0 ? ($cantidad / $estadisticas['total']) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($tipo) }}</span>
                                <span class="text-sm text-gray-600">{{ $cantidad }} ({{ number_format($porcentaje, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colorClase[$tipo] ?? 'bg-gray-500' }} h-2 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de distribución -->
    <div class="bg-white rounded-lg shadow mt-6">
        <div class="px-6 py-4 border-b">
            <h2 class="text-xl font-semibold">Resumen de Distribución</h2>
        </div>
        <div class="px-6 py-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Porcentaje</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tendencia</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pendientes
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $estadisticas['pendientes'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $estadisticas['total'] > 0 ? number_format(($estadisticas['pendientes'] / $estadisticas['total']) * 100, 1) : 0 }}%
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-green-600">↗️</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    En Progreso
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $estadisticas['en_progreso'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $estadisticas['total'] > 0 ? number_format(($estadisticas['en_progreso'] / $estadisticas['total']) * 100, 1) : 0 }}%
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-yellow-600">→</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Completadas
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $estadisticas['completados'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $estadisticas['total'] > 0 ? number_format(($estadisticas['completados'] / $estadisticas['total']) * 100, 1) : 0 }}%
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-green-600">↗️</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Vencidas
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $estadisticas['vencidos'] }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $estadisticas['total'] > 0 ? number_format(($estadisticas['vencidos'] / $estadisticas['total']) * 100, 1) : 0 }}%
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-red-600">⚠️</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection