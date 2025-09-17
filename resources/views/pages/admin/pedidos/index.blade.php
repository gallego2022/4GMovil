@extends('layouts.app-new')

@section('title', 'Administración de{{ {{ __('admin.webhooks.order') }}s }} - 4GMovil')

@php
    function getEstadoClasses($estado) {
        $estado = strtolower(trim($estado));
        
        switch($estado) {
            case 'pendiente':
                return [
                    'bg' => 'bg-yellow-50 dark:bg-yellow-900',
                    'text' => 'text-yellow-700 dark:text-yellow-300',
                    'ring' => 'ring-yellow-600/20 dark:ring-yellow-600/30'
                ];
            case 'confirmado':
                return [
                    'bg' => 'bg-green-50 dark:bg-green-900',
                    'text' => 'text-green-700 dark:text-green-300',
                    'ring' => 'ring-green-600/20 dark:ring-green-600/30'
                ];
            case 'cancelado':
                return [
                    'bg' => 'bg-red-50 dark:bg-red-900',
                    'text' => 'text-red-700 dark:text-red-300',
                    'ring' => 'ring-red-600/20 dark:ring-red-600/30'
                ];
            default:
                return [
                    'bg' => 'bg-gray-50 dark:bg-gray-700',
                    'text' => 'text-gray-700 dark:text-gray-300',
                    'ring' => 'ring-gray-600/20 dark:ring-gray-600/30'
                ];
        }
    }
@endphp

