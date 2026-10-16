<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();
            $table->string('asunto');
            $table->text('contenido');
            $table->enum('tipo', ['email', 'notificacion', 'recordatorio', 'tarea', 'interno', 'externo'])->default('interno');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->enum('estado', ['borrador', 'enviado', 'recibido', 'archivado', 'eliminado'])->default('borrador');
            $table->foreignId('remitente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('destinatario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('tarea_id')->nullable()->constrained('tareas')->nullOnDelete();
            $table->boolean('contacto_externo')->default(false);
            $table->boolean('leido')->default(false);
            $table->dateTime('fecha_lectura')->nullable();
            $table->boolean('archivado')->default(false);
            $table->boolean('respondido')->default(false);
            $table->text('nota_interna')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};