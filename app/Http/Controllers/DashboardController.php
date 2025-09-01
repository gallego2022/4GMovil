<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Usuario;
use App\Models\WebhookEvent;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        // Obtener estadísticas básicas
        $totalProductos = Producto::count();
        $usuarios = Usuario::count();
        $totalCategorias = Categoria::count();
        $totalMarcas = Marca::count();

        // Obtener estadísticas de webhooks
        $webhookStats = [
            'total_events' => WebhookEvent::count(),
            'processed_events' => WebhookEvent::processed()->count(),
            'failed_events' => WebhookEvent::failed()->count(),
            'pending_events' => WebhookEvent::pending()->count(),
        ];

        // Obtener estadísticas de pedidos
        $pedidoStats = [
            'total_pedidos' => Pedido::count(),
            'pedidos_pendientes' => Pedido::where('estado_id', 1)->count(),
            'pedidos_confirmados' => Pedido::where('estado_id', 2)->count(),
            'pedidos_cancelados' => Pedido::where('estado_id', 3)->count(),
        ];

        // Obtener eventos recientes de webhooks con filtros
        $recentWebhooks = $this->getFilteredWebhooks($request);

        // Obtener los últimos 5 productos agregados con sus relaciones
        $ultimosProductos = Producto::with(['categoria', 'marca', 'imagenes'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Obtener filtros aplicados
        $filters = $this->getAppliedFilters($request);

        return view('pages.admin.index', compact(
            'totalProductos',
            'totalCategorias',
            'totalMarcas',
            'usuarios',
            'ultimosProductos',
            'webhookStats',
            'pedidoStats',
            'recentWebhooks',
            'filters'
        ));
    }

    private function getFilteredWebhooks(Request $request)
    {
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
        return $query->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    private function getAppliedFilters(Request $request)
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
}
