<?php

namespace App\Http\Controllers\Cliente;

use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class DireccionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $direcciones = Direccion::where('usuario_id', Auth::id())->get();
            return view('direcciones.index', compact('direcciones'));
        } catch (\Exception $e) {
            return redirect()->route('perfil')
                ->with('mensaje', 'Hubo un error al cargar las direcciones')
                ->with('tipo', 'error');
        }
    }

    public function create(Request $request)
    {
        try {
            // Guardar la URL anterior para redirección posterior
            session(['direccion_redirect_url' => url()->previous()]);
            return view('direcciones.create');
        } catch (\Exception $e) {
            return redirect()->route('perfil')
                ->with('mensaje', 'No se pudo acceder al formulario de creación')
                ->with('tipo', 'error');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'departamento' => 'required|string|max:100',
                'ciudad' => 'required|string|max:100',
                'barrio' => 'required|string|max:100',
                'direccion' => 'required|string|max:255',
                'codigo_postal' => 'required|string|max:10',
                'telefono' => 'required|string|max:20',
                'instrucciones' => 'nullable|string|max:255',
                'tipo_direccion' => 'required|in:casa,apartamento,oficina',
            ]);

            $direccion = new Direccion();
            $direccion->usuario_id = Auth::id();
            $direccion->tipo_direccion = $request->tipo_direccion;
            $direccion->departamento = $request->departamento;
            $direccion->ciudad = $request->ciudad;
            $direccion->barrio = $request->barrio;
            $direccion->direccion = $request->direccion;
            $direccion->codigo_postal = $request->codigo_postal;
            $direccion->telefono = $request->telefono;
            $direccion->instrucciones = $request->instrucciones;
            $direccion->save();

            // Obtener la URL de redirección guardada
            $redirectUrl = session('direccion_redirect_url');
            session()->forget('direccion_redirect_url'); // Limpiar la URL guardada

            // Si la URL anterior contiene 'checkout', redirigir al checkout
            if (str_contains($redirectUrl, 'checkout')) {
                return redirect()->route('checkout.index')
                    ->with('mensaje', 'Dirección agregada correctamente')
                    ->with('tipo', 'success');
            }

            // Si la URL anterior contiene 'perfil', redirigir al perfil
            if (str_contains($redirectUrl, 'perfil')) {
                return redirect()->route('perfil')
                    ->with('mensaje', 'Dirección agregada correctamente')
                    ->with('tipo', 'success');
            }

            // Por defecto, redirigir al listado de direcciones
            return redirect()->route('direcciones.index')
                ->with('mensaje', 'Dirección agregada correctamente')
                ->with('tipo', 'success');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('mensaje', 'Por favor, verifica los datos ingresados')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            return back()
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
                return redirect()->route('direcciones.index')
                    ->with('mensaje', 'No tienes permiso para editar esta dirección')
                    ->with('tipo', 'error');
            }

            return view('direcciones.edit', compact('direccion'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('direcciones.index')
                ->with('mensaje', 'La dirección no existe')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            return redirect()->route('direcciones.index')
                ->with('mensaje', 'Hubo un error al cargar la dirección')
                ->with('tipo', 'error');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $direccion = Direccion::findOrFail($id);
            
            // Verificar que la dirección pertenezca al usuario
            if ($direccion->usuario_id !== Auth::id()) {
                return redirect()->route('direcciones.index')
                    ->with('mensaje', 'No tienes permiso para editar esta dirección')
                    ->with('tipo', 'error');
            }

            $request->validate([
                'departamento' => 'required|string|max:100',
                'ciudad' => 'required|string|max:100',
                'barrio' => 'required|string|max:100',
                'direccion' => 'required|string|max:255',
                'codigo_postal' => 'required|string|max:10',
                'telefono' => 'required|string|max:20',
                'instrucciones' => 'nullable|string|max:255',
                'tipo_direccion' => 'required|in:casa,apartamento,oficina',
            ]);

            $direccion->update($request->all());

            return redirect()->route('direcciones.index')
                ->with('mensaje', 'Dirección actualizada correctamente')
                ->with('tipo', 'success');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('direcciones.index')
                ->with('mensaje', 'La dirección no existe')
                ->with('tipo', 'error');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('mensaje', 'Por favor, verifica los datos ingresados')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            return back()
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
                return redirect()->route('direcciones.index')
                    ->with('mensaje', 'No tienes permiso para eliminar esta dirección')
                    ->with('tipo', 'error');
            }

            $direccion->delete();

            return redirect()->route('direcciones.index')
                ->with('mensaje', 'Dirección eliminada correctamente')
                ->with('tipo', 'success');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('direcciones.index')
                ->with('mensaje', 'La dirección no existe')
                ->with('tipo', 'error');
        } catch (\Exception $e) {
            return redirect()->route('direcciones.index')
                ->with('mensaje', 'Hubo un error al eliminar la dirección')
                ->with('tipo', 'error');
        }
    }
}
