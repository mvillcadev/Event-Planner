<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageReport extends Model
{
    use HasFactory;

    protected $table = 'reportes_danos';

    protected $fillable = [
        'detalle_reserva_id',
        'articulo_id',
        'cantidad',
        'descripcion',
        'costo_reposicion',
        'fecha_reporte',
    ];

    protected $casts = [
        'fecha_reporte' => 'date',
        'cantidad'      => 'integer',
        'costo_reposicion' => 'decimal:2',
    ];

    public function detalleReserva()
    {
        return $this->belongsTo(ReservationDetail::class, 'detalle_reserva_id');
    }

    public function articulo()
    {
        return $this->belongsTo(Article::class, 'articulo_id');
    }
}
