<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'email', 'password', 'rol'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Laravel Auth usa 'name' internamente para algunos helpers; lo mapeamos
    public function getNameAttribute(): string
    {
        return $this->nombre;
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function reservas()
    {
        return $this->hasMany(Reservation::class, 'usuario_id');
    }
}
