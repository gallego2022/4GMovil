<?php

namespace App\Services;

use App\Interfaces\ProductoRepositoryInterface;
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

    public function createProduct(array $data, array $images = []): array
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

            $producto = $this->productoRepository->create($data);
            Log::info('Producto creado correctamente', ['producto_id' => $producto->producto_id]);

            if (!empty($images)) {
                $this->productoRepository->attachImages($producto, $images);
                Log::info('Imágenes adjuntadas correctamente', ['count' => count($images)]);
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

    public function updateProduct(int $id, array $data, array $images = []): array
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

        return [
            'producto' => $producto,
            'categorias' => \App\Models\Categoria::all(),
            'marcas' => \App\Models\Marca::all()
        ];
    }
} 