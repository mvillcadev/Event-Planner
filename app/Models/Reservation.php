<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservas';

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'monto_total',
        'estado',
        'cliente_id',
        'usuario_id',
        'descripcion',
    ];

    public function getTotalPagadoAttribute(): float
    {
        return $this->pagos->sum('monto');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return max(0, $this->monto_total - $this->getTotalPagadoAttribute());
    }

    public function getPorcentajePagadoAttribute(): int
    {
        if ($this->monto_total <= 0) return 0;
        return min(100, (int) round(($this->getTotalPagadoAttribute() / $this->monto_total) * 100));
    }

    public function getNombreEventoAttribute(): string
    {
        return $this->descripcion ?? 'Evento de ' . ($this->cliente->nombre_completo ?? 'Cliente #' . $this->cliente_id);
    }

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(ReservationDetail::class, 'reserva_id');
    }

    public function pagos()
    {
        return $this->hasMany(Payment::class, 'reserva_id');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmada', 'en_curso']);
    }

    public function isFinalizada(): bool
    {
        return $this->estado === 'finalizada';
    }

    public function isCancelada(): bool
    {
        return $this->estado === 'cancelada';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->estado, ['pendiente', 'confirmada']);
    }

    public function canBeDespachada(): bool
    {
        return $this->estado === 'confirmada';
    }

    public function canBeReturned(): bool
    {
        return in_array($this->estado, ['confirmada', 'en_curso']);
    }
}
