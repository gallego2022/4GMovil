@extends('layouts.app-new')

@section('title', 'Alertas de Stock Optimizadas - 4GMovil')

@section('content')
<!-- Notificaciones -->
<x-notifications />

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Alertas de Stock Optimizadas</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Gestión inteligente de alertas agrupadas por producto</p>
            </div>
            <div class="flex items-center gap-3">
                <button id="refresh-alerts" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar
                </button>
                <a href="{{ route('admin.inventario.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Productos Críticos -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">Productos Críticos</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="productos-criticos">
                        {{ $alertas['productos_criticos_count'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-yellow-600 dark:text-yellow-400 uppercase tracking-wide">Stock Bajo</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="productos-stock-bajo">
                        {{ $alertas['productos_stock_bajo_count'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Variantes Agotadas -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase tracking-wide">Variantes Agotadas</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="variantes-agotadas">
                        {{ $alertas['variantes_agotadas_count'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Alertas -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wide">Total Alertas</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white" id="total-alertas">
                        {{ $alertas['total_alertas'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs de navegación -->
    <div id="tabs-container" class="bg-white dark:bg-gray-900 rounded-lg shadow-md" x-data="{ activeTab: '{{ $tipo ?? 'criticos' }}' }">
        <input type="hidden" id="tipo-alerta-actual" value="{{ $tipo ?? 'criticos' }}">
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="cargarTab('criticos')" data-tipo="criticos"
                   :class="activeTab === 'criticos' ? 'border-red-500 text-red-600 dark:text-red-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                   class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors whitespace-nowrap flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Críticos
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs font-medium bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300">
                        {{ $alertas['productos_criticos_count'] ?? 0 }}
                    </span>
                </button>
                <button onclick="cargarTab('bajo')" data-tipo="bajo"
                   :class="activeTab === 'bajo' ? 'border-yellow-500 text-yellow-600 dark:text-yellow-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                   class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors whitespace-nowrap flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Stock Bajo
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs font-medium bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300">
                        {{ $alertas['productos_stock_bajo_count'] ?? 0 }}
                    </span>
                </button>
                <button onclick="cargarTab('agotadas')" data-tipo="agotadas"
                   :class="activeTab === 'agotadas' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                   class="py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors whitespace-nowrap flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Agotadas
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs font-medium bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                        {{ $alertas['variantes_agotadas_count'] ?? 0 }}
                    </span>
                </button>
            </nav>
        </div>

        <div class="p-6" id="tab-content-container">
            @include('pages.admin.inventario.partials.tab-content', ['alertas' => $alertas, 'tipo' => $tipo ?? 'criticos'])
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
                                  placeholder="Reposición de stock desde alertas"></textarea>
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

<!-- Modal para mostrar variantes problemáticas -->
<div id="variantesModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="cerrarModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2" id="modal-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Variantes Problemáticas
                    </h3>
                    <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="variantes-content">
                    <div class="text-center py-4">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Cargando variantes...</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button onclick="cerrarModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Función para mostrar variantes problemáticas
function mostrarVariantes(productoId) {
    const modal = document.getElementById('variantesModal');
    modal.classList.remove('hidden');
    
    // Mostrar loading
    document.getElementById('variantes-content').innerHTML = `
        <div class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Cargando variantes...</p>
        </div>
    `;
    
    // Hacer petición AJAX
    fetch('{{ route("admin.inventario.alertas.variantes") }}?producto_id=' + productoId, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.variantes.length > 0) {
            let html = '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">';
            html += '<thead class="bg-gray-50 dark:bg-gray-800"><tr>';
            html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Variante</th>';
            html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>';
            html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Stock</th>';
            html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mínimo</th>';
            html += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">%</th>';
            html += '</tr></thead><tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">';
            
            data.variantes.forEach(function(variante) {
                const badgeClass = variante.tipo_alerta === 'critico' 
                    ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' 
                    : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
                
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-4 w-4 rounded border border-gray-300 dark:border-gray-600 mr-2" 
                                     style="background-color: ${variante.codigo_color || '#CCCCCC'};"></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">${variante.nombre}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">+$${variante.precio_adicional.toLocaleString()}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${badgeClass}">
                                ${variante.tipo_alerta.toUpperCase()}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            ${variante.stock_actual}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            ${variante.stock_minimo}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            ${variante.porcentaje}%
                        </td>
                    </tr>
                `;
            });
            
            html += '</tbody></table></div>';
            document.getElementById('variantes-content').innerHTML = html;
        } else {
            document.getElementById('variantes-content').innerHTML = `
                <div class="text-center py-4">
                    <svg class="w-12 h-12 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay variantes problemáticas para este producto</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('variantes-content').innerHTML = `
            <div class="text-center py-4">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <p class="text-sm text-red-600 dark:text-red-400">Error al cargar las variantes</p>
            </div>
        `;
    });
}

// Función para cerrar modal
function cerrarModal() {
    document.getElementById('variantesModal').classList.add('hidden');
}

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
    
    // Obtener el tipo de alerta actual para mantener el tab activo
    const tipoAlertaInput = document.getElementById('tipo-alerta-actual');
    if (tipoAlertaInput) {
        formData.set('tipo_alerta', tipoAlertaInput.value);
    }
    
    // Deshabilitar botón
    submitButton.disabled = true;
    submitButton.textContent = 'Reponiendo...';
    errorDiv.classList.add('hidden');
    
    // Enviar petición como formulario normal (no AJAX) para usar notificaciones de sesión
    form.submit();
}

// Función para cargar tab vía AJAX
function cargarTab(tipo) {
    // Actualizar estado de Alpine.js
    const tabContainer = document.getElementById('tabs-container');
    if (tabContainer && tabContainer.__x) {
        // Actualizar el estado de Alpine.js
        tabContainer.__x.$data.activeTab = tipo;
    }
    
    // Actualizar clases de los botones manualmente para asegurar que se vea el cambio
    const buttons = document.querySelectorAll('#tabs-container nav button');
    buttons.forEach(button => {
        const buttonTipo = button.getAttribute('data-tipo');
        // Remover todas las clases de estado activo/inactivo
        button.classList.remove('border-red-500', 'border-yellow-500', 'border-blue-500', 
                                'border-transparent', 
                                'text-red-600', 'text-yellow-600', 'text-blue-600',
                                'dark:text-red-400', 'dark:text-yellow-400', 'dark:text-blue-400',
                                'text-gray-500', 'dark:text-gray-400');
        
        if (buttonTipo === tipo) {
            // Botón activo
            if (tipo === 'criticos') {
                button.classList.add('border-red-500', 'text-red-600', 'dark:text-red-400');
            } else if (tipo === 'bajo') {
                button.classList.add('border-yellow-500', 'text-yellow-600', 'dark:text-yellow-400');
            } else if (tipo === 'agotadas') {
                button.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            }
        } else {
            // Botón inactivo
            button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        }
    });
    
    // Actualizar URL sin recargar
    const url = new URL(window.location.href);
    url.searchParams.set('tipo', tipo);
    window.history.pushState({ tipo: tipo }, '', url.toString());
    
    // Actualizar input hidden
    const tipoInput = document.getElementById('tipo-alerta-actual');
    if (tipoInput) {
        tipoInput.value = tipo;
    }
    
    // Mostrar loading
    const tabContentContainer = document.getElementById('tab-content-container');
    if (tabContentContainer) {
        tabContentContainer.innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cargando contenido...</p>
                </div>
            </div>
        `;
    }
    
    // Cargar contenido vía AJAX
    fetch(`{{ route('admin.inventario.alertas-optimizadas') }}?tipo=${tipo}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.html) {
            // Actualizar contenido del tab
            if (tabContentContainer) {
                tabContentContainer.innerHTML = data.html;
            }
            
            // Actualizar estadísticas si es necesario
            actualizarEstadisticas();
        } else {
            // Error al cargar
            if (tabContentContainer) {
                tabContentContainer.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Error al cargar</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No se pudo cargar el contenido del tab</p>
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('Error al cargar tab:', error);
        if (tabContentContainer) {
            tabContentContainer.innerHTML = `
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Error al cargar</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No se pudo cargar el contenido del tab. Por favor, intente nuevamente.</p>
                </div>
            `;
        }
    });
}

// Función para actualizar estadísticas
function actualizarEstadisticas() {
    fetch('{{ route("admin.inventario.alertas.estadisticas") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('productos-criticos').textContent = data.estadisticas.productos_criticos;
            document.getElementById('productos-stock-bajo').textContent = data.estadisticas.productos_stock_bajo;
            document.getElementById('variantes-agotadas').textContent = data.estadisticas.variantes_agotadas;
            document.getElementById('total-alertas').textContent = data.estadisticas.total_alertas;
        }
    })
    .catch(error => console.error('Error:', error));
}

// Manejar navegación del navegador (botón atrás/adelante)
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.tipo) {
        const tabContainer = document.getElementById('tabs-container');
        if (tabContainer && tabContainer.__x) {
            tabContainer.__x.$data.activeTab = event.state.tipo;
        }
        cargarTab(event.state.tipo);
    }
});

// Interceptar enlaces de paginación para usar AJAX
document.addEventListener('click', function(e) {
    const paginationLink = e.target.closest('.pagination a, .pagination button');
    if (paginationLink && paginationLink.href) {
        e.preventDefault();
        const url = new URL(paginationLink.href);
        const tipo = url.searchParams.get('tipo') || 'criticos';
        
        // Actualizar estado de Alpine.js
        const tabContainer = document.getElementById('tabs-container');
        if (tabContainer && tabContainer.__x) {
            tabContainer.__x.$data.activeTab = tipo;
        }
        
        // Cargar contenido con la página específica
        const page = url.searchParams.get('page') || 1;
        fetch(`${url.pathname}?tipo=${tipo}&page=${page}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.html) {
                const tabContentContainer = document.getElementById('tab-content-container');
                if (tabContentContainer) {
                    tabContentContainer.innerHTML = data.html;
                }
                
                // Actualizar URL sin recargar
                window.history.pushState({ tipo: tipo, page: page }, '', url.toString());
            }
        })
        .catch(error => {
            console.error('Error al cargar página:', error);
            // Fallback: recargar página completa
            window.location.href = paginationLink.href;
        });
    }
});

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botón de actualizar
    const refreshBtn = document.getElementById('refresh-alerts');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }
    
    // Actualizar estadísticas cada 30 segundos
    setInterval(actualizarEstadisticas, 30000);
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModal();
            cerrarModalReponer();
        }
    });
});
</script>
@endpush
