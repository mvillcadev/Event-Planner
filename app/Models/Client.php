<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = ['nombre_completo', 'ci_nit', 'telefono', 'email', 'direccion'];

    public function reservas()
    {
        return $this->hasMany(Reservation::class, 'cliente_id');
    }
}
