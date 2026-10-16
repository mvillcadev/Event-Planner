@extends('layouts.app')

@section('title', 'Mensaje: ' . $message->asunto)

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Mensaje</h1>
        <div class="flex space-x-2">
            @if($message->estado === 'enviado' && $message->remitente_id === Auth::id())
                <a href="{{ route('messages.edit', $message) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Editar
                </a>
            @endif
            <a href="{{ route('messages.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Volver a Mensajes
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="text-xl font-semibold">{{ $message->asunto }}</h2>
            <div class="mt-2 flex flex-wrap items-center text-sm text-gray-600">
                <span class="mr-4">De: {{ $message->remitente ? $message->remitente->name : 'Sistema' }}</span>
                <span class="mr-4">Para: {{ $message->destinatario ? $message->destinatario->name : 'N/A' }}</span>
                <span class="mr-4">Tipo: {{ ucfirst($message->tipo) }}</span>
                <span class="mr-4">Estado: 
                    @php
                        $estadoColor = [
                            'borrador' => 'text-gray-600',
                            'enviado' => 'text-green-600',
                            'recibido' => 'text-blue-600',
                            'archivado' => 'text-purple-600',
                            'eliminado' => 'text-red-600',
                        ];
                    @endphp
                    <span class="{{ $estadoColor[$message->estado] ?? 'text-gray-600' }}">
                        {{ ucfirst($message->estado) }}
                    </span>
                </span>
                <span class="mr-4">Prioridad: 
                    @php
                        $prioridadColor = [
                            'urgente' => 'text-red-600 font-semibold',
                            'alta' => 'text-orange-600',
                            'media' => 'text-blue-600',
                            'baja' => 'text-green-600',
                        ];
                    @endphp
                    <span class="{{ $prioridadColor[$message->prioridad] ?? 'text-gray-600' }}">
                        {{ ucfirst($message->prioridad) }}
                    </span>
                </span>
            </div>
        </div>

        <div class="px-6 py-4">
            <div class="prose max-w-none">
                <p class="text-gray-800 whitespace-pre-wrap">{{ $message->contenido }}</p>
            </div>
        </div>

        @if($message->nota_interna)
            <div class="px-6 py-4 bg-yellow-50 border-t">
                <h3 class="text-sm font-semibold text-yellow-800">Nota Interna</h3>
                <p class="text-sm text-yellow-700">{{ $message->nota_interna }}</p>
            </div>
        @endif

        @if($message->tarea())
            <div class="px-6 py-4 bg-blue-50 border-t">
                <h3 class="text-sm font-semibold text-blue-800">Asociado a Tarea</h3>
                <p class="text-sm text-blue-700">
                    <a href="{{ route('tasks.show', $message->tarea()) }}" class="font-medium">
                        {{ $message->tarea()->titulo }}
                    </a>
                </p>
            </div>
        @endif

        @if($message->estado === 'recibido' && $message->destinatario_id === Auth::id() && !$message->leido)
            <div class="px-6 py-4 bg-yellow-50 border-t">
                <form method="POST" action="{{ route('messages.marcarComoLeido', $message) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        Marcar como Leído
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection