<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index()
    {
        $articulos = Article::with('categoria')->latest()->paginate(12);
        return view('articles.index', compact('articulos'));
    }

    public function create()
    {
        $categorias = Category::orderBy('nombre')->get();
        return view('articles.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:255'],
            'descripcion'     => ['nullable', 'string'],
            'stock_total'     => ['required', 'integer', 'min:1'],
            'precio_alquiler' => ['required', 'numeric', 'min:0'],
            'estado'          => ['required', 'in:disponible,mantenimiento'],
            'categoria_id'    => ['required', 'exists:categorias,id'],
            'imagen'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('imagen')) {
            $data['ruta_imagen'] = $this->guardarImagen($request->file('imagen'));
        }

        unset($data['imagen']);
        Article::create($data);

        return redirect()->route('articles.index')->with('success', 'Artículo creado correctamente.');
    }

    public function edit(Article $article)
    {
        $categorias = Category::orderBy('nombre')->get();
        return view('articles.edit', compact('article', 'categorias'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:255'],
            'descripcion'     => ['nullable', 'string'],
            'stock_total'     => ['required', 'integer', 'min:1'],
            'precio_alquiler' => ['required', 'numeric', 'min:0'],
            'estado'          => ['required', 'in:disponible,mantenimiento'],
            'categoria_id'    => ['required', 'exists:categorias,id'],
            'imagen'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('imagen')) {
            if ($article->ruta_imagen) {
                Storage::disk('public')->delete($article->ruta_imagen);
            }
            $data['ruta_imagen'] = $this->guardarImagen($request->file('imagen'));
        }

        unset($data['imagen']);
        $article->update($data);

        return redirect()->route('articles.index')->with('success', 'Artículo actualizado correctamente.');
    }

    public function destroy(Article $article)
    {
        $tieneReservasActivas = $article->detalleReservas()
            ->whereHas('reserva', function ($q) {
                $q->whereIn('estado', ['pendiente', 'confirmada', 'en_curso'])
                  ->where('fecha_fin', '>=', today());
            })
            ->exists();

        if ($tieneReservasActivas) {
            return back()->with('error', "No se puede eliminar «{$article->nombre}» porque está comprometido en reservas activas o futuras.");
        }

        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Artículo eliminado del catálogo correctamente.');
    }

    /**
     * Almacena la imagen con nombre único en storage/app/public/articulos.
     */
    private function guardarImagen(\Illuminate\Http\UploadedFile $file): string
    {
        $nombreArchivo = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('articulos', $nombreArchivo, 'public');
    }
}
