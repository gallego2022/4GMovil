<?php

namespace App\Services\Business;

use App\Services\Base\BaseService;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\VarianteProducto;
use App\Models\ImagenProducto;
use App\Models\EspecificacionCategoria;
use App\Services\FileService;
use App\Services\RedisCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class ProductoServiceOptimizadoCorregido extends BaseService
{
    protected $fileService;
    protected $cacheService;

    public function __construct(FileService $fileService, RedisCacheService $cacheService)
    {
        $this->fileService = $fileService;
        $this->cacheService = $cacheService;
    }

    /**
     * Obtiene todos los productos con filtros
     */
    public function getAllProducts(array $filters = []): array
    {
        $this->logOperation('obteniendo_productos', ['filters' => $filters]);

        // Crear clave de caché basada en filtros
        $cacheKey = 'productos:all:' . md5(serialize($filters));
        
        return $this->cacheService->remember($cacheKey, 3600, function () use ($filters) {
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
        });
    }

    /**
     * Obtiene datos para formularios de productos
     */
    public function getFormData(): array
    {
        return $this->cacheService->remember('productos:form_data', 7200, function () {
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
        });
    }

    /**
     * Crea un nuevo producto con variantes e imágenes
     */
    public function createProduct(Request $request): array
    {
        $this->logOperation('creando_producto', ['user_id' => auth()->id() ?? 0]);

        return $this->executeInTransaction(function () use ($request) {
            // Validar datos del producto
            $productoData = $this->validateProductData($request);
            
            // Crear producto
            $producto = $this->createProductModel($productoData);
            
            // Procesar variantes
            if ($request->has('variantes')) {
                $variantesFiles = $request->file('variantes') ?? [];
                $this->processVariantes($producto, $request->input('variantes'), $variantesFiles);
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
                'user_id' => auth()->id() ?? 0
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
            'user_id' => auth()->id() ?? 0
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
                $variantesFiles = $request->file('variantes') ?? [];
                $this->updateVariantes($producto, $request->input('variantes'), $variantesFiles);
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
                'user_id' => auth()->id() ?? 0
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
            'user_id' => auth()->id() ?? 0
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
                'user_id' => auth()->id() ?? 0
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
                'especificaciones.especificacionCategoria'
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
            'precio' => 'required|numeric|min:10000|max:20000000',
            'stock' => 'nullable|integer|min:0',
            'stock_inicial' => 'required|integer|min:0',
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
            'precio.min' => 'El precio mínimo es $10,000 COP',
            'precio.max' => 'El precio máximo es $20,000,000 COP',
            'stock_inicial.required' => 'El stock inicial es obligatorio para calcular alertas',
            'stock_inicial.integer' => 'El stock inicial debe ser un número entero',
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
            'stock_inicial' => 'sometimes|required|integer|min:0',
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
            'stock' => 0, // Se calcula automáticamente desde variantes
            'stock_inicial' => $data['stock_inicial'], // Stock inicial para calcular alertas
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
        foreach ($imagenes as $index => $imagen) {
            if ($imagen && $imagen->isValid()) {
                // Usar método simple de Storage por ahora
                $path = $imagen->store('variantes', 'public');
                
                $variante->imagenes()->create([
                    'url_imagen' => $path,
                    'alt_text' => $variante->nombre . ' - Imagen ' . ($index + 1),
                    'orden' => $index + 1,
                    'principal' => $index === 0 // La primera imagen es la principal
                ]);
            }
        }
    }

    /**
     * Procesa las especificaciones del producto
     */
    private function processEspecificaciones(Producto $producto, array $especificaciones): void
    {
        foreach ($especificaciones as $nombreCampo => $valor) {
            // Validar que el valor no sea null o vacío
            if ($valor === null || $valor === '') {
                continue;
            }

            // Buscar la especificación de categoría
            $especificacionCategoria = EspecificacionCategoria::where('nombre_campo', $nombreCampo)
                ->where('categoria_id', $producto->categoria_id)
                ->where('estado', true)
                ->first();

            if ($especificacionCategoria) {
                // Crear o actualizar la especificación del producto
                $producto->especificaciones()->updateOrCreate(
                    [
                        'producto_id' => $producto->producto_id,
                        'especificacion_id' => $especificacionCategoria->especificacion_id
                    ],
                    [
                        'valor' => (string) $valor // Convertir a string para asegurar que no sea null
                    ]
                );
            }
        }
    }

    /**
     * Procesa las imágenes del producto
     */
    private function processProductImages(Producto $producto, array $imagenes): void
    {
        foreach ($imagenes as $index => $imagen) {
            if ($imagen && $imagen->isValid()) {
                // Usar método simple de Storage por ahora
                $path = $imagen->store('productos', 'public');
                
                $producto->imagenes()->create([
                    'ruta_imagen' => $path,
                    'alt_text' => $producto->nombre_producto . ' - Imagen ' . ($index + 1),
                    'titulo' => $producto->nombre_producto . ' - Imagen ' . ($index + 1),
                    'orden' => $index + 1,
                    'principal' => $index === 0, // La primera imagen es la principal
                    'activo' => true
                ]);
            }
        }
    }

    /**
     * Actualiza las variantes del producto
     */
    private function updateVariantes(Producto $producto, array $variantesData, array $variantesFiles = []): void
    {
        // Obtener variantes existentes
        $variantesExistentes = $producto->variantes()->get()->keyBy('variante_id');
        
        foreach ($variantesData as $index => $varianteData) {
            if (isset($varianteData['variante_id']) && $variantesExistentes->has($varianteData['variante_id'])) {
                // Actualizar variante existente
                $variante = $variantesExistentes->get($varianteData['variante_id']);
                $variante->update([
                    'nombre' => $varianteData['nombre'],
                    'codigo_color' => $varianteData['codigo_color'] ?? null,
                    'stock' => $varianteData['stock'],
                    'precio_adicional' => $varianteData['precio_adicional'] ?? 0,
                    'descripcion' => $varianteData['descripcion'] ?? null,
                ]);
                
                // Marcar como procesada
                $variantesExistentes->forget($varianteData['variante_id']);
            } else {
                // Crear nueva variante
                $variante = $producto->variantes()->create([
                    'nombre' => $varianteData['nombre'],
                    'codigo_color' => $varianteData['codigo_color'] ?? null,
                    'stock' => $varianteData['stock'],
                    'precio_adicional' => $varianteData['precio_adicional'] ?? 0,
                    'descripcion' => $varianteData['descripcion'] ?? null,
                ]);
            }
            
            // Procesar imágenes de la variante si se proporcionan
            if (isset($variantesFiles[$index]['imagenes'])) {
                $this->processVarianteImages($variante, $variantesFiles[$index]['imagenes']);
            }
        }
        
        // Eliminar solo las variantes que ya no están en los datos
        if ($variantesExistentes->isNotEmpty()) {
            $variantesExistentes->each(function ($variante) {
                // Solo eliminar si no tiene pedidos asociados
                if ($variante->detallesPedido()->count() == 0) {
                    $variante->delete();
                }
            });
        }
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
            // Verificar que la ruta de la imagen no sea null o esté vacía
            if (!empty($imagen->ruta_imagen)) {
                try {
                    Storage::delete($imagen->ruta_imagen);
                } catch (Exception $e) {
                    // Log del error pero continuar con la eliminación del registro
                    \Illuminate\Support\Facades\Log::warning('Error eliminando archivo de imagen', [
                        'imagen_id' => $imagen->imagen_id,
                        'ruta' => $imagen->ruta_imagen,
                        'error' => $e->getMessage()
                    ]);
                }
            }
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

    // ===== MÉTODOS DE VARIANTES (Consolidados desde ProductoVariantesController) =====

    /**
     * Obtiene productos con variantes
     */
    public function getProductosConVariantes(): array
    {
        try {
            $productos = Producto::with(['variantes', 'imagenes'])
                ->where('activo', true)
                ->whereHas('variantes', function($query) {
                    $query->where('disponible', true);
                })
                ->orWhereDoesntHave('variantes')
                ->get();

            return $this->formatSuccessResponse($productos, 'Productos con variantes obtenidos');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_productos_variantes', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene un producto con sus variantes
     */
    public function getProductoConVariantes(int $productoId): array
    {
        try {
            $producto = Producto::with(['variantes', 'imagenes', 'categoria'])
                ->where('activo', true)
                ->findOrFail($productoId);

            // Obtener productos relacionados
            $productosRelacionados = Producto::with(['variantes', 'imagenes'])
                ->where('activo', true)
                ->where('producto_id', '!=', $productoId)
                ->where('categoria_id', $producto->categoria_id)
                ->limit(4)
                ->get();

            return [
                'success' => true,
                'producto' => $producto,
                'productosRelacionados' => $productosRelacionados
            ];

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_producto_variantes', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene información de stock de un producto
     */
    public function getStockInfo(int $productoId): array
    {
        try {
            $producto = Producto::with('variantes')->findOrFail($productoId);

            $stockInfo = [
                'producto_id' => $producto->producto_id,
                'nombre_producto' => $producto->nombre_producto,
                'tiene_variantes' => $producto->tieneVariantes(),
                'stock_total' => $producto->stock,
                'stock_disponible' => $producto->stock_disponible_variantes,
                'estado_stock' => $producto->estado_stock_real,
                'necesita_reposicion' => $producto->necesitaReposicionVariantes(),
                'variantes' => []
            ];

            if ($producto->tieneVariantes()) {
                foreach ($producto->variantes as $variante) {
                    $stockInfo['variantes'][] = [
                        'variante_id' => $variante->variante_id,
                        'nombre' => $variante->nombre,
                        'codigo_color' => $variante->codigo_color,
                        'stock_disponible' => $variante->stock_disponible,
                        'stock_minimo' => $variante->stock_minimo,
                        'disponible' => $variante->disponible,
                        'precio_adicional' => $variante->precio_adicional,
                        'precio_final' => $variante->precio_final,
                        'necesita_reposicion' => $variante->necesitaReposicion(),
                        'estado_stock' => $variante->stock_disponible > 0 ? 'disponible' : 'sin_stock'
                    ];
                }
            }

            return $this->formatSuccessResponse($stockInfo, 'Información de stock obtenida');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_stock', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene variantes de un producto
     */
    public function getVariantesProducto(int $productoId): array
    {
        try {
            $variantes = VarianteProducto::where('producto_id', $productoId)
                ->where('disponible', true)
                ->get();

            return $this->formatSuccessResponse($variantes, 'Variantes obtenidas');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_variantes', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Busca productos con variantes
     */
    public function buscarProductosConVariantes(string $query, ?int $categoriaId = null): array
    {
        try {
            $productos = Producto::with(['variantes', 'imagenes', 'categoria'])
                ->where('activo', true)
                ->where(function($q) use ($query) {
                    $q->where('nombre_producto', 'like', "%{$query}%")
                      ->orWhere('descripcion', 'like', "%{$query}%");
                });

            if ($categoriaId) {
                $productos->where('categoria_id', $categoriaId);
            }

            $productos = $productos->get();

            return $this->formatSuccessResponse($productos, 'Búsqueda completada');

        } catch (Exception $e) {
            $this->logOperation('error_busqueda_variantes', [
                'query' => $query,
                'categoria_id' => $categoriaId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene productos por categoría con variantes
     */
    public function getProductosPorCategoriaConVariantes(int $categoriaId): array
    {
        try {
            $productos = Producto::with(['variantes', 'imagenes'])
                ->where('activo', true)
                ->where('categoria_id', $categoriaId)
                ->get();

            return $this->formatSuccessResponse($productos, 'Productos de categoría obtenidos');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_productos_categoria_variantes', [
                'categoria_id' => $categoriaId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene productos con stock bajo
     */
    public function getProductosStockBajo(): array
    {
        try {
            $productos = Producto::with(['variantes', 'imagenes'])
                ->where('activo', true)
                ->where(function($query) {
                    $query->whereHas('variantes', function($q) {
                        $q->whereRaw('stock_disponible <= stock_minimo');
                    })->orWhereDoesntHave('variantes')
                      ->whereRaw('stock <= stock_minimo');
                })
                ->get();

            return $this->formatSuccessResponse($productos, 'Productos con stock bajo obtenidos');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_productos_stock_bajo', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene productos sin stock
     */
    public function getProductosSinStock(): array
    {
        try {
            $productos = Producto::with(['variantes', 'imagenes'])
                ->where('activo', true)
                ->where(function($query) {
                    $query->whereHas('variantes', function($q) {
                        $q->where('stock_disponible', 0);
                    })->orWhereDoesntHave('variantes')
                      ->where('stock', 0);
                })
                ->get();

            return $this->formatSuccessResponse($productos, 'Productos sin stock obtenidos');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_productos_sin_stock', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    // ===== MÉTODOS DE GESTIÓN DE VARIANTES (Consolidados desde VarianteProductoController) =====

    /**
     * Crea una nueva variante para un producto
     */
    public function createVariante(int $productoId, Request $request): array
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'codigo_color' => 'nullable|string|max:7',
                'stock' => 'required|integer|min:0',
                'disponible' => 'boolean',
                'precio_adicional' => 'nullable|numeric|min:0',
                'descripcion' => 'nullable|string',
                'orden' => 'nullable|integer|min:0'
            ]);

            $variante = VarianteProducto::create([
                'producto_id' => $productoId,
                'nombre' => $request->nombre,
                'codigo_color' => $request->codigo_color,
                'stock' => $request->stock,
                'disponible' => $request->has('disponible'),
                'precio_adicional' => $request->precio_adicional ?? 0,
                'descripcion' => $request->descripcion,
                'orden' => $request->orden ?? 0
            ]);

            $this->logOperation('variante_creada', [
                'producto_id' => $productoId,
                'variante_id' => $variante->variante_id
            ]);

            return $this->formatSuccessResponse($variante, 'Variante creada exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_creando_variante', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Actualiza una variante existente
     */
    public function updateVariante(int $productoId, int $varianteId, Request $request): array
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'codigo_color' => 'nullable|string|max:7',
                'stock' => 'required|integer|min:0',
                'disponible' => 'boolean',
                'precio_adicional' => 'nullable|numeric|min:0',
                'descripcion' => 'nullable|string',
                'orden' => 'nullable|integer|min:0'
            ]);

            $variante = VarianteProducto::where('producto_id', $productoId)
                ->where('variante_id', $varianteId)
                ->firstOrFail();

            $variante->update([
                'nombre' => $request->nombre,
                'codigo_color' => $request->codigo_color,
                'stock' => $request->stock,
                'disponible' => $request->has('disponible'),
                'precio_adicional' => $request->precio_adicional ?? 0,
                'descripcion' => $request->descripcion,
                'orden' => $request->orden ?? 0
            ]);

            $this->logOperation('variante_actualizada', [
                'producto_id' => $productoId,
                'variante_id' => $varianteId
            ]);

            return $this->formatSuccessResponse($variante, 'Variante actualizada exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_actualizando_variante', [
                'producto_id' => $productoId,
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Elimina una variante
     */
    public function deleteVariante(int $productoId, int $varianteId): array
    {
        try {
            $variante = VarianteProducto::where('producto_id', $productoId)
                ->where('variante_id', $varianteId)
                ->firstOrFail();

            $variante->delete();

            $this->logOperation('variante_eliminada', [
                'producto_id' => $productoId,
                'variante_id' => $varianteId
            ]);

            return $this->formatSuccessResponse(null, 'Variante eliminada exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_eliminando_variante', [
                'producto_id' => $productoId,
                'variante_id' => $varianteId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    // ===== MÉTODOS DE GESTIÓN DE IMÁGENES (Consolidados desde ImagenProductoController) =====

    /**
     * Subir imágenes para un producto
     */
    public function uploadImages(int $productoId, Request $request): array
    {
        try {
            $request->validate([
                'imagenes.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'orden' => 'nullable|array',
                'orden.*' => 'integer|min:0'
            ]);

            $producto = Producto::findOrFail($productoId);
            $imagenes = $request->file('imagenes');
            $orden = $request->input('orden', []);

            foreach ($imagenes as $index => $imagen) {
                if ($imagen && $imagen->isValid()) {
                    $path = $imagen->store('productos', 'public');
                    
                    $producto->imagenes()->create([
                        'url' => $path,
                        'tipo' => 'producto',
                        'orden' => $orden[$index] ?? $index
                    ]);
                }
            }

            $this->logOperation('imagenes_subidas', [
                'producto_id' => $productoId,
                'cantidad' => count($imagenes)
            ]);

            return $this->formatSuccessResponse(null, 'Imágenes subidas exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_subiendo_imagenes', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Eliminar una imagen específica
     */
    public function deleteImage(int $productoId, int $imagenId): array
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $imagen = $producto->imagenes()->findOrFail($imagenId);

            // Verificar que la ruta de la imagen no sea null o esté vacía
            if (!empty($imagen->ruta_imagen)) {
                try {
                    // Eliminar archivo físico
                    Storage::disk('public')->delete($imagen->ruta_imagen);
                } catch (Exception $e) {
                    // Log del error pero continuar con la eliminación del registro
                    \Illuminate\Support\Facades\Log::warning('Error eliminando archivo de imagen', [
                        'imagen_id' => $imagenId,
                        'ruta' => $imagen->ruta_imagen,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Eliminar registro de la base de datos
            $imagen->delete();

            $this->logOperation('imagen_eliminada', [
                'producto_id' => $productoId,
                'imagen_id' => $imagenId
            ]);

            return $this->formatSuccessResponse(null, 'Imagen eliminada exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_eliminando_imagen', [
                'producto_id' => $productoId,
                'imagen_id' => $imagenId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    // ===== MÉTODOS DE GESTIÓN DE RESEÑAS (Consolidados desde ResenaController) =====

    /**
     * Crear una nueva reseña para un producto
     */
    public function createResena(int $productoId, Request $request): array
    {
        try {
            $request->validate([
                'usuario_id' => 'required|exists:usuarios,usuario_id',
                'calificacion' => 'required|integer|min:1|max:5',
                'comentario' => 'nullable|string|max:1000',
            ]);

            $producto = Producto::findOrFail($productoId);
            
            $resena = $producto->resenas()->create([
                'usuario_id' => $request->usuario_id,
                'calificacion' => $request->calificacion,
                'comentario' => $request->comentario,
                'fecha_resena' => now()
            ]);

            $this->logOperation('resena_creada', [
                'producto_id' => $productoId,
                'resena_id' => $resena->resena_id,
                'usuario_id' => $request->usuario_id
            ]);

            return $this->formatSuccessResponse($resena, 'Reseña creada exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_creando_resena', [
                'producto_id' => $productoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Actualizar una reseña existente
     */
    public function updateResena(int $productoId, int $resenaId, Request $request): array
    {
        try {
            $request->validate([
                'usuario_id' => 'required|exists:usuarios,usuario_id',
                'calificacion' => 'required|integer|min:1|max:5',
                'comentario' => 'nullable|string|max:1000',
            ]);

            $producto = Producto::findOrFail($productoId);
            $resena = $producto->resenas()->findOrFail($resenaId);

            $resena->update([
                'usuario_id' => $request->usuario_id,
                'calificacion' => $request->calificacion,
                'comentario' => $request->comentario
            ]);

            $this->logOperation('resena_actualizada', [
                'producto_id' => $productoId,
                'resena_id' => $resenaId,
                'usuario_id' => $request->usuario_id
            ]);

            return $this->formatSuccessResponse($resena, 'Reseña actualizada exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_actualizando_resena', [
                'producto_id' => $productoId,
                'resena_id' => $resenaId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Eliminar una reseña
     */
    public function deleteResena(int $productoId, int $resenaId): array
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $resena = $producto->resenas()->findOrFail($resenaId);

            $resena->delete();

            $this->logOperation('resena_eliminada', [
                'producto_id' => $productoId,
                'resena_id' => $resenaId
            ]);

            return $this->formatSuccessResponse(null, 'Reseña eliminada exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_eliminando_resena', [
                'producto_id' => $productoId,
                'resena_id' => $resenaId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }
}
