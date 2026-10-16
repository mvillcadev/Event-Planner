<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Reservation;
use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        $categorias    = Category::orderBy('nombre')->get();
        $articulos     = Article::with('categoria')->latest()->get();
        $entregasHoy   = Reservation::with('cliente')
            ->whereDate('fecha_inicio', today())
            ->whereIn('estado', ['confirmada', 'pendiente'])
            ->orderBy('fecha_inicio')
            ->get();

        // Alertas de retornos atrasados: eventos cuya fecha fin ya pasó y no están finalizados
        $retornosAtrasados = Reservation::with('cliente')
            ->whereIn('estado', ['confirmada', 'en_curso'])
            ->whereDate('fecha_fin', '<', today())
            ->orderBy('fecha_fin')
            ->get();

        return view('employee.dashboard', compact('categorias', 'articulos', 'entregasHoy', 'retornosAtrasados'));
    }

    /**
     * Endpoint AJAX — filtra artículos por categoría.
     * Devuelve HTML del partial listo para insertar en el DOM.
     */
    public function catalogo(Request $request)
    {
        $query = Article::with('categoria');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('busqueda')) {
            $query->where('nombre', 'like', '%' . $request->busqueda . '%');
        }

        $articulos = $query->latest()->get();

        return response()->json([
            'html'  => view('employee.partials.articulos-grid', compact('articulos'))->render(),
            'total' => $articulos->count(),
        ]);
    }
}
