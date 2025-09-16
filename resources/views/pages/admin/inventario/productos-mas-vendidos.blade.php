@extends('layouts.app-new')

@section('title', 'Productos Más Vendidos - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Productos Más Vendidos</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Análisis de los productos con mayor demanda</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.inventario.dashboard') }}" 
                   class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 4.158a.75.75 0 11-1.06 1.06l-5.5-5.5a.75.75 0 010-1.06l5.5-5.5a.75.75 0 111.06 1.06L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                    __('admin.actions.back')
                </a>
            </div>
        </div>
    </div>

    <!-- __('admin.webhooks.filters') de fecha -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">__('admin.webhooks.filters') de __('admin.webhooks.date')</h3>
        <form method="GET" action="{{ route('admin.inventario.productos-mas-vendidos') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    __('admin.webhooks.date') de Inicio
                </label>
                <input type="date" 
                       id="fecha_inicio" 
                       name="fecha_inicio" 
                       value="{{ $fechaInicio->format('Y-m-d') }}"
                       class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-800 sm:text-sm">
            </div>
            
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    __('admin.webhooks.date') de Fin
                </label>
                <input type="date" 
                       id="fecha_fin" 
                       name="fecha_fin" 
                       value="{{ $fechaFin->format('Y-m-d') }}"
                       class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-800 sm:text-sm">
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full inline-flex justify-center items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-black dark:text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                    __('admin.actions.filter')
                </button>
            </div>
        </form>
    </div>

    <!-- Vista móvil (cards) -->
    <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
        @forelse($productos as $index => $producto)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden producto-card">
            <div class="p-4">
                <div class="flex items-start space-x-4">
                    <!-- Posición -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-lg">
                            {{ $index + 1 }}
                        </div>
                    </div>
                    
                    <!-- Imagen del producto -->
                    <div class="flex-shrink-0">
                        @if($producto->imagenes->isNotEmpty())
                        <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                             class="h-20 w-20 rounded-lg object-cover shadow-sm" 
                             alt="{{ $producto->nombre_producto }}">
                        @else
                        <img src="{{ asset('img/Logo_2.png') }}" 
                             class="h-20 w-20 rounded-lg object-cover shadow-sm" 
                             alt="Sin imagen">
                        @endif
                    </div>
                    
                    <!-- __('admin.messages.info') del producto -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                            {{ $producto->nombre_producto }}
                        </h3>
                        <div class="mt-1 flex flex-col space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ID: {{ $producto->producto_id }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Stock: {{ $producto->stock }}
                            </p>
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">
                                Vendidos: {{ $producto->total_vendido ?? 0 }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                __('admin.fields.category'): {{ $producto->categoria->nombre ?? '__('admin.fields.without_category')' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">__('admin.status.no') hay datos de ventas</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">__('admin.status.no') se encontraron productos vendidos en el período seleccionado.</p>
        </div>
        @endforelse
    </div>

    <!-- Vista escritorio (tabla) -->
    <div class="hidden sm:block">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top 20 __('admin.products.product')s Más Vendidos</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Período: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Posición</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">__('admin.products.product')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">__('admin.fields.category')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">__('admin.fields.price')</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Vendidos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">__('admin.fields.value') Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($productos as $index => $producto)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($producto->imagenes->isNotEmpty())
                                        <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                             class="h-10 w-10 rounded-lg object-cover" 
                                             alt="{{ $producto->nombre_producto }}">
                                        @else
                                        <img src="{{ asset('img/Logo_2.png') }}" 
                                             class="h-10 w-10 rounded-lg object-cover" 
                                             alt="Sin imagen">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $producto->nombre_producto }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            ID: {{ $producto->producto_id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $producto->categoria->nombre ?? '__('admin.fields.without_category')' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                ${{ number_format($producto->precio, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $producto->stock <= 10 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ($producto->stock <= 50 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200') }}">
                                    {{ $producto->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                                {{ $producto->total_vendido ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                ${{ number_format(($producto->total_vendido ?? 0) * $producto->precio, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">__('admin.status.no') hay datos de ventas</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">__('admin.status.no') se encontraron productos vendidos en el período seleccionado.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Resumen estadístico -->
    @if($productos->isNotEmpty())
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen Estadístico</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Vendido</p>
                        <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                            {{ $productos->sum('total_vendido') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">__('admin.fields.value') Total</p>
                        <p class="text-2xl font-semibold text-green-900 dark:text-green-100">
                            ${{ number_format($productos->sum(function($p) { return ($p->total_vendido ?? 0) * $p->precio; }), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Promedio por __('admin.products.product')</p>
                        <p class="text-2xl font-semibold text-purple-900 dark:text-purple-100">
                            {{ $productos->count() > 0 ? number_format($productos->sum('total_vendido') / $productos->count(), 0, ',', '.') : 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Validación de fechas
        $('#fecha_inicio, #fecha_fin').on('change', function() {
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            
            if (fechaInicio && fechaFin && fechaInicio > fechaFin) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas inválidas',
                    text: 'La fecha de inicio no puede ser mayor a la fecha de fin.',
                    confirmButtonColor: '#0088ff'
                });
                $(this).val('');
            }
        });
        
        // Confirmación antes de filtrar
        $('form').on('submit', function(e) {
            const fechaInicio = $('#fecha_inicio').val();
            const fechaFin = $('#fecha_fin').val();
            
            if (!fechaInicio || !fechaFin) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Fechas requeridas',
                    text: 'Por favor seleccione ambas fechas para filtrar los resultados.',
                    confirmButtonColor: '#0088ff'
                });
            }
        });
    });
</script>
@endpush

@endsection 
