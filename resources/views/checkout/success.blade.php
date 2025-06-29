@extends('layouts.landing')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        @if(session('mensaje'))
            <div class="mb-4 rounded-md p-4 {{ session('tipo', 'success') === 'success' ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if(session('tipo', 'success') === 'success')
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('mensaje') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Encabezado -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">¡Gracias por tu compra!</h1>
                <p class="text-gray-600">Tu pedido #{{ $pedido->pedido_id }} ha sido confirmado</p>
                <p class="text-gray-600">Estado: {{ $pedido->estado->nombre ?? 'Pendiente' }}</p>
            </div>

            <!-- Detalles del pedido -->
            <div class="border-t border-b py-4 mb-6">
                <h2 class="text-lg font-semibold mb-4">Detalles del pedido</h2>
                
                <!-- Productos -->
                <div class="space-y-4 mb-6">
                    @foreach($pedido->detalles as $detalle)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-grow">
                                <p class="font-medium text-lg mb-2">{{ $detalle->producto->nombre_producto }}</p>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600">Cantidad:</p>
                                        <p class="font-medium">{{ $detalle->cantidad }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600">Precio unitario:</p>
                                        <p class="font-medium">${{ number_format($detalle->precio_unitario, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-gray-600">Subtotal:</p>
                                        <p class="font-medium text-primary">${{ number_format($detalle->cantidad * $detalle->precio_unitario, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Resumen de costos -->
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Envío</span>
                        <span class="font-medium">Gratis</span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total</span>
                        <span class="text-primary">${{ number_format($pedido->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Información de envío -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Información de envío</h2>
                <div class="bg-gray-50 rounded p-4">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pedido->direccion->tipo_direccion === 'casa' ? 'bg-green-100 text-green-800' : ($pedido->direccion->tipo_direccion === 'apartamento' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ ucfirst($pedido->direccion->tipo_direccion) }}
                        </span>
                    </div>
                    <p class="text-gray-600">{{ $pedido->direccion->direccion }}</p>
                    <p class="text-gray-600">{{ $pedido->direccion->barrio }}</p>
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
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Información de pago</h2>
                <div class="bg-gray-50 rounded p-4">
                    <p class="font-medium">Método de pago: {{ $pedido->pago->metodoPago->nombre_metodo }}</p>
                    <p class="text-gray-600">Estado: {{ ucfirst($pedido->pago->estado ?? 'pendiente') }}</p>
                    <p class="text-gray-600">Fecha: {{ $pedido->pago->fecha_pago instanceof \DateTime ? $pedido->pago->fecha_pago->format('d/m/Y H:i') : date('d/m/Y H:i', strtotime($pedido->pago->fecha_pago)) }}</p>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('landing') }}" 
                    class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-primary hover:bg-blue-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Volver al inicio
                </a>
                <button onclick="window.print()" 
                    class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-print mr-2"></i>
                    Imprimir pedido
                </button>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Limpiar el carrito del localStorage
        localStorage.removeItem('cart');
        
        // Actualizar el contador del carrito en el header
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = '0';
        }
    });
</script>
@endpush
@endsection 