<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index()
    {
        $reservas = Reservation::with(['cliente', 'pagos'])
            ->latest()
            ->paginate(15);

        return view('reservations.index', compact('reservas'));
    }

    public function create()
    {
        $categorias = Category::orderBy('nombre')->get();

        return view('reservations.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'   => 'required|exists:clientes,id',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'descripcion'  => 'nullable|string|max:255',
            'items'        => 'required|string',
            'adelanto'     => 'nullable|numeric|min:0',
            'metodo_pago'  => 'nullable|in:efectivo,qr,transferencia',
        ]);

        $items = json_decode($request->items, true);

        if (empty($items)) {
            return back()->withErrors(['items' => 'Debes añadir al menos un artículo.'])->withInput();
        }

        // Verificar stock disponible para cada artículo
        $errores = [];
        foreach ($items as $item) {
            $disponible = $this->calcularDisponible(
                (int) $item['articulo_id'],
                $request->fecha_inicio,
                $request->fecha_fin
            );
            if ((int) $item['cantidad'] > $disponible) {
                $articulo  = Article::find($item['articulo_id']);
                $errores[] = "Stock insuficiente para \"{$articulo->nombre}\": disponibles {$disponible}, solicitados {$item['cantidad']}.";
            }
        }

        if (!empty($errores)) {
            return back()->withErrors(['stock' => $errores])->withInput();
        }

        $reservaCreada = null;

        DB::transaction(function () use ($request, $items, &$reservaCreada) {
            $monto_total = collect($items)->sum(fn($i) => $i['cantidad'] * $i['precio_unitario']);

            $reservaCreada = Reservation::create([
                'cliente_id'   => $request->cliente_id,
                'usuario_id'   => Auth::id(),
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin'    => $request->fecha_fin,
                'monto_total'  => $monto_total,
                'estado'       => 'confirmada',
                'descripcion'  => $request->descripcion,
            ]);

            foreach ($items as $item) {
                ReservationDetail::create([
                    'reserva_id'      => $reservaCreada->id,
                    'articulo_id'     => $item['articulo_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);
            }

            if ($request->filled('adelanto') && floatval($request->adelanto) > 0) {
                Payment::create([
                    'reserva_id' => $reservaCreada->id,
                    'monto'      => $request->adelanto,
                    'fecha_pago' => now(),
                    'metodo'     => $request->metodo_pago ?? 'efectivo',
                    'nota'       => 'Adelanto inicial',
                ]);
            }
        });

        if (Auth::user()->rol === 'admin') {
            return redirect()->route('pagos.show', $reservaCreada)
                ->with('success', 'Reserva creada correctamente.');
        }

        return redirect()->route('employee.dashboard')
            ->with('success', 'Reserva creada correctamente.');
    }

    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $articulos = Article::with('categoria')
            ->where('estado', 'disponible')
            ->orderBy('nombre')
            ->get()
            ->map(function ($articulo) use ($request) {
                $disponible = $this->calcularDisponible(
                    $articulo->id,
                    $request->fecha_inicio,
                    $request->fecha_fin
                );

                return [
                    'id'          => $articulo->id,
                    'nombre'      => $articulo->nombre,
                    'disponible'  => $disponible,
                    'stock_total' => $articulo->stock_total,
                    'precio'      => (float) $articulo->precio_alquiler,
                    'url_imagen'  => $articulo->url_imagen,
                    'categoria'   => $articulo->categoria?->nombre ?? '—',
                    'agotado'     => $disponible === 0,
                ];
            });

        return response()->json(['articulos' => $articulos]);
    }

    public function despacho(Reservation $reserva)
    {
        $reserva->load(['cliente', 'detalles.articulo.categoria', 'usuario']);

        return view('reservations.despacho', compact('reserva'));
    }

    public function despachar(Reservation $reserva)
    {
        if ($reserva->estado !== 'confirmada') {
            return back()->with('error', 'Solo se pueden despachar reservas confirmadas.');
        }

        $reserva->update(['estado' => 'en_curso']);

        return back()->with('success', "Reserva #{$reserva->id} marcada como «En Curso». Artículos despachados de depósito.");
    }

    public function cancelar(Reservation $reserva)
    {
        if (!in_array($reserva->estado, ['pendiente', 'confirmada'])) {
            return back()->with('error', 'No se puede cancelar una reserva que ya está en curso o finalizada.');
        }

        $reserva->update(['estado' => 'cancelada']);

        return back()->with('success', "Reserva #{$reserva->id} cancelada. El inventario ha sido liberado automáticamente.");
    }

    public function comprobante(Reservation $reserva)
    {
        $reserva->load(['cliente', 'detalles.articulo', 'pagos']);

        $totalPagado    = $reserva->pagos->sum('monto');
        $saldoPendiente = max(0, $reserva->monto_total - $totalPagado);

        $pdf = Pdf::loadView('reservations.pdf.comprobante', compact('reserva', 'totalPagado', 'saldoPendiente'))
            ->setPaper('a4', 'portrait');

        $nombre = 'comprobante-reserva-' . $reserva->id . '.pdf';

        return $pdf->download($nombre);
    }

    private function calcularDisponible(int $articuloId, string $fechaInicio, string $fechaFin, ?int $excluirReservaId = null): int
    {
        $reservadas = ReservationDetail::where('articulo_id', $articuloId)
            ->whereHas('reserva', function ($q) use ($fechaInicio, $fechaFin, $excluirReservaId) {
                $q->whereIn('estado', ['pendiente', 'confirmada', 'en_curso'])
                  ->where('fecha_inicio', '<=', $fechaFin)
                  ->where('fecha_fin', '>=', $fechaInicio);

                if ($excluirReservaId) {
                    $q->where('id', '!=', $excluirReservaId);
                }
            })
            ->sum('cantidad');

        $articulo = Article::find($articuloId);
        if (!$articulo) return 0;

        return max(0, $articulo->stock_total - (int) $reservadas);
    }
}
