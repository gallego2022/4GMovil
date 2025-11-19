@extends('layouts.app-new')

@section('title', 'Dashboard de Inventario - 4GMovil')

@section('content')
<!-- Notificaciones -->
<x-notifications />

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard de Inventario</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Gestión y control del inventario de productos</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventario.alertas-optimizadas') }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                   Ver Alertas
                </a>
                <a href="{{ route('admin.inventario.movimientos') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Movimientos
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total del inventario -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($valorTotal, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Stock total de variantes -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Variantes</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stockTotalVariantes ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Productos con stock crítico -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Crítico</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $alertas['productos_stock_critico'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Variantes con stock bajo -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Variantes Bajo</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $alertas['variantes_stock_bajo'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con alertas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productos con stock crítico -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Crítico</h3>
                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                    {{ $productosStockCritico->count() }} productos
                </span>
            </div>
            
            @if($productosStockCritico->count() > 0)
                <div class="space-y-3">
                    @foreach($productosStockCritico->take(5) as $alerta)
                        @php
                            // OptimizedStockAlertService devuelve arrays con clave 'producto', InventarioService devuelve modelos directamente
                            $producto = is_array($alerta) && isset($alerta['producto']) ? $alerta['producto'] : $alerta;
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                @if($producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                         class="w-10 h-10 rounded-md object-cover flex-shrink-0" 
                                         alt="{{ $producto->nombre_producto }}">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $producto->nombre_producto }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $producto->producto_id }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <div class="text-right">
                                    <x-stock-indicator :producto="$producto" />
                                </div>
                                <button onclick="abrirModalReponer({{ $producto->producto_id }})" 
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Reponer
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                   No hay productos con stock crítico
                </div>
            @endif
        </div>

        <!-- Variantes con stock bajo -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Variantes con Stock Bajo</h3>
            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                {{ $alertas['variantes_stock_bajo'] ?? 0 }} variantes
            </span>
        </div>
        
        @php
            // Usar variantes del servicio si están disponibles, sino calcular manualmente
            $variantesStockBajo = $variantesStockBajo ?? collect();
            if ($variantesStockBajo->isEmpty()) {
                // Fallback: obtener variantes con stock bajo usando umbrales del producto
                $variantesStockBajo = \App\Models\VarianteProducto::with(['producto'])
                    ->where('disponible', true)
                    ->where('stock', '>', 0)
                    ->get()
                    ->filter(function($variante) {
                        $producto = $variante->producto;
                        
                        // Obtener el umbral crítico (stock_minimo)
                        $umbralCritico = $producto->stock_minimo ?? null;
                        
                        if ($umbralCritico === null) {
                            $stockInicial = $producto->stock_inicial ?? 0;
                            if ($stockInicial > 0) {
                                $umbralCritico = (int) ceil(($stockInicial * 20) / 100);
                            } else {
                                $umbralCritico = 5; // Fallback
                            }
                        }
                        
                        // Solo mostrar variantes con stock por debajo o igual al umbral crítico
                        // Si el stock está por encima del mínimo, no mostrar alerta
                        return $variante->stock <= $umbralCritico && $variante->stock > 0;
                    });
            }
            $variantesStockBajo = $variantesStockBajo->take(5);
        @endphp
        
        @if($variantesStockBajo->count() > 0)
            <div class="space-y-3">
                @foreach($variantesStockBajo as $variante)
                    @php
                        $stockInicial = $variante->producto->stock_inicial ?? 0;
                        $porcentaje = $stockInicial > 0 ? round(($variante->stock / $stockInicial) * 100, 1) : 0;
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            @if($variante->producto->imagenes->isNotEmpty())
                                <img src="{{ asset('storage/' . $variante->producto->imagenes[0]->ruta_imagen) }}" 
                                     class="w-10 h-10 rounded-md object-cover flex-shrink-0" 
                                     alt="{{ $variante->producto->nombre_producto }}">
                            @else
                                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $variante->producto->nombre_producto }}</div>
                                <div class="flex items-center space-x-2">
                                    @if($variante->codigo_color)
                                        <div class="w-3 h-3 rounded-full border border-gray-300 flex-shrink-0" style="background-color: {{ $variante->codigo_color }}"></div>
                                    @endif
                                    <span class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $variante->nombre }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    Stock: {{ $variante->stock }}
                                </div>
                                <div class="text-xs text-yellow-600 dark:text-yellow-400">
                                    {{ $porcentaje }}% del inicial
                                </div>
                            </div>
                            <button onclick="abrirModalReponer({{ $variante->producto->producto_id }}, {{ $variante->variante_id }})" 
                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Reponer
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                No hay variantes con stock bajo
            </div>
        @endif
    </div>
    </div>

    

    <!-- Productos con stock reservado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Reservado</h3>
            @php
                // Contar total de productos con stock reservado activo
                // Usa la misma lógica que la lista de productos para consistencia
                $todosProductos = \Illuminate\Support\Facades\DB::table('productos')
                    ->where('activo', true)
                    ->select('producto_id', 'stock_reservado')
                    ->get();
                
                $totalStockReservado = 0;
                foreach ($todosProductos as $producto) {
                    $stockReservadoTotal = 0;
                    
                    // Verificar si el producto tiene stock_reservado directo
                    if ($producto->stock_reservado > 0) {
                        $stockReservadoTotal = $producto->stock_reservado;
                    } else {
                        // SOLO calcular stock reservado desde reservas activas (fuente de verdad)
                        // Excluir completamente cualquier variante que tenga reservas confirmadas
                        $stockReservadoVariantes = \Illuminate\Support\Facades\DB::table('variantes_producto as vp')
                            ->join('reservas_stock_variantes as rsv', 'vp.variante_id', '=', 'rsv.variante_id')
                            ->leftJoin('pedidos as p', 'rsv.referencia_pedido', '=', 'p.pedido_id')
                            ->where('vp.producto_id', $producto->producto_id)
                            ->where('rsv.estado', 'activa')
                            ->where('rsv.fecha_expiracion', '>', now())
                            // Excluir reservas de pedidos confirmados (estado_id = 2)
                            ->where(function($query) {
                                $query->whereNull('p.estado_id')
                                      ->orWhere('p.estado_id', '!=', 2);
                            })
                            // Verificar que no haya reservas confirmadas para estas variantes
                            ->whereNotExists(function($query) use ($producto) {
                                $query->select(\Illuminate\Support\Facades\DB::raw(1))
                                      ->from('reservas_stock_variantes as rsv2')
                                      ->join('variantes_producto as vp2', 'rsv2.variante_id', '=', 'vp2.variante_id')
                                      ->whereColumn('vp2.variante_id', 'vp.variante_id')
                                      ->where('rsv2.estado', 'confirmada')
                                      ->whereNotNull('rsv2.referencia_pedido');
                            })
                            ->sum('rsv.cantidad');
                        
                        $stockReservadoTotal = $stockReservadoVariantes ?? 0;
                    }
                    
                    // Solo contar productos que realmente tienen stock reservado activo
                    if ($stockReservadoTotal > 0) {
                        $totalStockReservado++;
                    }
                }
            @endphp
            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                {{ $totalStockReservado }} productos
            </span>
        </div>
        
        @php
            // Obtener todos los productos con stock reservado activo (máximo 5 para la alerta)
            // Solo incluye productos que realmente tienen stock reservado activo
            $productosStockReservado = \Illuminate\Support\Facades\DB::table('productos')
                ->where('activo', true)
                ->select('producto_id', 'nombre_producto', 'stock', 'stock_reservado', 'stock_disponible')
                ->get();
            
            // Para cada producto, calcular el stock reservado total activo (incluyendo variantes)
            $productosConReserva = collect();
            foreach ($productosStockReservado as $producto) {
                $stockReservadoTotal = 0;
                $variantesConReserva = collect();
                
                // Verificar si el producto tiene stock_reservado directo
                if ($producto->stock_reservado > 0) {
                    $stockReservadoTotal = $producto->stock_reservado;
                } else {
                    // SOLO calcular stock reservado desde reservas activas (fuente de verdad)
                    // Excluir completamente cualquier variante que tenga reservas confirmadas
                    $reservasActivas = \Illuminate\Support\Facades\DB::table('variantes_producto as vp')
                        ->join('reservas_stock_variantes as rsv', 'vp.variante_id', '=', 'rsv.variante_id')
                        ->leftJoin('pedidos as p', 'rsv.referencia_pedido', '=', 'p.pedido_id')
                        ->where('vp.producto_id', $producto->producto_id)
                        ->where('rsv.estado', 'activa')
                        ->where('rsv.fecha_expiracion', '>', now())
                        // Excluir reservas de pedidos confirmados (estado_id = 2)
                        ->where(function($query) {
                            $query->whereNull('p.estado_id')
                                  ->orWhere('p.estado_id', '!=', 2);
                        })
                        // Verificar que no haya reservas confirmadas para estas variantes
                        ->whereNotExists(function($query) use ($producto) {
                            $query->select(\Illuminate\Support\Facades\DB::raw(1))
                                  ->from('reservas_stock_variantes as rsv2')
                                  ->join('variantes_producto as vp2', 'rsv2.variante_id', '=', 'vp2.variante_id')
                                  ->whereColumn('vp2.variante_id', 'vp.variante_id')
                                  ->where('rsv2.estado', 'confirmada')
                                  ->whereNotNull('rsv2.referencia_pedido');
                        })
                        ->select('vp.variante_id', 'vp.nombre', 'vp.stock', 'rsv.cantidad as stock_reservado')
                        ->distinct()
                        ->get();
                    
                    if ($reservasActivas->count() > 0) {
                        $stockReservadoTotal = $reservasActivas->sum('stock_reservado');
                        foreach ($reservasActivas as $reserva) {
                            $variantesConReserva->push([
                                'variante_id' => $reserva->variante_id,
                                'nombre' => $reserva->nombre,
                                'stock_reservado' => $reserva->stock_reservado,
                                'stock' => $reserva->stock
                            ]);
                        }
                    }
                }
                
                // Solo agregar productos que realmente tienen stock reservado activo
                if ($stockReservadoTotal > 0) {
                    $producto->stock_reservado = $stockReservadoTotal;
                    $producto->variantes = $variantesConReserva;
                    $productosConReserva->push($producto);
                }
            }
            
            // Ordenar por stock_reservado descendente y tomar máximo 5
            $productosStockReservado = $productosConReserva
                ->sortByDesc('stock_reservado')
                ->take(5)
                ->values();
        @endphp
        
        @if($productosStockReservado->count() > 0)
            <div class="space-y-3">
                @foreach($productosStockReservado as $producto)
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $producto->producto_id }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    Stock: {{ $producto->stock }}
                                </div>
                                <div class="text-xs text-blue-600 dark:text-blue-400">
                                    Reservado: {{ $producto->stock_reservado }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Disponible: {{ $producto->stock_disponible }}
                                </div>
                            </div>
                        </div>
                        @if(isset($producto->variantes) && $producto->variantes->count() > 0)
                            <div class="mt-2 pt-2 border-t border-blue-200 dark:border-blue-800">
                                <div class="text-xs font-medium text-blue-700 dark:text-blue-300 mb-1">Variantes con stock reservado:</div>
                                <div class="space-y-1">
                                    @foreach($producto->variantes as $variante)
                                        <div class="flex items-center justify-between text-xs pl-4">
                                            <span class="text-gray-700 dark:text-gray-300">
                                                • {{ $variante['nombre'] }}
                                            </span>
                                            <span class="text-blue-600 dark:text-blue-400 font-medium">
                                                Reservado: {{ $variante['stock_reservado'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
                @if($totalStockReservado > 5)
                    <div class="text-center py-2 text-xs text-gray-500 dark:text-gray-400 italic">
                        Mostrando 5 de {{ $totalStockReservado }} productos con stock reservado
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                No hay productos con stock reservado
            </div>
        @endif
    </div>
    
    <!-- Acciones rápidas -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('admin.inventario.movimientos') }}" 
               class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Ver Movimientos</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Historial de entradas y salidas</p>
                </div>
            </a>

            <a href="{{ route('admin.inventario.valor-por-categoria') }}" 
               class="flex items-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/30 transition-colors">
                <svg class="w-8 h-8 text-teal-600 dark:text-teal-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18m10-8H3"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Valor por Categoría</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Distribución del valor por categorías</p>
                </div>
            </a>
            
            <a href="{{ route('admin.inventario.reporte') }}" 
               class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Generar Reporte</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Reporte detallado de inventario</p>
                </div>
            </a>

            <a href="{{ route('admin.inventario.alertas-optimizadas') }}" 
               class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Ver Alertas</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Productos que requieren atención</p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Modal para reponer stock -->
<div id="reponerModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="reponer-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="cerrarModalReponer()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2" id="reponer-modal-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Reponer Stock
                    </h3>
                    <button onclick="cerrarModalReponer()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form id="reponer-form" method="POST" action="{{ route('admin.inventario.alertas.reponer-stock') }}" onsubmit="reponerStock(event)">
                    @csrf
                    <input type="hidden" id="reponer-producto-id" name="producto_id">
                    <input type="hidden" id="reponer-variante-id" name="variante_id">
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2" id="reponer-producto-nombre"></p>
                    </div>
                    
                    <div id="variantes-selector-container" class="mb-4 hidden">
                        <label for="variante-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Seleccionar Variante
                        </label>
                        <select id="variante-select" name="variante_id" class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Cargando variantes...</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="cantidad-reponer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Cantidad a Reponer
                        </label>
                        <input type="number" id="cantidad-reponer" name="cantidad" min="1" required 
                               class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Ingrese la cantidad">
                    </div>
                    
                    <div class="mb-4">
                        <label for="motivo-reponer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motivo (opcional)
                        </label>
                        <textarea id="motivo-reponer" name="motivo" rows="3" 
                                  class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Reposición de stock desde dashboard"></textarea>
                    </div>
                    
                    <div id="reponer-error" class="mb-4 hidden">
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-3">
                            <p class="text-sm text-red-600 dark:text-red-400" id="reponer-error-message"></p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Reponer Stock
                        </button>
                        <button type="button" onclick="cerrarModalReponer()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Función para abrir modal de reponer stock
function abrirModalReponer(productoId, varianteId = null) {
    const modal = document.getElementById('reponerModal');
    const form = document.getElementById('reponer-form');
    const errorDiv = document.getElementById('reponer-error');
    const variantesContainer = document.getElementById('variantes-selector-container');
    const varianteSelect = document.getElementById('variante-select');
    const productoIdInput = document.getElementById('reponer-producto-id');
    const varianteIdInput = document.getElementById('reponer-variante-id');
    const productoNombre = document.getElementById('reponer-producto-nombre');
    
    // Limpiar formulario
    form.reset();
    errorDiv.classList.add('hidden');
    variantesContainer.classList.add('hidden');
    varianteSelect.innerHTML = '<option value="">Cargando variantes...</option>';
    
    // Establecer IDs
    productoIdInput.value = productoId;
    if (varianteId) {
        varianteIdInput.value = varianteId;
    } else {
        varianteIdInput.value = '';
    }
    
    // Mostrar modal
    modal.classList.remove('hidden');
    
    // Cargar información del producto y variantes
    fetch(`{{ route('admin.inventario.alertas.variantes-producto') }}?producto_id=${productoId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            productoNombre.textContent = `Producto: ${data.producto.nombre}`;
            
            if (data.producto.tiene_variantes && data.variantes.length > 0) {
                // Mostrar selector de variantes
                variantesContainer.classList.remove('hidden');
                varianteSelect.innerHTML = '<option value="">Seleccione una variante</option>';
                
                data.variantes.forEach(variante => {
                    const option = document.createElement('option');
                    option.value = variante.variante_id;
                    option.textContent = `${variante.nombre} (Stock actual: ${variante.stock_actual})`;
                    if (varianteId && variante.variante_id == varianteId) {
                        option.selected = true;
                    }
                    varianteSelect.appendChild(option);
                });
                
                // Si se pasó una variante específica, ocultar el selector
                if (varianteId) {
                    variantesContainer.classList.add('hidden');
                }
            } else {
                // Producto sin variantes
                variantesContainer.classList.add('hidden');
            }
        } else {
            mostrarErrorReponer('Error al cargar la información del producto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarErrorReponer('Error al cargar la información del producto');
    });
}

// Función para cerrar modal de reponer
function cerrarModalReponer() {
    document.getElementById('reponerModal').classList.add('hidden');
}

// Función para mostrar error en el modal de reponer
function mostrarErrorReponer(mensaje) {
    const errorDiv = document.getElementById('reponer-error');
    const errorMessage = document.getElementById('reponer-error-message');
    errorMessage.textContent = mensaje;
    errorDiv.classList.remove('hidden');
}

// Función para reponer stock
function reponerStock(event) {
    event.preventDefault();
    
    const form = document.getElementById('reponer-form');
    const formData = new FormData(form);
    const errorDiv = document.getElementById('reponer-error');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Obtener variante_id del select si está visible
    const varianteSelect = document.getElementById('variante-select');
    if (varianteSelect && !varianteSelect.classList.contains('hidden') && varianteSelect.value) {
        formData.set('variante_id', varianteSelect.value);
    }
    
    // Deshabilitar botón
    submitButton.disabled = true;
    submitButton.textContent = 'Reponiendo...';
    errorDiv.classList.add('hidden');
    
    // Enviar petición como formulario normal (no AJAX) para usar notificaciones de sesión
    form.submit();
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalReponer();
    }
});
</script>
@endpush 