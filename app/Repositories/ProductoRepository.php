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
        return Producto::create([
            'nombre_producto' => $data['nombre_producto'],
            'descripcion' => $data['descripcion'],
            'precio' => $data['precio'],
            'stock' => $data['stock'],
            'estado' => $data['estado'],
            'categoria_id' => $data['categoria_id'],
            'marca_id' => $data['marca_id'],
            'sku' => $data['sku'] ?? null,
            'costo_unitario' => $data['costo_unitario'] ?? null,
            'stock_minimo' => $data['stock_minimo'] ?? null,
            'stock_maximo' => $data['stock_maximo'] ?? null,
            'peso' => $data['peso'] ?? null,
            'dimensiones' => $data['dimensiones'] ?? null,
            'codigo_barras' => $data['codigo_barras'] ?? null,
            'notas_inventario' => $data['notas_inventario'] ?? null,
            'activo' => $data['activo'] ?? true,
            'stock_reservado' => 0, // Inicialmente no hay stock reservado
            'stock_disponible' => $data['stock'], // El stock disponible inicial es igual al stock total
        ]);
    }

    public function update(int $id, array $data): bool
    {
        Log::info('Iniciando actualización en repositorio', ['id' => $id, 'data' => $data]);
        
        $producto = $this->findById($id);
        if (!$producto) {
            Log::warning('Producto no encontrado para actualizar', ['id' => $id]);
            return false;
        }

        try {
            // Calcular el nuevo stock_disponible si el stock cambió
            $nuevoStockDisponible = null;
            if (isset($data['stock']) && $data['stock'] != $producto->stock) {
                $nuevoStockDisponible = $data['stock'] - ($producto->stock_reservado ?? 0);
                $nuevoStockDisponible = max(0, $nuevoStockDisponible);
            }

            $updated = $producto->update([
                'nombre_producto' => $data['nombre_producto'],
                'descripcion' => $data['descripcion'] ?? '',
                'precio' => $data['precio'],
                'stock' => $data['stock'],
                'estado' => strtolower($data['estado']),
                'categoria_id' => $data['categoria_id'],
                'marca_id' => $data['marca_id'],
                'sku' => $data['sku'] ?? $producto->sku,
                'costo_unitario' => $data['costo_unitario'] ?? $producto->costo_unitario,
                'stock_minimo' => $data['stock_minimo'] ?? $producto->stock_minimo,
                'stock_maximo' => $data['stock_maximo'] ?? $producto->stock_maximo,
                'peso' => $data['peso'] ?? $producto->peso,
                'dimensiones' => $data['dimensiones'] ?? $producto->dimensiones,
                'codigo_barras' => $data['codigo_barras'] ?? $producto->codigo_barras,
                'notas_inventario' => $data['notas_inventario'] ?? $producto->notas_inventario,
                'activo' => $data['activo'] ?? $producto->activo,
                'stock_disponible' => $nuevoStockDisponible ?? $producto->stock_disponible,
            ]);

            Log::info('Resultado de actualización', [
                'id' => $id,
                'success' => $updated,
                'producto' => $producto->toArray()
            ]);

            return $updated;
        } catch (\Exception $e) {
            Log::error('Error al actualizar producto', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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