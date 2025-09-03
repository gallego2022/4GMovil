<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Base\WebController;
use App\Services\Business\CarritoService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class CarritoController extends WebController
{
    protected $carritoService;

    public function __construct(CarritoService $carritoService)
    {
        $this->carritoService = $carritoService;
    }

    /**
     * Muestra el carrito del usuario
     */
    public function index()
    {
        try {
            $result = $this->carritoService->getCart();
            
            return view('pages.cliente.carrito.index', [
                'carrito' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'landing');
        }
    }

    /**
     * Agrega un producto al carrito
     */
    public function addToCart(Request $request)
    {
        try {
            $result = $this->carritoService->addToCart($request);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($result);
            }
            
            return $this->backSuccess('Producto agregado al carrito exitosamente');

        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return $this->backWithInput('Por favor, corrige los errores en el formulario');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->backError($e->getMessage());
        }
    }

    /**
     * Actualiza la cantidad de un item en el carrito
     */
    public function updateItem(Request $request, int $itemId)
    {
        try {
            $result = $this->carritoService->updateCartItem($itemId, $request);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json($result);
            }
            
            return $this->backSuccess('Carrito actualizado exitosamente');

        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return $this->backWithInput('Por favor, corrige los errores en el formulario');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->backError($e->getMessage());
        }
    }

    /**
     * Elimina un item del carrito
     */
    public function removeItem(int $itemId)
    {
        try {
            $result = $this->carritoService->removeFromCart($itemId);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json($result);
            }
            
            return $this->backSuccess('Producto eliminado del carrito exitosamente');

        } catch (Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->backError($e->getMessage());
        }
    }

    /**
     * Limpia todo el carrito
     */
    public function clear()
    {
        try {
            $result = $this->carritoService->clearCart();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json($result);
            }
            
            return $this->redirectSuccess('carrito.index', 'Carrito limpiado exitosamente');

        } catch (Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->redirectError('carrito.index', $e->getMessage());
        }
    }

    /**
     * Obtiene el resumen del carrito (para AJAX)
     */
    public function summary()
    {
        try {
            $result = $this->carritoService->getCartSummary();
            
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Sincroniza el carrito de sesión con el usuario al hacer login
     */
    public function sync()
    {
        try {
            $result = $this->carritoService->syncSessionCartWithUser();
            
            return $this->redirectSuccess('carrito.index', 'Carrito sincronizado exitosamente');

        } catch (Exception $e) {
            return $this->redirectError('carrito.index', $e->getMessage());
        }
    }

    /**
     * Muestra el mini carrito (para el header)
     */
    public function mini()
    {
        try {
            $result = $this->carritoService->getCartSummary();
            
            return view('components.mini-carrito', [
                'summary' => $result['data']
            ]);

        } catch (Exception $e) {
            return view('components.mini-carrito', [
                'summary' => [
                    'total_items' => 0,
                    'total_precio' => 0,
                    'items_count' => 0
                ]
            ]);
        }
    }

    /**
     * Obtiene el carrito en formato JSON para APIs
     */
    public function getCartJson()
    {
        try {
            $result = $this->carritoService->getCart();
            
            return response()->json($result);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verifica si el carrito está vacío
     */
    public function isEmpty()
    {
        try {
            $result = $this->carritoService->getCartSummary();
            $isEmpty = $result['data']['total_items'] == 0;
            
            return response()->json([
                'success' => true,
                'is_empty' => $isEmpty
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verifica el stock disponible para el carrito
     */
    public function verificarStock()
    {
        try {
            $cart = session('cart', []);
            
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El carrito está vacío'
                ]);
            }

            // Usar el ReservaStockService directamente
            $reservaStockService = app(\App\Services\ReservaStockService::class);
            $resultado = $reservaStockService->verificarStockCarrito($cart);
            
            return response()->json([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
