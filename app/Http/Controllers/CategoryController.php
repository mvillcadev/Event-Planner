<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categorias = Category::withCount('articulos')->orderBy('nombre')->get();
        return view('categories.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        $categoria = Category::create($data);

        // Si es solicitud AJAX devuelve JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'categoria' => ['id' => $categoria->id, 'nombre' => $categoria->nombre],
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre,' . $category->id],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ]);

        $category->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->articulos()->count() > 0) {
            return back()->with('error', "No se puede eliminar «{$category->nombre}» porque tiene artículos asociados.");
        }

        $category->delete();
        return back()->with('success', 'Categoría eliminada correctamente.');
    }
}
