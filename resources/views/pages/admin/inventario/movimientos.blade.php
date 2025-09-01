@extends('layouts.app-new')

@section('title', 'Movimientos de Inventario - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Movimientos de Inventario</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Historial completo de entradas, salidas y ajustes de stock</p>
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

    <!-- Filtros -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filtros</h3>
        <form method="GET" action="{{ route('admin.inventario.movimientos') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="producto_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Producto</label>
                <select id="producto_id" name="producto_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Todos los productos</option>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->producto_id }}" {{ $productoId == $producto->producto_id ? 'selected' : '' }}>
                            {{ $producto->nombre_producto }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" 
                       value="{{ $fechaInicio ? $fechaInicio->format('Y-m-d') : '' }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fecha Fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin" 
                       value="{{ $fechaFin ? $fechaFin->format('Y-m-d') : '' }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Resumen de movimientos -->
    @if(isset($resumen))
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen del Período</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-800 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Entradas</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['total_entradas'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-800 rounded-full">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Salidas</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['total_salidas'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-800 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ajustes</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['total_ajustes'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Devoluciones</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $resumen['total_devoluciones'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla de movimientos -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Movimientos</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $movimientos->count() }} movimientos encontrados</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Anterior</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock Nuevo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuario</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($movimientos as $movimiento)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($movimiento->producto->imagenes->isNotEmpty())
                                    <img src="{{ asset('storage/' . $movimiento->producto->imagenes[0]->ruta_imagen) }}" 
                                         class="w-8 h-8 rounded-md object-cover" 
                                         alt="{{ $movimiento->producto->nombre_producto }}">
                                @else
                                    <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $movimiento->producto->nombre_producto }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $movimiento->producto->producto_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $tipoClasses = match($movimiento->tipo_movimiento) {
                                    'entrada' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'salida' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'ajuste' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'devolucion' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tipoClasses }}">
                                {{ $movimiento->tipo_movimiento_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->cantidad }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->stock_anterior }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->stock_nuevo }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <div class="max-w-xs truncate" title="{{ $movimiento->motivo }}">
                                {{ $movimiento->motivo }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $movimiento->usuario->nombre_usuario ?? 'Sistema' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No se encontraron movimientos para el período seleccionado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Removida la paginación ya que $movimientos es una Collection -->
    </div>
</div>
@endsection 