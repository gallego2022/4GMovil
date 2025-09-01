<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetodoPagoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $metodos = MetodoPago::all();
            return view('metodos_pago.index', compact('metodos'));
        } catch (\Exception $e) {
            Log::error('Error al listar métodos de pago: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al cargar los métodos de pago.');
        }
    }

    public function create()
    {
        return view('metodos_pago.create');
    }
    // Crear metodo de pago
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:metodos_pago,nombre',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'boolean',
        ], [
            'nombre.required' => 'El nombre del método de pago es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
            'nombre.unique' => 'Este método de pago ya existe.',
            'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',
            'estado.boolean' => 'El estado del método de pago debe ser un valor booleano.',
        ]);

        try {
            $metodoPago = MetodoPago::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado ? 1 : 0,
            ]);

            return redirect()->route('metodos-pago.index')
                ->with('success', 'Método de pago creado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear método de pago: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Hubo un error al crear el método de pago.');
        }
    }

    // Editar metodo de pago
    public function edit($id)
    {
        try {
            $metodo = MetodoPago::findOrFail($id);
            return view('metodos_pago.edit', compact('metodo'));
        } catch (\Exception $e) {
            Log::error('Error al editar método de pago: ' . $e->getMessage());
            return redirect()->route('metodos-pago.index')
                ->with('error', 'No se encontró el método de pago.');
        }
    }
    // Actualizar metodo de pago
    public function update(Request $request, $id)
    {
        try {
            $metodo = MetodoPago::findOrFail($id);

            $request->validate([
                'nombre' => 'required|string|max:100|unique:metodos_pago,nombre,' . $id . ',metodo_id',
                'descripcion' => 'nullable|string|max:255',
                'estado' => 'boolean',
            ], [
                'nombre.required' => 'El nombre del método de pago es obligatorio.',
                'nombre.max' => 'El nombre no puede tener más de 100 caracteres.',
                'nombre.unique' => 'Este método de pago ya existe.',
                'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',
                'estado.boolean' => 'El estado del método de pago debe ser un valor booleano.',
            ]);

            $metodo->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'estado' => $request->estado ? 1 : 0,
            ]);

            return redirect()->route('metodos-pago.index')
                ->with('success', 'Método de pago actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar método de pago: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Hubo un error al actualizar el método de pago.');
        }
    }
    // Eliminar metodo de pago
    public function destroy($id)
    {
        try {
            $metodo = MetodoPago::findOrFail($id);

            // Verificar si el método de pago está siendo usado
            if ($metodo->pagos()->exists()) {
                return back()->with('error', 'No se puede eliminar este método de pago porque está siendo utilizado en pedidos.');
            }

            $metodo->delete();

            return redirect()->route('metodos-pago.index')
                ->with('success', 'Método de pago eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar método de pago: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al eliminar el método de pago.');
        }
    }
}
