@extends('layouts.app-new')

@section('title', 'Detalle de Pedido #' . $pedido->pedido_id . ' - 4GMovil')

@section('content')
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
               Pedido #{{ $pedido->pedido_id }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Detalle completo del pedido realizado el {{ $pedido->fecha_pedido instanceof \Carbon\Carbon ? $pedido->fecha_pedido->format('d/m/Y \a \l\a\s H:i') : \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y \a \l\a\s H:i') }}
            </p>
        </div>
        
        <a href="{{ route('admin.pedidos.index') }}" 
           class="inline-flex items-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l7.158 7.158a.75.75 0 11-1.06 1.06l-8.5-8.5a.75.75 0 010-1.06l8.5-8.5a.75.75 0 111.06 1.06L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
            </svg>
           Volver a Pedidos
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Estado del Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                       Estado del Pedido                    </h3>
                    
                    @php
                        $estadosFinales = ['cancelado', 'confirmado', 'entregado'];
                        $estadoActual = strtolower($pedido->estado->nombre ?? '');
                        $permiteCambio = !in_array($estadoActual, $estadosFinales);
                    @endphp
                    
                    @if($permiteCambio)
                        <form action="{{ route('admin.pedidos.updateEstado', $pedido->pedido_id) }}" method="POST" class="flex items-center space-x-3">
                            @csrf
                            @method('PUT')
                            <select name="estado_id" 
                                    class="rounded-md px-4 py-3 border-gray-300 dark:border-gray-600 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm text-gray-900 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                                @foreach(\App\Models\EstadoPedido::all() as $estado)
                                    <option value="{{ $estado->estado_id }}" 
                                        {{ $pedido->estado_id == $estado->estado_id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" 
                            class="inline-flex justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-teal-700 transform hover:scale-105 transition-all duration-200 ease-in-out focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                                Actualizar
                            </button>
                        </form>
                    @else
                        <div class="flex items-center space-x-3">
                            <select disabled 
                                    class="rounded-md px-4 py-3 border-gray-300 dark:border-gray-600 shadow-sm bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed">
                                <option selected>{{ ucfirst($estadoActual) }}</option>
                            </select>
                            <button disabled 
                                    class="inline-flex justify-center rounded-lg bg-gray-400 px-4 py-2.5 text-sm font-semibold text-white cursor-not-allowed">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                </svg>
                                No Modificable
                            </button>
                        </div>
                    @endif
                </div>
                
                @if(!$permiteCambio)
                    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                    Estado Final Alcanzado
                                </h4>
                                <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                                    Este pedido ya está en estado <strong>{{ ucfirst($estadoActual) }}</strong> y no puede ser modificado. 
                                    Los pedidos finalizados (confirmados, cancelados o entregados) no permiten cambios de estado.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="mt-4">
                    @php
                        $estadoClasses = 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-600/30';
                        switch(strtolower($pedido->estado->nombre)) {
                            case 'pendiente':
                                $estadoClasses = 'bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-600/30';
                                break;
                            case 'confirmado':
                                $estadoClasses = 'bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-600/30';
                                break;
                            case 'cancelado':
                                $estadoClasses = 'bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 ring-red-600/20 dark:ring-red-600/30';
                                break;
                            default:
                                $estadoClasses = 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-600/30';
                        }
                    @endphp
                    <span class="inline-flex items-center rounded-md {{ $estadoClasses }} px-3 py-1 text-sm font-medium ring-1 ring-inset">
                        {{ $pedido->estado->nombre }}
                    </span>
                </div>
            </div>

            <!-- Detalles del Pedido -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                   Productos del Pedido                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                   Producto                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                   Precio Unitario
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pedido->detalles as $detalle)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($detalle->producto && $detalle->producto->imagenes && $detalle->producto->imagenes->isNotEmpty())
                                                    <img class="h-10 w-10 rounded-lg object-cover" 
                                                         src="{{ asset('storage/' . $detalle->producto->imagenes[0]->ruta_imagen) }}" 
                                                         alt="{{ $detalle->producto->nombre_producto }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $detalle->producto->nombre_producto ?? 'Producto no encontrado' }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    SKU: {{ $detalle->producto->sku ?? 'No disponible' }}
                                                </div>
                                                @if($detalle->variante)
                                                    <div class="text-xs text-blue-600 dark:text-blue-400">
                                                        Variante: {{ $detalle->variante->nombre }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $detalle->cantidad ?? 0 }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        ${{ number_format($detalle->precio_unitario ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        ${{ number_format(($detalle->cantidad ?? 0) * ($detalle->precio_unitario ?? 0), 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            @if($pedido->detalles->count() == 0)
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                       No hay productos en este pedido
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                    Total del Pedido:
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-gray-900 dark:bg-gray-700 dark:text-gray-100">
                                        ${{ number_format($pedido->total, 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Información de Envío -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Dirección de Envío
                </h3>
                
                @if($pedido->direccion)
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->direccion->direccion_completa ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Referencias</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->direccion->referencias ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Ciudad</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $pedido->direccion->ciudad ?? 'No especificado' }}{{ $pedido->direccion->departamento ? ', ' . $pedido->direccion->departamento : '' }}
                            </p>
                        </div>
                        @if($pedido->direccion->codigo_postal)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Código Postal</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->direccion->codigo_postal }}</p>
                            </div>
                        @endif
                        @if($pedido->direccion->instrucciones)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Instrucciones adicionales</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->direccion->instrucciones }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay dirección de envío registrada</p>
                @endif
            </div>
        </div>

        <!-- Columna lateral -->
        <div class="space-y-6">
            <!-- Información del Cliente -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                   Información del Cliente
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->usuario->nombre_usuario ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->usuario->correo_electronico ?? 'No especificado' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->usuario->telefono ?? 'No especificado' }}</p>
                    </div>
                    @if($pedido->notas)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Notas del pedido</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pedido->notas }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                   Información de Pago
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Método de pago</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            @if($pedido->pago && $pedido->pago->metodoPago)
                                {{ $pedido->pago->metodoPago->nombre }}
                            @else
                               No especificado
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Estado del pago</label>
                        @if($pedido->pago)
                            @php
                                $estadoPagoClasses = 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-600/30';
                                switch(strtolower($pedido->pago->estado)) {
                                    case 'pendiente':
                                        $estadoPagoClasses = 'bg-yellow-50 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300 ring-yellow-600/20 dark:ring-yellow-600/30';
                                        break;
                                    case 'completado':
                                        $estadoPagoClasses = 'bg-green-50 dark:bg-green-900 text-green-700 dark:text-green-300 ring-green-600/20 dark:ring-green-600/30';
                                        break;
                                    case 'fallido':
                                        $estadoPagoClasses = 'bg-red-50 dark:bg-red-900 text-red-700 dark:text-red-300 ring-red-600/20 dark:ring-red-600/30';
                                        break;
                                    default:
                                        $estadoPagoClasses = 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 ring-gray-600/20 dark:ring-gray-600/30';
                                }
                            @endphp
                            <span class="inline-flex items-center rounded-md {{ $estadoPagoClasses }} px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                {{ ucfirst($pedido->pago->estado ?? 'No especificado') }}
                            </span>
                        @else
                            <span class="text-sm text-gray-500 dark:text-gray-400">Sin pago registrado</span>
                        @endif
                    </div>
                    @if($pedido->pago && $pedido->pago->fecha_pago)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha del pago</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $pedido->pago->fecha_pago instanceof \Carbon\Carbon ? $pedido->pago->fecha_pago->format('d/m/Y H:i') : \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Monto total</label>
                        <p class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">
                            ${{ number_format($pedido->total, 0, ',', '.') }}
                        </p>
                    </div>
                    @if($pedido->pago && $pedido->pago->referencia_externa)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Referencia externa</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-mono">
                                {{ $pedido->pago->referencia_externa }}
                            </p>
                        </div>
                    @endif
                </div>
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
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: '¡Error!',
        text: "{{ session('error') }}",
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
        background: document.documentElement.classList.contains('dark') ? '#374151' : '#fff',
        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
    });
</script>
@endif

@endsection 