<?php

namespace App\Http\Controllers;

use App\Services\ProductoService;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use App\Traits\AdminCheck;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    use AdminCheck;

    protected $productoService;

    public function __construct(ProductoService $productoService)
    {
        $this->productoService = $productoService;
    }

    public function index()
    {
        $productos = $this->productoService->getAllProducts();
        $categorias = Categoria::all();
        $marcas = Marca::all();

        return view('pages.admin.productos.listadoP', compact('productos', 'categorias', 'marcas'));
    }

    public function listado()
    {
        if ($respuesta = $this->verificarAdmin()) {
            return $respuesta;
        }

        $productos = $this->productoService->getAllProducts();
        $categorias = Categoria::all();
        $marcas = Marca::all();

        return view('pages.admin.productos.listadoP', compact('productos', 'categorias', 'marcas'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        $marcas = Marca::all();
        return view('pages.admin.productos.create', compact('categorias', 'marcas'));
    }

    public function store(Request $request)
    {
        Log::info('Iniciando creación de producto', ['datos' => $request->all()]);

        try {
            $request->validate([
                'nombre_producto' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric',
                'stock' => 'required|integer',
                'estado' => 'required|in:nuevo,usado',
                'categoria_id' => 'required|exists:categorias,categoria_id',
                'marca_id' => 'required|exists:marcas,marca_id',
                'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            Log::info('Validación pasada correctamente');

            $result = $this->productoService->createProduct(
                $request->except('imagenes'),
                $request->file('imagenes') ?? []
            );

            Log::info('Resultado de la creación', $result);

            return redirect()
                ->route('productos.listadoP')
                ->with($result['success'] ? 'success' : 'error', $result['message']);

        } catch (\Exception $e) {
            Log::error('Error en la creación del producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $data = $this->productoService->getProductById($id);
        
        if (!$data) {
            return redirect()->route('productos.listadoP')->with('error', 'Producto no encontrado.');
        }

        return view('pages.admin.productos.edit', $data);
    }

    public function update(Request $request, $id)
    {
        Log::info('Iniciando actualización de producto', [
            'id' => $id,
            'request_all' => $request->all(),
            'files' => $request->hasFile('imagenes') ? 'Tiene archivos' : 'No tiene archivos'
        ]);

        try {
            $validated = $request->validate([
                'nombre_producto' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'estado' => 'required|in:nuevo,usado',
                'categoria_id' => 'required|exists:categorias,categoria_id',
                'marca_id' => 'required|exists:marcas,marca_id',
                'imagenes.*' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            Log::info('Datos validados correctamente', ['validated' => $validated]);

            // Preparar los datos para la actualización
            $updateData = array_merge($validated, [
                'estado' => strtolower($validated['estado']),
                'descripcion' => $validated['descripcion'] ?? ''
            ]);

            Log::info('Datos preparados para actualización', ['updateData' => $updateData]);

            $result = $this->productoService->updateProduct(
                $id,
                $updateData,
                $request->file('imagenes') ?? []
            );

            Log::info('Resultado de la actualización', $result);

            if (!$result['success']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['error' => $result['message']]);
            }

            return redirect()
                ->route('productos.listadoP')
                ->with('success', $result['message']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación', [
                'errors' => $e->errors(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error en la actualización del producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $result = $this->productoService->deleteProduct($id);

        return redirect()
            ->route('productos.listadoP')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
