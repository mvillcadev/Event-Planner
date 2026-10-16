<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mensajes';

    protected $fillable = [
        'asunto',
        'contenido',
        'tipo',
        'prioridad',
        'estado',
        'remitente_id',
        'destinatario_id',
        'tarea_id',
        'contacto_externo',
        'leido',
        'fecha_lectura',
        'archivado',
        'respondido',
        'nota_interna',
    ];

    protected $casts = [
        'fecha_lectura' => 'datetime',
    ];

    // Relaciones
    public function remitente()
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function tarea()
    {
        return $this->belongsTo(Task::class, 'tarea_id');
    }

    // Scopes
    public function scopeNoLeidos($query)
    {
        return $query->where('leido', false);
    }

    public function scopeImportantes($query)
    {
        return $query->whereIn('prioridad', ['urgente', 'alta']);
    }

    public function scopeArchivados($query)
    {
        return $query->where('archivado', true);
    }

    // Métodos auxiliares
    public function esUrgente(): bool
    {
        return $this->prioridad === 'urgente';
    }

    public function esExterno(): bool
    {
        return $this->contacto_externo === true;
    }

    public function marcarComoLeido(): void
    {
        $this->update([
            'leido' => true,
            'fecha_lectura' => now(),
        ]);
    }

    public function responder(string $contenidoRespuesta): Message
    {
        return $this->tarea()->first()
            ? Message::create([
                'asunto' => 'Re: ' . $this->asunto,
                'contenido' => $contenidoRespuesta,
                'tipo' => $this->tipo,
                'prioridad' => $this->prioridad,
                'estado' => 'enviado',
                'remitente_id' => auth()->id(),
                'destinatario_id' => $this->remitente_id,
                'tarea_id' => $this->tarea_id,
                'contacto_externo' => $this->contacto_externo,
                'leido' => false,
                'respondido' => true,
            ])
            : $this->replicate();
    }
}