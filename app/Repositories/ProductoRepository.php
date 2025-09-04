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
        $umbralBajo = $stockInicial > 0 ? (int) ceil(($stockInicial * 60) / 100) : 0;
        $umbralCritico = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 0;
        
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
            'stock_minimo' => $umbralCritico, // Umbral crítico (20% del stock inicial)
            'stock_maximo' => $umbralBajo, // Umbral bajo (60% del stock inicial)
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
     */
    public function actualizarUmbrales(Producto $producto): void
    {
        $stockInicial = $producto->stock_inicial;
        
        if ($stockInicial > 0) {
            $umbralBajo = (int) ceil(($stockInicial * 60) / 100);
            $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
            
            $producto->update([
                'stock_minimo' => $umbralCritico,
                'stock_maximo' => $umbralBajo
            ]);
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