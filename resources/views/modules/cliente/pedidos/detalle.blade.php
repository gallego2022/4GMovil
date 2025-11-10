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
                            <a href="{{ route('pedidos.factura', $pedido->pedido_id) }}" 
                                class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-file-pdf mr-2"></i>
                                Descargar Factura PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Calificación (solo si el pedido está confirmado y no ha sido calificado) -->
                @if(isset($puedeCalificar) && $puedeCalificar)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-star mr-2 text-yellow-500"></i>
                                Califica tu Pedido
                            </h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">
                                Tu opinión es muy importante para nosotros. Por favor, califica los productos de tu pedido.
                            </p>

                            @if(session('success'))
                                <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
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
                                <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form action="{{ route('pedidos.calificar', $pedido->pedido_id) }}" method="POST" id="formCalificar">
                                @csrf
                                <div class="space-y-6">
                                    @foreach($pedido->detalles as $index => $detalle)
                                        @php
                                            // Buscar reseña existente del usuario para este producto en este pedido específico
                                            $resenaExistente = $pedido->resenas->where('producto_id', $detalle->producto_id)->first();
                                        @endphp
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                            <div class="flex items-start space-x-4">
                                                <div class="flex-1">
                                                    <h3 class="font-medium text-gray-900 dark:text-white mb-2">
                                                        {{ $detalle->producto->nombre_producto }}
                                                    </h3>
                                                    
                                                    <!-- Calificación con estrellas -->
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Calificación
                                                        </label>
                                                        <div class="flex items-center space-x-2" data-rating-container data-product-index="{{ $index }}">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <input type="radio" 
                                                                       name="productos[{{ $index }}][calificacion]" 
                                                                       value="{{ $i }}" 
                                                                       id="rating_{{ $index }}_{{ $i }}"
                                                                       class="hidden rating-input"
                                                                       data-rating-value="{{ $i }}"
                                                                       {{ $resenaExistente && $resenaExistente->calificacion == $i ? 'checked' : '' }}
                                                                       required>
                                                                <label for="rating_{{ $index }}_{{ $i }}" 
                                                                       class="cursor-pointer text-2xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 transition-colors duration-200 rating-star"
                                                                       data-rating="{{ $i }}"
                                                                       data-product-index="{{ $index }}">
                                                                    <i class="fas fa-star"></i>
                                                                </label>
                                                            @endfor
                                                        </div>
                                                        <input type="hidden" name="productos[{{ $index }}][producto_id]" value="{{ $detalle->producto_id }}">
                                                    </div>

                                                    <!-- Comentario -->
                                                    <div>
                                                        <label for="comentario_{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                            Comentario (opcional)
                                                        </label>
                                                        <textarea 
                                                            name="productos[{{ $index }}][comentario]" 
                                                            id="comentario_{{ $index }}"
                                                            rows="3"
                                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                            placeholder="Escribe tu opinión sobre este producto...">{{ $resenaExistente ? $resenaExistente->comentario : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        <i class="fas fa-star mr-2"></i>
                                        Enviar Calificación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @elseif(isset($yaCalificado) && $yaCalificado)
                    <!-- Mensaje informativo cuando el pedido ya está completamente calificado -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                        <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                        Pedido Completamente Calificado
                                    </h2>
                                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                                        Has calificado todos los productos de este pedido. ¡Gracias por tu opinión!
                                    </p>
                                    
                                    <!-- Mostrar reseñas existentes -->
                                    @php
                                        $resenasPedido = $pedido->resenas;
                                    @endphp
                                    @if($resenasPedido->isNotEmpty())
                                        <div class="mt-4 space-y-4">
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                                                Tus Calificaciones
                                            </h3>
                                            @foreach($resenasPedido as $resena)
                                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                                                                {{ $resena->producto->nombre_producto }}
                                                            </h4>
                                                            <div class="flex items-center space-x-1 mb-2">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star {{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                                @endfor
                                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $resena->calificacion }}/5</span>
                                                            </div>
                                                            @if($resena->comentario)
                                                                <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $resena->comentario }}</p>
                                                            @endif
                                                        </div>
                                                        @if($resena->verificada)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                                Verificada
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        // Obtener todas las reseñas del pedido (con pedido_id)
                        $resenasPedido = $pedido->resenas;
                    @endphp
                    @if($resenasPedido->isNotEmpty())
                        <!-- Mostrar reseñas existentes -->
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white flex items-center">
                                <i class="fas fa-star mr-2 text-yellow-500"></i>
                                Tu Calificación
                            </h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-4 text-sm">
                                Puedes actualizar tus calificaciones usando el formulario de arriba.
                            </p>
                            <div class="space-y-4">
                                @foreach($resenasPedido as $resena)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900 dark:text-white mb-2">
                                                {{ $resena->producto->nombre_producto }}
                                            </h3>
                                            <div class="flex items-center space-x-1 mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                @endfor
                                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $resena->calificacion }}/5</span>
                                            </div>
                                            @if($resena->comentario)
                                                <p class="text-gray-700 dark:text-gray-300">{{ $resena->comentario }}</p>
                                            @endif
                                        </div>
                                        @if($resena->verificada)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Verificada
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de calificación con estrellas
    document.querySelectorAll('[data-rating-container]').forEach(container => {
        const stars = container.querySelectorAll('.rating-star');
        const inputs = container.querySelectorAll('.rating-input');
        
        // Función para resaltar estrellas
        function highlightStars(rating) {
            stars.forEach((star) => {
                const starRating = parseInt(star.dataset.rating);
                const starIcon = star.querySelector('i');
                if (starIcon) {
                    if (starRating <= rating) {
                        starIcon.classList.remove('text-gray-300', 'dark:text-gray-600');
                        starIcon.classList.add('text-yellow-400');
                        star.classList.remove('text-gray-300', 'dark:text-gray-600');
                        star.classList.add('text-yellow-400');
                    } else {
                        starIcon.classList.remove('text-yellow-400');
                        starIcon.classList.add('text-gray-300', 'dark:text-gray-600');
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300', 'dark:text-gray-600');
                    }
                }
            });
        }
        
        // Event listeners para cada estrella
        stars.forEach((star) => {
            // Hover para previsualizar
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                highlightStars(rating);
            });
            
            // Click para seleccionar
            star.addEventListener('click', function(e) {
                e.preventDefault();
                const rating = parseInt(this.dataset.rating);
                const input = container.querySelector(`input[data-rating-value="${rating}"]`);
                if (input) {
                    // Desmarcar todos los inputs del mismo grupo
                    inputs.forEach(inp => inp.checked = false);
                    // Marcar el input seleccionado
                    input.checked = true;
                    highlightStars(rating);
                }
            });
        });
        
        // Restaurar estado al salir del hover
        container.addEventListener('mouseleave', function() {
            const checkedInput = container.querySelector('.rating-input:checked');
            if (checkedInput) {
                const rating = parseInt(checkedInput.value);
                highlightStars(rating);
            } else {
                highlightStars(0);
            }
        });
        
        // Inicializar con la calificación seleccionada
        const checkedInput = container.querySelector('.rating-input:checked');
        if (checkedInput) {
            const rating = parseInt(checkedInput.value);
            highlightStars(rating);
        }
    });
    
    // Validación del formulario
    const form = document.getElementById('formCalificar');
    if (form) {
        form.addEventListener('submit', function(e) {
            const allRated = document.querySelectorAll('.rating-input:checked').length === {{ $pedido->detalles->count() }};
            if (!allRated) {
                e.preventDefault();
                alert('Por favor, califica todos los productos antes de enviar.');
                return false;
            }
        });
    }
});
</script>
@endpush

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