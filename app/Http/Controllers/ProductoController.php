<?php

namespace App\Http\Controllers;

use App\Services\ProductoService;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
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
                'variantes.*.nombre' => 'required|string|max:100',
                'variantes.*.codigo_color' => 'nullable|string|max:7',
                'variantes.*.stock' => 'required|integer|min:0',
                'variantes.*.precio_adicional' => 'nullable|numeric|min:0',
                'variantes.*.descripcion' => 'nullable|string',
                'variantes.*.imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
                'especificaciones.*' => 'nullable|string',
            ]);

            Log::info('Validación pasada correctamente');

            // Procesar imágenes de variantes correctamente
            $variantesImages = [];
            $variantesFiles = $request->file('variantes') ?? [];
            
            Log::info('Estructura de archivos de variantes recibida para creación', [
                'variantesFiles' => $variantesFiles,
                'keys' => array_keys($variantesFiles)
            ]);
            
            // Procesar las imágenes de variantes manteniendo la estructura por variante
            foreach ($variantesFiles as $varianteIndex => $varianteData) {
                if (is_array($varianteData) && isset($varianteData['imagenes'])) {
                    $imagenesVariante = [];
                    
                    foreach ($varianteData['imagenes'] as $file) {
                        if ($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                            $imagenesVariante[] = $file;
                            Log::info('Imagen de variante válida agregada', [
                                'variante_index' => $varianteIndex,
                                'filename' => $file->getClientOriginalName()
                            ]);
                        } elseif ($file && is_object($file) && method_exists($file, 'isValid')) {
                            Log::warning('Imagen de variante inválida', [
                                'variante_index' => $varianteIndex,
                                'filename' => $file->getClientOriginalName()
                            ]);
                        } else {
                            Log::warning('Archivo no es un objeto UploadedFile válido', [
                                'variante_index' => $varianteIndex,
                                'file_type' => gettype($file)
                            ]);
                        }
                    }
                    
                    if (!empty($imagenesVariante)) {
                        $variantesImages[$varianteIndex] = $imagenesVariante;
                    }
                }
            }
            
            Log::info('Imágenes de variantes procesadas para creación', [
                'variantes_con_imagenes' => count($variantesImages),
                'estructura' => array_keys($variantesImages)
            ]);

            $result = $this->productoService->createProduct(
                $request->except(['imagenes', 'variantes']),
                $request->file('imagenes') ?? [],
                $request->input('variantes', []),
                $variantesImages
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
        // Convertir el ID a entero ya que viene como string desde la ruta
        $id = (int) $id;
        


        Log::info('Verificación de admin pasada, buscando producto', ['id' => $id]);

        try {
            $data = $this->productoService->getProductById($id);
            
            if (!$data) {
                Log::error('Producto no encontrado', ['id' => $id]);
                return redirect()->route('admin.productos.index')->with('error', 'Producto no encontrado.');
            }

            Log::info('Producto encontrado, mostrando vista de edición', [
                'producto_id' => $data['producto']->producto_id,
                'producto_nombre' => $data['producto']->nombre_producto
            ]);

            return view('pages.admin.productos.edit', $data);
        } catch (\Exception $e) {
            Log::error('Error en método edit', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.productos.index')->with('error', 'Error al cargar el producto: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // Convertir el ID a entero ya que viene como string desde la ruta
        $id = (int) $id;
        
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
                'variantes.*.nombre' => 'required|string|max:100',
                'variantes.*.codigo_color' => 'nullable|string|max:7',
                'variantes.*.stock' => 'required|integer|min:0',
                'variantes.*.precio_adicional' => 'nullable|numeric|min:0',
                'variantes.*.descripcion' => 'nullable|string',
                'especificaciones.*' => 'nullable|string',
            ]);

            Log::info('Datos validados correctamente', ['validated' => $validated]);
            
            // Log específico para especificaciones
            if (isset($validated['especificaciones'])) {
                Log::info('Especificaciones recibidas en el controlador', [
                    'especificaciones' => $validated['especificaciones'],
                    'count' => count($validated['especificaciones']),
                    'keys' => array_keys($validated['especificaciones'])
                ]);
                
                // Verificar valores null o vacíos
                foreach ($validated['especificaciones'] as $campo => $valor) {
                    if ($valor === null || $valor === '') {
                        Log::warning('Especificación con valor vacío o null', [
                            'campo' => $campo,
                            'valor' => $valor,
                            'tipo' => gettype($valor)
                        ]);
                    }
                }
            }

            // Obtener el producto actual para comparar el stock
            $productoActual = $this->productoService->getProductById($id);
            $stockAnterior = $productoActual['producto']->stock;
            $nuevoStock = (int) $validated['stock'];

            // Preparar los datos para la actualización
            $updateData = array_merge($validated, [
                'estado' => strtolower($validated['estado']),
                'descripcion' => $validated['descripcion'] ?? ''
            ]);
            
            // Filtrar especificaciones vacías o null
            if (isset($updateData['especificaciones'])) {
                $updateData['especificaciones'] = array_filter($updateData['especificaciones'], function($valor) {
                    return $valor !== null && $valor !== '';
                });
                
                Log::info('Especificaciones filtradas', [
                    'original_count' => count($validated['especificaciones']),
                    'filtered_count' => count($updateData['especificaciones']),
                    'filtered_especificaciones' => $updateData['especificaciones']
                ]);
            }

            Log::info('Datos preparados para actualización', ['updateData' => $updateData]);

            // Procesar imágenes de variantes correctamente
            $variantesImages = [];
            $variantesFiles = $request->file('variantes') ?? [];
            $variantesData = $request->input('variantes', []);
            
            Log::info('Estructura de archivos de variantes recibida para actualización', [
                'variantesFiles' => $variantesFiles,
                'keys' => array_keys($variantesFiles)
            ]);
            
            // Procesar las imágenes de variantes manteniendo la estructura por variante
            foreach ($variantesFiles as $varianteIndex => $varianteData) {
                if (is_array($varianteData) && isset($varianteData['imagenes'])) {
                    $imagenesVariante = [];
                    
                    foreach ($varianteData['imagenes'] as $file) {
                        if ($file && is_object($file) && method_exists($file, 'isValid') && $file->isValid()) {
                            $imagenesVariante[] = $file;
                            Log::info('Imagen de variante válida agregada (actualización)', [
                                'variante_index' => $varianteIndex,
                                'filename' => $file->getClientOriginalName()
                            ]);
                        } elseif ($file && is_object($file) && method_exists($file, 'isValid')) {
                            Log::warning('Imagen de variante inválida (actualización)', [
                                'variante_index' => $varianteIndex,
                                'filename' => $file->getClientOriginalName()
                            ]);
                        } else {
                            Log::warning('Archivo no es un objeto UploadedFile válido (actualización)', [
                                'variante_index' => $varianteIndex,
                                'file_type' => gettype($file)
                            ]);
                        }
                    }
                    
                    if (!empty($imagenesVariante)) {
                        $variantesImages[$varianteIndex] = $imagenesVariante;
                    }
                }
            }
            
            // Procesar información de imágenes existentes y eliminadas
            foreach ($variantesData as $varianteIndex => $varianteInfo) {
                // Agregar información de imágenes existentes
                if (isset($varianteInfo['imagenes_existentes'])) {
                    if (!isset($variantesImages[$varianteIndex])) {
                        $variantesImages[$varianteIndex] = [];
                    }
                    $variantesImages[$varianteIndex]['existentes'] = $varianteInfo['imagenes_existentes'];
                }
                
                // Agregar información de imágenes eliminadas
                if (isset($varianteInfo['imagenes_eliminadas'])) {
                    if (!isset($variantesImages[$varianteIndex])) {
                        $variantesImages[$varianteIndex] = [];
                    }
                    $variantesImages[$varianteIndex]['eliminadas'] = $varianteInfo['imagenes_eliminadas'];
                }
            }
            
            Log::info('Imágenes de variantes procesadas', [
                'variantes_con_imagenes' => count($variantesImages),
                'estructura' => array_keys($variantesImages)
            ]);

            $result = $this->productoService->updateProduct(
                $id,
                $updateData,
                $request->file('imagenes') ?? [],
                $request->input('variantes', []),
                $variantesImages
            );

            Log::info('Resultado de la actualización', $result);

            if (!$result['success']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['error' => $result['message']]);
            }

            // Registrar movimiento de inventario si el stock cambió
            if ($stockAnterior !== $nuevoStock) {
                try {
                    $producto = $this->productoService->getProductById($id)['producto'];
                    
                    if ($nuevoStock > $stockAnterior) {
                        // Entrada de stock
                        $cantidadEntrada = $nuevoStock - $stockAnterior;
                        $producto->registrarEntrada(
                            $cantidadEntrada,
                            "Ajuste de stock desde formulario de edición",
                            Auth::id(),
                            "Edición de producto #{$id}"
                        );
                        Log::info('Movimiento de entrada registrado', [
                            'producto_id' => $id,
                            'cantidad' => $cantidadEntrada,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $nuevoStock
                        ]);
                    } elseif ($nuevoStock < $stockAnterior) {
                        // Salida de stock
                        $cantidadSalida = $stockAnterior - $nuevoStock;
                        $producto->registrarSalida(
                            $cantidadSalida,
                            "Ajuste de stock desde formulario de edición",
                            Auth::id(),
                            null
                        );
                        Log::info('Movimiento de salida registrado', [
                            'producto_id' => $id,
                            'cantidad' => $cantidadSalida,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo' => $nuevoStock
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error al registrar movimiento de inventario', [
                        'producto_id' => $id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // No fallar la actualización si el movimiento no se puede registrar
                }
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
        // Convertir el ID a entero ya que viene como string desde la ruta
        $id = (int) $id;
        
        $result = $this->productoService->deleteProduct($id);

        return redirect()
            ->route('productos.listadoP')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Mostrar un producto específico
     */
    public function show($id)
    {
        try {
            // Log para debug
            Log::info('Método show llamado', [
                'id_original' => $id,
                'tipo_id' => gettype($id),
                'url_completa' => request()->fullUrl(),
                'referer' => request()->header('referer'),
                'user_agent' => request()->header('user-agent')
            ]);
            
            // Convertir el ID a entero ya que viene como string desde la ruta
            $id = (int) $id;
            
            // Log después de la conversión
            Log::info('ID convertido', [
                'id_convertido' => $id,
                'tipo_id_convertido' => gettype($id)
            ]);
            
            $data = $this->productoService->getProductById($id);
            
            if (!$data || !$data['producto']) {
                Log::warning('Producto no encontrado en show', [
                    'id' => $id,
                    'data' => $data
                ]);
                abort(404, 'Producto no encontrado');
            }

            $producto = $data['producto'];
            
            // Cargar relaciones necesarias
            $producto->load(['categoria', 'marca', 'imagenes']);
            
            return view('productos.show', compact('producto'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar producto', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Error al cargar el producto');
        }
    }
}
