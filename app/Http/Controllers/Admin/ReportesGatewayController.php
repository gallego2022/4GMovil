<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReportesGatewayController
{
    private function baseUrl(): string
    {
        return env('REPORTES_SERVICE_URL', 'http://localhost:8081');
    }

    public function ventasResumen(Request $request)
    {
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        $res = Http::get($this->baseUrl().'/reportes/ventas/resumen', ['desde' => $desde, 'hasta' => $hasta]);
        return response()->json($res->json());
    }

    public function topProductosDt(Request $request)
    {
        $draw = (int) $request->query('draw', 1);
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        $limit = (int) $request->query('length', 10);
        $params = [
            'desde' => $desde,
            'hasta' => $hasta,
            'limit' => $limit,
        ];
        if ($request->filled('categoria_id')) {
            $params['categoriaId'] = (int) $request->query('categoria_id');
        }
        if ($request->filled('marca_id')) {
            $params['marcaId'] = (int) $request->query('marca_id');
        }
        $res = Http::get($this->baseUrl().'/reportes/ventas/top-productos', $params);
        $items = $res->json() ?? [];
        $data = array_map(function ($i) {
            return [
                'producto' => $i['nombre'] ?? '',
                'cantidad' => $i['cantidadVendida'] ?? 0,
                'ingresos' => $i['ingresos'] ?? 0,
            ];
        }, $items);
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    public function pedidosPorEstadoDt(Request $request)
    {
        $draw = (int) $request->query('draw', 1);
        $desde = $request->query('desde');
        $hasta = $request->query('hasta');
        $res = Http::get($this->baseUrl().'/reportes/pedidos/por-estado', ['desde' => $desde, 'hasta' => $hasta]);
        $items = $res->json() ?? [];
        $data = array_map(function ($i) {
            return [
                'estado' => $i['estado'] ?? '',
                'cantidad' => $i['cantidad'] ?? 0,
            ];
        }, $items);
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }


}
