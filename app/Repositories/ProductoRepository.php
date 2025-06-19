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
        return Producto::with(['imagenes', 'categoria', 'marca'])->find($id);
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
            $updated = $producto->update([
                'nombre_producto' => $data['nombre_producto'],
                'descripcion' => $data['descripcion'] ?? '',
                'precio' => $data['precio'],
                'stock' => $data['stock'],
                'estado' => strtolower($data['estado']),
                'categoria_id' => $data['categoria_id'],
                'marca_id' => $data['marca_id'],
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