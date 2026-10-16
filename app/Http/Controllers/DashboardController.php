<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStock       = Article::sum('stock_total') ?? 0;
        $reservasActivas  = Reservation::whereIn('estado', ['pendiente', 'confirmada', 'en_curso'])->count();
        $enMantenimiento  = Article::where('estado', 'mantenimiento')->count();

        // Alertas de retornos pendientes o vencidos (fecha fin ya pasó y no han retornado)
        $retornosPendientes = Reservation::with('cliente')
            ->whereIn('estado', ['confirmada', 'en_curso'])
            ->whereDate('fecha_fin', '<=', today())
            ->orderBy('fecha_fin')
            ->get();

        $proximasReservas = Reservation::with('cliente')
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_curso'])
            ->orderBy('fecha_inicio')
            ->take(6)
            ->get();

        $ingresosMes = \App\Models\Payment::whereMonth('fecha_pago', now()->month)
            ->whereYear('fecha_pago', now()->year)
            ->sum('monto');

        return view('dashboard', compact(
            'totalStock',
            'reservasActivas',
            'enMantenimiento',
            'retornosPendientes',
            'proximasReservas',
            'ingresosMes'
        ));
    }
}
