@extends('layouts.app-new')

@section('title', '__('admin.fields.value') del __('admin.inventory.title') por __('admin.fields.category') - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">__('admin.fields.value') del __('admin.inventory.title') por __('admin.fields.category')</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Análisis del valor del inventario distribuido por categorías</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.inventario.dashboard') }}" 
                   class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 4.158a.75.75 0 11-1.06 1.06l-5.5-5.5a.75.75 0 010-1.06l5.5-5.5a.75.75 0 111.06 1.06L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                    __('admin.actions.back')
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen general -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen General</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">__('admin.fields.value') Total</p>
                        <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">
                            ${{ number_format($valorTotal, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Categorías</p>
                        <p class="text-2xl font-semibold text-green-900 dark:text-green-100">
                            {{ $valorPorCategoria->count() }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Promedio por __('admin.fields.category')</p>
                        <p class="text-2xl font-semibold text-purple-900 dark:text-purple-100">
                            ${{ $valorPorCategoria->count() > 0 ? number_format($valorTotal / $valorPorCategoria->count(), 0, ',', '.') : '0.00' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista móvil (cards) -->
    <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
        @forelse($valorPorCategoria as $item)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden categoria-card">
            <div class="p-4">
                <div class="flex items-start space-x-4">
                    <!-- Icono de categoría -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-brand-600 text-white flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                    </div>
                    
                    <!-- __('admin.messages.info') de la categoría -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                            {{ $item['categoria']->nombre ?? '__('admin.fields.without_category')' }}
                        </h3>
                        <div class="mt-1 flex flex-col space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                __('admin.products.product')s: {{ $item['productos_count'] ?? 0 }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                __('admin.fields.value'): ${{ number_format($item['valor_total'], 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Stock Total: {{ $item['stock_total'] ?? 0 }}
                            </p>
                            @if($valorTotal > 0)
                            <div class="mt-2">
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    <span>Porcentaje</span>
                                    <span>{{ number_format(($item['valor_total'] / $valorTotal) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-brand-600 h-2 rounded-full" style="width: {{ ($item['valor_total'] / $valorTotal) * 100 }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">__('admin.status.no') hay categorías</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">__('admin.status.no') se encontraron categorías con productos en inventario.</p>
        </div>
        @endforelse
    </div>

    <!-- Vista escritorio (tabla y gráfico) -->
    <div class="hidden sm:block">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tabla de categorías -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">__('admin.fields.value') por __('admin.fields.category')</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Desglose detallado del inventario</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">__('admin.fields.category')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Productos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">__('admin.fields.value')</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">%</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($valorPorCategoria as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-brand-600 text-white flex items-center justify-center">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $item['categoria']->nombre ?? '__('admin.fields.without_category')' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $item['productos_count'] ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $item['stock_total'] ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                    ${{ number_format($item['valor_total'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($valorTotal > 0)
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                            <div class="bg-brand-600 h-2 rounded-full" style="width: {{ ($item['valor_total'] / $valorTotal) * 100 }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ number_format(($item['valor_total'] / $valorTotal) * 100, 1) }}%
                                        </span>
                                    </div>
                                    @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">0%</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    __('admin.status.no') hay categorías con productos en inventario
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Gráfico de dona -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Distribución del __('admin.fields.value')</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gráfico de dona del inventario por categoría</p>
                </div>
                
                <div class="p-6">
                    <div class="relative" style="height: 300px;">
                        <canvas id="valorPorCategoriaChart"></canvas>
                    </div>
                    
                    <!-- Leyenda -->
                    <div class="mt-6 grid grid-cols-1 gap-2">
                        @foreach($valorPorCategoria as $index => $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $index < 10 ? ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'][$index] : '#6B7280' }}"></div>
                                <span class="text-sm text-gray-900 dark:text-white truncate">
                                    {{ $item['categoria']->nombre ?? '__('admin.fields.without_category')' }}
                                </span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($item['valor_total'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('valorPorCategoriaChart').getContext('2d');
    
    const data = {
        labels: [
            @foreach($valorPorCategoria as $item)
                '{{ $item['categoria']->nombre ?? '__('admin.fields.without_category')' }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($valorPorCategoria as $item)
                    {{ $item['valor_total'] }},
                @endforeach
            ],
            backgroundColor: [
                '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    };
    
    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': $' + value.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    };
    
    new Chart(ctx, config);
});
</script>
@endpush

@endsection 
