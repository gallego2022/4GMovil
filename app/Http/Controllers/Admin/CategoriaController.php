<?php

namespace App\Http\Controllers\Admin;

use App\Services\CategoriaService;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class CategoriaController extends WebController
{
    protected $categoriaService;

    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;
    }

    public function index()
    {
        $categorias = $this->categoriaService->getAllCategorias();
        return View::make('pages.admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return View::make('pages.admin.categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
        ]);

        $result = $this->categoriaService->createCategoria($request->only('nombre'));

        if ($result['success']) {
            return Redirect::route('categorias.index')->with('mensaje', 'Categoría Creada')->with('tipo', 'success');
        }
        
        return Redirect::route('categorias.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    public function edit($id)
    {
        $data = $this->categoriaService->getCategoriaById($id);
        
        if (!$data) {
            return Redirect::route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        return View::make('pages.admin.categorias.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $id . ',categoria_id',
        ]);

        $result = $this->categoriaService->updateCategoria($id, $request->only('nombre'));

        if ($result['success']) {
            return Redirect::route('categorias.index')->with('mensaje', 'Categoría Actualizada')->with('tipo', 'success');
        }
        
        return Redirect::route('categorias.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }

    public function destroy($id)
    {
        $result = $this->categoriaService->deleteCategoria($id);

        if ($result['success']) {
            return Redirect::route('categorias.index')->with('mensaje', 'Categoría Eliminada')->with('tipo', 'success');
        }
        
        return Redirect::route('categorias.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }
}
