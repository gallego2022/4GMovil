<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Services\Business\ProductoServiceOptimizadoCorregido;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

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
            
            return view('pages.admin.productos.listadoP', [
                'productos' => $result['data'],
                'categorias' => \App\Models\Categoria::all(),
                'marcas' => \App\Models\Marca::all()
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
            
            return view('pages.admin.productos.create', [
                'categorias' => $result['categorias'],
                'marcas' => $result['marcas']
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
            
            return $this->redirectSuccess('productos.index', 'Producto creado exitosamente');

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
            
            return view('pages.admin.productos.show', [
                'producto' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'productos.index');
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
            
            return view('pages.admin.productos.edit', [
                'producto' => $productoResult['data'],
                'categorias' => $formDataResult['categorias'],
                'marcas' => $formDataResult['marcas']
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
            
            return $this->redirectSuccess('productos.index', 'Producto actualizado exitosamente');

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
            
            return view('pages.admin.productos.search', [
                'productos' => $result['data'],
                'searchTerm' => $searchTerm,
                'categorias' => \App\Models\Categoria::all(),
                'marcas' => \App\Models\Marca::all()
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
            
            return view('pages.admin.productos.byCategory', [
                'productos' => $result['data'],
                'categoria' => \App\Models\Categoria::find($categoriaId)
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
            
            return view('pages.admin.productos.byBrand', [
                'productos' => $result['data'],
                'marca' => \App\Models\Marca::find($marcaId)
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
            return redirect()->route('productos.index')->with('info', 'La vista de productos con variantes ha sido consolidada en la vista principal de productos.');

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
            
            return view('productos.detalle-variantes', [
                'producto' => $result['producto'],
                'productosRelacionados' => $result['productosRelacionados']
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
            
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de stock'
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
            
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener variantes'
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
            
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar productos'
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
            
            return view('productos.por-categoria', [
                'productos' => $result['data'],
                'categoria' => \App\Models\Categoria::find($categoriaId)
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
            
            return view('productos.stock-bajo', [
                'productos' => $result['data']
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
            
            return view('productos.sin-stock', [
                'productos' => $result['data']
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
            
            return view('admin.variantes.index', [
                'producto' => $result['producto'],
                'variantes' => $result['producto']->variantes()->orderBy('orden')->get()
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
            $producto = \App\Models\Producto::findOrFail($productoId);
            return view('admin.variantes.create', compact('producto'));

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
            $producto = \App\Models\Producto::findOrFail($productoId);
            $variante = \App\Models\VarianteProducto::where('producto_id', $productoId)
                ->where('variante_id', $varianteId)
                ->firstOrFail();
            
            return view('admin.variantes.edit', compact('producto', 'variante'));

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
            $producto = \App\Models\Producto::findOrFail($productoId);
            $resenas = $producto->resenas()->with('usuario')->orderBy('created_at', 'desc')->get();
            
            return view('admin.productos.resenas.index', compact('producto', 'resenas'));
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
            $producto = \App\Models\Producto::findOrFail($productoId);
            $usuarios = \App\Models\Usuario::all();
            return view('admin.productos.resenas.create', compact('producto', 'usuarios'));
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
            $producto = \App\Models\Producto::findOrFail($productoId);
            $resena = $producto->resenas()->findOrFail($resenaId);
            $usuarios = \App\Models\Usuario::all();
            
            return view('admin.productos.resenas.edit', compact('producto', 'resena', 'usuarios'));
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
