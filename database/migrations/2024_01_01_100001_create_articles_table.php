<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedInteger('stock_total');
            $table->decimal('precio_alquiler', 10, 2);
            $table->string('ruta_imagen')->nullable();
            $table->enum('estado', ['disponible', 'mantenimiento'])->default('disponible');
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
