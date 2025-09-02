<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'producto' => null,
    'showRating' => true,
    'showStock' => true,
    'showFeatures' => true,
    'showShipping' => true,
    'class' => '',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'producto' => null,
    'showRating' => true,
    'showStock' => true,
    'showFeatures' => true,
    'showShipping' => true,
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php if($producto): ?>
    <div
        class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 cursor-pointer group transform hover:-translate-y-3 border border-gray-100 overflow-hidden relative <?php echo e($class); ?>">
        <!-- Enlace para toda la tarjeta -->
        <a href="<?php echo e(route('productos.show', $producto['producto_id'] ?? $producto->producto_id)); ?>" class="block">
            <div class="relative overflow-hidden h-[200px] w-full bg-gradient-to-br from-gray-50 to-gray-100">
                <?php if(isset($producto['imagenes']) && !empty($producto['imagenes']) && isset($producto['imagenes'][0])): ?>
                    <img src="<?php echo e(asset('storage/' . $producto['imagenes'][0]['ruta_imagen'])); ?>"
                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 p-4"
                        alt="<?php echo e($producto['nombre_producto'] ?? $producto->nombre_producto); ?>">
                <?php elseif(isset($producto->imagenes) && $producto->imagenes->isNotEmpty()): ?>
                    <img src="<?php echo e(asset('storage/' . $producto->imagenes->first()->ruta_imagen)); ?>"
                        class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 p-4"
                        alt="<?php echo e($producto->nombre_producto); ?>">
                <?php else: ?>
                    <img src="<?php echo e(asset('img/Logo_2.png')); ?>" class="w-full h-full object-contain p-4" alt="Sin imagen">
                <?php endif; ?>

                <!-- Badge de estado -->
                <div class="absolute top-4 left-4">
                    <?php
                        $estado = $producto['estado'] ?? $producto->estado;
                    ?>
                    <span
                        class="bg-gradient-to-r from-<?php echo e($estado == 'nuevo' ? 'green' : 'yellow'); ?>-500 to-<?php echo e($estado == 'nuevo' ? 'green' : 'yellow'); ?>-600 text-<?php echo e($estado == 'nuevo' ? 'white' : 'gray-800'); ?> text-xs px-3 py-1 rounded-full font-bold shadow-lg backdrop-blur-sm">
                        <?php echo e(ucfirst($estado)); ?>

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
                    <?php echo e($producto['nombre_producto'] ?? $producto->nombre_producto); ?>

                </h3>

                <?php if($showRating): ?>
                    <!-- Rating Dinámico -->
                    <div class="flex items-center mb-4">
                        <?php
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
                        ?>
                        
                        <div class="flex text-yellow-400 text-sm">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $promedioCalificacion): ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif($i - 0.5 <= $promedioCalificacion): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        
                        <?php if($totalResenas > 0): ?>
                            <span class="text-gray-500 text-sm ml-2 font-medium">
                                (<?php echo e($totalResenas); ?> <?php echo e($totalResenas == 1 ? 'reseña' : 'reseñas'); ?>)
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm ml-2 font-medium">
                                (Sin reseñas)
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if($showStock): ?>
                    <!-- Información de Stock -->
                    <div class="mb-4">
                        <?php
                            $productoObj = is_array($producto) ? (object) $producto : $producto;
                        ?>
                        <?php if (isset($component)) { $__componentOriginal833a873e62731dcdf0549c603145beca = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal833a873e62731dcdf0549c603145beca = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stock-status','data' => ['producto' => $productoObj]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stock-status'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['producto' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($productoObj)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal833a873e62731dcdf0549c603145beca)): ?>
<?php $attributes = $__attributesOriginal833a873e62731dcdf0549c603145beca; ?>
<?php unset($__attributesOriginal833a873e62731dcdf0549c603145beca); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal833a873e62731dcdf0549c603145beca)): ?>
<?php $component = $__componentOriginal833a873e62731dcdf0549c603145beca; ?>
<?php unset($__componentOriginal833a873e62731dcdf0549c603145beca); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Precio -->
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <?php
                            $precio = $producto['precio'] ?? $producto->precio;
                        ?>
                        <span
                            class="font-black text-2xl text-blue-600">$<?php echo e(number_format($precio, 0, ',', '.')); ?></span>
                    </div>
                    <?php if($showShipping): ?>
                        <div class="text-right">
                            <span class="text-xs text-green-600 font-bold bg-green-100 px-2 py-1 rounded-full">
                                <i class="fas fa-truck mr-1"></i>Envío gratis
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if($showFeatures): ?>
                    <!-- Características -->
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <span><i class="fas fa-shield-alt mr-1"></i>Garantía 1 año</span>
                        <span><i class="fas fa-credit-card mr-1"></i>Pago seguro</span>
                        <span><i class="fas fa-undo mr-1"></i>30 días devolución</span>
                    </div>
                <?php endif; ?>
            </div>
        </a>

        <!-- Botón de agregar al carrito -->
        <div class="px-4 pb-4">
            <?php
                $stockDisponible = $producto['stock_disponible'] ?? $producto->stock_disponible;
                $productoId = $producto['producto_id'] ?? $producto->producto_id;
                $nombreProducto = $producto['nombre_producto'] ?? $producto->nombre_producto;
                $precio = $producto['precio'] ?? $producto->precio;
                
                // Verificar si el producto tiene variantes
                $productoObj = is_array($producto) ? (object) $producto : $producto;
                $tieneVariantes = $productoObj->variantes && $productoObj->variantes->count() > 0;
            ?>

            <?php if($stockDisponible > 0): ?>
                <?php if($tieneVariantes): ?>
                    <!-- Si tiene variantes, mostrar botón que abra modal de selección -->
                    <button type="button"
                        class="select-variant w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 group/btn"
                        data-producto-id="<?php echo e($productoId); ?>" 
                        data-producto-nombre="<?php echo e($nombreProducto); ?>" 
                        data-producto-precio="<?php echo e($precio); ?>">
                        <i class="fas fa-palette mr-2 group-hover/btn:animate-bounce"></i>
                        Seleccionar Variante
                    </button>
                <?php else: ?>
                    <!-- Si no tiene variantes, agregar directamente al carrito -->
                    <button type="button"
                        class="add-to-cart w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-1 group/btn"
                        data-id="<?php echo e($productoId); ?>" 
                        data-name="<?php echo e($nombreProducto); ?>" 
                        data-price="<?php echo e($precio); ?>">
                        <i class="fas fa-shopping-cart mr-2 group-hover/btn:animate-bounce"></i>
                        Agregar al carrito
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <button type="button"
                    class="w-full bg-gray-400 text-white py-4 rounded-xl cursor-not-allowed font-bold" disabled>
                    <i class="fas fa-times mr-2"></i>
                    Sin stock
                </button>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\components\product-card.blade.php ENDPATH**/ ?>