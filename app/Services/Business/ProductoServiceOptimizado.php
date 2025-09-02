<?php

namespace App\Services\Business;

use App\Services\Base\BaseService;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\VarianteProducto;
use App\Models\ImagenProducto;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductoServiceOptimizado extends BaseService
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Obtiene todos los productos con filtros
     */
    public function getAllProducts(array $filters = []): array
    {
        $this->logOperation('obteniendo_productos', ['filters' => $filters]);

        try {
            $query = Producto::with(['categoria', 'marca', 'variantes', 'imagenes']);

            // Aplicar filtros
            if (!empty($filters['categoria_id'])) {
                $query->where('categoria_id', $filters['categoria_id']);
            }

            if (!empty($filters['marca_id'])) {
                $query->where('marca_id', $filters['marca_id']);
            }

            if (!empty($filters['estado'])) {
                $query->where('estado', $filters['estado']);
            }

            if (!empty($filters['search'])) {
                $query->where('nombre_producto', 'like', '%' . $filters['search'] . '%')
                      ->orWhere('descripcion', 'like', '%' . $filters['search'] . '%');
            }

            $productos = $query->orderBy('created_at', 'desc')->get();

            return $this->formatSuccessResponse($productos, 'Productos obtenidos exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_productos', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene datos para formularios de productos
     */
    public function getFormData(): array
    {
        try {
            $categorias = Categoria::all();
            $marcas = Marca::all();

            return [
                'success' => true,
                'categorias' => $categorias,
                'marcas' => $marcas
            ];

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_datos_formulario', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Crea un nuevo producto con variantes e imágenes
     */
    public function createProduct(Request $request): array
    {
        $this->logOperation('creando_producto', ['user_id' => auth()->id()]);

        return $this->executeInTransaction(function () use ($request) {
            // Validar datos del producto
            $productoData = $this->validateProductData($request);
            
            // Crear producto
            $producto = $this->createProductModel($productoData);
            
            // Procesar variantes
            if ($request->has('variantes')) {
                $this->processVariantes($producto, $request->input('variantes'), $request->file('variantes'));
            }
            
            // Procesar especificaciones
            if ($request->has('especificaciones')) {
                $this->processEspecificaciones($producto, $request->input('especificaciones'));
            }
            
            // Procesar imágenes del producto
            if ($request->hasFile('imagenes')) {
                $this->processProductImages($producto, $request->file('imagenes'));
            }

            $this->logOperation('producto_creado_exitosamente', [
                'producto_id' => $producto->producto_id,
                'user_id' => auth()->id()
            ]);

            return $this->formatSuccessResponse($producto, 'Producto creado exitosamente');

        }, 'creación de producto');
    }

    /**
     * Actualiza un producto existente
     */
    public function updateProduct(int $productoId, Request $request): array
    {
        $this->logOperation('actualizando_producto', [
            'producto_id' => $productoId,
            'user_id' => auth()->id()
        ]);

        return $this->executeInTransaction(function () use ($productoId, $request) {
            // Validar que el producto exista
            $producto = Producto::findOrFail($productoId);
            
            // Validar datos de actualización
            $updateData = $this->validateProductUpdateData($request);
            
            // Actualizar producto
            $producto->update($updateData);
            
            // Actualizar variantes si se proporcionan
            if ($request->has('variantes')) {
                $this->updateVariantes($producto, $request->input('variantes'), $request->file('variantes'));
            }
            
            // Actualizar especificaciones
            if ($request->has('especificaciones')) {
                $this->updateEspecificaciones($producto, $request->input('especificaciones'));
            }
            
            // Actualizar imágenes si se proporcionan
            if ($request->hasFile('imagenes')) {
                $this->updateProductImages($producto, $request->file('imagenes'));
            }

            $this->logOperation('producto_actualizado_exitosamente', [
                'producto_id' => $producto->producto_id,
                'user_id' => Auth::id()
            ]);

            return $this->formatSuccessResponse($producto, 'Producto actualizado exitosamente');

        }, 'actualización de producto');
    }

    /**
     * Elimina un producto
     */
    public function deleteProduct(int $productoId): array
    {
        $this->logOperation('eliminando_producto', [
            'producto_id' => $productoId,
            'user_id' => auth()->id()
        ]);

        return $this->executeInTransaction(function () use ($productoId) {
            $producto = Producto::findOrFail($productoId);
            
            // Eliminar imágenes
            $this->deleteProductImages($producto);
            
            // Eliminar variantes
            $producto->variantes()->delete();
            
            // Eliminar producto
            $producto->delete();

            $this->logOperation('producto_eliminado_exitosamente', [
                'producto_id' => $productoId,
                'user_id' => auth()->id()
            ]);

            return $this->formatSuccessResponse(null, 'Producto eliminado exitosamente');

        }, 'eliminación de producto');
    }

    /**
     * Obtiene un producto por ID con todas sus relaciones
     */
    public function getProductById(int $productoId): array
    {
        try {
            $producto = Producto::with([
                'categoria', 
                'marca', 
                'variantes.imagenes', 
                'imagenes',
                'especificaciones'
            ])->findOrFail($productoId);

            return $this->formatSuccessResponse($producto, 'Producto obtenido exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_producto', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Valida los datos del producto
     */
    private function validateProductData(Request $request): array
    {
        $rules = [
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
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
        ];

        $messages = [
            'nombre_producto.required' => 'El nombre del producto es obligatorio',
            'precio.required' => 'El precio es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'stock.required' => 'El stock es obligatorio',
            'stock.integer' => 'El stock debe ser un número entero',
            'categoria_id.required' => 'Debe seleccionar una categoría',
            'marca_id.required' => 'Debe seleccionar una marca',
        ];

        $validator = validator($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new Exception('Datos del producto inválidos: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Valida los datos de actualización del producto
     */
    private function validateProductUpdateData(Request $request): array
    {
        $rules = [
            'nombre_producto' => 'sometimes|required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'estado' => 'sometimes|required|in:nuevo,usado',
            'categoria_id' => 'sometimes|required|exists:categorias,categoria_id',
            'marca_id' => 'sometimes|required|exists:marcas,marca_id',
        ];

        $validator = validator($request->all(), $rules);
        
        if ($validator->fails()) {
            throw new Exception('Datos de actualización inválidos: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Crea el modelo del producto
     */
    private function createProductModel(array $data): Producto
    {
        return Producto::create([
            'nombre_producto' => $data['nombre_producto'],
            'descripcion' => $data['descripcion'] ?? null,
            'precio' => $data['precio'],
            'stock' => $data['stock'],
            'estado' => $data['estado'],
            'categoria_id' => $data['categoria_id'],
            'marca_id' => $data['marca_id'],
        ]);
    }

    /**
     * Procesa las variantes del producto
     */
    private function processVariantes(Producto $producto, array $variantesData, array $variantesFiles = []): void
    {
        foreach ($variantesData as $index => $varianteData) {
            $variante = $producto->variantes()->create([
                'nombre' => $varianteData['nombre'],
                'codigo_color' => $varianteData['codigo_color'] ?? null,
                'stock' => $varianteData['stock'],
                'precio_adicional' => $varianteData['precio_adicional'] ?? 0,
                'descripcion' => $varianteData['descripcion'] ?? null,
            ]);

            // Procesar imágenes de la variante
            if (isset($variantesFiles[$index]['imagenes'])) {
                $this->processVarianteImages($variante, $variantesFiles[$index]['imagenes']);
            }
        }
    }

    /**
     * Procesa las imágenes de una variante
     */
    private function processVarianteImages(VarianteProducto $variante, array $imagenes): void
    {
        foreach ($imagenes as $imagen) {
            if ($imagen && $imagen->isValid()) {
                $path = $this->fileService->uploadImage($imagen, 'variantes');
                
                $variante->imagenes()->create([
                    'url' => $path,
                    'tipo' => 'variante'
                ]);
            }
        }
    }

    /**
     * Procesa las especificaciones del producto
     */
    private function processEspecificaciones(Producto $producto, array $especificaciones): void
    {
        foreach ($especificaciones as $especificacion) {
            if (!empty($especificacion)) {
                $producto->especificaciones()->create([
                    'valor' => $especificacion
                ]);
            }
        }
    }

    /**
     * Procesa las imágenes del producto
     */
    private function processProductImages(Producto $producto, array $imagenes): void
    {
        foreach ($imagenes as $imagen) {
            if ($imagen && $imagen->isValid()) {
                $path = $this->fileService->uploadImage($imagen, 'productos');
                
                $producto->imagenes()->create([
                    'url' => $path,
                    'tipo' => 'producto'
                ]);
            }
        }
    }

    /**
     * Actualiza las variantes del producto
     */
    private function updateVariantes(Producto $producto, array $variantesData, array $variantesFiles = []): void
    {
        // Eliminar variantes existentes
        $producto->variantes()->delete();
        
        // Crear nuevas variantes
        $this->processVariantes($producto, $variantesData, $variantesFiles);
    }

    /**
     * Actualiza las especificaciones del producto
     */
    private function updateEspecificaciones(Producto $producto, array $especificaciones): void
    {
        // Eliminar especificaciones existentes
        $producto->especificaciones()->delete();
        
        // Crear nuevas especificaciones
        $this->processEspecificaciones($producto, $especificaciones);
    }

    /**
     * Actualiza las imágenes del producto
     */
    private function updateProductImages(Producto $producto, array $imagenes): void
    {
        // Eliminar imágenes existentes
        $this->deleteProductImages($producto);
        
        // Crear nuevas imágenes
        $this->processProductImages($producto, $imagenes);
    }

    /**
     * Elimina las imágenes del producto
     */
    private function deleteProductImages(Producto $producto): void
    {
        foreach ($producto->imagenes as $imagen) {
            Storage::delete($imagen->url);
            $imagen->delete();
        }
    }

    /**
     * Busca productos por término de búsqueda
     */
    public function searchProducts(string $searchTerm, array $filters = []): array
    {
        try {
            $query = Producto::with(['categoria', 'marca', 'variantes', 'imagenes'])
                ->where('nombre_producto', 'like', '%' . $searchTerm . '%')
                ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');

            // Aplicar filtros adicionales
            if (!empty($filters['categoria_id'])) {
                $query->where('categoria_id', $filters['categoria_id']);
            }

            if (!empty($filters['marca_id'])) {
                $query->where('marca_id', $filters['marca_id']);
            }

            $productos = $query->orderBy('nombre_producto')->get();

            return $this->formatSuccessResponse($productos, 'Búsqueda completada');

        } catch (Exception $e) {
            $this->logOperation('error_busqueda_productos', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene productos por categoría
     */
    public function getProductsByCategory(int $categoriaId): array
    {
        try {
            $productos = Producto::with(['categoria', 'marca', 'variantes', 'imagenes'])
                ->where('categoria_id', $categoriaId)
                ->orderBy('nombre_producto')
                ->get();

            return $this->formatSuccessResponse($productos, 'Productos de categoría obtenidos');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_productos_categoria', [
                'categoria_id' => $categoriaId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene productos por marca
     */
    public function getProductsByBrand(int $marcaId): array
    {
        try {
            $productos = Producto::with(['categoria', 'marca', 'variantes', 'imagenes'])
                ->where('marca_id', $marcaId)
                ->orderBy('nombre_producto')
                ->get();

            return $this->formatSuccessResponse($productos, 'Productos de marca obtenidos');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_productos_marca', [
                'marca_id' => $marcaId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }
}
