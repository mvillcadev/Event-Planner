<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articulos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'stock_total',
        'precio_alquiler',
        'ruta_imagen',
        'estado',
        'categoria_id',
    ];

    protected $appends = ['url_imagen'];

    public function getUrlImagenAttribute(): string
    {
        if ($this->ruta_imagen && Storage::disk('public')->exists($this->ruta_imagen)) {
            return Storage::disk('public')->url($this->ruta_imagen);
        }

        return asset('images/no-image.png');
    }

    public function categoria()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }

    public function detalleReservas()
    {
        return $this->hasMany(ReservationDetail::class, 'articulo_id');
    }
}
