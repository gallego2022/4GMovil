@extends('layouts.app-new')

@section('title', '__('admin.dashboard.title') de __('admin.inventory.title') - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">__('admin.dashboard.title') de __('admin.inventory.title')</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Gestión y control del inventario de productos</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventario.alertas') }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    __('admin.actions.view') Alertas
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
        <!-- __('admin.fields.value') total del inventario -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">__('admin.fields.value') Total</p>
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

        <!-- __('admin.products.product')s con stock crítico -->
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

    <!-- __('admin.products.product')s con alertas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- __('admin.products.product')s con stock crítico -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Crítico</h3>
                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                    {{ $productosStockCritico->count() }} productos
                </span>
            </div>
            
            @if($productosStockCritico->count() > 0)
                <div class="space-y-3">
                    @foreach($productosStockCritico->take(5) as $producto)
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @if($producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                         class="w-10 h-10 rounded-md object-cover" 
                                         alt="{{ $producto->nombre_producto }}">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $producto->producto_id }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <x-stock-indicator :producto="$producto" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                    __('admin.products.no_products') con stock crítico
                </div>
            @endif
        </div>

        <!-- __('admin.products.product')s con stock bajo -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Bajo</h3>
                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                    {{ $productosStockBajo->count() }} productos
                </span>
            </div>
            
            @if($productosStockBajo->count() > 0)
                <div class="space-y-3">
                    @foreach($productosStockBajo->take(5) as $producto)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <div class="flex items-center space-x-3">
                                @if($producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                         class="w-10 h-10 rounded-md object-cover" 
                                         alt="{{ $producto->nombre_producto }}">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $producto->producto_id }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <x-stock-indicator :producto="$producto" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                    __('admin.products.no_products') con stock bajo
                </div>
            @endif
        </div>
    </div>

    <!-- __('admin.products.product')s con stock reservado alto -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Reservado Alto</h3>
            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                {{ $alertas['stock_reservado_alto'] ?? 0 }} productos
            </span>
        </div>
        
        @php
            // Obtener productos con stock reservado alto usando consulta directa
            $productosStockReservadoAlto = \Illuminate\Support\Facades\DB::table('productos')
                ->where('activo', true)
                ->where('stock_reservado', '>', 0)
                ->whereRaw('stock_reservado > stock * 0.3')
                ->select('producto_id', 'nombre_producto', 'stock', 'stock_reservado', 'stock_disponible')
                ->take(5)
                ->get();
        @endphp
        
        @if($productosStockReservadoAlto->count() > 0)
            <div class="space-y-3">
                @foreach($productosStockReservadoAlto as $producto)
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
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
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                __('admin.products.no_products') con alto stock reservado
            </div>
        @endif
    </div>
    
    <!-- __('admin.fields.actions') rápidas -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">__('admin.fields.actions') Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('admin.inventario.movimientos') }}" 
               class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">__('admin.actions.view') Movimientos</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Historial de entradas y salidas</p>
                </div>
            </a>
            
            <a href="{{ route('admin.inventario.reporte') }}" 
               class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">__('admin.reports.generate_report')</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">__('admin.reports.title') detallados de inventario</p>
                </div>
            </a>

            <a href="{{ route('admin.inventario.alertas') }}" 
               class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">__('admin.actions.view') Alertas</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Productos que requieren atención</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection 