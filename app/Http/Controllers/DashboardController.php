<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Traits\AdminCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use AdminCheck;

    public function index()
    {
        if ($respuesta = $this->verificarAdmin()) {
            return $respuesta;
        }

        // Obtener estadísticas
        $totalProductos = Producto::count();
        $productosNuevos = Producto::where('estado', 'nuevo')->count();
        $totalCategorias = Categoria::count();
        $totalMarcas = Marca::count();

        // Obtener los últimos 5 productos agregados con sus relaciones
        $ultimosProductos = Producto::with(['categoria', 'marca', 'imagenes'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pages.admin.index', compact(
            'totalProductos',
            'productosNuevos',
            'totalCategorias',
            'totalMarcas',
            'ultimosProductos'
        ));
    }
}
