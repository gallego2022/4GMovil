@props([
    'producto' => null,
    'showRating' => true,
    'showStock' => true,
    'showFeatures' => true,
    'showShipping' => true,
    'class' => '',
])

@if ($producto)
    <div
        class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 cursor-pointer group transform hover:-translate-y-3 border border-gray-100 overflow-hidden relative {{ $class }}">
        <!-- Enlace para toda la tarjeta -->
        <a href="{{ route('productos.show', $producto['producto_id'] ?? $producto->producto_id) }}" class="block">
            <div class="relative overflow-hidden h-[200px] w-full bg-gradient-to-br from-gray-50 to-gray-100">
                @if (isset($producto['imagenes']) && !empty($producto['imagenes']) && isset($producto['imagenes'][0]))
                    <img src="{{ asset('storage/' . $producto['imagenes'][0]['ruta_imagen']) }}"
                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 p-4"
                        alt="{{ $producto['nombre_producto'] ?? $producto->nombre_producto }}">
                @elseif(isset($producto->imagenes) && $producto->imagenes->isNotEmpty())
                    <img src="{{ asset('storage/' . $producto->imagenes->first()->ruta_imagen) }}"
                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 p-4"
                        alt="{{ $producto->nombre_producto }}">
                @else
                    <img src="{{ asset('img/Logo_2.png') }}" class="w-full h-full object-contain p-4" alt="Sin imagen">
                @endif

                <!-- Badge de estado -->
                <div class="absolute top-4 left-4">
                    @php
                        $estado = $producto['estado'] ?? $producto->estado;
                    @endphp
                    <span
                        class="bg-gradient-to-r from-{{ $estado == 'nuevo' ? 'green' : 'yellow' }}-500 to-{{ $estado == 'nuevo' ? 'green' : 'yellow' }}-600 text-{{ $estado == 'nuevo' ? 'white' : 'gray-800' }} text-xs px-3 py-1 rounded-full font-bold shadow-lg backdrop-blur-sm">
                        {{ ucfirst($estado) }}
                    </span>
                </div>

                <!-- Overlay de hover -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                </div>

                <!-- Iconos de hover -->
                <div
                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    <div class="bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-lg">
                        <i class="fas fa-eye text-gray-700 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="p-4">
                <h3
                    class="font-bold text-xl mb-3 group-hover:text-blue-600 transition-colors duration-300 line-clamp-2 leading-tight">
                    {{ $producto['nombre_producto'] ?? $producto->nombre_producto }}
                </h3>

                @if ($showRating)
                    <!-- Rating Dinámico -->
                    <div class="flex items-center mb-4">
                        @php
                            // Obtener las reseñas del producto
                            $resenas = null;
                            if (is_array($producto)) {
                                // Si es un array, buscar las reseñas en el array
                                $resenas = $producto['resenas'] ?? collect();
                            } else {
                                // Si es un objeto, usar la relación
                                $resenas = $producto->resenas ?? collect();
                            }
                            
                            // Calcular el promedio de calificaciones
                            $promedioCalificacion = $resenas->count() > 0 ? $resenas->avg('calificacion') : 0;
                            $totalResenas = $resenas->count();
                        @endphp
                        
                        <div class="flex text-yellow-400 text-sm">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $promedioCalificacion)
                                    <i class="fas fa-star"></i>
                                @elseif ($i - 0.5 <= $promedioCalificacion)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        
                        @if ($totalResenas > 0)
                            <span class="text-gray-500 text-sm ml-2 font-medium">
                                ({{ $totalResenas }} {{ $totalResenas == 1 ? __('messages.products.review') : __('messages.products.reviews') }})
                            </span>
                        @else
                            <span class="text-gray-400 text-sm ml-2 font-medium">
                                ({{ __('messages.products.no_reviews') }})
                            </span>
                        @endif
                    </div>
                @endif

                @if ($showStock)
                    <!-- Información de Stock -->
                    <div class="mb-4">
                        @php
                            $productoObj = is_array($producto) ? (object) $producto : $producto;
                        @endphp
                        <x-stock-status :producto="$productoObj" />
                    </div>
                @endif

                <!-- Precio -->
                <div class="flex justify-between items-center mb-4">
                    <div>
                        @php
                            $precio = $producto['precio'] ?? $producto->precio;
                        @endphp
                        <span
                            class="font-black text-2xl text-blue-600">{{ \App\Helpers\CurrencyHelper::formatPrice($precio) }}</span>
                    </div>
                    @if ($showShipping)
                        <div class="text-right">
                            <span class="text-xs text-green-600 font-bold bg-green-100 px-2 py-1 rounded-full">
                                <i class="fas fa-truck mr-1"></i>{{ __('messages.product_show.free_shipping') }}
                            </span>
                        </div>
                    @endif
                </div>

                @if ($showFeatures)
                    <!-- Características -->
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <span><i class="fas fa-shield-alt mr-1"></i>{{ __('messages.product_show.warranty_period') }}</span>
                        <span><i class="fas fa-credit-card mr-1"></i>{{ __('messages.product_show.secure_payment') }}</span>
                        <span><i class="fas fa-undo mr-1"></i>{{ __('messages.product_show.return_description') }}</span>
                    </div>
                @endif
            </div>
        </a>

        <!-- Botón de agregar al carrito -->
        <div class="px-4 pb-4">
            @php
                $stockDisponible = $producto['stock_disponible'] ?? $producto->stock_disponible;
                $productoId = $producto['producto_id'] ?? $producto->producto_id;
                $nombreProducto = $producto['nombre_producto'] ?? $producto->nombre_producto;
                $precio = $producto['precio'] ?? $producto->precio;
                
                // Verificar si el producto tiene variantes
                $productoObj = is_array($producto) ? (object) $producto : $producto;
                $tieneVariantes = $productoObj->variantes && $productoObj->variantes->count() > 0;
            @endphp

            @if ($stockDisponible > 0)
                @if ($tieneVariantes)
                    <!-- Si tiene variantes, mostrar botón que abra modal de selección -->
                    <button type="button"
                        class="select-variant w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 group/btn"
                        data-producto-id="{{ $productoId }}" 
                        data-producto-nombre="{{ $nombreProducto }}" 
                        data-producto-precio="{{ $precio }}">
                        <i class="fas fa-palette mr-2 group-hover/btn:animate-bounce"></i>
                        {{ __('messages.products.select_variant') }}
                    </button>
                @else
                    <!-- Si no tiene variantes, agregar directamente al carrito -->
                    <button type="button"
                        class="add-to-cart w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 group/btn"
                        data-id="{{ $productoId }}" 
                        data-name="{{ $nombreProducto }}" 
                        data-price="{{ $precio }}">
                        <i class="fas fa-shopping-cart mr-2 group-hover/btn:animate-bounce"></i>
                        {{ __('messages.products.add_to_cart') }}
                    </button>
                @endif
            @else
                <button type="button"
                    class="w-full bg-gray-400 text-white py-4 rounded-xl cursor-not-allowed font-bold" disabled>
                    <i class="fas fa-times mr-2"></i>
                    {{ __('messages.products.out_of_stock') }}
                </button>
            @endif
        </div>
    </div>
@endif
