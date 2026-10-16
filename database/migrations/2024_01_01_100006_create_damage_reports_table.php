<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_danos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detalle_reserva_id')->constrained('detalle_reservas')->restrictOnDelete()->restrictOnUpdate();
            $table->text('descripcion');
            $table->decimal('costo_reposicion', 10, 2);
            $table->date('fecha_reporte');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_danos');
    }
};
