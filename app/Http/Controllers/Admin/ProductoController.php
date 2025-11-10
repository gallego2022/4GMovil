<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\VarianteProducto;
use App\Services\Business\ProductoServiceOptimizadoCorregido;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class ProductoController extends WebController
{
    protected $productoService;

    public function __construct(ProductoServiceOptimizadoCorregido $productoService)
    {
        $this->productoService = $productoService;
    }

    /**
     * Muestra la lista de productos
     */
    public function index(Request $request)
    {
        try {
            $filters = $this->getFilterParams($request);
            $result = $this->productoService->getAllProducts($filters);

            return View::make('pages.admin.productos.listadoP', [
                'productos' => $result['data'],
                'categorias' => Categoria::all(),
                'marcas' => Marca::all(),
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Muestra la lista de productos (método listado para compatibilidad)
     */
    public function listado(Request $request)
    {
        // Redirige al método index para mantener consistencia
        return $this->index($request);
    }

    /**
     * Muestra el formulario de creación
     */
    public function create()
    {
        try {
            $result = $this->productoService->getFormData();

            return View::make('pages.admin.productos.create', [
                'categorias' => $result['categorias'],
                'marcas' => $result['marcas'],
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Almacena un nuevo producto
     */
    public function store(Request $request)
    {
        try {
            $result = $this->productoService->createProduct($request);

            return $this->redirectSuccess('productos.index', 'Producto Creado');

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'productos.create');
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.create');
        }
    }

    /**
     * Muestra un producto específico
     */
    public function show(int $id)
    {
        try {
            $result = $this->productoService->getProductById($id);

            return View::make('pages.admin.productos.show', [
                'producto' => $result['data'],
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Obtiene los datos completos de un producto para el modal
     */
    public function getDetalles(int $id)
    {
        try {
            $producto = Producto::with([
                'categoria',
                'marca',
                'imagenes',
                'variantes.imagenes',
                'especificaciones.especificacionCategoria',
            ])->findOrFail($id);

            // Calcular stock reservado total (considerando variantes y reservas activas)
            $stockReservadoTotal = 0;
            if ($producto->tieneVariantes()) {
                // Si tiene variantes, calcular desde reservas activas
                // IMPORTANTE: Excluir reservas de pedidos confirmados PRIMERO
                $stockReservadoTotal = \Illuminate\Support\Facades\DB::table('variantes_producto as vp')
                    ->join('reservas_stock_variantes as rsv', 'vp.variante_id', '=', 'rsv.variante_id')
                    ->leftJoin('pedidos as p', 'rsv.referencia_pedido', '=', 'p.pedido_id')
                    ->where('vp.producto_id', $producto->producto_id)
                    ->where('rsv.estado', 'activa')
                    ->where('rsv.fecha_expiracion', '>', now())
                    // Excluir reservas de pedidos confirmados (estado_id = 2)
                    // Esto es lo más importante: si el pedido está confirmado, no contar la reserva
                    ->where(function ($query) {
                        $query->whereNull('p.estado_id')
                            ->orWhere('p.estado_id', '!=', 2);
                    })
                    // Verificar que no haya reservas confirmadas para estas variantes
                    ->whereNotExists(function ($query) {
                        $query->select(\Illuminate\Support\Facades\DB::raw(1))
                            ->from('reservas_stock_variantes as rsv2')
                            ->join('variantes_producto as vp2', 'rsv2.variante_id', '=', 'vp2.variante_id')
                            ->whereColumn('vp2.variante_id', 'vp.variante_id')
                            ->where('rsv2.estado', 'confirmada')
                            ->whereNotNull('rsv2.referencia_pedido');
                    })
                    // Excluir reservas que están en estado 'confirmada' (por si acaso)
                    ->where('rsv.estado', '!=', 'confirmada')
                    ->sum('rsv.cantidad');
            } else {
                // Si no tiene variantes, usar el stock_reservado directo del producto
                $stockReservadoTotal = $producto->stock_reservado ?? 0;
            }

            return Response::json([
                'success' => true,
                'producto' => [
                    'id' => $producto->producto_id,
                    'nombre' => $producto->nombre_producto,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'stock' => $producto->stock,
                    'stock_disponible' => $producto->stock_disponible,
                    'stock_reservado' => $stockReservadoTotal,
                    'estado' => $producto->estado,
                    'activo' => $producto->activo,
                    'sku' => $producto->sku,
                    'codigo_barras' => $producto->codigo_barras,
                    'costo_unitario' => $producto->costo_unitario,
                    'peso' => $producto->peso,
                    'dimensiones' => $producto->dimensiones,
                    'categoria' => $producto->categoria ? [
                        'id' => $producto->categoria->categoria_id,
                        'nombre' => $producto->categoria->nombre,
                        'descripcion' => $producto->categoria->descripcion,
                    ] : null,
                    'marca' => $producto->marca ? [
                        'id' => $producto->marca->marca_id,
                        'nombre' => $producto->marca->nombre,
                        'descripcion' => $producto->marca->descripcion,
                    ] : null,
                    'imagenes' => $producto->imagenes->map(function ($imagen) {
                        return [
                            'id' => $imagen->imagen_id,
                            'ruta' => asset('storage/'.$imagen->ruta_imagen),
                            'alt_text' => $imagen->alt_text,
                            'titulo' => $imagen->titulo,
                            'principal' => $imagen->principal,
                            'orden' => $imagen->orden,
                        ];
                    }),
                    'variantes' => $producto->variantes->map(function ($variante) {
                        // Calcular stock reservado real desde reservas activas
                        // Solo contar reservas que realmente están activas y no son de pedidos confirmados
                        // IMPORTANTE: Excluir reservas de pedidos confirmados PRIMERO, antes de filtrar por estado
                        $stockReservadoVariante = \Illuminate\Support\Facades\DB::table('reservas_stock_variantes as rsv')
                            ->leftJoin('pedidos as p', 'rsv.referencia_pedido', '=', 'p.pedido_id')
                            ->where('rsv.variante_id', $variante->variante_id)
                            ->where('rsv.estado', 'activa')
                            ->where('rsv.fecha_expiracion', '>', now())
                            // Excluir reservas de pedidos confirmados (estado_id = 2)
                            // Esto es lo más importante: si el pedido está confirmado, no contar la reserva
                            ->where(function ($query) {
                                $query->whereNull('p.estado_id')
                                    ->orWhere('p.estado_id', '!=', 2);
                            })
                            // Excluir reservas que tienen una versión confirmada para el mismo pedido
                            ->whereNotExists(function ($query) {
                                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                                    ->from('reservas_stock_variantes as rsv2')
                                    ->whereColumn('rsv2.variante_id', 'rsv.variante_id')
                                    ->whereColumn('rsv2.referencia_pedido', 'rsv.referencia_pedido')
                                    ->where('rsv2.estado', 'confirmada')
                                    ->whereNotNull('rsv2.referencia_pedido');
                            })
                            // Excluir reservas que están en estado 'confirmada' (por si acaso)
                            ->where('rsv.estado', '!=', 'confirmada')
                            ->sum('rsv.cantidad');

                        // El stock reservado final es el calculado desde reservas activas
                        // Si no hay reservas activas, el stock reservado es 0
                        $stockReservadoFinal = $stockReservadoVariante ?? 0;

                        return [
                            'id' => $variante->variante_id,
                            'nombre' => $variante->nombre,
                            'descripcion' => $variante->descripcion,
                            'precio_adicional' => $variante->precio_adicional,
                            'stock' => $variante->stock,
                            'stock_reservado' => $stockReservadoFinal,
                            'stock_disponible' => ($variante->stock ?? 0) - $stockReservadoFinal,
                            'codigo_color' => $variante->codigo_color,
                            'sku' => $variante->sku,
                            'disponible' => $variante->disponible,
                            'imagenes' => $variante->imagenes->map(function ($imagen) {
                                return [
                                    'id' => $imagen->imagen_id,
                                    'url' => str_starts_with($imagen->url_imagen, 'http') ? $imagen->url_imagen : asset('storage/'.$imagen->url_imagen),
                                    'alt_text' => $imagen->alt_text,
                                    'principal' => $imagen->principal,
                                    'orden' => $imagen->orden,
                                ];
                            }),
                        ];
                    }),
                    'especificaciones' => $producto->especificaciones->map(function ($especificacion) {
                        return [
                            'id' => $especificacion->especificacion_producto_id,
                            'valor' => $especificacion->valor,
                            'especificacion' => $especificacion->especificacionCategoria ? [
                                'id' => $especificacion->especificacionCategoria->especificacion_id,
                                'etiqueta' => $especificacion->especificacionCategoria->etiqueta,
                                'nombre_campo' => $especificacion->especificacionCategoria->nombre_campo,
                                'tipo_campo' => $especificacion->especificacionCategoria->tipo_campo,
                                'unidad' => $especificacion->especificacionCategoria->unidad,
                                'descripcion' => $especificacion->especificacionCategoria->descripcion,
                                'orden' => $especificacion->especificacionCategoria->orden,
                            ] : null,
                        ];
                    })->sortBy(function ($especificacion) {
                        return $especificacion['especificacion']['orden'] ?? 999;
                    })->values(),
                ],
            ]);
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al cargar los datos del producto: '.$e->getMessage(),
            ], 404);
        }
    }

    /**
     * Obtiene el stock actualizado de todos los productos
     */
    public function getStockActualizado()
    {
        try {
            $productos = Producto::select('producto_id', 'stock', 'stock_disponible', 'stock_reservado')
                ->get()
                ->map(function ($producto) {
                    return [
                        'id' => $producto->producto_id,
                        'stock' => $producto->stock,
                        'stock_disponible' => $producto->stock_disponible,
                        'stock_reservado' => $producto->stock_reservado ?? 0,
                    ];
                });

            return Response::json([
                'success' => true,
                'productos' => $productos,
            ]);
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al obtener el stock actualizado: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Muestra el formulario de edición
     */
    public function edit(int $id)
    {
        try {
            $productoResult = $this->productoService->getProductById($id);
            $formDataResult = $this->productoService->getFormData();

            return View::make('pages.admin.productos.edit', [
                'producto' => $productoResult['data'],
                'categorias' => $formDataResult['categorias'],
                'marcas' => $formDataResult['marcas'],
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Actualiza un producto existente
     */
    public function update(Request $request, int $id)
    {
        try {
            $result = $this->productoService->updateProduct($id, $request);

            // Verificar si hay variantes que no se pudieron eliminar
            $mensaje = 'Producto Actualizado';
            $tipo = 'success';
            
            if (isset($result['message']) && strpos($result['message'], 'no se pudieron eliminar') !== false) {
                $mensaje = $result['message'];
                $tipo = 'warning';
            }

            return redirect()->route('productos.index')
                ->with('mensaje', $mensaje)
                ->with('tipo', $tipo);

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'productos.edit', ['producto' => $id]);
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.edit', ['producto' => $id]);
        }
    }

    /**
     * Elimina un producto
     */
    public function destroy(int $id)
    {
        try {
            $result = $this->productoService->deleteProduct($id);

            return $this->redirectSuccess('productos.index', 'Producto eliminado exitosamente');

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Busca productos
     */
    public function search(Request $request)
    {
        try {
            $searchTerm = $request->get('q', '');
            $filters = $this->getFilterParams($request);

            if (empty($searchTerm)) {
                return $this->redirectError('productos.index', 'Término de búsqueda requerido');
            }

            $result = $this->productoService->searchProducts($searchTerm, $filters);

            return View::make('pages.admin.productos.search', [
                'productos' => $result['data'],
                'searchTerm' => $searchTerm,
                'categorias' => Categoria::all(),
                'marcas' => Marca::all(),
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Muestra productos por categoría
     */
    public function byCategory(int $categoriaId)
    {
        try {
            $result = $this->productoService->getProductsByCategory($categoriaId);

            return View::make('pages.admin.productos.byCategory', [
                'productos' => $result['data'],
                'categoria' => Categoria::find($categoriaId),
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Muestra productos por marca
     */
    public function byBrand(int $marcaId)
    {
        try {
            $result = $this->productoService->getProductsByBrand($marcaId);

            return View::make('pages.admin.productos.byBrand', [
                'productos' => $result['data'],
                'marca' => Marca::find($marcaId),
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Obtiene parámetros de filtrado específicos para productos
     */
    protected function getFilterParams(Request $request): array
    {
        $filters = $request->only(['search', 'categoria_id', 'marca_id', 'estado', 'precio_min', 'precio_max']);

        // Eliminar filtros vacíos
        return array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });
    }

    // ===== MÉTODOS DE VARIANTES (Consolidados desde ProductoVariantesController) =====

    /**
     * Mostrar productos con variantes
     */
    public function conVariantes()
    {
        try {
            $result = $this->productoService->getProductosConVariantes();

            // Vista eliminada - usar show.blade.php en su lugar
            return Redirect::route('productos.index')->with('info', 'La vista de productos con variantes ha sido consolidada en la vista principal de productos.');

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Mostrar detalle de un producto con sus variantes
     */
    public function detalleConVariantes(int $productoId)
    {
        try {
            $result = $this->productoService->getProductoConVariantes($productoId);

            return View::make('productos.detalle-variantes', [
                'producto' => $result['producto'],
                'productosRelacionados' => $result['productosRelacionados'],
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Obtener información de stock de un producto
     */
    public function obtenerStock(int $productoId)
    {
        try {
            $result = $this->productoService->getStockInfo($productoId);

            return Response::json($result);

        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al obtener información de stock',
            ], 500);
        }
    }

    /**
     * Obtener variantes de un producto
     */
    public function obtenerVariantes(int $productoId)
    {
        try {
            $result = $this->productoService->getVariantesProducto($productoId);

            return Response::json($result);

        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al obtener variantes',
            ], 500);
        }
    }

    /**
     * Buscar productos con variantes
     */
    public function buscarConVariantes(Request $request)
    {
        try {
            $query = $request->get('q');
            $categoriaId = $request->get('categoria_id');

            $result = $this->productoService->buscarProductosConVariantes($query, $categoriaId);

            return Response::json($result);

        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al buscar productos',
            ], 500);
        }
    }

    /**
     * Obtener productos por categoría con variantes
     */
    public function porCategoriaConVariantes(int $categoriaId)
    {
        try {
            $result = $this->productoService->getProductosPorCategoriaConVariantes($categoriaId);

            return View::make('productos.por-categoria', [
                'productos' => $result['data'],
                'categoria' => Categoria::find($categoriaId),
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    public function stockBajo()
    {
        try {
            $result = $this->productoService->getProductosStockBajo();

            return View::make('productos.stock-bajo', [
                'productos' => $result['data'],
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Obtener productos sin stock
     */
    public function sinStock()
    {
        try {
            $result = $this->productoService->getProductosSinStock();

            return View::make('productos.sin-stock', [
                'productos' => $result['data'],
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    // ===== MÉTODOS DE GESTIÓN DE VARIANTES (Consolidados desde VarianteProductoController) =====

    /**
     * Mostrar las variantes de un producto
     */
    public function variantesIndex(int $productoId)
    {
        try {
            $result = $this->productoService->getProductoConVariantes($productoId);

            return View::make('admin.variantes.index', [
                'producto' => $result['producto'],
                'variantes' => $result['producto']->variantes()->orderBy('orden')->get(),
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Mostrar formulario para crear nueva variante
     */
    public function variantesCreate(int $productoId)
    {
        try {
            $producto = Producto::findOrFail($productoId);

            return View::make('admin.variantes.create', compact('producto'));

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Guardar nueva variante
     */
    public function variantesStore(Request $request, int $productoId)
    {
        try {
            $result = $this->productoService->createVariante($productoId, $request);

            if ($result['success']) {
                return $this->redirectSuccess('productos.variantes.index', 'Variante creada exitosamente', ['productoId' => $productoId]);
            }

            return $this->backWithInput($result['message']);

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'productos.variantes.create', ['producto' => $productoId]);
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.variantes.create', ['producto' => $productoId]);
        }
    }

    /**
     * Mostrar formulario para editar variante
     */
    public function variantesEdit(int $productoId, int $varianteId)
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $variante = VarianteProducto::where('producto_id', $productoId)
                ->where('variante_id', $varianteId)
                ->firstOrFail();

            return View::make('admin.variantes.edit', compact('producto', 'variante'));

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Actualizar variante
     */
    public function variantesUpdate(Request $request, int $productoId, int $varianteId)
    {
        try {
            $result = $this->productoService->updateVariante($productoId, $varianteId, $request);

            if ($result['success']) {
                return $this->redirectSuccess('productos.variantes.index', 'Variante actualizada exitosamente', ['productoId' => $productoId]);
            }

            return $this->backWithInput($result['message']);

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'productos.variantes.edit', ['producto' => $productoId, 'variante' => $varianteId]);
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.variantes.edit', ['producto' => $productoId, 'variante' => $varianteId]);
        }
    }

    /**
     * Eliminar variante
     */
    public function variantesDestroy(int $productoId, int $varianteId)
    {
        try {
            $result = $this->productoService->deleteVariante($productoId, $varianteId);

            if ($result['success']) {
                return $this->redirectSuccess('productos.variantes.index', 'Variante eliminada exitosamente', ['productoId' => $productoId]);
            }

            return $this->backWithInput($result['message']);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.variantes.index', ['producto' => $productoId]);
        }
    }

    // ===== MÉTODOS DE GESTIÓN DE RESEÑAS (Consolidados desde ResenaController) =====

    /**
     * Mostrar reseñas de un producto
     */
    public function resenasIndex(int $productoId)
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $resenas = $producto->resenas()->with('usuario')->orderBy('created_at', 'desc')->get();

            return View::make('admin.productos.resenas.index', compact('producto', 'resenas'));
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Mostrar formulario para crear reseña
     */
    public function resenasCreate(int $productoId)
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $usuarios = Usuario::all();

            return View::make('admin.productos.resenas.create', compact('producto', 'usuarios'));
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Guardar nueva reseña
     */
    public function resenasStore(Request $request, int $productoId)
    {
        try {
            $result = $this->productoService->createResena($productoId, $request);

            if ($result['success']) {
                return $this->redirectSuccess('productos.resenas.index', 'Reseña creada exitosamente', ['productoId' => $productoId]);
            }

            return $this->backWithInput($result['message']);

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'productos.resenas.create', ['producto' => $productoId]);
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.resenas.create', ['producto' => $productoId]);
        }
    }

    /**
     * Mostrar formulario para editar reseña
     */
    public function resenasEdit(int $productoId, int $resenaId)
    {
        try {
            $producto = Producto::findOrFail($productoId);
            $resena = $producto->resenas()->findOrFail($resenaId);
            $usuarios = Usuario::all();

            return View::make('admin.productos.resenas.edit', compact('producto', 'resena', 'usuarios'));
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Actualizar reseña
     */
    public function resenasUpdate(Request $request, int $productoId, int $resenaId)
    {
        try {
            $result = $this->productoService->updateResena($productoId, $resenaId, $request);

            if ($result['success']) {
                return $this->redirectSuccess('productos.resenas.index', 'Reseña actualizada exitosamente', ['productoId' => $productoId]);
            }

            return $this->backWithInput($result['message']);

        } catch (ValidationException $e) {
            return $this->handleValidationException($e, 'productos.resenas.edit', ['producto' => $productoId, 'resena' => $resenaId]);
        } catch (Exception $e) {
            return $this->handleException($e, 'productos.resenas.edit', ['producto' => $productoId, 'resena' => $resenaId]);
        }
    }

    /**
     * Eliminar reseña
     */
    public function resenasDestroy(int $productoId, int $resenaId)
    {
        try {
            $result = $this->productoService->deleteResena($productoId, $resenaId);

            if ($result['success']) {
                return $this->redirectSuccess('productos.resenas.index', 'Reseña eliminada exitosamente', ['productoId' => $productoId]);
            }

            return $this->backWithInput($result['message']);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
        }
    }

    /**
     * Eliminar una imagen específica del producto
     */
    public function destroyImagen(int $productoId, int $imagenId)
    {
        try {
            $result = $this->productoService->deleteImage($productoId, $imagenId);

            if ($result['success']) {
                return $this->redirectSuccess('productos.edit', 'Imagen eliminada exitosamente', ['producto' => $productoId]);
            }

            return $this->backError($result['message']);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.edit', ['producto' => $productoId]);
        }
    }
}
