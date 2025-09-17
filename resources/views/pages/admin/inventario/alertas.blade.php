@extends('layouts.app-new')

@section('title', 'Alertas de{{ {{ __('admin.inventory.title') }} }}- 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Alertas de{{ {{ __('admin.inventory.title') }}< }}/h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Productos que requieren atención inmediata</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.inventario.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                   {{ {{ __('admin.actions.back') }} }}al{{ {{ __('admin.dashboard.title') }} }}                </a>
            </div>
        </div>
    </div>

    <!-- Tabs de alertas -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 px-6 overflow-x-auto" aria-label="Tabs">
                <button onclick="cambiarTab('critico')" 
                        class="tab-button active border-red-500 text-red-600 dark:text-red-400 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Stock Crítico ({{ $productosStockCritico->count() }})
                </button>
                <button onclick="cambiarTab('bajo')" 
                        class="tab-button border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Stock Bajo ({{ \App\Models\VarianteProducto::with('producto')->where('disponible', true)->where('stock', '>', 0)->get()->filter(function($variante) { $stockInicial = $variante->producto->stock_inicial ?? 0; $umbralBajo = $stockInicial > 0 ? (int) ceil(($stockInicial * 60) / 100) : 10; $umbralCritico = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 5; return $variante->stock <= $umbralBajo && $variante->stock > $umbralCritico; })->count() }})
                </button>
                <button onclick="cambiarTab('sin-stock')" 
                        class="tab-button border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                   {{ {{ __('admin.inventory.out_of_stock') }} }}({{ $productosSinStock->count() }})
                </button>
                <button onclick="cambiarTab('excesivo')" 
                        class="tab-button border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Stock Excesivo ({{ $productosStockExcesivo->count() }})
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Tab Stock Crítico -->
            <div id="tab-critico" class="tab-content">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Productos con Stock Crítico</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Productos que están por debajo del 50% de su stock mínimo</p>
                </div>
                
                @if($productosStockCritico->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.products.product') }}< }}/th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.fields.category') }}< }}/th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Mínimo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.fields.actions') }}< }}/th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($productosStockCritico as $producto)
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
                                            {{ $producto->categoria->nombre ?? {{ '{{ __('admin.fields.without_category') }}' }} }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                <!-- Stock Total -->
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Total:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ $producto->stock }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Stock Disponible -->
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        {{ $producto->stock_disponible }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Stock Reservado (solo si hay) -->
                                                @if($producto->stock_reservado > 0)
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        {{ $producto->stock_reservado }}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $producto->stock_minimo }} unidades
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="abrirModalEntrada({{ $producto->producto_id }}, '{{ $producto->nombre_producto }}')" 
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                Registrar Entrada
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400"{{ >{{ __('admin.products.no_products') }} }}con stock crítico</p>
                    </div>
                @endif
            </div>

            <!-- Tab Stock Bajo -->
            <div id="tab-bajo" class="tab-content hidden">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Variantes con Stock Bajo</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Variantes específicas que están por debajo del 60% de su stock inicial</p>
                </div>
                
                @php
                    // Obtener variantes con stock bajo
                    $variantesStockBajo = \App\Models\VarianteProducto::with(['producto.categoria', 'producto.marca'])
                        ->where('disponible', true)
                        ->where('stock', '>', 0)
                        ->get()
                        ->filter(function($variante) {
                            $stockInicial = $variante->producto->stock_inicial ?? 0;
                            $umbralBajo = $stockInicial > 0 ? (int) ceil(($stockInicial * 60) / 100) : 10;
                            $umbralCritico = $stockInicial > 0 ? (int) ceil(($stockInicial * 20) / 100) : 5;
                            return $variante->stock <= $umbralBajo && $variante->stock > $umbralCritico;
                        });
                @endphp
                
                @if($variantesStockBajo->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.products.product') }}< }}/th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Inicial</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Porcentaje</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.fields.actions') }}< }}/th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($variantesStockBajo as $variante)
                                    @php
                                        $stockInicial = $variante->producto->stock_inicial ?? 0;
                                        $porcentaje = $stockInicial > 0 ? round(($variante->stock / $stockInicial) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($variante->producto->imagenes->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $variante->producto->imagenes[0]->ruta_imagen) }}" 
                                                         class="w-10 h-10 rounded-md object-cover" 
                                                         alt="{{ $variante->producto->nombre_producto }}">
                                                @else
                                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $variante->producto->nombre_producto }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $variante->producto->categoria->nombre ?? {{ '{{ __('admin.fields.without_category') }}' }} }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                @if($variante->codigo_color)
                                                    <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: {{ $variante->codigo_color }}"></div>
                                                @endif
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $variante->nombre }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    {{ $variante->stock }} unidades
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $stockInicial }} unidades
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ min($porcentaje, 100) }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $porcentaje }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="abrirModalEntradaVariante({{ $variante->variante_id }}, '{{ $variante->producto->nombre_producto }} - {{ $variante->nombre }}')" 
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                Registrar Entrada
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400"{{ >{{ __('admin.status.no') }} }}hay variantes con stock bajo</p>
                    </div>
                @endif
            </div>

            <!-- Tab{{ {{ __('admin.inventory.out_of_stock') }} }}-->
            <div id="tab-sin-stock" class="tab-content hidden">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Productos{{ {{ __('admin.inventory.out_of_stock') }}< }}/h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Productos que no tienen stock disponible</p>
                </div>
                
                @if($productosSinStock->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.products.product') }}< }}/th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.fields.category') }}< }}/th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Mínimo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.fields.actions') }}< }}/th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($productosSinStock as $producto)
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
                                            {{ $producto->categoria->nombre ?? {{ '{{ __('admin.fields.without_category') }}' }} }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                <!-- Stock Total -->
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Total:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ $producto->stock }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Stock Disponible -->
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                        {{ $producto->stock_disponible }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Stock Reservado (solo si hay) -->
                                                @if($producto->stock_reservado > 0)
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        {{ $producto->stock_reservado }}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $producto->stock_minimo }} unidades
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="abrirModalEntrada({{ $producto->producto_id }}, '{{ $producto->nombre_producto }}')" 
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                Registrar Entrada
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400"{{ >{{ __('admin.products.no_products') }} }}sin stock</p>
                    </div>
                @endif
            </div>

            <!-- Tab Stock Excesivo -->
            <div id="tab-excesivo" class="tab-content hidden">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Productos con Stock Excesivo</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Productos que superan su stock máximo recomendado según análisis de demanda</p>
                </div>
                
                @if($productosStockExcesivo->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.products.product') }}< }}/th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.fields.category') }}< }}/th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Recomendado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Demanda</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"{{ >{{ __('admin.fields.actions') }}< }}/th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($productosStockExcesivo as $producto)
                                    @php
                                        $inventarioService = app(\App\Services\InventarioService::class);
                                        $stockOptimo = $inventarioService->calcularStockOptimo($producto->producto_id);
                                        $demanda = $inventarioService->calcularDemandaPromedio($producto->producto_id);
                                    @endphp
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
                                            {{ $producto->categoria->nombre ?? {{ '{{ __('admin.fields.without_category') }}' }} }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col space-y-1">
                                                <!-- Stock Total -->
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Total:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        {{ $producto->stock }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Stock Disponible -->
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                        {{ $producto->stock_disponible }}
                                                    </span>
                                                </div>
                                                
                                                <!-- Stock Reservado (solo si hay) -->
                                                @if($producto->stock_reservado > 0)
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        {{ $producto->stock_reservado }}
                                                    </span>
                                                </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(!empty($stockOptimo))
                                                <div class="flex flex-col space-y-1">
                                                    <!-- Stock Óptimo -->
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Óptimo:</span>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            {{ $stockOptimo['stock_optimo_recomendado'] }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Stock Máximo -->
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Máximo:</span>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                            {{ $stockOptimo['stock_maximo_recomendado'] }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Exceso -->
                                                    @php
                                                        $exceso = $producto->stock - $stockOptimo['stock_maximo_recomendado'];
                                                    @endphp
                                                    @if($exceso > 0)
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Exceso:</span>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                            +{{ $exceso }}
                                                        </span>
                                                    </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500 dark:text-gray-400"{{ >{{ __('admin.fields.without_data') }}< }}/span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(!empty($demanda))
                                                <div class="flex flex-col space-y-1">
                                                    <!-- Demanda Diaria -->
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Diaria:</span>
                                                        <span class="text-xs font-medium text-gray-900 dark:text-white">
                                                            {{ number_format($demanda['venta_promedio_diaria'], 1) }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Demanda Semanal -->
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">Semanal:</span>
                                                        <span class="text-xs font-medium text-gray-900 dark:text-white">
                                                            {{ number_format($demanda['venta_promedio_semanal'], 1) }}
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Total 30 días -->
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">30 días:</span>
                                                        <span class="text-xs font-medium text-gray-900 dark:text-white">
                                                            {{ $demanda['total_vendido'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Sin ventas</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-col space-y-2">
                                                @if(!empty($stockOptimo) && $producto->stock > $stockOptimo['stock_maximo_recomendado'])
                                                    <button onclick="mostrarRecomendacionStock({{ $producto->producto_id }}, '{{ $producto->nombre_producto }}', {{ $stockOptimo['stock_maximo_recomendado'] }})" 
                                                            class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300">
                                                       {{ {{ __('admin.actions.view') }} }}Recomendación
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-green-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400"{{ >{{ __('admin.products.no_products') }} }}con stock excesivo</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar entrada -->
<div id="modalEntrada" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Registrar Entrada de{{ {{ __('admin.inventory.title') }}< }}/h3>
            <form id="formEntrada" method="POST" action="{{ route('admin.inventario.registrar-entrada') }}">
                @csrf
                <input type="hidden" id="producto_id" name="producto_id">
                <input type="hidden" id="tipo_entrada" name="tipo" value="producto">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Producto/Variante</label>
                    <input type="text" id="producto_nombre" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
                </div>
                
                <div class="mb-4">
                    <label for="cantidad" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label for="motivo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motivo</label>
                    <textarea id="motivo" name="motivo" rows="3" required 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Ej: Compra a proveedor, Devolución de cliente, etc."></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="referencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Referencia (opcional)</label>
                    <input type="text" id="referencia" name="referencia" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Número de factura, nota de crédito, etc.">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalEntrada()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                       {{ {{ __('admin.actions.cancel') }} }}                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Registrar Entrada
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para recomendaciones de stock -->
<div id="modalRecomendacion" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recomendación de Stock</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"{{ >{{ __('admin.products.product') }}< }}/label>
                <input type="text" id="recomendacion_producto_nombre" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock Máximo Recomendado</label>
                <input type="text" id="recomendacion_stock_maximo" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" readonly>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recomendaciones</label>
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-3">
                    <ul class="text-sm text-yellow-800 dark:text-yellow-200 space-y-1">
                        <li>• Considera pausar compras de este producto</li>
                        <li>• Implementa promociones para reducir stock</li>
                        <li>• Revisa si hay productos similares con mejor rotación</li>
                        <li>• Evalúa si el producto sigue siendo relevante</li>
                    </ul>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="cerrarModalRecomendacion()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mostrarRecomendacionStock(productoId, nombreProducto, stockMaximo) {
    document.getElementById('recomendacion_producto_nombre').value = nombreProducto;
    document.getElementById('recomendacion_stock_maximo').value = stockMaximo + ' unidades';
    document.getElementById('modalRecomendacion').classList.remove('hidden');
}

function cerrarModalRecomendacion() {
    document.getElementById('modalRecomendacion').classList.add('hidden');
}

function abrirModalEntrada(productoId, nombreProducto) {
    document.getElementById('producto_id').value = productoId;
    document.getElementById('producto_nombre').value = nombreProducto;
    document.getElementById('tipo_entrada').value = 'producto';
    document.getElementById('modalEntrada').classList.remove('hidden');
}

function abrirModalEntradaVariante(varianteId, nombreVariante) {
    document.getElementById('producto_id').value = varianteId;
    document.getElementById('producto_nombre').value = nombreVariante;
    document.getElementById('tipo_entrada').value = 'variante';
    document.getElementById('modalEntrada').classList.remove('hidden');
}

function cerrarModalEntrada() {
    document.getElementById('modalEntrada').classList.add('hidden');
    document.getElementById('formEntrada').reset();
}

// Cerrar modales al hacer clic fuera de ellos
window.onclick = function(event) {
    const modalEntrada = document.getElementById('modalEntrada');
    const modalRecomendacion = document.getElementById('modalRecomendacion');
    
    if (event.target === modalEntrada) {
        cerrarModalEntrada();
    }
    
    if (event.target === modalRecomendacion) {
        cerrarModalRecomendacion();
    }
}

// Función para cambiar entre tabs
function cambiarTab(tabName) {
    //{{ {{ __('admin.actions.hide') }} }}todos los tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.add('hidden'));
    
    //{{ {{ __('admin.actions.show') }} }}el tab seleccionado
    const selectedTab = document.getElementById('tab-' + tabName);
    if (selectedTab) {
        selectedTab.classList.remove('hidden');
    }
    
    // Actualizar botones activos
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => {
        button.classList.remove('border-red-500', 'text-red-600', 'dark:text-red-400');
        button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });
    
    // Activar el botón seleccionado
    event.target.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    event.target.classList.add('border-red-500', 'text-red-600', 'dark:text-red-400');
}
</script>
@endsection 
