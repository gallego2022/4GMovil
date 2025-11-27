<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

/**
 * Controlador de administración para gestionar Marcas.
 *
 * CRUD de marcas y limpieza de caché relacionada al formulario de productos.
 */
class MarcaController extends WebController
{
    private function forgetProductoFormCache(): void
    {
        Cache::forget('productos:form_data');
    }

    /**
     * Listado de marcas.
     *
     * @return ViewView Vista con el listado de marcas
     */
    public function index(): ViewView
    {
        $marcas = Marca::all();

        return View::make('pages.admin.marcas.index', compact('marcas'));
    }

    /**
     * Formulario de creación de marca.
     *
     * @return ViewView Vista del formulario de creación
     */
    public function create(): ViewView
    {
        return View::make('pages.admin.marcas.create');
    }

    /**
     * Crear una marca.
     *
     * @param Request $request Datos de la marca
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre',
        ]);

        Marca::create($request->only('nombre'));

        $this->forgetProductoFormCache();

        return Redirect::route('marcas.index')->with('mensaje', 'Marca Creada')->with('tipo', 'success');
    }

    /**
     * Formulario de edición de marca.
     *
     * @param int $id Identificador de la marca
     * @return ViewView Vista del formulario de edición
     */
    public function edit(int $id): ViewView
    {
        $marca = Marca::findOrFail($id);

        return View::make('pages.admin.marcas.edit', compact('marca'));
    }

    /**
     * Actualizar una marca.
     *
     * @param Request $request Datos nuevos
     * @param int $id Identificador de la marca
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $marca = Marca::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:marcas,nombre,'.$id.',marca_id',
        ]);

        $marca->update([
            'nombre' => $request->nombre,
        ]);

        $this->forgetProductoFormCache();

        return Redirect::route('marcas.index')->with('mensaje', 'Marca Actualizada')->with('tipo', 'success');
    }

    /**
     * Eliminar una marca.
     *
     * @param int $id Identificador de la marca
     * @return RedirectResponse Redirección con mensaje de estado
     */
    public function destroy(int $id): RedirectResponse
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();

        $this->forgetProductoFormCache();

        return Redirect::route('marcas.index')->with('mensaje', 'Marca Eliminada')->with('tipo', 'success');
    }
}
