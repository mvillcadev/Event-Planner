<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tareas';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_vencimiento',
        'prioridad',
        'estado',
        'tipo_tarea',
        'asignado_a',
        'creado_por',
        'fecha_inicio',
        'fecha_fin_estimada',
        'dependencia_tarea_id',
        'recordatorio_enviado',
        'notas',
        'metadatos',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_inicio' => 'date',
        'fecha_fin_estimada' => 'date',
        'metadatos' => 'array',
    ];

    // Relaciones
    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function dependencia()
    {
        return $this->belongsTo(Task::class, 'dependencia_tarea_id');
    }

    public function dependientes()
    {
        return $this->hasMany(Task::class, 'dependencia_tarea_id');
    }

    public function mensajes()
    {
        return $this->hasMany(Message::class, 'tarea_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', '!=', 'completado')
                    ->where('fecha_vencimiento', '<', now());
    }

    // Métodos auxiliares
    public function estaVencida(): bool
    {
        return $this->estado !== 'completado' && $this->fecha_vencimiento < now();
    }

    public function esHito(): bool
    {
        return $this->tipo_tarea === 'hito';
    }

    public function puedeSerEditada(): bool
    {
        return in_array($this->estado, ['pendiente', 'en_progreso']);
    }

    public function puedeSerCompletada(): bool
    {
        $dependenciasCompletadas = $this->dependencia()
            ? $this->dependencia()->where('estado', 'completado')->exists()
            : true;

        return $dependenciasCompletadas && $this->estado === 'en_progreso';
    }
}