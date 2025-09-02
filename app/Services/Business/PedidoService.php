<?php

namespace App\Services\Business;

use App\Services\Base\BaseService;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\VarianteProducto;
use App\Models\Usuario;
use App\Models\DireccionEnvio;
use App\Models\MetodoPago;
use App\Models\EstadoPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PedidoService extends BaseService
{
    /**
     * Obtiene todos los pedidos del usuario autenticado
     */
    public function getUserOrders(array $filters = []): array
    {
        $this->logOperation('obteniendo_pedidos_usuario', ['user_id' => Auth::id()]);

        try {
            $query = Pedido::where('usuario_id', Auth::id())
                ->with(['items.producto', 'items.variante', 'estado', 'direccionEnvio', 'metodoPago'])
                ->orderBy('created_at', 'desc');

            // Aplicar filtros
            if (!empty($filters['estado_id'])) {
                $query->where('estado_id', $filters['estado_id']);
            }

            if (!empty($filters['fecha_desde'])) {
                $query->whereDate('created_at', '>=', $filters['fecha_desde']);
            }

            if (!empty($filters['fecha_hasta'])) {
                $query->whereDate('created_at', '<=', $filters['fecha_hasta']);
            }

            $pedidos = $query->paginate($filters['per_page'] ?? 15);

            return $this->formatSuccessResponse($pedidos, 'Pedidos obtenidos exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_pedidos_usuario', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene todos los pedidos (para administradores)
     */
    public function getAllOrders(array $filters = []): array
    {
        $this->logOperation('obteniendo_todos_pedidos', ['user_id' => Auth::id()]);

        try {
            $query = Pedido::with([
                'usuario', 
                'items.producto', 
                'items.variante', 
                'estado', 
                'direccionEnvio', 
                'metodoPago'
            ])->orderBy('created_at', 'desc');

            // Aplicar filtros
            if (!empty($filters['estado_id'])) {
                $query->where('estado_id', $filters['estado_id']);
            }

            if (!empty($filters['usuario_id'])) {
                $query->where('usuario_id', $filters['usuario_id']);
            }

            if (!empty($filters['fecha_desde'])) {
                $query->whereDate('created_at', '>=', $filters['fecha_desde']);
            }

            if (!empty($filters['fecha_hasta'])) {
                $query->whereDate('created_at', '<=', $filters['fecha_hasta']);
            }

            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('numero_pedido', 'like', '%' . $filters['search'] . '%')
                      ->orWhereHas('usuario', function ($uq) use ($filters) {
                          $uq->where('nombre', 'like', '%' . $filters['search'] . '%')
                             ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                      });
                });
            }

            $pedidos = $query->paginate($filters['per_page'] ?? 20);

            return $this->formatSuccessResponse($pedidos, 'Pedidos obtenidos exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_todos_pedidos', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene un pedido específico por ID
     */
    public function getOrderById(int $pedidoId): array
    {
        try {
            $pedido = Pedido::with([
                'usuario',
                'items.producto', 
                'items.variante', 
                'estado', 
                'direccionEnvio', 
                'metodoPago',
                'historialEstados'
            ])->findOrFail($pedidoId);

            // Verificar permisos
            if (!Auth::user()->hasRole('admin') && $pedido->usuario_id !== Auth::id()) {
                throw new Exception('No tienes permisos para ver este pedido');
            }

            return $this->formatSuccessResponse($pedido, 'Pedido obtenido exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_pedido', [
                'pedido_id' => $pedidoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Crea un nuevo pedido desde el carrito
     */
    public function createOrderFromCart(Request $request): array
    {
        $this->logOperation('creando_pedido_desde_carrito', ['user_id' => Auth::id()]);

        return $this->executeInTransaction(function () use ($request) {
            // Validar datos de entrada
            $data = $this->validateCreateOrderData($request);
            
            // Obtener carrito del usuario
            $carrito = $this->getUserCart();
            
            if (empty($carrito['items'])) {
                throw new Exception('El carrito está vacío');
            }

            // Verificar disponibilidad de stock
            $this->validateCartStock($carrito['items']);
            
            // Crear pedido
            $pedido = $this->createOrder($data, $carrito);
            
            // Crear items del pedido
            $this->createOrderItems($pedido, $carrito['items']);
            
            // Actualizar stock de productos
            $this->updateProductStock($carrito['items']);
            
            // Limpiar carrito
            $this->clearUserCart();
            
            // Crear historial de estado
            $this->createOrderStatusHistory($pedido, 'creado');

            $this->logOperation('pedido_creado_exitosamente', [
                'pedido_id' => $pedido->id,
                'user_id' => Auth::id(),
                'total_items' => count($carrito['items'])
            ]);

            return $this->formatSuccessResponse($pedido, 'Pedido creado exitosamente');

        }, 'crear pedido desde carrito');
    }

    /**
     * Actualiza el estado de un pedido
     */
    public function updateOrderStatus(int $pedidoId, Request $request): array
    {
        $this->logOperation('actualizando_estado_pedido', [
            'pedido_id' => $pedidoId,
            'user_id' => Auth::id()
        ]);

        return $this->executeInTransaction(function () use ($pedidoId, $request) {
            $data = $this->validateUpdateStatusData($request);
            
            $pedido = Pedido::findOrFail($pedidoId);
            
            // Verificar permisos
            if (!Auth::user()->hasRole('admin')) {
                throw new Exception('No tienes permisos para actualizar el estado del pedido');
            }

            $estadoAnterior = $pedido->estado->nombre;
            $pedido->update(['estado_id' => $data['estado_id']]);
            
            // Crear historial de estado
            $this->createOrderStatusHistory($pedido, $estadoAnterior, $data['comentario'] ?? null);
            
            // Enviar notificaciones según el estado
            $this->sendStatusNotifications($pedido, $estadoAnterior);

            $this->logOperation('estado_pedido_actualizado', [
                'pedido_id' => $pedidoId,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $pedido->estado->nombre,
                'user_id' => Auth::id()
            ]);

            return $this->formatSuccessResponse($pedido, 'Estado del pedido actualizado exitosamente');

        }, 'actualizar estado del pedido');
    }

    /**
     * Cancela un pedido
     */
    public function cancelOrder(int $pedidoId, Request $request): array
    {
        $this->logOperation('cancelando_pedido', [
            'pedido_id' => $pedidoId,
            'user_id' => Auth::id()
        ]);

        return $this->executeInTransaction(function () use ($pedidoId, $request) {
            $pedido = Pedido::with(['items.producto', 'items.variante'])->findOrFail($pedidoId);
            
            // Verificar permisos
            if (!Auth::user()->hasRole('admin') && $pedido->usuario_id !== Auth::id()) {
                throw new Exception('No tienes permisos para cancelar este pedido');
            }

            // Verificar que el pedido se pueda cancelar
            if (!in_array($pedido->estado->nombre, ['creado', 'confirmado', 'en_proceso'])) {
                throw new Exception('No se puede cancelar un pedido en este estado');
            }

            // Restaurar stock
            $this->restoreProductStock($pedido->items);
            
            // Actualizar estado a cancelado
            $estadoCancelado = EstadoPedido::where('nombre', 'cancelado')->first();
            $pedido->update(['estado_id' => $estadoCancelado->id]);
            
            // Crear historial de estado
            $this->createOrderStatusHistory($pedido, 'cancelado', $request->input('motivo_cancelacion'));
            
            // Enviar notificación de cancelación
            $this->sendCancellationNotification($pedido);

            $this->logOperation('pedido_cancelado_exitosamente', [
                'pedido_id' => $pedidoId,
                'user_id' => Auth::id(),
                'motivo' => $request->input('motivo_cancelacion')
            ]);

            return $this->formatSuccessResponse($pedido, 'Pedido cancelado exitosamente');

        }, 'cancelar pedido');
    }

    /**
     * Obtiene estadísticas de pedidos
     */
    public function getOrderStatistics(array $filters = []): array
    {
        try {
            $query = Pedido::query();

            // Aplicar filtros de fecha
            if (!empty($filters['fecha_desde'])) {
                $query->whereDate('created_at', '>=', $filters['fecha_desde']);
            }

            if (!empty($filters['fecha_hasta'])) {
                $query->whereDate('created_at', '<=', $filters['fecha_hasta']);
            }

            $estadisticas = [
                'total_pedidos' => $query->count(),
                'pedidos_pendientes' => $query->whereHas('estado', function ($q) {
                    $q->whereIn('nombre', ['creado', 'confirmado', 'en_proceso']);
                })->count(),
                'pedidos_completados' => $query->whereHas('estado', function ($q) {
                    $q->where('nombre', 'entregado');
                })->count(),
                'pedidos_cancelados' => $query->whereHas('estado', function ($q) {
                    $q->where('nombre', 'cancelado');
                })->count(),
                'total_ventas' => $query->sum('total'),
                'promedio_por_pedido' => $query->avg('total')
            ];

            return $this->formatSuccessResponse($estadisticas, 'Estadísticas obtenidas exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_estadisticas', ['error' => $e->getMessage()], 'error');
            throw $e;
        }
    }

    /**
     * Obtiene el historial de estados de un pedido
     */
    public function getOrderStatusHistory(int $pedidoId): array
    {
        try {
            $pedido = Pedido::findOrFail($pedidoId);
            
            // Verificar permisos
            if (!Auth::user()->hasRole('admin') && $pedido->usuario_id !== Auth::id()) {
                throw new Exception('No tienes permisos para ver este pedido');
            }

            $historial = $pedido->historialEstados()->orderBy('created_at', 'desc')->get();

            return $this->formatSuccessResponse($historial, 'Historial de estados obtenido exitosamente');

        } catch (Exception $e) {
            $this->logOperation('error_obteniendo_historial_estados', [
                'pedido_id' => $pedidoId,
                'error' => $e->getMessage()
            ], 'error');
            throw $e;
        }
    }

    /**
     * Valida los datos para crear un pedido
     */
    private function validateCreateOrderData(Request $request): array
    {
        $rules = [
            'direccion_envio_id' => 'required|exists:direcciones_envio,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'notas' => 'nullable|string|max:500'
        ];

        $messages = [
            'direccion_envio_id.required' => 'Debe seleccionar una dirección de envío',
            'direccion_envio_id.exists' => 'La dirección de envío no existe',
            'metodo_pago_id.required' => 'Debe seleccionar un método de pago',
            'metodo_pago_id.exists' => 'El método de pago no existe'
        ];

        $validator = validator($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new Exception('Datos inválidos: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Valida los datos para actualizar el estado
     */
    private function validateUpdateStatusData(Request $request): array
    {
        $rules = [
            'estado_id' => 'required|exists:estados_pedido,id',
            'comentario' => 'nullable|string|max:500'
        ];

        $messages = [
            'estado_id.required' => 'Debe seleccionar un estado',
            'estado_id.exists' => 'El estado seleccionado no existe'
        ];

        $validator = validator($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            throw new Exception('Datos inválidos: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }

    /**
     * Obtiene el carrito del usuario
     */
    private function getUserCart(): array
    {
        // Aquí se integraría con el CarritoService
        // Por ahora, simulamos la obtención del carrito
        $carrito = DB::table('carritos')
            ->join('carrito_items', 'carritos.id', '=', 'carrito_items.carrito_id')
            ->join('productos', 'carrito_items.producto_id', '=', 'productos.producto_id')
            ->leftJoin('variantes_producto', 'carrito_items.variante_id', '=', 'variantes_producto.variante_id')
            ->where('carritos.usuario_id', Auth::id())
            ->select([
                'carrito_items.id',
                'carrito_items.producto_id',
                'carrito_items.variante_id',
                'carrito_items.cantidad',
                'productos.precio',
                'variantes_producto.precio_adicional'
            ])
            ->get()
            ->toArray();

        return [
            'items' => $carrito,
            'total' => collect($carrito)->sum(function ($item) {
                $precio = $item->precio + ($item->precio_adicional ?? 0);
                return $precio * $item->cantidad;
            })
        ];
    }

    /**
     * Valida el stock del carrito
     */
    private function validateCartStock(array $items): void
    {
        foreach ($items as $item) {
            if ($item->variante_id) {
                $variante = VarianteProducto::find($item->variante_id);
                if (!$variante || $variante->stock < $item->cantidad) {
                    throw new Exception("Stock insuficiente para la variante del producto");
                }
            } else {
                $producto = Producto::find($item->producto_id);
                if (!$producto || $producto->stock < $item->cantidad) {
                    throw new Exception("Stock insuficiente para el producto {$producto->nombre_producto}");
                }
            }
        }
    }

    /**
     * Crea el pedido
     */
    private function createOrder(array $data, array $carrito): Pedido
    {
        $estadoCreado = EstadoPedido::where('nombre', 'creado')->first();
        
        return Pedido::create([
            'usuario_id' => Auth::id(),
            'numero_pedido' => $this->generateOrderNumber(),
            'estado_id' => $estadoCreado->id,
            'direccion_envio_id' => $data['direccion_envio_id'],
            'metodo_pago_id' => $data['metodo_pago_id'],
            'total' => $carrito['total'],
            'notas' => $data['notas'] ?? null,
            'fecha_pedido' => now()
        ]);
    }

    /**
     * Crea los items del pedido
     */
    private function createOrderItems(Pedido $pedido, array $items): void
    {
        foreach ($items as $item) {
            $precio = $item->precio + ($item->precio_adicional ?? 0);
            
            PedidoItem::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $item->producto_id,
                'variante_id' => $item->variante_id,
                'cantidad' => $item->cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $precio * $item->cantidad
            ]);
        }
    }

    /**
     * Actualiza el stock de los productos
     */
    private function updateProductStock(array $items): void
    {
        foreach ($items as $item) {
            if ($item->variante_id) {
                $variante = VarianteProducto::find($item->variante_id);
                $variante->decrement('stock', $item->cantidad);
            } else {
                $producto = Producto::find($item->producto_id);
                $producto->decrement('stock', $item->cantidad);
            }
        }
    }

    /**
     * Restaura el stock de los productos
     */
    private function restoreProductStock($items): void
    {
        foreach ($items as $item) {
            if ($item->variante_id) {
                $variante = VarianteProducto::find($item->variante_id);
                $variante->increment('stock', $item->cantidad);
            } else {
                $producto = Producto::find($item->producto_id);
                $producto->increment('stock', $item->cantidad);
            }
        }
    }

    /**
     * Limpia el carrito del usuario
     */
    private function clearUserCart(): void
    {
        DB::table('carrito_items')
            ->join('carritos', 'carrito_items.carrito_id', '=', 'carritos.id')
            ->where('carritos.usuario_id', Auth::id())
            ->delete();
    }

    /**
     * Crea el historial de estados del pedido
     */
    private function createOrderStatusHistory(Pedido $pedido, string $estado, ?string $comentario = null): void
    {
        $pedido->historialEstados()->create([
            'estado_id' => $pedido->estado_id,
            'comentario' => $comentario,
            'fecha_cambio' => now()
        ]);
    }

    /**
     * Envía notificaciones según el estado
     */
    private function sendStatusNotifications(Pedido $pedido, string $estadoAnterior): void
    {
        // Aquí se implementaría el envío de notificaciones
        // Por ejemplo, emails, SMS, notificaciones push, etc.
        $this->logOperation('notificaciones_enviadas', [
            'pedido_id' => $pedido->id,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $pedido->estado->nombre
        ]);
    }

    /**
     * Envía notificación de cancelación
     */
    private function sendCancellationNotification(Pedido $pedido): void
    {
        // Aquí se implementaría el envío de notificación de cancelación
        $this->logOperation('notificacion_cancelacion_enviada', [
            'pedido_id' => $pedido->id
        ]);
    }

    /**
     * Genera un número único de pedido
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'PED';
        $year = date('Y');
        $month = date('m');
        
        $lastOrder = Pedido::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (intval(substr($lastOrder->numero_pedido, -4)) + 1) : 1;
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }
}
