<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Usuario;
use App\Models\VarianteProducto;
use App\Models\WebhookEvent;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    /**
     * Obtener estadísticas básicas del dashboard
     */
    public function getBasicStats(): array
    {
        try {
            return [
                'totalProductos' => Producto::count(),
                'usuarios' => Usuario::count(),
                'totalCategorias' => Categoria::count(),
                'totalMarcas' => Marca::count(),
                'total_variantes'=> VarianteProducto::count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas básicas: ' . $e->getMessage());
            return [
                'totalProductos' => 0,
                'usuarios' => 0,
                'totalCategorias' => 0,
                'totalMarcas' => 0,
                'total_variantes'=> 0,
            ];
        }
    }

    /**
     * Obtener estadísticas de webhooks
     */
    public function getWebhookStats(): array
    {
        try {
            return [
                'total_events' => WebhookEvent::count(),
                'processed_events' => WebhookEvent::processed()->count(),
                'failed_events' => WebhookEvent::failed()->count(),
                'pending_events' => WebhookEvent::pending()->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de webhooks: ' . $e->getMessage());
            return [
                'total_events' => 0,
                'processed_events' => 0,
                'failed_events' => 0,
                'pending_events' => 0,
            ];
        }
    }

    /**
     * Obtener estadísticas de pedidos
     */
    public function getPedidoStats(): array
    {
        try {
            return [
                'total_pedidos' => Pedido::count(),
                'pedidos_pendientes' => Pedido::where('estado_id', 1)->count(),
                'pedidos_confirmados' => Pedido::where('estado_id', 2)->count(),
                'pedidos_cancelados' => Pedido::where('estado_id', 3)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de pedidos: ' . $e->getMessage());
            return [
                'total_pedidos' => 0,
                'pedidos_pendientes' => 0,
                'pedidos_confirmados' => 0,
                'pedidos_cancelados' => 0,
            ];
        }
    }

    /**
     * Obtener productos recientes
     */
    public function getRecentProducts(int $limit = 5): array
    {
        try {
            $productos = Producto::with(['categoria', 'marca', 'imagenes'])
                ->orderBy('created_at', 'desc')
                ->take($limit)
                ->get();

            return [
                'success' => true,
                'data' => $productos
            ];
        } catch (\Exception $e) {
            Log::error('Error obteniendo productos recientes: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => collect(),
                'error' => 'Error al cargar productos recientes'
            ];
        }
    }

    /**
     * Obtener webhooks filtrados
     */
    public function getFilteredWebhooks(Request $request): array
    {
        try {
            $query = WebhookEvent::with('pedido');

            // Filtro por estado
            if ($request->filled('status')) {
                $status = $request->status;
                switch ($status) {
                    case 'processed':
                        $query->processed();
                        break;
                    case 'failed':
                        $query->failed();
                        break;
                    case 'pending':
                        $query->pending();
                        break;
                }
            }

            // Filtro por tipo de evento
            if ($request->filled('event_type')) {
                $query->where('event_type', $request->event_type);
            }

            // Filtro por fecha desde
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            // Filtro por fecha hasta
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Filtro por pedido
            if ($request->filled('pedido_id')) {
                $query->where('pedido_id', $request->pedido_id);
            }

            // Ordenar y limitar
            $limit = $request->get('limit', 10);
            $webhooks = $query->orderBy('created_at', 'desc')->limit($limit)->get();

            return [
                'success' => true,
                'data' => $webhooks
            ];

        } catch (\Exception $e) {
            Log::error('Error obteniendo webhooks filtrados: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => collect(),
                'error' => 'Error al cargar webhooks'
            ];
        }
    }

    /**
     * Obtener filtros aplicados
     */
    public function getAppliedFilters(Request $request): array
    {
        return [
            'status' => $request->status,
            'event_type' => $request->event_type,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'pedido_id' => $request->pedido_id,
            'limit' => $request->get('limit', 10),
        ];
    }

    /**
     * Obtener datos completos del dashboard
     */
    public function getDashboardData(Request $request): array
    {
        try {
            $basicStats = $this->getBasicStats();
            $webhookStats = $this->getWebhookStats();
            $pedidoStats = $this->getPedidoStats();
            $recentProducts = $this->getRecentProducts(5);
            $filteredWebhooks = $this->getFilteredWebhooks($request);
            $filters = $this->getAppliedFilters($request);

            return [
                'success' => true,
                'basicStats' => $basicStats,
                'webhookStats' => $webhookStats,
                'pedidoStats' => $pedidoStats,
                'recentProducts' => $recentProducts['data'],
                'filteredWebhooks' => $filteredWebhooks['data'],
                'filters' => $filters
            ];

        } catch (\Exception $e) {
            Log::error('Error obteniendo datos del dashboard: ' . $e->getMessage());
            return [
                'success' => false,
                'basicStats' => [],
                'webhookStats' => [],
                'pedidoStats' => [],
                'recentProducts' => collect(),
                'filteredWebhooks' => collect(),
                'filters' => [],
                'error' => 'Error al cargar datos del dashboard'
            ];
        }
    }
}
