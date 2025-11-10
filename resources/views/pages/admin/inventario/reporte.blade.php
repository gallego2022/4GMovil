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
                <p class="text-xs text-gray-400 dark:text-gray-500">Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
                @if(isset($estadisticas['periodo']))
                <p class="text-xs text-gray-400 dark:text-gray-500">Período: {{ $estadisticas['periodo']['inicio'] }} - {{ $estadisticas['periodo']['fin'] }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventario.exportar-reporte', array_merge(request()->query(), ['formato' => 'excel'])) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                   title="Descargar reporte en formato CSV (compatible con Excel)">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar CSV
                </a>
                <a href="{{ route('admin.inventario.reporte-pdf', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700"
                   title="Descargar reporte en formato PDF">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Exportar PDF
                </a>
                <a href="{{ route('admin.inventario.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                   Volver a Dashboard                </a>
            </div>
        </div>
    </div>

    <!-- Información de Exportación -->
    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Información sobre las Exportaciones</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>CSV:</strong> Archivo de texto separado por comas, compatible con Excel, Google Sheets y otros programas de hojas de cálculo.</li>
                        <li><strong>PDF:</strong> Archivo PDF generado con DomPDF, listo para imprimir o compartir.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('admin.webhooks.filters') }}</h3>
        <form method="GET" action="{{ route('admin.inventario.reporte') }}" id="filtrosForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.webhooks.date') }} Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" 
                       value="{{ $fecha_inicio->format('Y-m-d') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.webhooks.date') }} Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" 
                       value="{{ $fecha_fin->format('Y-m-d') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="categoria_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.fields.category') }}</label>
                <select id="categoria_id" name="categoria_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->categoria_id }}" {{ request('categoria_id') == $categoria->categoria_id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="marca_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.fields.brand') }}</label>
                <select id="marca_id" name="marca_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todas las marcas</option>
                    @foreach($marcas as $marca)
                        <option value="{{ $marca->marca_id }}" {{ request('marca_id') == $marca->marca_id ? 'selected' : '' }}>
                            {{ $marca->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                   {{ __('admin.actions.filter') }}                </button>
                <button type="button" id="limpiarFiltros" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </form>
        
        <!-- Filtros rápidos -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('admin.webhooks.filters') }} Rápidos</h4>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-dias="7">
                   {{ __('admin.actions.last') }}s 7 días
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-dias="30">
                   {{ __('admin.actions.last') }}s 30 días
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-dias="90">
                   {{ __('admin.actions.last') }}s 90 días
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-dias="365">
                   {{ __('admin.actions.last') }} año
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-mes="actual">
                    Mes actual
                </button>
                <button type="button" class="filtro-rapido px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" data-mes="anterior">
                    Mes anterior
                </button>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.stats.total_products') }}</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $estadisticas['total_productos'] ?? 0 }}</p>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Variantes</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $estadisticas['total_variantes'] ?? 0 }}</p>
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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.fields.value') }} Total</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($estadisticas['valor_inventario'] ?? 0, 0, ',', '.') }}</p>
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
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($estadisticas['stock_total'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de Inventario -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Alertas de Inventario</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-800 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.inventory.out_of_stock') }}</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $estadisticas['productos_sin_stock'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-800 rounded-full">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Crítico</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $estadisticas['productos_stock_critico'] ?? 0 }}</p>
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
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $estadisticas['productos_stock_bajo'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Entradas</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $estadisticas['movimientos_entrada'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l-4-4m0 0l-4 4m4-4v12"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Salidas</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $estadisticas['movimientos_salida'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos Recientes -->
    @if($movimientos->count() > 0)
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Movimientos Recientes</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.webhooks.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.products.product') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.fields.type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Motivo</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($movimientos->take(20) as $movimiento)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $movimiento->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $movimiento->variante->producto->nombre_producto ?? __('admin.webhooks.not_available') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $movimiento->variante->nombre ?? __('admin.webhooks.not_available') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $tipoVal = $movimiento->tipo_movimiento ?? $movimiento->tipo; @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $tipoVal === 'entrada' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ ucfirst($tipoVal) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $movimiento->cantidad }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $movimiento->motivo }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <!-- Lista de Productos -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Productos en Inventario</h3>
        @if($productos->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.products.product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.fields.category') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.fields.brand') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.fields.price') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.fields.value') }} Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($productos as $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
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
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $producto->producto_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $producto->categoria->nombre ?? __('admin.fields.without_category') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $producto->marca->nombre ?? 'Sin marca' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $producto->stock <= 0 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                           ($producto->stock <= 10 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                           'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200') }}">
                                        {{ $producto->stock }} unidades
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ${{ number_format($producto->precio, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ${{ number_format($producto->stock * $producto->precio, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707-.293l-2.414-2.414a1 1 0 01-.707-.293H6.586a1 1 0 00-.707.293L3.707 12.707A1 1 0 004.586 13H2"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No hay productos que coincidan con los filtros seleccionados</p>
            </div>
        @endif
    </div>

    
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filtrosForm');
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const limpiarBtn = document.getElementById('limpiarFiltros');
    const filtrosRapidos = document.querySelectorAll('.filtro-rapido');

    // Validación de fechas en tiempo real
    function validarFechas() {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        if (fechaInicio.value && fechaFin.value) {
            if (inicio > fin) {
                fechaFin.setCustomValidity('La fecha de fin debe ser posterior a la fecha de inicio');
                fechaFin.reportValidity();
                return false;
            }
            
            // Validar que el rango no sea mayor a 2 años para reportes
            const diffTime = Math.abs(fin - inicio);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays > 730) {
                fechaFin.setCustomValidity('El rango de fechas no puede ser mayor a 2 años para reportes');
                fechaFin.reportValidity();
                return false;
            }
        }
        
        fechaFin.setCustomValidity('');
        return true;
    }

    // Event listeners para validación
    fechaInicio.addEventListener('change', validarFechas);
    fechaFin.addEventListener('change', validarFechas);

    // Validación antes de enviar el formulario
    form.addEventListener('submit', function(e) {
        if (!validarFechas()) {
            e.preventDefault();
            return false;
        }
    });

    // Limpiar filtros
    limpiarBtn.addEventListener('click', function() {
        form.reset();
        fechaInicio.value = '';
        fechaFin.value = '';
        form.submit();
    });

    // Filtros rápidos
    filtrosRapidos.forEach(btn => {
        btn.addEventListener('click', function() {
            const dias = this.dataset.dias;
            const mes = this.dataset.mes;
            
            if (dias) {
                const fechaFin = new Date();
                const fechaInicio = new Date();
                fechaInicio.setDate(fechaInicio.getDate() - parseInt(dias));
                
                document.getElementById('fecha_inicio').value = fechaInicio.toISOString().split('T')[0];
                document.getElementById('fecha_fin').value = fechaFin.toISOString().split('T')[0];
            }
            
            if (mes === 'actual') {
                const hoy = new Date();
                const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                const ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
                
                document.getElementById('fecha_inicio').value = primerDia.toISOString().split('T')[0];
                document.getElementById('fecha_fin').value = ultimoDia.toISOString().split('T')[0];
            }
            
            if (mes === 'anterior') {
                const hoy = new Date();
                const primerDiaAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
                const ultimoDiaAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
                
                document.getElementById('fecha_inicio').value = primerDiaAnterior.toISOString().split('T')[0];
                document.getElementById('fecha_fin').value = ultimoDiaAnterior.toISOString().split('T')[0];
            }
            
            form.submit();
        });
    });

    // Auto-submit cuando cambian los filtros de categoría o marca
    const selects = document.querySelectorAll('select[name="categoria_id"], select[name="marca_id"]');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Solo auto-submit si hay fechas válidas
            if (fechaInicio.value && fechaFin.value && validarFechas()) {
                form.submit();
            }
        });
    });

    // Mostrar mensaje de validación personalizado
    function mostrarMensaje(mensaje, tipo = 'error') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
            tipo === 'error' ? 'bg-red-100 text-red-700 border border-red-200' : 
            'bg-green-100 text-green-700 border border-green-200'
        }`;
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${tipo === 'error' ? 
                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>' :
                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                    }
                </svg>
                ${mensaje}
            </div>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Validación adicional para fechas futuras
    const hoy = new Date().toISOString().split('T')[0];
    fechaInicio.setAttribute('max', hoy);
    fechaFin.setAttribute('max', hoy);

    // Validación inicial al cargar la página
    validarFechas();
});
</script>
@endsection