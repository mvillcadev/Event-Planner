<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationDetail extends Model
{
    use HasFactory;

    protected $table = 'detalle_reservas';

    protected $fillable = ['reserva_id', 'articulo_id', 'cantidad', 'precio_unitario'];

    public function reserva()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }

    public function articulo()
    {
        return $this->belongsTo(Article::class, 'articulo_id');
    }

    public function reportesDanos()
    {
        return $this->hasMany(DamageReport::class, 'detalle_reserva_id');
    }
}
