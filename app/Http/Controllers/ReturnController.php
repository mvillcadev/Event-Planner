<?php

namespace App\Http\Controllers;

use App\Models\DamageReport;
use App\Models\Article;
use App\Models\Reservation;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index()
    {
        $reservas = Reservation::with(['cliente', 'detalles.articulo'])
            ->whereIn('estado', ['confirmada', 'en_curso', 'finalizada'])
            ->latest()
            ->paginate(15);

        return view('returns.index', compact('reservas'));
    }

    public function create(Reservation $reserva)
    {
        $reserva->load(['cliente', 'detalles.articulo.categoria', 'detalles.reportesDanos']);

        return view('returns.create', compact('reserva'));
    }

    public function store(Request $request, Reservation $reserva)
    {
        $request->validate([
            'danos'                        => 'required|array',
            'danos.*.detalle_reserva_id'   => 'required|exists:detalle_reservas,id',
            'danos.*.articulo_id'          => 'required|exists:articulos,id',
            'danos.*.cantidad_danada'      => 'required|integer|min:0',
            'danos.*.descripcion'          => 'nullable|string|max:500',
            'danos.*.costo_reposicion'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $reserva) {
            $totalDanos = 0;

            foreach ($request->danos as $dano) {
                $cantidadDanada = (int) $dano['cantidad_danada'];

                if ($cantidadDanada > 0) {
                    $descripcion = !empty($dano['descripcion']) ? trim($dano['descripcion']) : 'Dañado / Faltante en retorno';

                    DamageReport::create([
                        'detalle_reserva_id' => $dano['detalle_reserva_id'],
                        'articulo_id'        => $dano['articulo_id'],
                        'cantidad'           => $cantidadDanada,
                        'descripcion'        => $descripcion,
                        'costo_reposicion'   => $dano['costo_reposicion'],
                        'fecha_reporte'      => now(),
                    ]);

                    $articulo = Article::find($dano['articulo_id']);
                    if ($articulo) {
                        $articulo->decrement('stock_total', $cantidadDanada);

                        // Auditoría de ajuste de stock automático
                        StockAdjustment::create([
                            'articulo_id' => $articulo->id,
                            'usuario_id'  => Auth::id() ?? $reserva->usuario_id,
                            'tipo'        => 'baja',
                            'cantidad'    => $cantidadDanada,
                            'motivo'      => "Baja por daño en Reserva #{$reserva->id} - {$descripcion}",
                        ]);
                    }

                    $totalDanos += (float) $dano['costo_reposicion'];
                }
            }

            if ($totalDanos > 0) {
                $reserva->increment('monto_total', $totalDanos);
            }

            $reserva->update(['estado' => 'finalizada']);
        });

        return redirect()->route('pagos.show', $reserva)
            ->with('success', 'Retorno procesado exitosamente. Los daños fueron registrados, el inventario ajustado y el saldo actualizado en Bolivianos (Bs.).');
    }
}
