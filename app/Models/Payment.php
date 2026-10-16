<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = ['reserva_id', 'monto', 'fecha_pago', 'metodo', 'nota'];

    protected $casts = [
        'fecha_pago' => 'date',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reservation::class, 'reserva_id');
    }
}
