<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Base\WebController;
use App\Models\Pedido;
use App\Services\Business\PedidoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class PedidoController extends WebController
{
    protected $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    /**
     * Muestra el historial de pedidos del usuario autenticado
     */
    public function historial(Request $request)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (! Auth::check()) {
                return Redirect::route('login');
            }

            $filters = [
                'per_page' => $request->get('per_page', 15),
                'estado_id' => $request->get('estado_id'),
                'fecha_desde' => $request->get('fecha_desde'),
                'fecha_hasta' => $request->get('fecha_hasta'),
            ];

            $result = $this->pedidoService->getUserOrders($filters);

            return View::make('modules.cliente.pedidos.historial', [
                'pedidos' => $result['data'],
            ]);

        } catch (Exception $e) {
            // Log del error
            Log::error('Error al cargar historial de pedidos: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::back()->with('error', 'Error al cargar el historial de pedidos. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Muestra el detalle de un pedido específico
     */
    public function detalle($pedidoId)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (! Auth::check()) {
                return Redirect::route('login');
            }

            // Obtener el pedido y verificar que pertenezca al usuario
            $pedido = Pedido::where('pedido_id', $pedidoId)
                ->where('usuario_id', Auth::id())
                ->with(['detalles.producto', 'detalles.variante', 'estado', 'direccion', 'pago.metodoPago', 'resenas'])
                ->firstOrFail();

            // Verificar si el pedido está confirmado y puede ser calificado
            $estaConfirmado = $pedido->isConfirmado();
            $puedeCalificar = $estaConfirmado && $pedido->puedeCalificar();
            $yaCalificado = $estaConfirmado && !$pedido->puedeCalificar() && $pedido->resenas->isNotEmpty();

            return View::make('modules.cliente.pedidos.detalle', [
                'pedido' => $pedido,
                'puedeCalificar' => $puedeCalificar,
                'yaCalificado' => $yaCalificado,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Redirect::route('pedidos.historial')->with('error', 'Pedido no encontrado.');
        } catch (Exception $e) {
            Log::error('Error al cargar detalle de pedido: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'pedido_id' => $pedidoId,
                'error' => $e->getMessage(),
            ]);

            return Redirect::back()->with('error', 'Error al cargar el detalle del pedido. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Genera y descarga la factura en PDF del pedido
     */
    public function factura($pedidoId)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (! Auth::check()) {
                return Redirect::route('login');
            }

            // Obtener el pedido y verificar que pertenezca al usuario
            $pedido = Pedido::where('pedido_id', $pedidoId)
                ->where('usuario_id', Auth::id())
                ->with(['detalles.producto', 'detalles.variante', 'estado', 'direccion', 'pago.metodoPago', 'usuario'])
                ->firstOrFail();

            // Calcular totales
            $subtotal = $pedido->detalles->sum(function ($detalle) {
                return $detalle->cantidad * $detalle->precio_unitario;
            });
            $total = $subtotal; // Envío gratis

            // Generar HTML de la vista
            $html = View::make('modules.cliente.pedidos.factura', [
                'pedido' => $pedido,
                'subtotal' => $subtotal,
                'total' => $total,
            ])->render();

            // Crear instancia de DomPDF
            $dompdf = new \Dompdf\Dompdf;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Configurar el nombre del archivo
            $filename = 'factura_pedido_'.$pedido->pedido_id.'_'.date('Y-m-d').'.pdf';

            // Descargar el PDF
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Redirect::route('pedidos.historial')->with('error', 'Pedido no encontrado.');
        } catch (Exception $e) {
            Log::error('Error al generar factura PDF: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'pedido_id' => $pedidoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::back()->with('error', 'Error al generar la factura. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Calificar pedido confirmado (solo el cliente que hizo el pedido)
     */
    public function calificar(Request $request, $pedidoId)
    {
        try {
            // Verificar que el usuario esté autenticado
            if (!Auth::check()) {
                return Redirect::route('login')->with('error', 'Debes iniciar sesión para calificar un pedido.');
            }

            // Validar datos
            $request->validate([
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required|exists:productos,producto_id',
                'productos.*.calificacion' => 'required|integer|min:1|max:5',
                'productos.*.comentario' => 'nullable|string|max:1000',
            ]);

            // Obtener el pedido y verificar que pertenezca al usuario
            $pedido = Pedido::where('pedido_id', $pedidoId)
                ->where('usuario_id', Auth::id())
                ->with(['detalles.producto', 'estado', 'resenas'])
                ->firstOrFail();

            // Verificar que el pedido esté confirmado
            if (!$pedido->isConfirmado()) {
                return Redirect::back()
                    ->with('error', 'Solo puedes calificar pedidos confirmados.');
            }

            // Verificar si el pedido ya está completamente calificado
            // Si todos los productos tienen reseñas, no permitir enviar más
            if (!$pedido->puedeCalificar() && $pedido->resenas->isNotEmpty()) {
                return Redirect::back()
                    ->with('error', 'Este pedido ya ha sido completamente calificado. No puedes agregar más reseñas.');
            }

            // Verificar que los productos calificados pertenezcan al pedido
            $productosPedido = $pedido->detalles->pluck('producto_id')->toArray();
            foreach ($request->productos as $productoData) {
                if (!in_array($productoData['producto_id'], $productosPedido)) {
                    return Redirect::back()
                        ->with('error', 'Uno o más productos no pertenecen a este pedido.');
                }
            }

            // Crear o actualizar reseñas de pedido para cada producto calificado
            // Las reseñas desde el pedido son privadas (con pedido_id) y no se muestran en el show del producto
            $resenasCreadas = [];
            foreach ($request->productos as $productoData) {
                // Buscar reseña existente del usuario para este producto en este pedido específico
                $resenaExistente = \App\Models\Resena::where('usuario_id', Auth::id())
                    ->where('producto_id', $productoData['producto_id'])
                    ->where('pedido_id', $pedido->pedido_id)
                    ->first();

                if ($resenaExistente) {
                    // Actualizar reseña existente del pedido
                    $resenaExistente->update([
                        'calificacion' => $productoData['calificacion'],
                        'comentario' => $productoData['comentario'] ?? null,
                        'activa' => true,
                        'verificada' => false, // Resetear verificación al actualizar
                    ]);
                    $resenasCreadas[] = $resenaExistente;
                } else {
                    // Verificar límite de reseñas de pedido por usuario/producto
                    // Contar solo reseñas con pedido_id (reseñas privadas de pedidos)
                    $totalResenasPedido = \App\Models\Resena::where('usuario_id', Auth::id())
                        ->where('producto_id', $productoData['producto_id'])
                        ->whereNotNull('pedido_id') // Solo reseñas de pedidos
                        ->count();
                    
                    if ($totalResenasPedido >= 2) {
                        return Redirect::back()
                            ->with('error', 'Ya has alcanzado el máximo de 2 reseñas de pedido para uno o más productos.');
                    }
                    
                    // Crear nueva reseña de pedido (con pedido_id - privada)
                    $resena = \App\Models\Resena::create([
                        'usuario_id' => Auth::id(),
                        'producto_id' => $productoData['producto_id'],
                        'pedido_id' => $pedido->pedido_id, // Reseña privada asociada al pedido
                        'calificacion' => $productoData['calificacion'],
                        'comentario' => $productoData['comentario'] ?? null,
                        'verificada' => false,
                        'activa' => true,
                    ]);
                    $resenasCreadas[] = $resena;
                }
            }

            Log::info('Pedido calificado por cliente', [
                'pedido_id' => $pedido->pedido_id,
                'usuario_id' => Auth::id(),
                'productos_calificados' => count($resenasCreadas),
            ]);

            return Redirect::back()
                ->with('success', '¡Gracias por calificar tu pedido! Tu opinión es muy importante para nosotros.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Redirect::route('pedidos.historial')
                ->with('error', 'Pedido no encontrado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Redirect::back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            Log::error('Error al calificar pedido: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'pedido_id' => $pedidoId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::back()
                ->with('error', 'Error al calificar el pedido. Por favor, inténtalo de nuevo.');
        }
    }
}
