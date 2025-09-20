@extends('layouts.landing')

@section('title', 'Pedido Confirmado')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header de éxito -->
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">¡Pedido Confirmado!</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">Tu pedido ha sido procesado exitosamente</p>
        </div>

        <!-- Información del pedido -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Detalles del Pedido</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Número de Pedido</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">#{{ $pedido->pedido_id }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha del Pedido</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">${{ number_format($pedido->total, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @switch($pedido->estado->nombre)
                            @case('Pendiente')
                                bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200
                                @break
                            @case('Confirmado')
                                bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                                @break
                            @case('Cancelado')
                                bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200
                                @break
                            @default
                                bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                        @endswitch">
                        {{ $pedido->estado->nombre}}
                    </span>
                </div>
            </div>

            @if($pedido->notas)
            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notas del Pedido</p>
                <p class="text-gray-900 dark:text-white">{{ $pedido->notas }}</p>
            </div>
            @endif
        </div>

        <!-- Productos del pedido -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Productos del Pedido</h3>
            
            @php
                $detalles = \App\Models\DetallePedido::where('pedido_id', $pedido->pedido_id)->get();
            @endphp
            
            @if($detalles->count() > 0)
                <div class="space-y-4">
                    @foreach($detalles as $detalle)
                        @php
                            $producto = \App\Models\Producto::find($detalle->producto_id);
                            $variante = $detalle->variante_id ? \App\Models\VarianteProducto::find($detalle->variante_id) : null;
                        @endphp
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($producto->imagen_principal)
                                        <img src="{{ asset('storage/' . $producto->imagen_principal) }}" 
                                             alt="{{ $producto->nombre_producto }}" 
                                             class="h-16 w-16 rounded-lg object-cover">
                                    @else
                                        <div class="h-16 w-16 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $producto->nombre_producto }}</h4>
                                    @if($variante)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $variante->nombre }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Cantidad: {{ $detalle->cantidad }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($detalle->subtotal, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${{ number_format($detalle->precio_unitario, 0, ',', '.') }} c/u</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No se encontraron detalles del pedido</p>
            @endif
        </div>

        <!-- Información de envío -->
        @if($pedido->direccion)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dirección de Envío</h3>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $pedido->direccion->nombre_destinatario }}</p>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $pedido->direccion->calle }} {{ $pedido->direccion->numero }}
                        @if($pedido->direccion->piso)
                            , Piso {{ $pedido->direccion->piso }}
                        @endif
                        @if($pedido->direccion->departamento)
                            , Depto {{ $pedido->direccion->departamento }}
                        @endif
                    </p>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ $pedido->direccion->ciudad }}, {{ $pedido->direccion->provincia }}
                        @if($pedido->direccion->codigo_postal)
                            , CP: {{ $pedido->direccion->codigo_postal }}
                        @endif
                    </p>
                    <p class="text-gray-600 dark:text-gray-400">{{ $pedido->direccion->pais }}</p>
                    @if($pedido->direccion->referencias)
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                            <strong class="text-gray-700 dark:text-gray-300">Referencias:</strong> {{ $pedido->direccion->referencias }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Acciones -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('pedidos.show', $pedido->pedido_id) }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Ver Detalle del Pedido
            </a>
            
            <a href="{{ route('landing') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Volver al Inicio
            </a>
        </div>

        <!-- Información adicional -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Recibirás un email de confirmación con los detalles de tu pedido.
                <br>
                Si tienes alguna pregunta, no dudes en contactarnos.
            </p>
        </div>
    </div>
</div>

<!-- Script para limpiar el carrito del localStorage -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🛒 Limpiando carrito del localStorage...');
    
    // Limpiar el carrito del localStorage
    localStorage.removeItem('cart');
    
    // También limpiar cualquier variable global del carrito si existe
    if (typeof window.cart !== 'undefined') {
        window.cart = [];
    }
    
    console.log('✅ Carrito limpiado del localStorage');
    
    // Actualizar el contador del carrito en la interfaz si existe
    const cartCount = document.getElementById('cart-count');
    const cartCountMobile = document.getElementById('cart-count-mobile');
    
    if (cartCount) {
        cartCount.textContent = '0';
    }
    
    if (cartCountMobile) {
        cartCountMobile.textContent = '0';
    }
    
    console.log('✅ Contadores del carrito actualizados');
});
</script>
@endsection 