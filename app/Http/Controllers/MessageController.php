<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $mensajes = Message::with(['remitente', 'destinatario', 'tarea'])
            ->latest()
            ->paginate(15);

        return view('messages.index', compact('mensajes'));
    }

    public function create()
    {
        $empleados = User::whereHas('roles', function ($query) {
            $query->where('name', 'empleado');
        })->get(['id', 'name', 'email']);

        $tareas = Task::all(['id', 'titulo']);

        return view('messages.create', compact('empleados', 'tareas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|in:email,notificacion,recordatorio,tarea,interno,externo',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'estado' => 'required|in:borrador,enviado,recibido,archivado,eliminado',
            'remitente_id' => 'required|exists:users,id',
            'destinatario_id' => 'required|exists:users,id',
            'tarea_id' => 'nullable|exists:tareas,id',
            'contacto_externo' => 'boolean',
            'leido' => 'boolean',
            'archivado' => 'boolean',
            'nota_interna' => 'nullable|string',
        ]);

        Message::create($data);

        return redirect()->route('messages.index')
            ->with('success', 'Mensaje creado correctamente.');
    }

    public function show(Message $message)
    {
        $message->load(['remitente', 'destinatario', 'tarea']);

        if (!$message->leido && $message->destinatario_id === Auth::id()) {
            $message->marcarComoLeido();
        }

        return view('messages.show', compact('message'));
    }

    public function edit(Message $message)
    {
        $empleados = User::whereHas('roles', function ($query) {
            $query->where('name', 'empleado');
        })->get(['id', 'name', 'email']);

        $tareas = Task::all(['id', 'titulo']);

        return view('messages.edit', compact('message', 'empleados', 'tareas'));
    }

    public function update(Request $request, Message $message)
    {
        $data = $request->validate([
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
            'tipo' => 'required|in:email,notificacion,recordatorio,tarea,interno,externo',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'estado' => 'required|in:borrador,enviado,recibido,archivado,eliminado',
            'remitente_id' => 'required|exists:users,id',
            'destinatario_id' => 'required|exists:users,id',
            'tarea_id' => 'nullable|exists:tareas,id',
            'contacto_externo' => 'boolean',
            'leido' => 'boolean',
            'archivado' => 'boolean',
            'nota_interna' => 'nullable|string',
        ]);

        $message->update($data);

        return redirect()->route('messages.show', $message)
            ->with('success', 'Mensaje actualizado correctamente.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('messages.index')
            ->with('success', 'Mensaje eliminado correctamente.');
    }

    public function responder(Message $message, Request $request)
    {
        $request->validate([
            'contenido' => 'required|string',
        ]);

        $nuevoMensaje = $message->responder($request->contenido);

        return redirect()->route('messages.show', $nuevoMensaje)
            ->with('success', 'Respuesta enviada correctamente.');
    }

    public function marcarComoLeido(Message $message)
    {
        if ($message->destinatario_id === Auth::id() && !$message->leido) {
            $message->marcarComoLeido();
            return back()->with('success', 'Mensaje marcado como leído.');
        }

        return back()->with('error', 'No se puede marcar este mensaje como leído.');
    }

    public function archivar(Message $message)
    {
        $message->update(['archivado' => true]);
        return back()->with('success', 'Mensaje archivado correctamente.');
    }

    public function desarchivar(Message $message)
    {
        $message->update(['archivado' => false]);
        return back()->with('success', 'Mensaje desarchivado correctamente.');
    }
}