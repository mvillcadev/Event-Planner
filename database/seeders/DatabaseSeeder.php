<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@vanessa.com'],
            [
                'nombre'   => 'Administrador',
                'email'    => 'admin@vanessa.com',
                'password' => Hash::make('admin123'),
                'rol'      => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'empleado@vanessa.com'],
            [
                'nombre'   => 'Empleado',
                'email'    => 'empleado@vanessa.com',
                'password' => Hash::make('empleado123'),
                'rol'      => 'empleado',
            ]
        );
    }
}
