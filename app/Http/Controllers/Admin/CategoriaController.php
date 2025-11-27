<?php

namespace App\Http\Controllers\Admin;

use App\Services\Catalog\CategoriaService;
use Illuminate\Http\Request;
use App\Http\Controllers\Base\WebController;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador de administración para gestionar Categorías.
 *
 * Expone acciones CRUD básicas y delega la lógica de negocio al
 * servicio `CategoriaService`. Las respuestas devuelven vistas Blade
 * o redirecciones con mensajes flash.
 */
class CategoriaController extends WebController
{
    protected CategoriaService $categoriaService;

    /**
     * Inyecta el servicio de categorías.
     *
     * @param CategoriaService $categoriaService Servicio de catálogo para categorías
     */
    public function __construct(CategoriaService $categoriaService)
    {
        $this->categoriaService = $categoriaService;
    }

    /**
     * Lista todas las categorías.
     *
     * @return ViewView Vista con el listado de categorías
     */
    public function index(): ViewView
    {
        $categorias = $this->categoriaService->getAllCategorias();
        return View::make('pages.admin.categorias.index', compact('categorias'));
    }

    /**
     * Muestra el formulario de creación de categoría.
     *
     * @return ViewView Vista de creación
     */
    public function create(): ViewView
    {
        return View::make('pages.admin.categorias.create');
    }

    /**
     * Crea una nueva categoría.
     *
     * Valida que el nombre sea único en la tabla `categorias`.
     *
     * @param Request $request Request HTTP con datos del formulario
     * @return RedirectResponse Redirige al índice con mensaje
     */
    public function store(Request $request): RedirectResponse
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

    /**
     * Muestra el formulario de edición para una categoría.
     *
     * @param int $id Identificador de la categoría
     * @return RedirectResponse|ViewView Redirige si no existe o muestra la vista
     */
    public function edit(int $id): RedirectResponse|ViewView
    {
        $data = $this->categoriaService->getCategoriaById($id);
        
        if (!$data) {
            return Redirect::route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        return View::make('pages.admin.categorias.edit', $data);
    }

    /**
     * Actualiza una categoría existente.
     *
     * Valida que el nombre sea único, excluyendo la categoría actual.
     *
     * @param Request $request Request HTTP con datos del formulario
     * @param int $id Identificador de la categoría
     * @return RedirectResponse Redirige al índice con mensaje
     */
    public function update(Request $request, int $id): RedirectResponse
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

    /**
     * Elimina una categoría.
     *
     * @param int $id Identificador de la categoría
     * @return RedirectResponse Redirige al índice con mensaje
     */
    public function destroy(int $id): RedirectResponse
    {
        $result = $this->categoriaService->deleteCategoria($id);

        if ($result['success']) {
            return Redirect::route('categorias.index')->with('mensaje', 'Categoría Eliminada')->with('tipo', 'success');
        }
        
        return Redirect::route('categorias.index')->with('mensaje', $result['message'])->with('tipo', 'error');
    }
}
