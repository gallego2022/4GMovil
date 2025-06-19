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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre_metodo' => 'required|string|max:50|unique:metodos_pago,nombre_metodo',
            ], [
                'nombre_metodo.required' => 'El nombre del método de pago es obligatorio.',
                'nombre_metodo.max' => 'El nombre no puede tener más de 50 caracteres.',
                'nombre_metodo.unique' => 'Este método de pago ya existe.',
            ]);

            MetodoPago::create([
                'nombre_metodo' => $request->nombre_metodo
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

    public function show($id)
    {
        $metodo = MetodoPago::findOrFail($id);
        return view('metodos_pago.show', compact('metodo'));
    }

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

    public function update(Request $request, $id)
    {
        try {
            $metodo = MetodoPago::findOrFail($id);

            $request->validate([
                'nombre_metodo' => 'required|string|max:50|unique:metodos_pago,nombre_metodo,' . $id . ',metodo_id',
            ], [
                'nombre_metodo.required' => 'El nombre del método de pago es obligatorio.',
                'nombre_metodo.max' => 'El nombre no puede tener más de 50 caracteres.',
                'nombre_metodo.unique' => 'Este método de pago ya existe.',
            ]);

            $metodo->update([
                'nombre_metodo' => $request->nombre_metodo
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
