@extends('layouts.app-new')

@section('title', 'Listado de ' . __('admin.products.product') . 's - 4GMovil')

@push('datatables-css')
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">
@endpush

@push('jquery-script')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
@endpush

@push('datatables-script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
@endpush

@php
    function getEstadoClasses($estado) {
        $estado = strtolower(trim($estado));
        
        switch($estado) {
            case 'nuevo':
                return [
                    'bg' => 'bg-green-50 dark:bg-green-900',
                    'text' => 'text-green-700 dark:text-green-300',
                    'ring' => 'ring-green-600/20 dark:ring-green-600/30'
                ];
            case 'usado':
                return [
                    'bg' => 'bg-blue-50 dark:bg-blue-900',
                    'text' => 'text-blue-700 dark:text-blue-300',
                    'ring' => 'ring-blue-600/20 dark:ring-blue-600/30'
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
<!-- Notificaciones -->
<x-notifications />

<div class="space-y-6">
         <!-- Vista móvil (cards) -->
     <div class="grid grid-cols-1 gap-4 sm:hidden" id="mobileCards">
         <!-- Encabezado móvil -->
         <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
             <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Listado de {{ __('admin.products.product') }}s</h2>
                 <p class="text-sm text-gray-500 dark:text-gray-300">Gestiona los productos de la tienda</p>
        </div>
             
             <!-- Botones de acción móvil -->
             <div class="flex flex-wrap items-center gap-2 mb-4">
                <!-- Botón crear producto -->
            <a href="{{ route('productos.create') }}" 
               class="inline-flex items-center rounded-lg bg-gradient-to-r from-slate-600 to-gray-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5 transition-transform duration-200 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('admin.actions.create') }} {{ __('admin.products.product') }}
            </a>
                 
                 <!-- Botones de exportación móvil -->
                 <div class="relative group">
                     <button id="exportExcelMovil"
                         class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-full transition duration-300"
                         title="Exportar a Excel">
                         <!-- Excel Icon -->
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M12 4v16m8-8H4" />
                         </svg>
                     </button>
                     <!-- Tooltip para Excel -->
                     <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg z-10 whitespace-nowrap">
                         Exportar a Excel
        </div>
    </div>

                 <div class="relative group">
                     <button id="exportPDFMovil"
                         class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full transition duration-300"
                         title="Exportar a PDF">
                         <!-- PDF Icon -->
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M19 11H5m14 0v10a2 2 0 01-2 2H7a2 2 0 01-2-2V11m14-6H5a2 2 0 00-2 2v2h18V7a2 2 0 00-2-2z" />
                </svg>
            </button>
                     <!-- Tooltip para PDF -->
                     <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded px-2 py-1 shadow-lg z-10 whitespace-nowrap">
                         Exportar a PDF
                     </div>
        </div>
    </div>

        <!-- Campo de búsqueda móvil -->
            <div class="relative mt-2 rounded-md shadow-sm">
                <input type="text" 
                       id="busquedaMovil" 
                       class="block w-full rounded-md border-0 py-1.5 pl-4 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-800 sm:text-sm sm:leading-6" 
                       placeholder="{{ __('admin.actions.search') }} productos...">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        @forelse($productos as $producto)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden producto-card">
            <div class="p-4">
                <div class="flex items-start space-x-4">
                    <!-- Imagen del producto -->
                    <div class="flex-shrink-0">
                        @if($producto->imagenes->isNotEmpty())
                        <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                             class="h-24 w-24 rounded-lg object-cover shadow-sm" 
                             alt="{{ $producto->nombre_producto }}">
                        @else
                        <img src="{{ asset('img/Logo_2.png') }}" 
                             class="h-24 w-24 rounded-lg object-cover shadow-sm" 
                             alt="Sin imagen">
                        @endif
                    </div>
                    <!-- Detalles del producto -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                            {{ $producto->nombre_producto }}
                        </h3>
                        <div class="mt-1 flex flex-col space-y-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                ID: {{ $producto->producto_id }}
                            </p>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                ${{ number_format($producto->precio, 2) }}
                            </p>
                            
                            <!-- Sección de Stock Mejorada -->
                            <div class="space-y-1">
                                <!-- Stock Total -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Stock Total:</span>
                                    <span class="text-xs font-medium text-gray-900 dark:text-gray-100">{{ $producto->stock }}</span>
                                </div>
                                
                                <!-- Stock Disponible -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                                    <span class="text-xs font-medium {{ $producto->stock_disponible > 10 ? 'text-green-600 dark:text-green-400' : ($producto->stock_disponible > 5 ? 'text-yellow-600 dark:text-yellow-400' : ($producto->stock_disponible > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400')) }}">
                                        {{ $producto->stock_disponible }}
                                    </span>
                                </div>
                                
                                <!-- Stock Reservado (solo si hay) -->
                                @if($producto->stock_reservado > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                                    <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ $producto->stock_reservado }}</span>
                                </div>
                                @endif
                                
                                <!-- Indicador de estado -->
                                @if($producto->stock_disponible <= 0)
                                    <div class="flex items-center space-x-1 mt-1">
                                        <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs text-red-600 dark:text-red-400">Sin stock disponible</span>
                                    </div>
                                @elseif($producto->stock_reservado > $producto->stock * 0.5)
                                    <div class="flex items-center space-x-1 mt-1">
                                        <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400">Alto stock reservado</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Categoría: {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Marca: {{ $producto->marca->nombre ?? 'Sin marca' }}
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Botones de acción -->
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" 
                            onclick="abrirModalProducto({{ $producto->producto_id }})"
                            class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900/50 px-3 py-2 text-sm font-semibold text-green-700 dark:text-green-300 shadow-sm ring-1 ring-inset ring-green-300 dark:ring-green-600 hover:bg-green-100 dark:hover:bg-green-900">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Ver Detalles
                    </button>
                    <a href="{{ route('productos.edit', $producto) }}" 
                       class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400 dark:text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"/>
                        </svg>
                        {{ __('admin.actions.edit') }}
                    </a>
                    <form action="{{ route('productos.destroy', $producto) }}" 
                          method="POST" 
                          class="inline confirm-action"
                          data-title="¿Eliminar producto?"
                          data-message="¿Estás seguro de eliminar el producto {{ $producto->nombre_producto }}?"
                          data-confirm-text="Sí, eliminar"
                          data-cancel-text="Cancelar"
                          data-confirm-color="red"
                          data-show-warning="true">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-950 px-3 py-2 text-sm font-semibold text-red-700 dark:text-red-300 shadow-sm ring-1 ring-inset ring-red-600/20 dark:ring-red-900/20 hover:bg-red-100 dark:hover:bg-red-900">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd"/>
                            </svg>
                            {{ __('admin.actions.edit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
            {{ __('admin.actions.edit') }} registrados en el sistema
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
                         <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">Listado de {{ __('admin.products.product') }}s</h2>
                         <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Gestiona los productos de la tienda</p>
                     </div>

                      <!-- Botón crear producto -->
                    <a href="{{ route('productos.create') }}" 
                    class="group inline-flex items-center rounded-xl bg-gradient-to-r from-slate-600 to-gray-700 px-8 py-4 text-base font-semibold text-white shadow-lg hover:from-slate-700 hover:to-gray-800 transform hover:scale-105 transition-all duration-300 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 hover:shadow-xl min-w-[180px] justify-center">
                     <svg class="-ml-0.5 mr-3 h-6 w-6 transition-transform duration-300 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                     </svg>
                     {{ __('admin.actions.create') }} {{ __('admin.products.product') }}
                 </a>

                 </div>
             </div>
             
            <!-- Barra de herramientas con búsqueda y filtros -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                     <!-- Búsqueda personalizada -->
                     <div class="flex-1 max-w-md">
                         <label for="busquedaEscritorio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                             {{ __('admin.actions.search') }} productos
                         </label>
                         <div class="relative">
                             <input type="text" 
                                    id="busquedaEscritorio" 
                                    class="block w-full rounded-md border-0 py-2 pl-10 pr-4 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand-600 dark:bg-gray-700 sm:text-sm" 
                                    placeholder="{{ __('admin.actions.search') }} por nombre, ID, categoría...">
                             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                 <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                     <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                 </svg>
                             </div>
                         </div>
                     </div>
                     
                     <!-- {{ __('admin.actions.edit') }} de registros -->
                     <div class="text-sm text-gray-500 dark:text-gray-400">
                         <span id="infoRegistros">Mostrando todos los productos</span>
                     </div>
            <!-- Botones de Exportacion -->
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
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="tablaProductos"
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 text-gray-700 dark:text-gray-300 text-sm font-semibold">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">ID</th>
                                <th class="px-6 py-4 text-left font-semibold">Imagen</th>
                                <th class="px-6 py-4 text-left font-semibold">Nombre</th>
                                <th class="px-6 py-4 text-left font-semibold">Precio</th>
                                <th class="px-6 py-4 text-left font-semibold">Stock</th>
                                <th class="px-6 py-4 text-left font-semibold">Estado</th>
                                <th class="px-6 py-4 text-left font-semibold">Categoría</th>
                                <th class="px-6 py-4 text-left font-semibold">Marca</th>
                                <th class="px-6 py-4 text-center font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 text-sm">
                            @forelse($productos as $producto)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200 ease-in-out border-b border-gray-100 dark:border-gray-800" data-producto-id="{{ $producto->producto_id }}">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ $producto->producto_id }}</td>
                            <td class="px-6 py-4">
                                @if($producto->imagenes->isNotEmpty())
                                <img src="{{ asset('storage/' . $producto->imagenes[0]->ruta_imagen) }}" 
                                     class="h-14 w-14 rounded-lg object-cover shadow-md border border-gray-200 dark:border-gray-700" 
                                     alt="{{ $producto->nombre_producto }}">
                                @else
                                <img src="{{ asset('img/Logo_2.png') }}" 
                                     class="h-14 w-14 rounded-lg object-cover shadow-md border border-gray-200 dark:border-gray-700" 
                                     alt="Sin imagen">
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">{{ $producto->nombre_producto }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">${{ number_format($producto->precio, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1" data-stock-container>
                                    <!-- Stock Total -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Total:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300" data-stock-total="{{ $producto->producto_id }}">
                                            {{ $producto->stock }}
                                        </span>
                                    </div>
                                    
                                    <!-- Stock Disponible -->
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $producto->stock_disponible > 10 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : ($producto->stock_disponible > 5 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : ($producto->stock_disponible > 0 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300')) }}" data-stock-disponible="{{ $producto->producto_id }}">
                                            {{ $producto->stock_disponible }}
                                        </span>
                                    </div>
                                    
                                    <!-- Stock Reservado (solo si hay) -->
                                    <div class="flex items-center space-x-2" data-stock-reservado-container="{{ $producto->producto_id }}" style="{{ $producto->stock_reservado > 0 ? '' : 'display: none;' }}">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300" data-stock-reservado="{{ $producto->producto_id }}">
                                            {{ $producto->stock_reservado }}
                                        </span>
                                    </div>
                                    
                                    <!-- Indicador de estado -->
                                    <div data-stock-indicador="{{ $producto->producto_id }}">
                                        @if($producto->stock_disponible <= 0)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs text-red-600 dark:text-red-400">Sin stock disponible</span>
                                            </div>
                                        @elseif($producto->stock_reservado > $producto->stock * 0.5)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs text-yellow-600 dark:text-yellow-400">Alto stock reservado</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $estadoClasses = getEstadoClasses($producto->estado);
                                @endphp
                                <span class="inline-flex items-center rounded-full {{ $estadoClasses['bg'] }} px-3 py-1 text-sm font-medium {{ $estadoClasses['text'] }} ring-1 ring-inset {{ $estadoClasses['ring'] }}">
                                    {{ ucfirst($producto->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $producto->marca->nombre ?? 'Sin marca' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <!-- Botón Ver Detalles -->
                                    <div class="relative group">
                                        <button type="button" 
                                                onclick="abrirModalProducto({{ $producto->producto_id }})"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-green-50 hover:bg-green-100 dark:bg-green-900/50 dark:hover:bg-green-900 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 transition-all duration-200 ease-in-out transform hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <div
                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg dark:bg-gray-700 z-10 whitespace-nowrap">
                                            Ver Detalles
                                        </div>
                                    </div>

                                    <!-- Botón Editar -->
                                    <div class="relative group">
                                        <a href="{{ route('productos.edit', $producto) }}" 
                                           class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/50 dark:hover:bg-blue-900 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-all duration-200 ease-in-out transform hover:scale-110">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536M9 13l6-6m-6 6v3h3l6-6m-3-3L6 18H3v-3L15.232 5.232z" />
                                            </svg>
                                        </a>
                                        <div
                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg dark:bg-gray-700 z-10 whitespace-nowrap">
                                            Editar
                                        </div>
                                    </div>

                                    <!-- Botón Eliminar -->
                                    <div class="relative group">
                                        <form action="{{ route('productos.destroy', $producto) }}" 
                                              method="POST" 
                                              class="inline confirm-action"
                                              data-title="¿Eliminar producto?"
                                              data-message="¿Estás seguro de eliminar el producto {{ $producto->nombre_producto }}?"
                                              data-confirm-text="Sí, eliminar"
                                              data-cancel-text="Cancelar"
                                              data-confirm-color="red"
                                              data-show-warning="true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-50 hover:bg-red-100 dark:bg-red-900/50 dark:hover:bg-red-900 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-all duration-200 ease-in-out transform hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                        <div
                                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-gray-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg dark:bg-gray-700 z-10 whitespace-nowrap">
                                            Eliminar
                                        </div>
                                    </div>
                                </div>
                            </td>
                            </tr>
                            @empty
                            <tr>
                            <td colspan="9" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">
            No hay productos registrados en el sistema
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

@if(session('eliminado') == 'ok')
<script>
    Swal.fire({
        title: 'Eliminado',
        text: 'El producto ha sido eliminado correctamente.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

@push('styles')
<style>
    /* Ocultar completamente los botones por defecto de DataTables */
    .dt-buttons {
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
    .dark #tablaProductos {
        border: 1px solid #374151 !important;
    }
    
    .dark #tablaProductos th {
        border-bottom: 1px solid #4b5563 !important;
        border-right: 1px solid #4b5563 !important;
    }
    
    .dark #tablaProductos td {
        border-bottom: 1px solid #374151 !important;
        border-right: 1px solid #374151 !important;
    }
    
    .dark #tablaProductos th:last-child,
    .dark #tablaProductos td:last-child {
        border-right: none !important;
    }
    
    .dark #tablaProductos tr:last-child td {
        border-bottom: none !important;
    }
    
    /* Estilos para bordes de tabla en modo claro */
    #tablaProductos {
        border: 1px solid #d1d5db !important;
    }
    
    #tablaProductos th {
        border-bottom: 1px solid #e5e7eb !important;
        border-right: 1px solid #e5e7eb !important;
    }
    
    #tablaProductos td {
        border-bottom: 1px solid #f3f4f6 !important;
        border-right: 1px solid #f3f4f6 !important;
    }
    
    #tablaProductos th:last-child,
    #tablaProductos td:last-child {
        border-right: none !important;
    }
    
    #tablaProductos tr:last-child td {
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
     
    /* Ocultar elementos de DataTables que no necesitamos */
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
     .bg-green-500.hover\:bg-green-600,
     .bg-red-500.hover\:bg-red-600 {
         box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
     }
     
     .bg-green-500.hover\:bg-green-600:hover,
     .bg-red-500.hover\:bg-red-600:hover {
         box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
         transform: translateY(-1px) !important;
     }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Variable global para la tabla
        var table = null;
        
        // Verificar si hay datos antes de inicializar DataTables
        var hasData = {{ $productos->count() > 0 ? 'true' : 'false' }};
        
        if (hasData) {
            // Inicializar DataTable solo si hay datos
            table = $('#tablaProductos').DataTable({
                dom: 'Brtip', // 'B' para botones, 'r' para processing, 't' para tabla, 'i' para info, 'p' para paginación
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Productos - 4GMovil',
                        text: 'Exportar a Excel',
                        className: 'buttons-excel', 
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7]
                        },
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c[r^="C"]', sheet).attr('s', '2');
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Productos - 4GMovil',
                        text: 'Exportar a PDF',
                        className: 'buttons-pdf',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5, 6, 7]
                        },
                        customize: function(doc) {
                            try {
                                // Obtener fecha actual
                                var fecha = new Date().toLocaleDateString('es-CO', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                
                                // Estilos personalizados
                                doc.defaultStyle.fontSize = 9;
                                // No especificar fuente para usar la fuente por defecto de pdfmake
                                
                                // Buscar la tabla en el contenido y guardar su referencia
                                var tableObj = null;
                                var tableIndex = -1;
                                
                                for (var i = 0; i < doc.content.length; i++) {
                                    if (doc.content[i] && doc.content[i].table) {
                                        tableObj = doc.content[i].table;
                                        tableIndex = i;
                                        break;
                                    }
                                }
                                
                                // Si no se encuentra la tabla, salir
                                if (!tableObj || !tableObj.body) {
                                    console.warn('No se encontró la tabla en el contenido del PDF');
                                    return;
                                }
                                
                                // Guardar referencia al body de la tabla antes de modificar el contenido
                                var tableBody = tableObj.body;
                                
                                // Verificar que tableBody tenga al menos una fila
                                if (!tableBody || tableBody.length === 0) {
                                    console.error('La tabla está vacía');
                                    return;
                                }
                                
                                // Encabezado personalizado (insertar antes de la tabla)
                                doc.content.splice(tableIndex, 0, {
                                    margin: [0, 0, 0, 12],
                                    alignment: 'center',
                                    fontSize: 18,
                                    text: '4GMovil',
                                    bold: true,
                                    color: '#1f2937'
                                });
                                
                                doc.content.splice(tableIndex + 1, 0, {
                                    margin: [0, 0, 0, 8],
                                    alignment: 'center',
                                    fontSize: 14,
                                    text: 'Listado de Productos',
                                    bold: true,
                                    color: '#4b5563'
                                });
                                
                                doc.content.splice(tableIndex + 2, 0, {
                                    margin: [0, 0, 0, 12],
                                    alignment: 'center',
                                    fontSize: 10,
                                    text: 'Generado el: ' + fecha,
                                    color: '#6b7280'
                                });
                                
                                // Ahora trabajar con la referencia guardada de la tabla
                                // Estilo para el encabezado de la tabla
                                if (tableBody[0] && Array.isArray(tableBody[0]) && tableBody[0].length > 0) {
                                    for (var i = 0; i < tableBody[0].length; i++) {
                                        if (tableBody[0][i]) {
                                            tableBody[0][i].fillColor = '#3b82f6';
                                            tableBody[0][i].color = '#ffffff';
                                            tableBody[0][i].bold = true;
                                            tableBody[0][i].fontSize = 10;
                                            tableBody[0][i].alignment = 'center';
                                        }
                                    }
                                }
                                
                                // Estilos alternados para las filas
                                for (var i = 1; i < tableBody.length; i++) {
                                    if (tableBody[i] && Array.isArray(tableBody[i]) && tableBody[i].length > 0) {
                                        if (i % 2 === 0) {
                                            for (var j = 0; j < tableBody[i].length; j++) {
                                                if (tableBody[i][j]) {
                                                    tableBody[i][j].fillColor = '#f3f4f6';
                                                }
                                            }
                                        }
                                        // Alineación de columnas
                                        if (tableBody[i][0]) tableBody[i][0].alignment = 'left'; // ID
                                        if (tableBody[i][1]) tableBody[i][1].alignment = 'left'; // Nombre
                                        if (tableBody[i][2]) tableBody[i][2].alignment = 'left'; // Categoría
                                        if (tableBody[i][3]) tableBody[i][3].alignment = 'right'; // Precio
                                        if (tableBody[i][4]) tableBody[i][4].alignment = 'center'; // Stock
                                        if (tableBody[i][5]) tableBody[i][5].alignment = 'center'; // Estado
                                        if (tableBody[i][6]) tableBody[i][6].alignment = 'left'; // Fecha
                                    }
                                }
                                
                                // Anchos de columnas
                                if (tableBody[0] && Array.isArray(tableBody[0]) && tableBody[0].length > 0) {
                                    tableObj.widths = ['8%', '25%', '15%', '12%', '10%', '12%', '18%'];
                                }
                                
                                // Pie de página
                                doc['footer'] = function(page, pages) {
                                    return {
                                        margin: [40, 0, 40, 0],
                                        columns: [
                                            {
                                                alignment: 'left',
                                                text: 'Página ' + page + ' de ' + pages,
                                                fontSize: 9,
                                                color: '#6b7280'
                                            },
                                            {
                                                alignment: 'right',
                                                text: '4GMovil - Sistema de Gestión',
                                                fontSize: 9,
                                                color: '#6b7280'
                                            }
                                        ]
                                    };
                                };
                                
                                // Márgenes
                                doc.pageMargins = [40, 100, 40, 60];
                            } catch (error) {
                                console.error('Error al personalizar el PDF:', error);
                                console.error('Stack:', error.stack);
                            }
                        }
                    }
                ],
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
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
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
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
                    { orderable: false, targets: [1, -1] },
                    { searchable: false, targets: [1, -1] }
                ],
                order: [[0, 'asc']],
                responsive: true,
                pagingType: "simple_numbers",
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
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
                     // Verificar si la tabla está vacía
                     if (this.api().data().length === 0) {
                         // Ocultar elementos de DataTables cuando no hay datos
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
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${totalRecords} productos`);
                    } else {
                         $('#infoRegistros').text(`Mostrando ${startRecord} a ${endRecord} de ${filteredRecords} productos (filtrado de ${totalRecords} total)`);
                     }
                 } catch (error) {
                     console.error('Error al actualizar información de registros:', error);
                     $('#infoRegistros').text('Información no disponible');
                 }
             }
             
             // Búsqueda personalizada para escritorio
             $('#busquedaEscritorio').on('keyup', function() {
                 var searchValue = $(this).val();
                 if (table && typeof table.search === 'function') {
                     table.search(searchValue).draw();
                 } else {
                     console.warn('DataTable no está disponible para búsqueda');
                 }
             });

             // Búsqueda personalizada para móvil
             $('#busquedaMovil').on('keyup', function() {
                 var searchValue = $(this).val();
                 searchMobileCards(searchValue);
             });

             // Asegurar que la búsqueda funcione después de que DataTables esté listo
             setTimeout(function() {
                 // Re-registrar el evento de búsqueda para escritorio
                 $('#busquedaEscritorio').off('keyup').on('keyup', function() {
                     var searchValue = $(this).val();
                     if (table && typeof table.search === 'function') {
                         table.search(searchValue).draw();
                     }
                 });
                 
                 // Re-registrar el evento de búsqueda para móvil
                 $('#busquedaMovil').off('keyup').on('keyup', function() {
                     var searchValue = $(this).val();
                     searchMobileCards(searchValue);
                 });
             }, 500);

             // Botones personalizados de exportación (escritorio)
             setTimeout(function() {
                 $('#exportExcelEscritorio').off('click').on('click', function (e) {
                     e.preventDefault();
                     e.stopPropagation();
                     
                     console.log('Exportar Excel - Escritorio clickeado');
                     
                     if (table && table.buttons) {
                         try {
                             table.buttons('.buttons-excel').trigger();
                             console.log('Botón Excel activado');
                         } catch (error) {
                             console.error('Error al exportar Excel:', error);
                         }
                     } else {
                         console.error('Tabla o botones no disponibles');
                     }
                 });
     
                 $('#exportPDFEscritorio').off('click').on('click', function (e) {
                     e.preventDefault();
                     e.stopPropagation();
                     
                     console.log('Exportar PDF - Escritorio clickeado');
                     
                     if (table && table.buttons) {
                         try {
                             table.buttons('.buttons-pdf').trigger();
                             console.log('Botón PDF activado');
                         } catch (error) {
                             console.error('Error al exportar PDF:', error);
                         }
                     } else {
                         console.error('Tabla o botones no disponibles');
                     }
                 });
             }, 500);
             
             // Botones personalizados de exportación (móvil)
             setTimeout(function() {
                 $('#exportExcelMovil').off('click').on('click', function (e) {
                     e.preventDefault();
                     e.stopPropagation();
                     
                     console.log('Exportar Excel - Móvil clickeado');
                     
                     if (table && table.buttons) {
                         try {
                             table.buttons('.buttons-excel').trigger();
                             console.log('Botón Excel activado');
                         } catch (error) {
                             console.error('Error al exportar Excel:', error);
                         }
                     } else {
                         console.error('Tabla o botones no disponibles');
                     }
                 });
     
                 $('#exportPDFMovil').off('click').on('click', function (e) {
                     e.preventDefault();
                     e.stopPropagation();
                     
                     console.log('Exportar PDF - Móvil clickeado');
                     
                     if (table && table.buttons) {
                         try {
                             table.buttons('.buttons-pdf').trigger();
                             console.log('Botón PDF activado');
                         } catch (error) {
                             console.error('Error al exportar PDF:', error);
                         }
                     } else {
                         console.error('Tabla o botones no disponibles');
                     }
                 });
             }, 500);
        } else {
            // Si no hay datos, ocultar elementos de DataTables
            $('.dataTables_paginate').hide();
            $('.dataTables_length').hide();
            $('.dataTables_info').hide();
            $('#infoRegistros').text('No hay productos registrados');
        }

        // Confirmación para eliminar producto
        $('.form-eliminar').submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0088ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Función para buscar en las tarjetas móviles
        function searchMobileCards(searchTerm) {
            searchTerm = searchTerm.toLowerCase();
            var visibleCount = 0;
            
            $('#mobileCards .producto-card').each(function() {
                const card = $(this);
                
                // Obtener todos los textos de la tarjeta
                const nombre = card.find('h3').text().toLowerCase();
                const id = card.find('p').filter(function() {
                    return $(this).text().includes('ID:');
                }).text().toLowerCase();
                const precio = card.find('p').filter(function() {
                    return $(this).text().includes('$');
                }).text().toLowerCase();
                const categoria = card.find('p').filter(function() {
                    return $(this).text().includes('Categoría:');
                }).text().toLowerCase();
                const marca = card.find('p').filter(function() {
                    return $(this).text().includes('Marca:');
                }).text().toLowerCase();
                const stock = card.find('p').filter(function() {
                    return $(this).text().includes('Stock:');
                }).text().toLowerCase();
                
                // Leer estados en todos los spans
                const estados = card.find('span').map(function() {
                    return $(this).text().toLowerCase();
                }).get().join(' ');
                
                // Verificar si el término de búsqueda coincide con algún campo
                const matchFound = nombre.includes(searchTerm) || 
                    id.includes(searchTerm) || 
                    precio.includes(searchTerm) || 
                    categoria.includes(searchTerm) || 
                    marca.includes(searchTerm) || 
                    stock.includes(searchTerm) ||
                    estados.includes(searchTerm);
                
                if (matchFound) {
                    card.show();
                    visibleCount++;
                } else {
                    card.hide();
                }
            });

            // Mostrar mensaje cuando no hay resultados
            const noResultsMsg = $('#mobileNoResults');
            
            if (visibleCount === 0 && searchTerm !== '') {
                if (noResultsMsg.length === 0) {
                    $('#mobileCards').append(`
                        <div id="mobileNoResults" class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                            No se encontraron productos que coincidan con la búsqueda
                        </div>
                    `);
                }
            } else {
                noResultsMsg.remove();
            }
        }

        // Debounce de búsqueda en móvil
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

<!-- Modal de Detalles del Producto -->
<div id="modalProducto" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Fondo oscuro -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" onclick="cerrarModalProducto()"></div>

        <!-- Contenedor del modal -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
            <!-- Header del modal -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white" id="modalProductoTitulo">
                        Detalles del Producto
                    </h3>
                    <button type="button" onclick="cerrarModalProducto()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenido del modal -->
            <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <!-- Loading -->
                <div id="modalProductoLoading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 dark:border-white"></div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Cargando información...</p>
                </div>

                <!-- Contenido -->
                <div id="modalProductoContenido" class="hidden">
                    <!-- Información Principal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Imágenes del Producto -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Imágenes del Producto</h4>
                            <div id="modalProductoImagenes" class="grid grid-cols-2 gap-3">
                                <!-- Las imágenes se cargarán aquí -->
                            </div>
                        </div>

                        <!-- Información Básica -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Información Básica</h4>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre:</span>
                                    <p id="modalProductoNombre" class="text-base font-semibold text-gray-900 dark:text-white"></p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción:</span>
                                    <p id="modalProductoDescripcion" class="text-sm text-gray-700 dark:text-gray-300"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Precio:</span>
                                        <p id="modalProductoPrecio" class="text-lg font-bold text-gray-900 dark:text-white"></p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU:</span>
                                        <p id="modalProductoSKU" class="text-sm text-gray-700 dark:text-gray-300"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categoría y Marca -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Categoría</h4>
                            <div id="modalProductoCategoria" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <!-- Información de categoría -->
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Marca</h4>
                            <div id="modalProductoMarca" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <!-- Información de marca -->
                            </div>
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Información de Stock</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Stock Total</span>
                                <p id="modalProductoStock" class="text-2xl font-bold text-blue-900 dark:text-blue-100"></p>
                            </div>
                            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">Disponible</span>
                                <p id="modalProductoStockDisponible" class="text-2xl font-bold text-green-900 dark:text-green-100"></p>
                            </div>
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Reservado</span>
                                <p id="modalProductoStockReservado" class="text-2xl font-bold text-yellow-900 dark:text-yellow-100"></p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Estado</span>
                                <p id="modalProductoEstado" class="text-lg font-semibold text-gray-900 dark:text-white"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Variantes -->
                    <div id="modalProductoVariantesSection" class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Variantes del Producto</h4>
                        <div id="modalProductoVariantes" class="space-y-4">
                            <!-- Las variantes se cargarán aquí -->
                        </div>
                    </div>

                    <!-- Especificaciones -->
                    <div id="modalProductoEspecificacionesSection" class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Especificaciones Técnicas</h4>
                        <div id="modalProductoEspecificaciones" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Las especificaciones se cargarán aquí -->
                        </div>
                    </div>

                    <!-- Información Adicional -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Código de Barras:</span>
                            <p id="modalProductoCodigoBarras" class="text-sm text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Costo Unitario:</span>
                            <p id="modalProductoCostoUnitario" class="text-sm text-gray-700 dark:text-gray-300"></p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Peso:</span>
                            <p id="modalProductoPeso" class="text-sm text-gray-700 dark:text-gray-300"></p>
                        </div>
                    </div>
                </div>

                <!-- Error -->
                <div id="modalProductoError" class="hidden text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400" id="modalProductoErrorMessage"></p>
                </div>
            </div>

            <!-- Footer del modal -->
            <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <button type="button" onclick="cerrarModalProducto()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    Cerrar
                </button>
                <a id="modalProductoEditar" href="#" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Editar Producto
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function abrirModalProducto(productoId) {
        const modal = document.getElementById('modalProducto');
        const loading = document.getElementById('modalProductoLoading');
        const contenido = document.getElementById('modalProductoContenido');
        const error = document.getElementById('modalProductoError');
        
        // Mostrar modal y loading
        modal.classList.remove('hidden');
        loading.classList.remove('hidden');
        contenido.classList.add('hidden');
        error.classList.add('hidden');
        
        // Cargar datos del producto
        fetch(`/productos/${productoId}/detalles`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarDatosProducto(data.producto);
                    loading.classList.add('hidden');
                    contenido.classList.remove('hidden');
                } else {
                    throw new Error(data.message || 'Error al cargar los datos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                document.getElementById('modalProductoErrorMessage').textContent = error.message || 'Error al cargar los datos del producto';
            });
    }

    function cargarDatosProducto(producto) {
        // Información básica
        document.getElementById('modalProductoTitulo').textContent = producto.nombre;
        document.getElementById('modalProductoNombre').textContent = producto.nombre;
        document.getElementById('modalProductoDescripcion').textContent = producto.descripcion || 'Sin descripción';
        document.getElementById('modalProductoPrecio').textContent = `$${parseFloat(producto.precio).toLocaleString('es-CO')}`;
        document.getElementById('modalProductoSKU').textContent = producto.sku || 'N/A';
        document.getElementById('modalProductoCodigoBarras').textContent = producto.codigo_barras || 'N/A';
        document.getElementById('modalProductoCostoUnitario').textContent = producto.costo_unitario ? `$${parseFloat(producto.costo_unitario).toLocaleString('es-CO')}` : 'N/A';
        document.getElementById('modalProductoPeso').textContent = producto.peso ? `${producto.peso} kg` : 'N/A';
        
        // Stock
        document.getElementById('modalProductoStock').textContent = producto.stock || 0;
        document.getElementById('modalProductoStockDisponible').textContent = producto.stock_disponible || 0;
        document.getElementById('modalProductoStockReservado').textContent = producto.stock_reservado || 0;
        document.getElementById('modalProductoEstado').textContent = producto.activo ? 'Activo' : 'Inactivo';
        
        // Categoría
        const categoriaDiv = document.getElementById('modalProductoCategoria');
        if (producto.categoria) {
            categoriaDiv.innerHTML = `
                <p class="font-semibold text-gray-900 dark:text-white">${producto.categoria.nombre}</p>
                ${producto.categoria.descripcion ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${producto.categoria.descripcion}</p>` : ''}
            `;
        } else {
            categoriaDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Sin categoría</p>';
        }
        
        // Marca
        const marcaDiv = document.getElementById('modalProductoMarca');
        if (producto.marca) {
            marcaDiv.innerHTML = `
                <p class="font-semibold text-gray-900 dark:text-white">${producto.marca.nombre}</p>
                ${producto.marca.descripcion ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${producto.marca.descripcion}</p>` : ''}
            `;
        } else {
            marcaDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Sin marca</p>';
        }
        
        // Imágenes del producto
        const imagenesDiv = document.getElementById('modalProductoImagenes');
        if (producto.imagenes && producto.imagenes.length > 0) {
            imagenesDiv.innerHTML = producto.imagenes.map(imagen => `
                <div class="relative group">
                    <img src="${imagen.ruta}" alt="${imagen.alt_text || producto.nombre}" 
                         class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                    ${imagen.principal ? '<span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">Principal</span>' : ''}
                </div>
            `).join('');
        } else {
            imagenesDiv.innerHTML = '<p class="text-gray-500 dark:text-gray-400 col-span-2">No hay imágenes disponibles</p>';
        }
        
        // Variantes
        const variantesSection = document.getElementById('modalProductoVariantesSection');
        const variantesDiv = document.getElementById('modalProductoVariantes');
        if (producto.variantes && producto.variantes.length > 0) {
            variantesSection.classList.remove('hidden');
            variantesDiv.innerHTML = producto.variantes.map(variante => `
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h5 class="font-semibold text-gray-900 dark:text-white">${variante.nombre}</h5>
                            ${variante.descripcion ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${variante.descripcion}</p>` : ''}
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded ${variante.disponible ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}">
                            ${variante.disponible ? 'Disponible' : 'No Disponible'}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-3">
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Precio Adicional:</span>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">$${parseFloat(variante.precio_adicional || 0).toLocaleString('es-CO')}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Stock:</span>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">${variante.stock || 0}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Disponible:</span>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">${variante.stock_disponible || 0}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Reservado:</span>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">${variante.stock_reservado || 0}</p>
                        </div>
                    </div>
                    ${variante.sku ? `<p class="text-xs text-gray-500 dark:text-gray-400">SKU: ${variante.sku}</p>` : ''}
                    ${variante.codigo_color ? `<p class="text-xs text-gray-500 dark:text-gray-400">Código Color: ${variante.codigo_color}</p>` : ''}
                    ${variante.imagenes && variante.imagenes.length > 0 ? `
                        <div class="mt-3">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 block">Imágenes:</span>
                            <div class="flex gap-2">
                                ${variante.imagenes.map(img => `
                                    <img src="${img.url}" alt="${img.alt_text || variante.nombre}" 
                                         class="w-16 h-16 object-cover rounded border border-gray-200 dark:border-gray-600">
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `).join('');
        } else {
            variantesSection.classList.add('hidden');
        }
        
        // Especificaciones
        const especificacionesSection = document.getElementById('modalProductoEspecificacionesSection');
        const especificacionesDiv = document.getElementById('modalProductoEspecificaciones');
        if (producto.especificaciones && producto.especificaciones.length > 0) {
            especificacionesSection.classList.remove('hidden');
            // Filtrar y ordenar especificaciones
            const especificacionesValidas = producto.especificaciones
                .filter(esp => esp.especificacion !== null)
                .sort((a, b) => {
                    const ordenA = a.especificacion.orden ?? 999;
                    const ordenB = b.especificacion.orden ?? 999;
                    return ordenA - ordenB;
                });
            
            if (especificacionesValidas.length > 0) {
                especificacionesDiv.innerHTML = especificacionesValidas.map(especificacion => {
                    const espec = especificacion.especificacion;
                    
                    let valor = especificacion.valor;
                    // Formatear el valor según el tipo
                    if (espec.unidad) {
                        valor = `${valor} ${espec.unidad}`;
                    }
                    
                    return `
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h5 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                        ${espec.etiqueta}
                                    </h5>
                                    <p class="text-base text-gray-700 dark:text-gray-300 font-medium">
                                        ${valor}
                                    </p>
                                    ${espec.descripcion ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${espec.descripcion}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                especificacionesSection.classList.add('hidden');
            }
        } else {
            especificacionesSection.classList.add('hidden');
        }
        
        // Link de editar
        document.getElementById('modalProductoEditar').href = `/productos/${producto.id}/edit`;
    }

    function cerrarModalProducto() {
        document.getElementById('modalProducto').classList.add('hidden');
    }

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarModalProducto();
        }
    });

    // Función para actualizar el stock en la tabla
    function actualizarStockTabla() {
        fetch('/productos/stock/actualizado')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.productos) {
                    data.productos.forEach(producto => {
                        const fila = document.querySelector(`tr[data-producto-id="${producto.id}"]`);
                        if (fila) {
                            // Actualizar stock total
                            const stockTotal = fila.querySelector(`[data-stock-total="${producto.id}"]`);
                            if (stockTotal) {
                                stockTotal.textContent = producto.stock;
                            }

                            // Actualizar stock disponible
                            const stockDisponible = fila.querySelector(`[data-stock-disponible="${producto.id}"]`);
                            if (stockDisponible) {
                                stockDisponible.textContent = producto.stock_disponible;
                                
                                // Actualizar clases según el stock disponible
                                stockDisponible.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ';
                                if (producto.stock_disponible > 10) {
                                    stockDisponible.className += 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                                } else if (producto.stock_disponible > 5) {
                                    stockDisponible.className += 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                                } else if (producto.stock_disponible > 0) {
                                    stockDisponible.className += 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                                } else {
                                    stockDisponible.className += 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
                                }
                            }

                            // Actualizar stock reservado
                            const stockReservado = fila.querySelector(`[data-stock-reservado="${producto.id}"]`);
                            const stockReservadoContainer = fila.querySelector(`[data-stock-reservado-container="${producto.id}"]`);
                            if (stockReservado && stockReservadoContainer) {
                                if (producto.stock_reservado > 0) {
                                    stockReservado.textContent = producto.stock_reservado;
                                    stockReservadoContainer.style.display = '';
                                } else {
                                    stockReservadoContainer.style.display = 'none';
                                }
                            }

                            // Actualizar indicador de estado
                            const indicador = fila.querySelector(`[data-stock-indicador="${producto.id}"]`);
                            if (indicador) {
                                let html = '';
                                if (producto.stock_disponible <= 0) {
                                    html = `
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs text-red-600 dark:text-red-400">Sin stock disponible</span>
                                        </div>
                                    `;
                                } else if (producto.stock_reservado > producto.stock * 0.5) {
                                    html = `
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-xs text-yellow-600 dark:text-yellow-400">Alto stock reservado</span>
                                        </div>
                                    `;
                                }
                                indicador.innerHTML = html;
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error al actualizar el stock:', error);
            });
    }

    // Actualizar el stock cada 30 segundos
    setInterval(actualizarStockTabla, 30000);

    // Actualizar el stock cuando la página gana el foco
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            actualizarStockTabla();
        }
    });
</script>
@endpush

@endsection
