<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Base\WebController;
use App\Http\Requests\StoreDireccionRequest;
use App\Http\Requests\UpdateDireccionRequest;
use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class DireccionController extends WebController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $direcciones = Direccion::where('usuario_id', Auth::id())->get();

            return View::make('direcciones.index', compact('direcciones'));
        } catch (\Exception $e) {
            return Redirect::route('perfil')
                ->with('mensaje', 'Hubo un error al cargar las direcciones')
                ->with('tipo', 'error');
        }
    }

    public function create(Request $request)
    {
        try {
            // Guardar la URL anterior para redirección posterior
            Session::put('direccion_redirect_url', URL::previous());

            return View::make('direcciones.create');
        } catch (\Exception $e) {
            return Redirect::route('perfil')
                ->with('mensaje', 'No se pudo acceder al formulario de creación')
                ->with('tipo', 'error');
        }
    }

    public function store(StoreDireccionRequest $request)
    {
        try {
            $direccion = new Direccion;
            $direccion->usuario_id = Auth::id();
            $direccion->nombre_destinatario = $request->nombre_destinatario;
            $direccion->telefono = $request->telefono;
            $direccion->calle = $request->calle;
            $direccion->numero = $request->numero;
            $direccion->piso = $request->piso;
            $direccion->departamento = $request->departamento;
            $direccion->codigo_postal = $request->codigo_postal;
            $direccion->ciudad = $request->ciudad;
            $direccion->provincia = $request->provincia;
            $direccion->pais = $request->pais ?? 'Argentina';
            $direccion->referencias = $request->referencias;
            $direccion->tipo_direccion = $request->tipo_direccion ?? 'casa';
            $direccion->activo = true;

            // Si se marca como predeterminada, usar el método que desmarca las demás
            if ($request->predeterminada) {
                $direccion->predeterminada = false; // Temporal, se marcará en marcarComoPredeterminada()
                $direccion->save();
                $direccion->marcarComoPredeterminada();
            } else {
                $direccion->predeterminada = false;
                $direccion->save();
            }

            // Obtener la URL de redirección guardada
            $redirectUrl = Session::get('direccion_redirect_url');
            Session::forget('direccion_redirect_url'); // Limpiar la URL guardada

            // Si la URL anterior contiene 'checkout', redirigir al checkout
            if (str_contains($redirectUrl, 'checkout')) {
                return Redirect::route('checkout.index')
                    ->with('mensaje', 'Dirección agregada correctamente')
                    ->with('tipo', 'success');
            }

            // Si la URL anterior contiene 'perfil', redirigir al perfil
            if (str_contains($redirectUrl, 'perfil')) {
                return Redirect::route('perfil')
                    ->with('mensaje', 'Dirección agregada correctamente')
                    ->with('tipo', 'success');
            }

            // Por defecto, redirigir al listado de direcciones
            return Redirect::route('direcciones.index')
                ->with('mensaje', 'Dirección agregada correctamente')
                ->with('tipo', 'success');

        } catch (\Exception $e) {
            return Redirect::back()
                ->with('mensaje', 'Hubo un error al guardar la dirección')
                ->with('tipo', 'error')
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $direccion = Direccion::findOrFail($id);

            // Verificar que la dirección pertenezca al usuario
            if ($direccion->usuario_id !== Auth::id()) {
                return Redirect::route('direcciones.index')
                    ->with('mensaje', 'No tienes permiso para editar esta dirección')
                    ->with('tipo', 'error');
            }

            return View::make('direcciones.edit', compact('direccion'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Redirect::route('direcciones.index')
                ->with('mensaje', 'La dirección no existe')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            return Redirect::route('direcciones.index')
                ->with('mensaje', 'Hubo un error al cargar la dirección')
                ->with('tipo', 'error');
        }
    }

    public function update(UpdateDireccionRequest $request, $id)
    {
        try {
            $direccion = Direccion::findOrFail($id);

            // Verificar que la dirección pertenezca al usuario
            if ($direccion->usuario_id !== Auth::id()) {
                return Redirect::route('direcciones.index')
                    ->with('mensaje', 'No tienes permiso para editar esta dirección')
                    ->with('tipo', 'error');
            }

            $direccion->update($request->validated());

            // Si se marca como predeterminada, desmarcar las demás
            if ($request->predeterminada) {
                $direccion->marcarComoPredeterminada();
            }

            return Redirect::route('direcciones.index')
                ->with('mensaje', 'Dirección actualizada correctamente')
                ->with('tipo', 'success');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Redirect::route('direcciones.index')
                ->with('mensaje', 'La dirección no existe')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            return Redirect::back()
                ->with('mensaje', 'Hubo un error al actualizar la dirección')
                ->with('tipo', 'error')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $direccion = Direccion::findOrFail($id);

            // Verificar que la dirección pertenezca al usuario
            if ($direccion->usuario_id !== Auth::id()) {
                return Redirect::route('direcciones.index')
                    ->with('mensaje', 'No tienes permiso para eliminar esta dirección')
                    ->with('tipo', 'error');
            }

            $direccion->delete();

            return Redirect::route('direcciones.index')
                ->with('mensaje', 'Dirección eliminada correctamente')
                ->with('tipo', 'success');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Redirect::route('direcciones.index')
                ->with('mensaje', 'La dirección no existe')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            return Redirect::route('direcciones.index')
                ->with('mensaje', 'Hubo un error al eliminar la dirección')
                ->with('tipo', 'error');
        }
    }
}
