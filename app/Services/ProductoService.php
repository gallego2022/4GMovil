<?php

namespace App\Services;

use App\Interfaces\ProductoRepositoryInterface;
use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ProductoService
{
    protected $productoRepository;

    public function __construct(ProductoRepositoryInterface $productoRepository)
    {
        $this->productoRepository = $productoRepository;
    }

    public function getAllProducts(): Collection
    {
        return $this->productoRepository->getAllWithRelations();
    }

    public function createProduct(array $data, array $images = [], array $variantes = [], array $variantesImages = []): array
    {
        try {
            Log::info('Iniciando creación en el servicio', ['data' => $data]);

            // Asegurarse de que los campos requeridos estén presentes
            $requiredFields = ['nombre_producto', 'precio', 'stock', 'estado', 'categoria_id', 'marca_id'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new \InvalidArgumentException("El campo {$field} es requerido");
                }
            }

            // Asegurarse de que el estado esté en minúsculas
            $data['estado'] = strtolower($data['estado']);
            
            // Asegurarse de que la descripción no sea null
            $data['descripcion'] = $data['descripcion'] ?? '';

            // Establecer valores por defecto para inventario si no están presentes
            if (!isset($data['stock_minimo']) || $data['stock_minimo'] === null || $data['stock_minimo'] == 0) {
                $data['stock_minimo'] = 5; // Valor por defecto
                Log::info('Stock mínimo establecido por defecto', ['valor' => 5]);
            }
            
            if (!isset($data['stock_maximo']) || $data['stock_maximo'] === null) {
                $data['stock_maximo'] = 100; // Valor por defecto
                Log::info('Stock máximo establecido por defecto', ['valor' => 100]);
            }
            
            if (!isset($data['costo_unitario']) || $data['costo_unitario'] === null || $data['costo_unitario'] == 0) {
                $data['costo_unitario'] = $data['precio'] * 0.7; // 70% del precio de venta
                Log::info('Costo unitario establecido por defecto', ['valor' => $data['costo_unitario']]);
            }

            $producto = $this->productoRepository->create($data);
            Log::info('Producto creado correctamente', ['producto_id' => $producto->producto_id]);

            // Registrar movimiento de entrada de inventario si hay stock inicial
            if ($data['stock'] > 0) {
                try {
                    // Crear el movimiento de entrada sin modificar el stock (ya está establecido)
                    MovimientoInventario::create([
                        'producto_id' => $producto->producto_id,
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $data['stock'],
                        'stock_anterior' => 0,
                        'stock_nuevo' => $data['stock'],
                        'motivo' => "Entrada inicial - Producto creado",
                        'usuario_id' => 1, // Usuario admin por defecto
                        'referencia' => null,
                        'costo_unitario' => $producto->costo_unitario
                    ]);
                    Log::info('Movimiento de entrada registrado', [
                        'producto_id' => $producto->producto_id,
                        'cantidad' => $data['stock']
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al registrar movimiento de entrada', [
                        'producto_id' => $producto->producto_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if (!empty($images)) {
                $this->productoRepository->attachImages($producto, $images);
                Log::info('Imágenes adjuntadas correctamente', ['count' => count($images)]);
            }

            // Crear variantes si se proporcionaron
            if (!empty($variantes)) {
                $this->createVariantes($producto, $variantes, $variantesImages);
                Log::info('Variantes creadas correctamente', ['count' => count($variantes)]);
            }

            // Guardar especificaciones si se proporcionaron
            if (!empty($data['especificaciones'])) {
                $this->saveEspecificaciones($producto, $data['especificaciones']);
                Log::info('Especificaciones guardadas correctamente', ['count' => count($data['especificaciones'])]);
            }

            return [
                'success' => true,
                'producto' => $producto,
                'message' => 'Producto creado correctamente.'
            ];
        } catch (\Exception $e) {
            Log::error('Error en la creación del producto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Crear variantes para un producto
     */
    private function createVariantes($producto, array $variantes, array $variantesImages = []): void
    {
        Log::info('Creando variantes', [
            'producto_id' => $producto->producto_id,
            'variantes_count' => count($variantes),
            'images_count' => count($variantesImages)
        ]);
        
        foreach ($variantes as $index => $variante) {
            $varianteModel = \App\Models\VarianteProducto::create([
                'producto_id' => $producto->producto_id,
                'nombre' => $variante['nombre'],
                'codigo_color' => $variante['codigo_color'] ?? null,
                'stock' => $variante['stock'],
                'disponible' => isset($variante['disponible']),
                'precio_adicional' => $variante['precio_adicional'] ?? 0,
                'descripcion' => $variante['descripcion'] ?? null,
                'orden' => $index + 1
            ]);

            Log::info('Variante creada', [
                'variante_id' => $varianteModel->variante_id,
                'nombre' => $varianteModel->nombre
            ]);

            // Procesar imágenes específicas de esta variante si existen
            if (isset($variantesImages[$index])) {
                $imagenesData = $variantesImages[$index];
                
                // Si es un array de archivos (nuevas imágenes)
                if (is_array($imagenesData) && !empty($imagenesData) && !isset($imagenesData['existentes'])) {
                    Log::info('Procesando nuevas imágenes para variante', [
                        'variante_id' => $varianteModel->variante_id,
                        'variante_nombre' => $varianteModel->nombre,
                        'imagenes_count' => count($imagenesData)
                    ]);
                    $this->attachVarianteImages($varianteModel, $imagenesData);
                }
                // Si es un array con información de imágenes existentes/eliminadas
                elseif (is_array($imagenesData) && (isset($imagenesData['existentes']) || isset($imagenesData['eliminadas']))) {
                    Log::info('Procesando información de imágenes existentes/eliminadas para variante', [
                        'variante_id' => $varianteModel->variante_id,
                        'variante_nombre' => $varianteModel->nombre,
                        'existentes' => $imagenesData['existentes'] ?? [],
                        'eliminadas' => $imagenesData['eliminadas'] ?? []
                    ]);
                    
                    // Procesar imágenes eliminadas
                    if (isset($imagenesData['eliminadas']) && !empty($imagenesData['eliminadas'])) {
                        $this->deleteVariantImages($imagenesData['eliminadas']);
                    }
                    
                    // Procesar nuevas imágenes si las hay
                    if (isset($imagenesData[0]) && is_object($imagenesData[0])) {
                        $nuevasImagenes = array_filter($imagenesData, function($item) {
                            return is_object($item);
                        });
                        if (!empty($nuevasImagenes)) {
                            $this->attachVarianteImages($varianteModel, $nuevasImagenes);
                        }
                    }
                }
            } else {
                Log::info('No hay imágenes específicas para esta variante', [
                    'variante_id' => $varianteModel->variante_id,
                    'variante_nombre' => $varianteModel->nombre
                ]);
            }
        }
    }

    /**
     * Guardar especificaciones de un producto
     */
    private function saveEspecificaciones($producto, array $especificaciones): void
    {
        Log::info('Guardando especificaciones', [
            'producto_id' => $producto->producto_id,
            'especificaciones_count' => count($especificaciones)
        ]);

        foreach ($especificaciones as $nombreCampo => $valor) {
            // Validar que el valor no sea null o vacío
            if ($valor === null || $valor === '') {
                Log::warning('Valor de especificación vacío o null, saltando', [
                    'producto_id' => $producto->producto_id,
                    'nombre_campo' => $nombreCampo,
                    'valor' => $valor
                ]);
                continue;
            }

            // Buscar la especificación de categoría
            $especificacionCategoria = \App\Models\EspecificacionCategoria::where('nombre_campo', $nombreCampo)
                ->where('categoria_id', $producto->categoria_id)
                ->where('activo', true)
                ->first();

            if ($especificacionCategoria) {
                // Crear o actualizar la especificación del producto
                \App\Models\EspecificacionProducto::updateOrCreate(
                    [
                        'producto_id' => $producto->producto_id,
                        'especificacion_id' => $especificacionCategoria->especificacion_id
                    ],
                    [
                        'valor' => (string) $valor // Convertir a string para asegurar que no sea null
                    ]
                );

                Log::info('Especificación guardada', [
                    'producto_id' => $producto->producto_id,
                    'nombre_campo' => $nombreCampo,
                    'valor' => $valor
                ]);
            } else {
                Log::warning('Especificación de categoría no encontrada', [
                    'producto_id' => $producto->producto_id,
                    'nombre_campo' => $nombreCampo,
                    'categoria_id' => $producto->categoria_id
                ]);
            }
        }
    }

    /**
     * Adjuntar imágenes a una variante
     */
    private function attachVarianteImages($variante, array $images): void
    {
        foreach ($images as $index => $image) {
            // Verificar que $image sea un objeto UploadedFile válido
            if (!$image || !is_object($image) || !method_exists($image, 'store')) {
                Log::warning('Imagen inválida para variante', [
                    'variante_id' => $variante->variante_id,
                    'index' => $index,
                    'image_type' => gettype($image)
                ]);
                continue;
            }
            
            try {
                $path = $image->store('variantes', 'public');
                
                \App\Models\ImagenVariante::create([
                    'variante_id' => $variante->variante_id,
                    'ruta_imagen' => $path,
                    'nombre_archivo' => $image->getClientOriginalName(),
                    'tipo_mime' => $image->getMimeType(),
                    'tamaño_bytes' => $image->getSize(),
                    'orden' => $index + 1,
                    'es_principal' => $index === 0 // La primera imagen es la principal
                ]);
                
                Log::info('Imagen de variante adjuntada correctamente', [
                    'variante_id' => $variante->variante_id,
                    'path' => $path
                ]);
            } catch (\Exception $e) {
                Log::error('Error al adjuntar imagen de variante', [
                    'variante_id' => $variante->variante_id,
                    'index' => $index,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Eliminar imágenes de variantes por IDs
     */
    private function deleteVariantImages(array $imageIds): void
    {
        foreach ($imageIds as $imageId) {
            try {
                $imagen = \App\Models\ImagenVariante::find($imageId);
                if ($imagen) {
                    // Eliminar el archivo físico
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imagen->ruta_imagen)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($imagen->ruta_imagen);
                    }
                    
                    // Eliminar el registro de la base de datos
                    $imagen->delete();
                    
                    Log::info('Imagen de variante eliminada correctamente', [
                        'imagen_id' => $imageId,
                        'ruta' => $imagen->ruta_imagen
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error al eliminar imagen de variante', [
                    'imagen_id' => $imageId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Actualizar variantes para un producto
     */
    private function updateVariantes($producto, array $variantes, array $variantesImages = []): void
    {
        // Eliminar variantes existentes
        $producto->variantes()->delete();
        
        // Crear nuevas variantes
        $this->createVariantes($producto, $variantes, $variantesImages);
    }

    public function updateProduct(int $id, array $data, array $images = [], array $variantes = [], array $variantesImages = []): array
    {
        try {
            Log::info('Iniciando actualización en el servicio', [
                'id' => $id,
                'data' => $data,
                'has_images' => !empty($images)
            ]);

            $producto = $this->productoRepository->findById($id);
            
            if (!$producto) {
                Log::warning('Producto no encontrado', ['id' => $id]);
                return [
                    'success' => false,
                    'message' => 'No se encontró el producto.'
                ];
            }

            // Asegurarse de que los campos requeridos estén presentes
            $requiredFields = ['nombre_producto', 'precio', 'stock', 'estado', 'categoria_id', 'marca_id'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    Log::warning('Campo requerido faltante', ['field' => $field]);
                    throw new \InvalidArgumentException("El campo {$field} es requerido");
                }
            }

            // Asegurarse de que el estado esté en minúsculas
            $data['estado'] = strtolower($data['estado']);
            
            // Asegurarse de que la descripción no sea null
            $data['descripcion'] = $data['descripcion'] ?? '';

            $success = $this->productoRepository->update($id, $data);
            Log::info('Resultado de actualización básica', ['success' => $success]);

            if ($success && !empty($images)) {
                $this->productoRepository->attachImages($producto, $images);
                Log::info('Imágenes adjuntadas correctamente', ['count' => count($images)]);
            }

            // Actualizar variantes si se proporcionaron
            if ($success && !empty($variantes)) {
                $this->updateVariantes($producto, $variantes, $variantesImages);
                Log::info('Variantes actualizadas correctamente', ['count' => count($variantes)]);
            }

            // Actualizar especificaciones si se proporcionaron
            if ($success && !empty($data['especificaciones'])) {
                $this->saveEspecificaciones($producto, $data['especificaciones']);
                Log::info('Especificaciones actualizadas correctamente', ['count' => count($data['especificaciones'])]);
            }

            return [
                'success' => $success,
                'message' => $success ? 'Producto actualizado correctamente.' : 'No se pudo actualizar el producto.'
            ];
        } catch (\Exception $e) {
            Log::error('Error en la actualización del producto', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    public function deleteProduct(int $id): array
    {
        try {
            $success = $this->productoRepository->delete($id);

            return [
                'success' => $success,
                'message' => $success ? 'Producto eliminado correctamente.' : 'No se encontró el producto.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ];
        }
    }

    public function getProductById(int $id): ?array
    {
        $producto = $this->productoRepository->findById($id);
        
        if (!$producto) {
            return null;
        }

        // Cargar variantes del producto con sus imágenes
        $producto->load('variantes.imagenes');
        
        // Cargar especificaciones del producto
        $producto->load('especificaciones.especificacionCategoria');

        return [
            'producto' => $producto,
            'categorias' => \App\Models\Categoria::all(),
            'marcas' => \App\Models\Marca::all()
        ];
    }
} 