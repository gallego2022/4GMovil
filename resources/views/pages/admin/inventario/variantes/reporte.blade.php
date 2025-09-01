@extends('layouts.app-new')

@section('title', 'Reporte de Variantes - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reporte de Variantes</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Análisis detallado del inventario de variantes</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventario.variantes.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
                <button onclick="exportarExcel()" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filtros</h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="producto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Producto
                </label>
                <select name="producto_id" id="producto_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos los productos</option>
                    @foreach(\App\Models\Producto::activos()->orderBy('nombre_producto')->get() as $producto)
                        <option value="{{ $producto->producto_id }}" {{ request('producto_id') == $producto->producto_id ? 'selected' : '' }}>
                            {{ $producto->nombre_producto }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Estado
                </label>
                <select name="estado" id="estado" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Todos</option>
                    <option value="con_stock" {{ request('estado') == 'con_stock' ? 'selected' : '' }}>Con Stock</option>
                    <option value="sin_stock" {{ request('estado') == 'sin_stock' ? 'selected' : '' }}>Sin Stock</option>
                    <option value="stock_bajo" {{ request('estado') == 'stock_bajo' ? 'selected' : '' }}>Stock Bajo</option>
                    <option value="necesita_reposicion" {{ request('estado') == 'necesita_reposicion' ? 'selected' : '' }}>Necesita Reposición</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Resumen estadístico -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Variantes</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $reporte['total_variantes'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Con Stock</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $reporte['variantes_con_stock'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin Stock</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $reporte['variantes_sin_stock'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Necesitan Reposición</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $reporte['variantes_necesitan_reposicion'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Valor total -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Valor Total del Inventario</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Valor de todas las variantes en stock</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                    ${{ number_format($reporte['valor_total_inventario'] ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Stock total: {{ number_format($reporte['stock_total'] ?? 0, 0, ',', '.') }} unidades
                </p>
            </div>
        </div>
    </div>

    <!-- Tabla de variantes -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detalle de Variantes</h3>
        </div>
        
        @if(isset($reporte['detalle_variantes']) && count($reporte['detalle_variantes']) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Variante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Stock
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Precio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($reporte['detalle_variantes'] as $variante)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $variante['producto'] }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {{ $variante['variante_id'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $variante['color'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $variante['stock_disponible'] }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Mín: {{ $variante['stock_minimo'] }} | Máx: {{ $variante['stock_maximo'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        ${{ number_format($variante['precio_unitario'], 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ number_format($variante['valor_inventario'], 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($variante['stock_disponible'] == 0)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Sin Stock
                                        </span>
                                    @elseif($variante['necesita_reposicion'])
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Stock Bajo
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="registrarEntrada({{ $variante['variante_id'] }})" 
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                        <button onclick="registrarSalida({{ $variante['variante_id'] }})" 
                                                class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <button onclick="ajustarStock({{ $variante['variante_id'] }})" 
                                                class="text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H6.586a1 1 0 00-.707.293L3.707 12.707A1 1 0 004.586 13H2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay variantes</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    No se encontraron variantes con los filtros aplicados.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
function exportarExcel() {
    // Implementar exportación a Excel
    alert('Función de exportación a Excel en desarrollo');
}

function registrarEntrada(varianteId) {
    // Implementar modal para registrar entrada
    alert('Función de registro de entrada en desarrollo para variante ID: ' + varianteId);
}

function registrarSalida(varianteId) {
    // Implementar modal para registrar salida
    alert('Función de registro de salida en desarrollo para variante ID: ' + varianteId);
}

function ajustarStock(varianteId) {
    // Implementar modal para ajustar stock
    alert('Función de ajuste de stock en desarrollo para variante ID: ' + varianteId);
}
</script>
@endsection
