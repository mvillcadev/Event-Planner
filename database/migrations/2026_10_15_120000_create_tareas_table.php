<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_vencimiento');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('estado', ['pendiente', 'en_progreso', 'completado', 'cancelado'])->default('pendiente');
            $table->enum('tipo_tarea', ['tarea', 'hito', 'reunion', 'recordatorio'])->default('tarea');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin_estimada')->nullable();
            $table->foreignId('asignado_a')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dependencia_tarea_id')->nullable()->constrained('tareas')->nullOnDelete();
            $table->boolean('recordatorio_enviado')->default(false);
            $table->text('notas')->nullable();
            $table->json('metadatos')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};