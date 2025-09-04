@extends('layouts.landing')

@section('title', 'Detalle de Pedido - 4GMovil')
@section('meta-description', 'Detalle completo de tu pedido en 4GMovil')

@section('content')
<div class="bg-gray-100 dark:bg-gray-800 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Botón de volver -->
        <div class="mb-6">
            <a href="{{ route('pedidos.historial') }}" class="inline-flex items-center text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al historial
            </a>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-lg overflow-hidden">
            <!-- Encabezado del pedido -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Pedido #{{ $pedido->pedido_id }}</h1>
                        <p class="text-blue-100">
                            @if($pedido->fecha_pedido instanceof \DateTime)
                                Realizado el {{ $pedido->fecha_pedido->format('d/m/Y') }} a las {{ $pedido->fecha_pedido->format('H:i') }}
                            @else
                                Realizado el {{ date('d/m/Y', strtotime($pedido->fecha_pedido)) }} a las {{ date('H:i', strtotime($pedido->fecha_pedido)) }}
                            @endif
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @switch($pedido->estado->nombre)
                            @case('Pendiente')
                                bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                @break
                            @case('Confirmado')
                                bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                @break
                            @case('Cancelado')
                                bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                @break
                            @default
                                bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        @endswitch">
                        {{ $pedido->estado->nombre }}
                    </span>
                </div>
            </div>

            <!-- Contenido del pedido -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Productos -->
                    <div>
                        <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Productos</h2>
                        <div class="space-y-4">
                            @foreach($pedido->detalles as $detalle)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                               
                                    <div class="flex-grow">
                                        <h3 class="font-medium text-gray-900 dark:text-white">{{ $detalle->producto->nombre_producto }}</h3>
                                        
                                        <!-- Información de la variante -->
                                        @if($detalle->variante)
                                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">Variante:</span>
                                                <span class="ml-1">{{ $detalle->variante->nombre }}</span>
                                            </div>
                                        @endif
                                        
                                        <!-- Especificaciones del producto -->
                                        @if($detalle->producto->especificaciones && $detalle->producto->especificaciones->isNotEmpty())
                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                                @foreach($detalle->producto->especificaciones as $especificacion)
                                                    <span class="inline-block mr-2 mb-1">
                                                        <span class="font-medium">{{ $especificacion->especificacionCategoria->etiqueta }}:</span>
                                                        <span class="ml-1">{{ $especificacion->valor }}</span>
                                                        @if($especificacion->especificacionCategoria->unidad)
                                                            <span class="text-gray-400">{{ $especificacion->especificacionCategoria->unidad }}</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <div class="grid grid-cols-2 gap-2 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            <p>Cantidad: {{ $detalle->cantidad }}</p>
                                            <p>Precio: ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</p>
                                            <p class="col-span-2 font-medium text-primary dark:text-blue-400">
                                                Subtotal: ${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Resumen de costos -->
                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Envío</span>
                                    <span class="font-medium text-gray-900 dark:text-white">Gratis</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                                    <span class="text-gray-900 dark:text-white">Total</span>
                                    <span class="text-primary dark:text-blue-400">${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="space-y-6">
                        <!-- Dirección de envío -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Dirección de envío</h2>
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $pedido->direccion->tipo_direccion === 'casa' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 
                                        ($pedido->direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' : 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200') 
                                    }}">
                                        {{ ucfirst($pedido->direccion->tipo_direccion) }}
                                    </span>
                                </div>
                                <p class="text-gray-900 dark:text-white">{{ $pedido->direccion->direccion_completa }}</p>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $pedido->direccion->referencias ?? 'Sin referencias' }}</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $pedido->direccion->ciudad }}{{ $pedido->direccion->departamento ? ', ' . $pedido->direccion->departamento : '' }}</p>
                                <p class="text-gray-600 dark:text-gray-400">Código Postal: {{ $pedido->direccion->codigo_postal }}</p>
                                <p class="text-gray-600 dark:text-gray-400">Teléfono: {{ $pedido->direccion->telefono }}</p>
                                @if($pedido->direccion->referencias)
                                    <p class="text-gray-600 dark:text-gray-400 mt-2">
                                        <span class="font-medium text-gray-900 dark:text-white">Referencias:</span><br>
                                        {{ $pedido->direccion->referencias }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Información de pago -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Información de pago</h2>
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                @if($pedido->pago && $pedido->pago->metodoPago)
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $pedido->pago->metodoPago->nombre }}
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400 mt-1">Estado: {{ ucfirst($pedido->pago->estado ?? 'pendiente') }}</p>
                                    @if($pedido->pago->fecha_pago)
                                        <p class="text-gray-600 dark:text-gray-400">Fecha: 
                                            @if($pedido->pago->fecha_pago instanceof \Carbon\Carbon)
                                                {{ $pedido->pago->fecha_pago->format('d/m/Y H:i') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($pedido->pago->fecha_pago)->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                    @endif
                                    @if($pedido->pago->referencia_externa)
                                        <p class="text-gray-600 dark:text-gray-400">Referencia: {{ $pedido->pago->referencia_externa }}</p>
                                    @endif
                                @else
                                    <p class="text-gray-600 dark:text-gray-400">No hay información de pago disponible</p>
                                @endif
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button onclick="window.print()" 
                                class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-print mr-2"></i>
                                Imprimir pedido
                            </button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Estilos para modo oscuro */
.dark .bg-primary {
    background-color: #3b82f6;
}

.dark .bg-primary:hover {
    background-color: #2563eb;
}

.dark .text-primary {
    color: #3b82f6;
}

/* Transiciones suaves para todos los elementos */
.dark * {
    transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out, border-color 0.2s ease-in-out;
}

@media print {
    .container {
        max-width: 100% !important;
    }
    .shadow-md {
        box-shadow: none !important;
    }
    .bg-primary, .hover\:bg-blue-700 {
        background-color: #000 !important;
    }
    .text-primary {
        color: #000 !important;
    }
    button, a {
        display: none !important;
    }
    
    /* Asegurar que el texto sea legible en impresión */
    .dark * {
        color: #000 !important;
        background-color: #fff !important;
    }
}
</style>
@endpush
@endsection 