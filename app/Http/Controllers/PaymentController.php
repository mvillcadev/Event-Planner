<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $reservas = Reservation::with(['cliente', 'pagos'])
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_curso', 'finalizada'])
            ->latest()
            ->paginate(15);

        return view('payments.index', compact('reservas'));
    }

    public function show(Reservation $reserva)
    {
        $reserva->load(['cliente', 'usuario', 'detalles.articulo', 'pagos']);

        $totalPagado      = $reserva->pagos->sum('monto');
        $saldoPendiente   = max(0, $reserva->monto_total - $totalPagado);
        $porcentajePagado = $reserva->monto_total > 0
            ? min(100, (int) round(($totalPagado / $reserva->monto_total) * 100))
            : 0;

        return view('payments.show', compact(
            'reserva', 'totalPagado', 'saldoPendiente', 'porcentajePagado'
        ));
    }

    public function store(Request $request, Reservation $reserva)
    {
        $totalPagado    = $reserva->pagos->sum('monto');
        $saldoPendiente = max(0, $reserva->monto_total - $totalPagado);

        $request->validate([
            'monto'  => "required|numeric|min:0.01|max:{$saldoPendiente}",
            'metodo' => 'required|in:efectivo,qr,transferencia',
            'nota'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $reserva) {
            Payment::create([
                'reserva_id' => $reserva->id,
                'monto'      => $request->monto,
                'fecha_pago' => now(),
                'metodo'     => $request->metodo,
                'nota'       => $request->nota,
            ]);
        });

        return redirect()->route('pagos.show', $reserva)
            ->with('success', 'Abono registrado correctamente.');
    }

    public function liquidar(Reservation $reserva)
    {
        DB::transaction(function () use ($reserva) {
            $totalPagado    = $reserva->pagos->sum('monto');
            $saldoPendiente = max(0, $reserva->monto_total - $totalPagado);

            if ($saldoPendiente > 0) {
                Payment::create([
                    'reserva_id' => $reserva->id,
                    'monto'      => $saldoPendiente,
                    'fecha_pago' => now(),
                    'metodo'     => 'efectivo',
                    'nota'       => 'Liquidación final',
                ]);
            }

            // Si la reserva no estaba cancelada, se marca como finalizada
            $reserva->update(['estado' => 'finalizada']);
        });

        return redirect()->route('pagos.show', $reserva)
            ->with('success', 'Reserva liquidada y finalizada correctamente.');
    }
}