@section('content')
<div class="space-y-6">
    <!-- Vista móvil (cards) -->
    <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
        <!-- Encabezado móvil -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Administración de{{ {{ __('admin.webhooks.order') }}s }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-300">Gestiona todos los pedidos de la tienda</p>
            </div>
            
            <!-- Botones de exportación móvil -->
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <div class="relative group">
                    <button id="exportExcelMovil"
                        class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-full transition duration-300"
                        title="Exportar a Excel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg z-10 whitespace-nowrap">
                        Exportar a Excel
                    </div>
                </div>

                <div class="relative group">
                    <button id="exportPDFMovil"
                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition duration-300"
                        title="Exportar a PDF">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                        </svg>
                    </button>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg z-10 whitespace-nowrap">
                        Exportar a PDF
                    </div>
                </div>
            </div>

            <!--{{ {{ __('admin.fields.field') }} }}de búsqueda móvil -->
            <div class="relative mt-2 rounded-md shadow-sm">
                <input type="text" 
                       id="busquedaMovil" 
                       class="block w-full rounded-md border-0 py-1.5 pl-4 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-800 sm:text-sm sm:leading-6" 
                       placeholder={{ "{{ __('admin.actions.search') }} }}pedidos...">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        @forelse($pedidos as $pedido)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden pedido-card">
            <div class="p-4">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-16 w-16 rounded-lg bg-brand-100 dark:bg-brand-900 flex items-center justify-center">
                            <svg class="h-8 w-8 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                           {{ {{ __('admin.webhooks.order') }} }}#{{ $pedido->pedido_id }}
                        </h3>
                        <div class="mt-1 flex flex-col space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Cliente: {{ $pedido->usuario->nombre_usuario }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                               {{ {{ __('admin.fields.email') }}: }} {{ $pedido->usuario->correo_electronico }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                Total: ${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                               {{ {{ __('admin.webhooks.date') }}: }} {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Método: {{ \App\Helpers\PaymentHelper::getPaymentMethodName($pedido) }}
                            </p>
                            <div class="flex items-center space-x-2">
                                @php
                                    $estadoClasses = getEstadoClasses($pedido->estado->nombre);
                                @endphp
                                <span class="inline-flex items-center rounded-md {{ $estadoClasses['bg'] }} px-2 py-1 text-xs font-medium {{ $estadoClasses['text'] }} ring-1 ring-inset {{ $estadoClasses['ring'] }}">
                                    {{ $pedido->estado->nombre }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Botones de acción -->
                <div class="mt-4 flex justify-end space-x-2">
                    <a href="{{ route('admin.pedidos.show', $pedido->pedido_id) }}" 
                       class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                       {{ {{ __('admin.actions.view') }} }}Detalles
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
           {{ {{ __('admin.status.no') }} }}hay pedidos registrados en el sistema
        </div>
        @endforelse
    </div>

    <!-- Vista escritorio (tabla) -->
    <div class="hidden sm:block">
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-md">
            <!-- Encabezado -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <div>
                        <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Administración de{{ {{ __('admin.webhooks.order') }}s }}</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Gestiona todos los pedidos de la tienda</p>
                    </div>
                    
                </div>
            </div>
            <!-- Barra de herramientas con búsqueda y filtros -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <!-- Búsqueda personalizada -->
                    <div class="flex-1 max-w-md">
                        <label for="busquedaEscritorio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                           {{ {{ __('admin.actions.search') }} }}pedidos
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="busquedaEscritorio" 
                                   class="block w-full rounded-md border-0 py-2 pl-10 pr-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm" 
                                   placeholder={{ "{{ __('admin.actions.search') }} }}por ID, cliente, estado...">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!--{{ {{ __('admin.messages.info') }} }}de registros -->
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span id="infoRegistros">Mostrando todos los pedidos</span>
                    </div>

                    <button id="exportExcelEscritorio"
                            class="group inline-flex items-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 hover:shadow-xl min-w-[120px] justify-center">
                            <svg class="mr-2 h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Excel
                        </button>
                        
                        <button id="exportPDFEscritorio"
                            class="group inline-flex items-center rounded-xl bg-gradient-to-r from-red-500 to-pink-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:from-red-600 hover:to-pink-700 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 hover:shadow-xl min-w-[120px] justify-center">
                            <svg class="mr-2 h-5 w-5 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                            </svg>
                            PDF
                        </button>
                </div>
            </div>
            
            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table id="tablaPedidos"
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm">
                        <tr>
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Cliente</th>
                            <th class="px-4 py-2 text-left"{{ >{{ __('admin.webhooks.date') }}< }}/th>
                            <th class="px-4 py-2 text-left">Total</th>
                            <th class="px-4 py-2 text-left"{{ >{{ __('admin.fields.status') }}< }}/th>
                            <th class="px-4 py-2 text-left">Método de Pago</th>
                            <th class="px-4 py-2 text-center"{{ >{{ __('admin.fields.actions') }}< }}/th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm">
                        @forelse($pedidos as $pedido)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-4 py-2">#{{ $pedido->pedido_id }}</td>
                            <td class="px-4 py-2">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $pedido->usuario->nombre_usuario }}
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        {{ $pedido->usuario->correo_electronico }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div>
                                    <div class="text-gray-900 dark:text-gray-100">
                                        {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('H:i') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">
                                ${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $estadoClasses = getEstadoClasses($pedido->estado->nombre);
                                @endphp
                                <span class="inline-flex items-center rounded-md {{ $estadoClasses['bg'] }} px-2 py-1 text-xs font-medium {{ $estadoClasses['text'] }} ring-1 ring-inset {{ $estadoClasses['ring'] }}">
                                    {{ $pedido->estado->nombre }}
                                </span>
                            </td>
                            <td class="px-4 py-2">{{ \App\Helpers\PaymentHelper::getPaymentMethodName($pedido) }}</td>
                            <td class="px-4 py-2 flex items-center gap-2 justify-center">
                                <!-- Botón{{ {{ __('admin.actions.view') }} }}Detalles -->
                                <div class="relative group">
                                    <a href="{{ route('admin.pedidos.show', $pedido->pedido_id) }}" 
                                       class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <div
                                        class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg dark:bg-gray-700 z-10">
                                       {{ {{ __('admin.actions.view') }} }}Detalles
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
                               {{ {{ __('admin.status.no') }} }}hay pedidos registrados en el sistema
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: "{{ session('success') }}",
        timer: 2500,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: "{{ session('error') }}",
        timer: 2500,
        showConfirmButton: false
    });
</script>
@endif

@push('datatables-css')
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
@endpush

@push('jquery-script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
@endpush

@push('datatables-script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
@endpush

@push('styles')
<style>
    /*{{ {{ __('admin.actions.hide') }} }}completamente los botones por defecto de DataTables */
    .dt-buttons {
        display: none !important;
    }
    
    /*{{ {{ __('admin.actions.hide') }} }}botones específicos */
    .buttons-excel,
    .buttons-pdf {
        display: none !important;
    }
    
    /* Estilos para tooltips personalizados */
    .tooltip {
        position: relative;
        display: inline-block;
    }
    
    .tooltip .tooltiptext {
        visibility: hidden;
        width: auto;
        background-color: #1f2937;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px 12px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 12px;
        white-space: nowrap;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1f2937 transparent transparent transparent;
    }
    
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
    
    /* Estilos para modo oscuro */
    .dark .tooltip .tooltiptext {
        background-color: #374151;
    }
    
    .dark .tooltip .tooltiptext::after {
        border-color: #374151 transparent transparent transparent;
    }
    
    /* Estilos para bordes de tabla en modo oscuro */
    .dark #tablaPedidos {
        border: 1px solid #374151 !important;
    }
    
    .dark #tablaPedidos th {
        border-bottom: 1px solid #4b5563 !important;
        border-right: 1px solid #4b5563 !important;
    }
    
    .dark #tablaPedidos td {
        border-bottom: 1px solid #374151 !important;
        border-right: 1px solid #374151 !important;
    }
    
    .dark #tablaPedidos th:last-child,
    .dark #tablaPedidos td:last-child {
        border-right: none !important;
    }
    
    .dark #tablaPedidos tr:last-child td {
        border-bottom: none !important;
    }
    
    /* Estilos para bordes de tabla en modo claro */
    #tablaPedidos {
        border: 1px solid #d1d5db !important;
    }
    
    #tablaPedidos th {
        border-bottom: 1px solid #e5e7eb !important;
        border-right: 1px solid #e5e7eb !important;
    }
    
    #tablaPedidos td {
        border-bottom: 1px solid #f3f4f6 !important;
        border-right: 1px solid #f3f4f6 !important;
    }
    
    #tablaPedidos th:last-child,
    #tablaPedidos td:last-child {
        border-right: none !important;
    }
    
    #tablaPedidos tr:last-child td {
        border-bottom: none !important;
    }
    
    /* Asegurar que los bordes de DataTables sean visibles */
    .dark .dataTables_wrapper .dataTables_scroll {
        border: 1px solid #374151 !important;
    }
    
    .dataTables_wrapper .dataTables_scroll {
        border: 1px solid #d1d5db !important;
    }
    
    /* Mejorar visibilidad del contenedor principal en modo oscuro */
    .dark .bg-white.dark\:bg-gray-900 {
        border: 2px solid #4b5563 !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2) !important;
    }
    
    /* Mejorar visibilidad de títulos en modo oscuro */
    .dark h2.text-2xl.font-bold {
        color: #f9fafb !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
    }
    
    .dark p.text-sm.text-gray-500.dark\:text-gray-300 {
        color: #d1d5db !important;
    }
    
    /* Mejorar contraste del contenedor en modo claro */
    .bg-white.dark\:bg-gray-900 {
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
    }
    
    /*{{ {{ __('admin.actions.hide') }} }}elementos de DataTables que no necesitamos */
    .dataTables_filter {
        display: none !important;
    }
    
    .dataTables_length {
        display: none !important;
    }
    
    .dataTables_info {
        display: none !important;
    }
    
    /* Estilos para la barra de herramientas personalizada */
    .bg-gray-50.dark\:bg-gray-800 {
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    }
    
    .dark .bg-gray-50.dark\:bg-gray-800 {
        border: 1px solid #374151 !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.2) !important;
    }
    
    /* Mejorar apariencia de los botones de exportación */
    .bg-green-600.hover\:bg-green-500,
    .bg-red-600.hover\:bg-red-500 {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
    }
    
    .bg-green-600.hover\:bg-green-500:hover,
    .bg-red-600.hover\:bg-red-500:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        transform: translateY(-1px) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        //{{ {{ __('admin.actions.view') }}i }}ficar si hay datos antes de inicializar DataTables
        var hasData = {{ $pedidos->count() > 0 ? 'true' : 'false' }};
        
        if (hasData) {
            // Inicializar DataTable solo si hay datos
            var table = $('#tablaPedidos').DataTable({
                dom: 'rtip', // Removido 'f' para ocultar el campo de búsqueda por defecto
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Pedidos - 4GMovil',
                        text: 'Exportar a Excel',
                        className: 'buttons-excel', 
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c[r^="C"]', sheet).attr('s', '2');
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Pedidos - 4GMovil',
                        text: 'Exportar a PDF',
                        className: 'buttons-pdf', 
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        customize: function(doc) {
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }
                    }
                ],
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": {{ "{{ __('admin.actions.show') }} }}_MENU_ registros",
                    "sZeroRecords": {{ "{{ __('admin.status.no') }} }}se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": {{ "{{ __('admin.actions.first') }}" }},
                        "sLast": {{ "{{ __('admin.actions.last') }}" }},
                        "sNext": {{ "{{ __('admin.actions.next') }}" }},
                        "sPrevious": {{ "{{ __('admin.actions.previous') }}" }}
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                processing: true,
                serverSide: false,
                searching: true,
                ordering: true,
                columnDefs: [
                    { orderable: false, targets: [-1] },
                    { searchable: false, targets: [-1] }
                ],
                order: [[0, 'desc']],
                responsive: true,
                pagingType: "simple_numbers",
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, {{ "{{ __('admin.actions.all') }}" }}]],
                pageLength: 10,
                // Configuración para manejar tablas vacías
                deferRender: true,
                drawCallback: function(settings) {
                    // Aplicar estilos a los botones de paginación
                    $('.paginate_button').addClass('relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 focus:z-20 focus:outline-offset-0');
                    $('.paginate_button.current').addClass('z-10 bg-brand-600 text-white hover:bg-brand-500 ring-brand-600').removeClass('text-gray-900 ring-gray-300');
                    $('.paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');

                    // Aplicar estilos dark mode a elementos de DataTables
                    $('.dataTables_length select').addClass('dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700');
                    $('.dataTables_info').addClass('dark:text-gray-100');
                     
                     // Actualizar información de registros después de un pequeño delay
                     setTimeout(function() {
                         if (typeof updateInfoRegistros === 'function') {
                             updateInfoRegistros();
                         }
                     }, 100);
                 },
                 // Configuración para manejar tablas vacías
                 initComplete: function(settings, json) {
                     //{{ {{ __('admin.actions.view') }}i }}ficar si la tabla está vacía
                     if (this.api().data().length === 0) {
                         //{{ {{ __('admin.actions.hide') }} }}elementos de DataTables cuando no hay datos
                         $('.dataTables_paginate').hide();
                         $('.dataTables_length').hide();
                         $('.dataTables_info').hide();
                     }
                 }
             });
             
             // Inicializar información de registros después de que DataTable esté listo
             setTimeout(function() {
                 if (typeof updateInfoRegistros === 'function') {
                     updateInfoRegistros();
                 }
             }, 200);
             
             // Función para actualizar información de registros
             function updateInfoRegistros() {
                 if (!table || typeof table.page !== 'function') {
                     console.warn('DataTable no está disponible para actualizar información');
                     return;
                 }
                 
                 try {
                     var info = table.page.info();
                     var totalRecords = info.recordsTotal;
                     var filteredRecords = info.recordsDisplay;
                     var currentPage = info.page + 1;
                     var totalPages = info.pages;
                     var startRecord = info.start + 1;
                     var endRecord = info.end;
                     
                     if (filteredRecords === totalRecords) {
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${totalRecords} pedidos`);
                    } else {
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${filteredRecords} pedidos (filtrado de ${totalRecords} total)`);
                     }
                 } catch (error) {
                     console.error('Error al actualizar información de registros:', error);
                     $('#infoRegistros').text({{ '{{ __('admin.messages.info') }} }}no disponible');
                 }
             }
             
             // Búsqueda personalizada para escritorio
             $('#busquedaEscritorio').on('keyup', function() {
                 var searchValue = $(this).val();
                 if (table && typeof table.search === 'function') {
                     table.search(searchValue).draw();
                 }
             });

            // Botones personalizados de exportación (escritorio)
             setTimeout(function() {
                 $('#exportExcelEscritorio').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-excel').trigger();
                     }
                 });
     
                 $('#exportPDFEscritorio').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-pdf').trigger();
                     }
                 });
             }, 100);
             
             // Botones personalizados de exportación (móvil)
             setTimeout(function() {
                 $('#exportExcelMovil').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-excel').trigger();
                     }
                 });
     
                 $('#exportPDFMovil').on('click', function () {
                     if (table && table.buttons) {
                         table.buttons('.buttons-pdf').trigger();
                     }
                 });
             }, 100);
        } else {
            // Si no hay datos, ocultar elementos de DataTables
            $('.dataTables_paginate').hide();
            $('.dataTables_length').hide();
            $('.dataTables_info').hide();
            $('#infoRegistros').text({{ '{{ __('admin.status.no') }} }}hay pedidos registrados');
        }

        // Función para buscar en las tarjetas móviles
        function searchMobileCards(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            var visibleCount = 0;
            
            $('#mobileCards .pedido-card').each(function() {
                const card = $(this);
                
                // Obtener todos los textos de la tarjeta
                const id = card.find('h3').text().toLowerCase();
                const cliente = card.find('p').filter(function() {
                    return $(this).text().includes('Cliente:');
                }).text().toLowerCase();
                const email = card.find('p').filter(function() {
                    return $(this).text().includes('Email:');
                }).text().toLowerCase();
                const total = card.find('p').filter(function() {
                    return $(this).text().includes('Total:');
                }).text().toLowerCase();
                const fecha = card.find('p').filter(function() {
                    return $(this).text().includes('Fecha:');
                }).text().toLowerCase();
                const metodo = card.find('p').filter(function() {
                    return $(this).text().includes('Método:');
                }).text().toLowerCase();
                
                //{{ {{ __('admin.actions.search') }} }}en todos los spans (estados)
                const estados = card.find('span').map(function() {
                    return $(this).text().toLowerCase();
                }).get().join(' ');
                
                //{{ {{ __('admin.actions.view') }}i }}ficar si el término de búsqueda coincide con algún campo
                const matchFound = id.includes(searchTerm) || 
                    cliente.includes(searchTerm) || 
                    email.includes(searchTerm) || 
                    total.includes(searchTerm) || 
                    fecha.includes(searchTerm) ||
                    metodo.includes(searchTerm) ||
                    estados.includes(searchTerm);
                
                if (matchFound) {
                    card.show();
                    visibleCount++;
                } else {
                    card.hide();
                }
            });

            //{{ {{ __('admin.actions.show') }} }}mensaje cuando no hay resultados
            const noResultsMsg = $('#mobileNoResults');
            
            if (visibleCount === 0 && searchTerm !== '') {
                if (noResultsMsg.length === 0) {
                    $('#mobileCards').append(`
                        <div id="mobileNoResults" class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                           {{ {{ __('admin.status.no') }} }}se encontraron pedidos que coincidan con la búsqueda
                        </div>
                    `);
                }
            } else {
                noResultsMsg.remove();
            }
        }

        //{{ {{ __('admin.webhooks.event') }} }}de búsqueda en móvil
        let searchTimeout;
        $('#busquedaMovil').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();
            searchTimeout = setTimeout(() => {
                searchMobileCards(searchTerm);
            }, 300);
        });
    });
</script>
@endpush

@endsection 