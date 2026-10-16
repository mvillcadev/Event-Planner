<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tareas = Task::with(['asignado', 'creador', 'dependencia'])
            ->latest()
            ->paginate(15);

        $tareasPendientes = Task::pendientes()->count();
        $tareasEnProgreso = Task::enProgreso()->count();
        $tareasCompletadas = Task::completados()->count();
        $tareasVencidas = Task::vencidos()->count();

        return view('tasks.index', compact(
            'tareas', 'tareasPendientes', 'tareasEnProgreso',
            'tareasCompletadas', 'tareasVencidas'
        ));
    }

    public function create()
    {
        $empleados = User::whereHas('roles', function ($query) {
            $query->where('name', 'empleado');
        })->get(['id', 'name', 'email']);

        $tareasPadre = Task::whereNull('dependencia_tarea_id')->get(['id', 'titulo']);

        return view('tasks.create', compact('empleados', 'tareasPadre'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_vencimiento' => 'required|date',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'estado' => 'required|in:pendiente,en_progreso,completado,cancelado',
            'tipo_tarea' => 'required|in:tarea,hito,reunion,recordatorio',
            'asignado_a' => 'nullable|exists:users,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin_estimada' => 'nullable|date',
            'dependencia_tarea_id' => 'nullable|exists:tareas,id',
            'recordatorio_enviado' => 'boolean',
            'notas' => 'nullable|string',
            'metadatos' => 'nullable|array',
        ]);

        Task::create($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea creada correctamente.');
    }

    public function show(Task $task)
    {
        $task->load(['asignado', 'creador', 'dependencia', 'dependientes', 'mensajes']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $empleados = User::whereHas('roles', function ($query) {
            $query->where('name', 'empleado');
        })->get(['id', 'name', 'email']);

        $tareasPadre = Task::whereNull('dependencia_tarea_id')
            ->where('id', '!=', $task->id)
            ->get(['id', 'titulo']);

        return view('tasks.edit', compact('task', 'empleados', 'tareasPadre'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_vencimiento' => 'required|date',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'estado' => 'required|in:pendiente,en_progreso,completado,cancelado',
            'tipo_tarea' => 'required|in:tarea,hito,reunion,recordatorio',
            'asignado_a' => 'nullable|exists:users,id',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin_estimada' => 'nullable|date',
            'dependencia_tarea_id' => 'nullable|exists:tareas,id',
            'recordatorio_enviado' => 'boolean',
            'notas' => 'nullable|string',
            'metadatos' => 'nullable|array',
        ]);

        $task->update($data);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea eliminada correctamente.');
    }

    public function completar(Task $task)
    {
        if (!$task->puedeSerCompletada()) {
            return back()->with('error', 'No se puede completar esta tarea debido a dependencias pendientes.');
        }

        $task->update(['estado' => 'completado']);

        return back()->with('success', "Tarea '{$task->titulo}' completada correctamente.");
    }

    public function iniciar(Task $task)
    {
        if ($task->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden iniciar tareas pendientes.');
        }

        $task->update(['estado' => 'en_progreso']);

        return back()->with('success', "Tarea '{$task->titulo}' iniciada correctamente.");
    }

    public function cancelar(Task $task)
    {
        if (in_array($task->estado, ['completado', 'cancelado'])) {
            return back()->with('error', 'No se puede cancelar una tarea que ya está completada o cancelada.');
        }

        $task->update(['estado' => 'cancelado']);

        return back()->with('success', "Tarea '{$task->titulo}' cancelada correctamente.");
    }

    public function verificarDependencias()
    {
        $tareasConDependencias = Task::whereNotNull('dependencia_tarea_id')
            ->whereHas('dependencia', function ($query) {
                $query->where('estado', '!=', 'completado');
            })
            ->with(['dependencia', 'asignado'])
            ->get();

        return view('tasks.dependencias', compact('tareasConDependencias'));
    }

    public function estadisticas()
    {
        $estadisticas = [
            'total' => Task::count(),
            'pendientes' => Task::pendientes()->count(),
            'en_progreso' => Task::enProgreso()->count(),
            'completados' => Task::completados()->count(),
            'vencidos' => Task::vencidos()->count(),
            'por_prioridad' => Task::selectRaw('prioridad, COUNT(*) as total')
                ->groupBy('prioridad')
                ->pluck('total', 'prioridad'),
            'por_tipo' => Task::selectRaw('tipo_tarea, COUNT(*) as total')
                ->groupBy('tipo_tarea')
                ->pluck('total', 'tipo_tarea'),
        ];

        return view('tasks.estadisticas', compact('estadisticas'));
    }
}