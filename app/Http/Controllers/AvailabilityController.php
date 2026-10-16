<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ReservationDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $mes  = (int) $request->get('mes', now()->month);
        $anio = (int) $request->get('anio', now()->year);

        // Validar rango de mes/año
        $mes  = max(1, min(12, $mes));
        $anio = max(2020, min(2099, $anio));

        $fechaInicio = Carbon::create($anio, $mes, 1)->startOfMonth();
        $fechaFin    = $fechaInicio->copy()->endOfMonth();
        $diasDelMes  = $fechaInicio->daysInMonth;

        $articulos = Article::with('categoria')->orderBy('nombre')->paginate(10);

        // Construir mapa de disponibilidad cuantitativa: articulo_id → [dia → data]
        $calendario = [];

        foreach ($articulos as $articulo) {
            $detalles = ReservationDetail::where('articulo_id', $articulo->id)
                ->whereHas('reserva', function ($q) use ($fechaInicio, $fechaFin) {
                    $q->whereIn('estado', ['pendiente', 'confirmada', 'en_curso'])
                      ->where('fecha_inicio', '<=', $fechaFin)
                      ->where('fecha_fin', '>=', $fechaInicio);
                })
                ->with('reserva.cliente')
                ->get();

            $calendario[$articulo->id] = [];

            for ($dia = 1; $dia <= $diasDelMes; $dia++) {
                $fecha = Carbon::create($anio, $mes, $dia);
                $reservadosEnDia = 0;
                $reservasCoincidentes = [];

                foreach ($detalles as $detalle) {
                    if ($fecha->between($detalle->reserva->fecha_inicio, $detalle->reserva->fecha_fin)) {
                        $reservadosEnDia += (int) $detalle->cantidad;
                        $reservasCoincidentes[] = [
                            'reserva_id' => $detalle->reserva->id,
                            'evento'     => $detalle->reserva->nombre_evento,
                            'cliente'    => $detalle->reserva->cliente->nombre_completo ?? 'Cliente',
                            'cantidad'   => $detalle->cantidad,
                            'estado'     => $detalle->reserva->estado,
                        ];
                    }
                }

                $disponible = max(0, $articulo->stock_total - $reservadosEnDia);
                $porcentajeOcupado = $articulo->stock_total > 0
                    ? min(100, (int) round(($reservadosEnDia / $articulo->stock_total) * 100))
                    : 0;

                $calendario[$articulo->id][$dia] = [
                    'reservado'     => $reservadosEnDia,
                    'disponible'    => $disponible,
                    'stock_total'   => $articulo->stock_total,
                    'porcentaje'    => $porcentajeOcupado,
                    'reservas'      => $reservasCoincidentes,
                    'mantenimiento' => $articulo->estado === 'mantenimiento',
                ];
            }
        }

        $mesPrevio    = $fechaInicio->copy()->subMonth();
        $mesSiguiente = $fechaInicio->copy()->addMonth();

        return view('admin.disponibilidad', compact(
            'articulos', 'calendario', 'diasDelMes',
            'mes', 'anio', 'fechaInicio',
            'mesPrevio', 'mesSiguiente'
        ));
    }
}
