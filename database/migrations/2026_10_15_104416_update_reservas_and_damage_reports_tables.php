<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ampliar estados de la tabla reservas
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE reservas MODIFY COLUMN estado ENUM('pendiente', 'confirmada', 'en_curso', 'finalizada', 'cancelada') NOT NULL DEFAULT 'pendiente'");

        // 2. Modificar reportes_danos: hacer descripcion nullable y añadir cantidad y articulo_id
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE reportes_danos MODIFY COLUMN descripcion TEXT NULL");

        Schema::table('reportes_danos', function (Blueprint $table) {
            if (!Schema::hasColumn('reportes_danos', 'cantidad')) {
                $table->unsignedInteger('cantidad')->default(1)->after('detalle_reserva_id');
            }
            if (!Schema::hasColumn('reportes_danos', 'articulo_id')) {
                $table->foreignId('articulo_id')->nullable()->after('detalle_reserva_id')->constrained('articulos')->nullOnDelete();
            }
        });

        // 3. Añadir softDeletes a articulos, clientes y reservas para evitar borrados accidentales
        Schema::table('articulos', function (Blueprint $table) {
            if (!Schema::hasColumn('articulos', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('reservas', function (Blueprint $table) {
            if (!Schema::hasColumn('reservas', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('articulos', function (Blueprint $table) {
            if (Schema::hasColumn('articulos', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('reportes_danos', function (Blueprint $table) {
            if (Schema::hasColumn('reportes_danos', 'articulo_id')) {
                $table->dropForeign(['articulo_id']);
                $table->dropColumn('articulo_id');
            }
            if (Schema::hasColumn('reportes_danos', 'cantidad')) {
                $table->dropColumn('cantidad');
            }
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE reservas MODIFY COLUMN estado ENUM('pendiente', 'confirmada', 'finalizada') NOT NULL DEFAULT 'pendiente'");
    }
};
