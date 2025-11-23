<?php

namespace App\Repositories;

use App\Models\Producto;
use App\Models\ImagenProducto;
use App\Interfaces\ProductoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductoRepository implements ProductoRepositoryInterface
{
    public function getAllWithRelations(): Collection
    {
        return Producto::with(['imagenes', 'categoria', 'marca'])->get();
    }

    public function findById(int $id): ?Producto
    {
        return Producto::with(['imagenes', 'categoria', 'marca', 'resenas.usuario'])->find($id);
    }

    public function create(array $data): Producto
    {
        $stockInicial = $data['stock_inicial'] ?? 0;
        
        // Calcular umbrales automáticamente basados en el stock inicial
        // Solo calcular si stock_inicial > 0 y no se proporcionaron umbrales personalizados
        $umbralBajo = null;
        $umbralCritico = null;
        
        if ($stockInicial > 0) {
            // Si no se proporcionaron umbrales personalizados, calcularlos automáticamente
            if (!isset($data['umbral_stock_bajo']) || $data['umbral_stock_bajo'] === null) {
                $umbralBajo = (int) ceil(($stockInicial * 60) / 100);
            } else {
                $umbralBajo = $data['umbral_stock_bajo'];
            }
            
            if (!isset($data['umbral_stock_critico']) || $data['umbral_stock_critico'] === null) {
                $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
            } else {
                $umbralCritico = $data['umbral_stock_critico'];
            }
        }
        
        return Producto::create([
            'nombre_producto' => $data['nombre_producto'],
            'descripcion' => $data['descripcion'],
            'precio' => $data['precio'],
            'stock' => 0, // Se calcula automáticamente desde variantes
            'stock_inicial' => $stockInicial, // Stock inicial para calcular alertas
            'estado' => $data['estado'],
            'categoria_id' => $data['categoria_id'],
            'marca_id' => $data['marca_id'],
            'sku' => $data['sku'] ?? null,
            'costo_unitario' => $data['costo_unitario'] ?? null,
            'umbral_stock_critico' => $umbralCritico, // Umbral crítico (20% del stock inicial)
            'umbral_stock_bajo' => $umbralBajo, // Umbral bajo (60% del stock inicial)
            'peso' => $data['peso'] ?? null,
            'dimensiones' => $data['dimensiones'] ?? null,
            'codigo_barras' => $data['codigo_barras'] ?? null,
            'notas_inventario' => $data['notas_inventario'] ?? null,
            'activo' => $data['activo'] ?? true,
            'stock_reservado' => 0, // Inicialmente no hay stock reservado
            'stock_disponible' => 0, // Se calcula automáticamente desde variantes
        ]);
    }

    /**
     * Actualizar los umbrales de alerta basados en el stock inicial
     * Solo actualiza si los umbrales no existen (null o 0)
     */
    public function actualizarUmbrales(Producto $producto, bool $forzar = false): void
    {
        $stockInicial = $producto->stock_inicial;
        
        if ($stockInicial > 0) {
            // Solo actualizar si no hay umbrales personalizados o si se fuerza
            $umbralCriticoActual = $producto->getAttribute('umbral_stock_critico');
            $umbralBajoActual = $producto->getAttribute('umbral_stock_bajo');
            
            $actualizarCritico = $forzar || ($umbralCriticoActual === null || $umbralCriticoActual <= 0);
            $actualizarBajo = $forzar || ($umbralBajoActual === null || $umbralBajoActual <= 0);
            
            if ($actualizarCritico || $actualizarBajo) {
                $umbralBajo = (int) ceil(($stockInicial * 60) / 100);
                $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
                
                $updateData = [];
                if ($actualizarCritico) {
                    $updateData['umbral_stock_critico'] = $umbralCritico;
                }
                if ($actualizarBajo) {
                    $updateData['umbral_stock_bajo'] = $umbralBajo;
                }
                
                if (!empty($updateData)) {
                    $producto->update($updateData);
                    Log::info('Umbrales actualizados automáticamente', [
                        'producto_id' => $producto->producto_id,
                        'stock_inicial' => $stockInicial,
                        'umbral_critico' => $umbralCritico,
                        'umbral_bajo' => $umbralBajo
                    ]);
                }
            }
        }
    }

    /**
     * Actualizar producto con recálculo automático de umbrales
     */
    public function update(int $id, array $data): bool
    {
        $producto = $this->findById($id);
        if (!$producto) {
            return false;
        }
        
        $resultado = $producto->update($data);
        
        // Si se cambió el stock inicial, recalcular umbrales
        if (isset($data['stock_inicial']) && $data['stock_inicial'] != $producto->getOriginal('stock_inicial')) {
            $this->actualizarUmbrales($producto->fresh());
        }
        
        return $resultado;
    }

    public function delete(int $id): bool
    {
        $producto = $this->findById($id);
        if (!$producto) {
            return false;
        }

        $this->deleteImages($producto);
        return $producto->delete();
    }

    public function attachImages(Producto $producto, array $images): void
    {
        foreach ($images as $imagen) {
            $path = $imagen->store('productos', 'public');
            ImagenProducto::create([
                'producto_id' => $producto->producto_id,
                'ruta_imagen' => $path
            ]);
        }
    }

    public function deleteImages(Producto $producto): void
    {
        foreach ($producto->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta_imagen);
            $imagen->delete();
        }
    }
} 