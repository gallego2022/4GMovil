<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\WebController;
use App\Services\Business\PedidoService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\DireccionEnvio;
use App\Models\MetodoPago;
use App\Models\Pedido;
use App\Models\EstadoPedido;

class PedidoController extends WebController
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    /**
     * Muestra la lista de pedidos del usuario
     */
    public function index(Request $request)
    {
        try {
            $filters = $this->getFilterParams($request);
            $result = $this->pedidoService->getUserOrders($filters);
            
            return View::make('pages.cliente.pedidos.index', [
                'pedidos' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'landing');
        }
    }

    /**
     * Muestra el historial de pedidos del usuario (cliente)
     */
    public function historial(Request $request)
    {
        try {
            $filters = $this->getFilterParams($request);
            $result = $this->pedidoService->getUserOrders($filters);
            
            return View::make('pages.cliente.pedidos.historial', [
                'pedidos' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'landing');
        }
    }

    /**
     * Muestra la lista de todos los pedidos (admin)
     */
    public function adminIndex(Request $request)
    {
        try {
            $this->requireRole('admin');
            
            $filters = $this->getFilterParams($request);
            $result = $this->pedidoService->getAllOrders($filters);
            
            return View::make('pages.admin.pedidos.index', [
                'pedidos' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'admin.dashboard');
        }
    }

    /**
     * Muestra un pedido específico
     */
    public function show(int $id)
    {
        try {
            $result = $this->pedidoService->getOrderById($id);
            
            return View::make('pages.cliente.pedidos.show', [
                'pedido' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'pedidos.index');
        }
    }

    /**
     * Muestra el detalle de un pedido específico (cliente)
     */
    public function detalle(int $id)
    {
        try {
            $result = $this->pedidoService->getOrderById($id);
            
            return View::make('pages.cliente.pedidos.detalle', [
                'pedido' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'pedidos.historial');
        }
    }

    /**
     * Muestra un pedido específico (admin)
     */
    public function adminShow(int $id)
    {
        try {
            $this->requireRole('admin');
            
            $result = $this->pedidoService->getOrderById($id);
            
            return View::make('pages.admin.pedidos.show', [
                'pedido' => $result['data']
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'admin.pedidos.index');
        }
    }

    /**
     * Crea un pedido desde el carrito
     */
    public function store(Request $request)
    {
        try {
            $result = $this->pedidoService->createOrderFromCart($request);
            
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json($result);
            }
            
            return $this->redirectSuccess('pedidos.show', 'Pedido creado exitosamente', ['id' => $result['data']['id']]);

        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return $this->backWithInput('Por favor, corrige los errores en el formulario');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->backError($e->getMessage());
        }
    }

    /**
     * Actualiza el estado de un pedido (admin)
     */
    public function updateStatus(Request $request, int $id)
    {
        try {
            $this->requireRole('admin');
            
            $result = $this->pedidoService->updateOrderStatus($id, $request);
            
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json($result);
            }
            
            return $this->redirectSuccess('admin.pedidos.show', 'Estado del pedido actualizado exitosamente', ['id' => $id]);

        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return $this->backWithInput('Por favor, corrige los errores en el formulario');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->backError($e->getMessage());
        }
    }

    /**
     * Cancela un pedido
     */
    public function cancel(Request $request, int $id)
    {
        try {
            $result = $this->pedidoService->cancelOrder($id, $request);
            
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json($result);
            }
            
            return $this->redirectSuccess('pedidos.show', 'Pedido cancelado exitosamente', ['id' => $id]);

        } catch (ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return $this->backWithInput('Por favor, corrige los errores en el formulario');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->backError($e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas de pedidos (admin)
     */
    public function statistics(Request $request)
    {
        try {
            $this->requireRole('admin');
            
            $filters = $this->getFilterParams($request);
            $result = $this->pedidoService->getOrderStatistics($filters);
            
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json($result);
            }
            
            return View::make('pages.admin.pedidos.statistics', [
                'estadisticas' => $result['data']
            ]);

        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->handleException($e, 'admin.pedidos.index');
        }
    }

    /**
     * Obtiene el historial de estados de un pedido
     */
    public function statusHistory(int $id)
    {
        try {
            $result = $this->pedidoService->getOrderStatusHistory($id);
            
            if (request()->ajax() || request()->wantsJson()) {
                return Response::json($result);
            }
            
            return View::make('pages.cliente.pedidos.status-history', [
                'historial' => $result['data']
            ]);

        } catch (Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return Response::json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            
            return $this->handleException($e, 'pedidos.show', ['id' => $id]);
        }
    }

    /**
     * Muestra el formulario de creación de pedido
     */
    public function create()
    {
        try {
            // Aquí se obtendrían las direcciones de envío y métodos de pago del usuario
            $direcciones = DireccionEnvio::where('usuario_id', \Illuminate\Support\FacadesAuth::id())->get();
            $metodosPago = MetodoPago::where('activo', true)->get();
            
            return View::make('pages.cliente.pedidos.create', [
                'direcciones' => $direcciones,
                'metodosPago' => $metodosPago
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'pedidos.index');
        }
    }

    /**
     * Muestra el formulario de edición de estado (admin)
     */
    public function editStatus(int $id)
    {
        try {
            $this->requireRole('admin');
            
            $pedido = Pedido::with(['estado'])->findOrFail($id);
            $estados = EstadoPedido::all();
            
            return View::make('pages.admin.pedidos.edit-status', [
                'pedido' => $pedido,
                'estados' => $estados
            ]);

        } catch (Exception $e) {
            return $this->handleException($e, 'admin.pedidos.index');
        }
    }

    /**
     * Obtiene pedidos en formato JSON para APIs
     */
    public function getOrdersJson(Request $request)
    {
        try {
            if (\Illuminate\Support\FacadesAuth::user()->hasRole('admin')) {
                $result = $this->pedidoService->getAllOrders($request->all());
            } else {
                $result = $this->pedidoService->getUserOrders($request->all());
            }
            
            return Response::json($result);

        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtiene un pedido en formato JSON para APIs
     */
    public function getOrderJson(int $id)
    {
        try {
            $result = $this->pedidoService->getOrderById($id);
            
            return Response::json($result);

        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtiene parámetros de filtrado específicos para pedidos
     */
    protected function getFilterParams(Request $request): array
    {
        $filters = $request->only([
            'estado_id', 
            'usuario_id', 
            'fecha_desde', 
            'fecha_hasta', 
            'search', 
            'per_page'
        ]);
        
        // Eliminar filtros vacíos
        return array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
