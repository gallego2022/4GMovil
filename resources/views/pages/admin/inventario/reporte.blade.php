@extends('layouts.app-new')

@section('title', 'Reporte de Inventario - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reporte de Inventario</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Análisis completo del estado del inventario</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">Generado el: {{ $reporte['fecha_reporte'] }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventario.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen General</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Productos</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reporte['resumen_general']['total_productos'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Productos Activos</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reporte['resumen_general']['productos_activos'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Total</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($reporte['resumen_general']['valor_total_inventario'], 0, ',', '.' ) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707-.293l-2.414-2.414a1 1 0 01-.707-.293H6.586a1 1 0 00-.707.293L3.707 12.707A1 1 0 004.586 13H2"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Total</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($reporte['resumen_general']['stock_total']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Inventario -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Alertas de Inventario</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-800 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Crítico</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reporte['alertas']['stock_critico'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Bajo</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reporte['alertas']['stock_bajo'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-full">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707-.293l-2.414-2.414a1 1 0 01-.707-.293H6.586a1 1 0 00-.707.293L3.707 12.707A1 1 0 004.586 13H2"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin Stock</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reporte['alertas']['sin_stock'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-full">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707-.293l-2.414-2.414a1 1 0 01-.707-.293H6.586a1 1 0 00-.707.293L3.707 12.707A1 1 0 004.586 13H2"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Excesivo</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reporte['alertas']['stock_excesivo'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-800 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Inactivos</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $reporte['alertas']['productos_inactivos'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con Stock Bajo -->
    @if($reporte['productos_stock_bajo']->count() > 0)
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Productos con Stock Bajo</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Mínimo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reporte['productos_stock_bajo']->take(10) as $producto)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                         class="w-8 h-8 rounded-md object-cover" 
                                         alt="{{ $producto->nombre_producto }}">
                                @else
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $producto->producto_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                {{ $producto->stock }} unidades
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $producto->stock_minimo }} unidades
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $estadoClasses = match($producto->estado) {
                                    'Nuevo' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'De Exhibición' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $estadoClasses }}">
                                {{ ucfirst($producto->estado) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Valor por Categoría -->
    @if($reporte['valor_por_categoria']->count() > 0)
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Valor del Inventario por Categoría</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Productos</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Porcentaje</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reporte['valor_por_categoria'] as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $item['categoria']->nombre_categoria }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $item['productos_count'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ number_format($item['stock_total']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            ${{ number_format($item['valor_total'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            @php
                                $porcentaje = $reporte['resumen_general']['valor_total_inventario'] > 0 
                                    ? ($item['valor_total'] / $reporte['resumen_general']['valor_total_inventario']) * 100 
                                    : 0;
                            @endphp
                            {{ number_format($porcentaje, 1) }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Productos Más Vendidos -->
    @if($reporte['productos_mas_vendidos']->count() > 0)
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Productos Más Vendidos (Último Mes)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unidades Vendidas</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($reporte['productos_mas_vendidos']->take(10) as $producto)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                         class="w-8 h-8 rounded-md object-cover" 
                                         alt="{{ $producto->nombre_producto }}">
                                @else
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $producto->producto_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $producto->categoria->nombre_categoria ?? 'Sin categoría' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $producto->stock }} unidades
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $producto->total_vendido ?? 0 }} unidades
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection 