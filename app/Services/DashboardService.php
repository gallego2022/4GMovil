<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Usuario;
use App\Models\VarianteProducto;
use App\Models\WebhookEvent;
use App\Models\Pedido;
use App\Services\RedisCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    protected $cacheService;

    public function __construct(RedisCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    /**
     * Obtener estadísticas básicas del dashboard (optimizado con caché)
     */
    public function getBasicStats(): array
    {
        $cacheKey = 'dashboard:basic_stats';
        
        return $this->cacheService->remember($cacheKey, 300, function () {
            try {
                // Usar una sola consulta para obtener todos los conteos
                $stats = \DB::select("
                    SELECT 
                        (SELECT COUNT(*) FROM productos) as totalProductos,
                        (SELECT COUNT(*) FROM usuarios) as usuarios,
                        (SELECT COUNT(*) FROM categorias) as totalCategorias,
                        (SELECT COUNT(*) FROM marcas) as totalMarcas,
                        (SELECT COUNT(*) FROM variantes_producto) as total_variantes
                ")[0];

                return [
                    'totalProductos' => (int) $stats->totalProductos,
                    'usuarios' => (int) $stats->usuarios,
                    'totalCategorias' => (int) $stats->totalCategorias,
                    'totalMarcas' => (int) $stats->totalMarcas,
                    'total_variantes' => (int) $stats->total_variantes,
                ];
            } catch (\Exception $e) {
                Log::error('Error obteniendo estadísticas básicas: ' . $e->getMessage());
                return [
                    'totalProductos' => 0,
                    'usuarios' => 0,
                    'totalCategorias' => 0,
                    'totalMarcas' => 0,
                    'total_variantes' => 0,
                ];
            }
        });
    }

    /**
     * Obtener estadísticas de webhooks (optimizado con caché)
     */
    public function getWebhookStats(): array
    {
        $cacheKey = 'dashboard:webhook_stats';
        
        return $this->cacheService->remember($cacheKey, 180, function () {
            try {
                // Usar una sola consulta con GROUP BY para obtener todos los conteos
                $stats = \DB::select("
                    SELECT 
                        status,
                        COUNT(*) as count
                    FROM webhook_events 
                    GROUP BY status
                ");

                $result = [
                    'total_events' => 0,
                    'processed_events' => 0,
                    'failed_events' => 0,
                    'pending_events' => 0,
                ];

                foreach ($stats as $stat) {
                    $result['total_events'] += $stat->count;
                    switch ($stat->status) {
                        case 'processed':
                            $result['processed_events'] = $stat->count;
                            break;
                        case 'failed':
                            $result['failed_events'] = $stat->count;
                            break;
                        case 'pending':
                            $result['pending_events'] = $stat->count;
                            break;
                    }
                }

                return $result;
            } catch (\Exception $e) {
                Log::error('Error obteniendo estadísticas de webhooks: ' . $e->getMessage());
                return [
                    'total_events' => 0,
                    'processed_events' => 0,
                    'failed_events' => 0,
                    'pending_events' => 0,
                ];
            }
        });
    }

    /**
     * Obtener estadísticas de pedidos (optimizado con caché)
     */
    public function getPedidoStats(): array
    {
        $cacheKey = 'dashboard:pedido_stats';
        
        return $this->cacheService->remember($cacheKey, 300, function () {
            try {
                // Usar una sola consulta con GROUP BY para obtener todos los conteos
                $stats = \DB::select("
                    SELECT 
                        estado_id,
                        COUNT(*) as count
                    FROM pedidos 
                    GROUP BY estado_id
                ");

                $result = [
                    'total_pedidos' => 0,
                    'pedidos_pendientes' => 0,
                    'pedidos_confirmados' => 0,
                    'pedidos_cancelados' => 0,
                ];

                foreach ($stats as $stat) {
                    $result['total_pedidos'] += $stat->count;
                    switch ($stat->estado_id) {
                        case 1:
                            $result['pedidos_pendientes'] = $stat->count;
                            break;
                        case 2:
                            $result['pedidos_confirmados'] = $stat->count;
                            break;
                        case 3:
                            $result['pedidos_cancelados'] = $stat->count;
                            break;
                    }
                }

                return $result;
            } catch (\Exception $e) {
                Log::error('Error obteniendo estadísticas de pedidos: ' . $e->getMessage());
                return [
                    'total_pedidos' => 0,
                    'pedidos_pendientes' => 0,
                    'pedidos_confirmados' => 0,
                    'pedidos_cancelados' => 0,
                ];
            }
        });
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
     * Obtener datos completos del dashboard (optimizado con caché)
     */
    public function getDashboardData(Request $request): array
    {
        $cacheKey = 'dashboard:complete_data:' . md5(serialize($request->all()));
        
        return $this->cacheService->remember($cacheKey, 120, function () use ($request) {
            try {
                // Ejecutar consultas en paralelo para mejor rendimiento
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
                    'filters' => $filters,
                    'cached_at' => now()->toDateTimeString()
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
        });
    }
}
