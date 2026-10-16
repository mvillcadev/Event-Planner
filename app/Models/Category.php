<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = ['nombre', 'descripcion'];

    public function articulos()
    {
        return $this->hasMany(Article::class, 'categoria_id');
    }
}
