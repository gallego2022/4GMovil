<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\VarianteProducto;
use Illuminate\Http\Request;

class ProductoVariantesController extends Controller
{
    /**
     * Mostrar productos con variantes
     */
    public function index()
    {
        $productos = Producto::with(['variantes', 'imagenes'])
            ->where('activo', true)
            ->whereHas('variantes', function($query) {
                $query->where('disponible', true);
            })
            ->orWhereDoesntHave('variantes')
            ->get();

        return view('productos.con-variantes', compact('productos'));
    }

    /**
     * Mostrar detalle de un producto con sus variantes
     */
    public function show($productoId)
    {
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

        return view('productos.detalle-variantes', compact('producto', 'productosRelacionados'));
    }

    /**
     * Obtener información de stock de un producto
     */
    public function obtenerStock($productoId)
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

            return response()->json([
                'success' => true,
                'data' => $stockInfo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de stock'
            ], 500);
        }
    }

    /**
     * Obtener variantes de un producto
     */
    public function obtenerVariantes($productoId)
    {
        try {
            $variantes = VarianteProducto::where('producto_id', $productoId)
                ->where('disponible', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $variantes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener variantes'
            ], 500);
        }
    }

    /**
     * Buscar productos con variantes
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        $categoriaId = $request->get('categoria_id');

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

        return response()->json([
            'success' => true,
            'data' => $productos
        ]);
    }

    /**
     * Obtener productos por categoría
     */
    public function porCategoria($categoriaId)
    {
        $productos = Producto::with(['variantes', 'imagenes'])
            ->where('activo', true)
            ->where('categoria_id', $categoriaId)
            ->get();

        return view('productos.por-categoria', compact('productos'));
    }

    /**
     * Obtener productos con stock bajo
     */
    public function stockBajo()
    {
        $productos = Producto::with(['variantes', 'imagenes'])
            ->where('activo', true)
            ->where(function($query) {
                $query->whereHas('variantes', function($q) {
                    $q->whereRaw('stock_disponible <= stock_minimo');
                })->orWhereDoesntHave('variantes')
                  ->whereRaw('stock <= stock_minimo');
            })
            ->get();

        return view('productos.stock-bajo', compact('productos'));
    }

    /**
     * Obtener productos sin stock
     */
    public function sinStock()
    {
        $productos = Producto::with(['variantes', 'imagenes'])
            ->where('activo', true)
            ->where(function($query) {
                $query->whereHas('variantes', function($q) {
                    $q->where('stock_disponible', 0);
                })->orWhereDoesntHave('variantes')
                  ->where('stock', 0);
            })
            ->get();

        return view('productos.sin-stock', compact('productos'));
    }
}
