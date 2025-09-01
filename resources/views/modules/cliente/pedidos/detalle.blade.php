@extends('layouts.landing')

@section('title', 'Detalle de Pedido - 4GMovil')
@section('meta-description', 'Detalle completo de tu pedido en 4GMovil')

@section('content')
<div class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Botón de volver -->
        <div class="mb-6">
            <a href="{{ route('pedidos.historial') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al historial
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
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
                                bg-yellow-100 text-yellow-800
                                @break
                            @case('Confirmado')
                                bg-green-100 text-green-800
                                @break
                            @case('Cancelado')
                                bg-red-100 text-red-800
                                @break
                            @default
                                bg-gray-100 text-gray-800
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
                        <h2 class="text-lg font-semibold mb-4">Productos</h2>
                        <div class="space-y-4">
                            @foreach($pedido->detalles as $detalle)
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    @if($detalle->producto->imagenes->isNotEmpty())
                                        <img src="{{ asset('storage/' . $detalle->producto->imagenes[0]->ruta_imagen) }}" 
                                             alt="{{ $detalle->producto->nombre_producto }}"
                                             class="w-16 h-16 object-cover rounded">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow">
                                        <h3 class="font-medium">{{ $detalle->producto->nombre_producto }}</h3>
                                        <div class="grid grid-cols-2 gap-2 mt-2 text-sm text-gray-600">
                                            <p>Cantidad: {{ $detalle->cantidad }}</p>
                                            <p>Precio: ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</p>
                                            <p class="col-span-2 font-medium text-primary">
                                                Subtotal: ${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Resumen de costos -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-medium">${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Envío</span>
                                    <span class="font-medium">Gratis</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                    <span>Total</span>
                                    <span class="text-primary">${{ number_format($pedido->detalles->sum(function($detalle) { return $detalle->cantidad * $detalle->precio_unitario; }), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="space-y-6">
                        <!-- Dirección de envío -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4">Dirección de envío</h2>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $pedido->direccion->tipo_direccion === 'casa' ? 'bg-green-100 text-green-800' : 
                                        ($pedido->direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') 
                                    }}">
                                        {{ ucfirst($pedido->direccion->tipo_direccion) }}
                                    </span>
                                </div>
                                <p class="text-gray-900">{{ $pedido->direccion->direccion }}</p>
                                <p class="text-gray-600 mt-1">{{ $pedido->direccion->barrio }}</p>
                                <p class="text-gray-600">{{ $pedido->direccion->ciudad }}, {{ $pedido->direccion->departamento }}</p>
                                <p class="text-gray-600">Código Postal: {{ $pedido->direccion->codigo_postal }}</p>
                                <p class="text-gray-600">Teléfono: {{ $pedido->direccion->telefono }}</p>
                                @if($pedido->direccion->instrucciones)
                                    <p class="text-gray-600 mt-2">
                                        <span class="font-medium">Instrucciones:</span><br>
                                        {{ $pedido->direccion->instrucciones }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Información de pago -->
                        <div>
                            <h2 class="text-lg font-semibold mb-4">Información de pago</h2>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                @if($pedido->pago)
                                    <p class="font-medium">
                                        {{ \App\Helpers\PaymentHelper::getPaymentMethodName($pedido) }}
                                    </p>
                                    <p class="text-gray-600 mt-1">Estado: {{ ucfirst($pedido->pago->estado ?? 'pendiente') }}</p>
                                    <p class="text-gray-600">Fecha: 
                                        @if($pedido->pago->fecha_pago instanceof \Carbon\Carbon)
                                            {{ $pedido->pago->fecha_pago->format('d/m/Y H:i') }}
                                        @else
                                            {{ date('d/m/Y H:i', strtotime($pedido->pago->fecha_pago)) }}
                                        @endif
                                    </p>
                                @else
                                    <p class="text-gray-600">No hay información de pago disponible</p>
                                @endif
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button onclick="window.print()" 
                                class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <i class="fas fa-print mr-2"></i>
                                Imprimir pedido
                            </button>
                            <a href="#" 
                                class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary hover:bg-blue-700 transition-colors">
                                <i class="fas fa-headset mr-2"></i>
                                Contactar soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
}
</style>
@endpush
@endsection 