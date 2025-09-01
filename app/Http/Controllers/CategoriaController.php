<?php

namespace App\Http\Controllers;

use App\Services\CategoriaService;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    protected $categoriaService;

    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;
    }

    public function index()
    {
        $categorias = $this->categoriaService->getAllCategorias();
        return view('pages.admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('pages.admin.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:255|unique:categorias,nombre_categoria',
        ]);

        $result = $this->categoriaService->createCategoria($request->only('nombre_categoria'));

        return redirect()
            ->route('categorias.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function edit($id)
    {
        $data = $this->categoriaService->getCategoriaById($id);
        
        if (!$data) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        return view('pages.admin.categorias.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:255|unique:categorias,nombre_categoria,' . $id . ',categoria_id',
        ]);

        $result = $this->categoriaService->updateCategoria($id, $request->only('nombre_categoria'));

        return redirect()
            ->route('categorias.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']); 
    }

    public function destroy($id)
    {
        $result = $this->categoriaService->deleteCategoria($id);

        return redirect()
            ->route('categorias.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
