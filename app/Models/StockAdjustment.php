<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $table = 'ajustes_stock';

    protected $fillable = ['articulo_id', 'usuario_id', 'tipo', 'cantidad', 'motivo'];

    public function articulo()
    {
        return $this->belongsTo(Article::class, 'articulo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
