@props(['producto'])

@php
    $stockDisponible = $producto->stock_disponible;
    $stockTotal = $producto->stock;
    $porcentajeDisponible = $stockTotal > 0 ? ($stockDisponible / $stockTotal) * 100 : 0;
@endphp

<div class="stock-status flex items-center space-x-2" 
     data-product-id="{{ $producto->producto_id ?? $producto->id }}"
     data-original-stock="{{ $stockDisponible }}"
     data-total-stock="{{ $stockTotal }}">
    
    <!-- Indicador visual -->
    <div class="flex items-center space-x-1">
        @if($stockDisponible > 10)
            <!-- Stock alto -->
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse stock-dot"></div>
            <span class="text-xs text-green-600 font-medium stock-text">{{ $stockDisponible }} disponibles</span>
        @elseif($stockDisponible > 5)
            <!-- Stock medio -->
            <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse stock-dot"></div>
            <span class="text-xs text-yellow-600 font-medium stock-text">{{ $stockDisponible }} disponibles</span>
        @elseif($stockDisponible > 0)
            <!-- Stock bajo -->
            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse stock-dot"></div>
            <span class="text-xs text-red-600 font-medium stock-text">Solo {{ $stockDisponible }} disponibles</span>
        @else
            <!-- Sin stock -->
            <div class="w-3 h-3 bg-gray-400 rounded-full stock-dot"></div>
            <span class="text-xs text-gray-500 font-medium stock-text">Agotado</span>
        @endif
    </div>
    
    <!-- Barra de progreso sutil -->
    @if($stockTotal > 0)
        <div class="flex-1 max-w-16">
            <div class="w-full bg-gray-200 rounded-full h-1">
                <div class="h-1 rounded-full transition-all duration-300 stock-progress
                    {{ $porcentajeDisponible > 50 ? 'bg-green-500' : ($porcentajeDisponible > 20 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                     style="width: {{ $porcentajeDisponible }}%"></div>
            </div>
        </div>
    @endif
</div> 