<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clientes      = Client::withCount('reservas')->latest()->paginate(15);
        $totalClientes = Client::count();

        return view('clients.index', compact('clientes', 'totalClientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'ci_nit'          => 'nullable|string|max:20',
            'telefono'        => 'required|string|max:20',
            'email'           => 'nullable|email|max:255',
            'direccion'       => 'nullable|string|max:500',
        ]);

        $cliente = Client::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cliente' => $cliente,
                'message' => 'Cliente registrado correctamente.',
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Client $client)
    {
        $reservas = $client->reservas()
            ->with(['detalles.articulo', 'pagos'])
            ->latest()
            ->paginate(10);

        return view('clients.show', compact('client', 'reservas'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'ci_nit'          => 'nullable|string|max:20',
            'telefono'        => 'required|string|max:20',
            'email'           => 'nullable|email|max:255',
            'direccion'       => 'nullable|string|max:500',
        ]);

        $client->update($data);

        return redirect()->route('clients.index')
            ->with('success', 'Datos del cliente actualizados correctamente.');
    }

    public function destroy(Client $client)
    {
        $tieneReservasActivas = $client->reservas()
            ->whereIn('estado', ['pendiente', 'confirmada', 'en_curso'])
            ->exists();

        if ($tieneReservasActivas) {
            return back()->with('error', "No se puede eliminar a «{$client->nombre_completo}» porque tiene reservas activas.");
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado del registro correctamente.');
    }

    public function buscar(Request $request)
    {
        $q = $request->get('q', '');

        $clientes = Client::where('nombre_completo', 'like', "%{$q}%")
            ->orWhere('telefono', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('ci_nit', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'nombre_completo', 'ci_nit', 'telefono', 'email']);

        return response()->json($clientes);
    }
}
