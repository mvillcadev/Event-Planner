@extends('layouts.app')

@section('title', 'Gestión de Mensajes')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestión de Mensajes</h1>
        <a href="{{ route('messages.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Nuevo Mensaje
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asunto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">De</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Para</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-700 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leído</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($mensajes as $mensaje)
                <tr class="hover:bg-gray-50 {{ $mensaje->esUrgente() ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $mensaje->asunto }}</div>
                        @if($mensaje->tarea())
                            <div class="text-xs text-gray-500">Tarea: {{ $mensaje->tarea()->titulo }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $mensaje->remitente ? $mensaje->remitente->name : 'Sistema' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $mensaje->destinatario ? $mensaje->destinatario->name : 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($mensaje->tipo) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $estadoColor = [
                                'borrador' => 'bg-gray-100 text-gray-800',
                                'enviado' => 'bg-green-100 text-green-800',
                                'recibido' => 'bg-blue-100 text-blue-800',
                                'archivado' => 'bg-purple-100 text-purple-800',
                                'eliminado' => 'bg-red-100 text-red-800',
                            ];
                            $estadoTexto = [
                                'borrador' => 'Borrador',
                                'enviado' => 'Enviado',
                                'recibido' => 'Recibido',
                                'archivado' => 'Archivado',
                                'eliminado' => 'Eliminado',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $estadoColor[$mensaje->estado] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $estadoTexto[$mensaje->estado] ?? $mensaje->estado }}
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
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $prioridadColor[$mensaje->prioridad] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($mensaje->prioridad) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($mensaje->leido)
                            <span class="text-green-600 text-sm">✓ Leído</span>
                        @else
                            <span class="text-yellow-600 text-sm font-semibold">● No leído</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('messages.show', $mensaje) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Ver</a>
                        @if($mensaje->estado === 'borrador' || $mensaje->remitente_id === Auth::id())
                            <a href="{{ route('messages.edit', $mensaje) }}" class="text-blue-600 hover:text-blue-900 mr-2">Editar</a>
                        @endif
                        @if($mensaje->estado === 'enviado' && $mensaje->remitente_id === Auth::id())
                            <form method="POST" action="{{ route('messages.archivar', $mensaje) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-purple-600 hover:text-purple-900 mr-2">Archivar</button>
                            </form>
                        @endif
                        @if($mensaje->estado === 'recibido' && $mensaje->destinatario_id === Auth::id())
                            <form method="POST" action="{{ route('messages.marcarComoLeido', $mensaje) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Marcar como leído</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $mensajes->links() }}
    </div>
</div>
@endsection