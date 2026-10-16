<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ReservationDetail;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $articulos  = Article::orderBy('nombre')->get();
        $historial  = StockAdjustment::with(['articulo', 'usuario'])
            ->latest()
            ->paginate(20);

        return view('stock.ajustes', compact('articulos', 'historial'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'articulo_id' => 'required|exists:articulos,id',
            'tipo'        => 'required|in:entrada,baja',
            'cantidad'    => 'required|integer|min:1',
            'motivo'      => 'required|string|max:255',
        ]);

        $articulo = Article::findOrFail($request->articulo_id);

        if ($request->tipo === 'baja') {
            if ($request->cantidad > $articulo->stock_total) {
                return back()->withErrors(['cantidad' => 'La cantidad de baja supera el stock físico total (' . $articulo->stock_total . ' u.)'])->withInput();
            }

            // Validar que la baja no comprometa reservas activas futuras
            $maxReservadoFuturo = ReservationDetail::where('articulo_id', $articulo->id)
                ->whereHas('reserva', function ($q) {
                    $q->whereIn('estado', ['pendiente', 'confirmada', 'en_curso'])
                      ->where('fecha_fin', '>=', today());
                })
                ->max('cantidad') ?? 0;

            $stockRestante = $articulo->stock_total - $request->cantidad;
            if ($stockRestante < $maxReservadoFuturo) {
                return back()->withErrors([
                    'cantidad' => "No se puede aplicar la baja: el stock resultante ({$stockRestante} u.) sería insuficiente para cubrir reservas futuras activas que requieren hasta {$maxReservadoFuturo} unidades."
                ])->withInput();
            }
        }

        DB::transaction(function () use ($request, $articulo) {
            StockAdjustment::create([
                'articulo_id' => $request->articulo_id,
                'usuario_id'  => Auth::id(),
                'tipo'        => $request->tipo,
                'cantidad'    => $request->cantidad,
                'motivo'      => $request->motivo,
            ]);

            if ($request->tipo === 'entrada') {
                $articulo->increment('stock_total', $request->cantidad);
            } else {
                $articulo->decrement('stock_total', $request->cantidad);
            }
        });

        return back()->with('success', 'Ajuste de inventario registrado y auditado correctamente.');
    }
}
