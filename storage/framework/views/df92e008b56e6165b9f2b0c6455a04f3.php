<?php $__env->startSection('title', 'Dashboard de Inventario - 4GMovil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard de Inventario</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Gestión y control del inventario de productos</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.inventario.alertas')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Ver Alertas
                </a>
                <a href="<?php echo e(route('admin.inventario.movimientos')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Movimientos
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Valor total del inventario -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor Total</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">$<?php echo e(number_format($valorTotal, 0, ',', '.')); ?></p>
                </div>
            </div>
        </div>

        <!-- Productos con stock crítico -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Crítico</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($alertas['stock_critico']); ?></p>
                </div>
            </div>
        </div>

        <!-- Productos con stock bajo -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Bajo</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($alertas['stock_bajo']); ?></p>
                </div>
            </div>
        </div>

        <!-- Productos sin stock -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 01-.707-.293l-2.414-2.414a1 1 0 00-.707-.293H6.586a1 1 0 00-.707.293L3.707 12.707A1 1 0 004.586 13H2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin Stock</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($alertas['sin_stock']); ?></p>
                </div>
            </div>
        </div>

        <!-- Stock Reservado -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Reservado</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($alertas['stock_reservado_alto'] ?? 0); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con alertas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productos con stock crítico -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Crítico</h3>
                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                    <?php echo e($productosStockCritico->count()); ?> productos
                </span>
            </div>
            
            <?php if($productosStockCritico->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $productosStockCritico->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <?php if($producto->imagenes->isNotEmpty()): ?>
                                    <img src="<?php echo e(asset('storage/' . $producto->imagenes[0]->ruta_imagen)); ?>" 
                                         class="w-10 h-10 rounded-md object-cover" 
                                         alt="<?php echo e($producto->nombre_producto); ?>">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($producto->nombre_producto); ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: <?php echo e($producto->producto_id); ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php if (isset($component)) { $__componentOriginal1cb78344edecc901a4e11979204e6a33 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1cb78344edecc901a4e11979204e6a33 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stock-indicator','data' => ['producto' => $producto]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stock-indicator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['producto' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($producto)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1cb78344edecc901a4e11979204e6a33)): ?>
<?php $attributes = $__attributesOriginal1cb78344edecc901a4e11979204e6a33; ?>
<?php unset($__attributesOriginal1cb78344edecc901a4e11979204e6a33); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1cb78344edecc901a4e11979204e6a33)): ?>
<?php $component = $__componentOriginal1cb78344edecc901a4e11979204e6a33; ?>
<?php unset($__componentOriginal1cb78344edecc901a4e11979204e6a33); ?>
<?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                    No hay productos con stock crítico
                </div>
            <?php endif; ?>
        </div>

        <!-- Productos con stock bajo -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Bajo</h3>
                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                    <?php echo e($productosStockBajo->count()); ?> productos
                </span>
            </div>
            
            <?php if($productosStockBajo->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $productosStockBajo->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <?php if($producto->imagenes->isNotEmpty()): ?>
                                    <img src="<?php echo e(asset('storage/' . $producto->imagenes[0]->ruta_imagen)); ?>" 
                                         class="w-10 h-10 rounded-md object-cover" 
                                         alt="<?php echo e($producto->nombre_producto); ?>">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($producto->nombre_producto); ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">ID: <?php echo e($producto->producto_id); ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php if (isset($component)) { $__componentOriginal1cb78344edecc901a4e11979204e6a33 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1cb78344edecc901a4e11979204e6a33 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stock-indicator','data' => ['producto' => $producto]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stock-indicator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['producto' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($producto)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1cb78344edecc901a4e11979204e6a33)): ?>
<?php $attributes = $__attributesOriginal1cb78344edecc901a4e11979204e6a33; ?>
<?php unset($__attributesOriginal1cb78344edecc901a4e11979204e6a33); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1cb78344edecc901a4e11979204e6a33)): ?>
<?php $component = $__componentOriginal1cb78344edecc901a4e11979204e6a33; ?>
<?php unset($__componentOriginal1cb78344edecc901a4e11979204e6a33); ?>
<?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                    No hay productos con stock bajo
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Productos con stock reservado alto -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Reservado Alto</h3>
            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                <?php echo e($alertas['stock_reservado_alto'] ?? 0); ?> productos
            </span>
        </div>
        
        <?php
            // Obtener productos con stock reservado alto usando consulta directa
            $productosStockReservadoAlto = \Illuminate\Support\Facades\DB::table('productos')
                ->where('activo', true)
                ->where('stock_reservado', '>', 0)
                ->whereRaw('stock_reservado > stock * 0.3')
                ->select('producto_id', 'nombre_producto', 'stock', 'stock_reservado', 'stock_disponible')
                ->take(5)
                ->get();
        ?>
        
        <?php if($productosStockReservadoAlto->count() > 0): ?>
            <div class="space-y-3">
                <?php $__currentLoopData = $productosStockReservadoAlto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($producto->nombre_producto); ?></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">ID: <?php echo e($producto->producto_id); ?></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                Stock: <?php echo e($producto->stock); ?>

                            </div>
                            <div class="text-xs text-blue-600 dark:text-blue-400">
                                Reservado: <?php echo e($producto->stock_reservado); ?>

                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Disponible: <?php echo e($producto->stock_disponible); ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                No hay productos con alto stock reservado
            </div>
        <?php endif; ?>
    </div>

    <!-- Sección de Variantes -->
    <?php if(isset($reporteVariantes) && $reporteVariantes['total_variantes'] > 0): ?>
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Inventario de Variantes</h3>
            <a href="<?php echo e(route('admin.inventario.variantes.dashboard')); ?>" 
               class="inline-flex items-center px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Ver Detalles
            </a>
        </div>
        
        <!-- Estadísticas de variantes -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-800">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Variantes</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['total_variantes']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 dark:bg-green-800">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Con Stock</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['variantes_con_stock']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-red-100 dark:bg-red-800">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin Stock</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['variantes_sin_stock']); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-800">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Necesitan Reposición</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['variantes_necesitan_reposicion']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Variantes con stock bajo -->
        <?php if(isset($variantesStockBajo) && $variantesStockBajo->count() > 0): ?>
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Variantes con Stock Bajo</h4>
            <div class="space-y-2">
                <?php $__currentLoopData = $variantesStockBajo->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/10 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                <?php echo e($variante->producto->nombre_producto); ?> - <?php echo e($variante->nombre); ?>

                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Stock: <?php echo e($variante->stock_disponible); ?> / Mín: <?php echo e($variante->stock_minimo); ?>

                            </div>
                        </div>
                    </div>
                    <div class="text-sm text-red-600 dark:text-red-400 font-medium">
                        <?php echo e($variante->stock_disponible); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($variantesStockBajo->count() > 5): ?>
                <div class="text-center">
                    <a href="<?php echo e(route('admin.inventario.variantes.dashboard')); ?>" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                        Ver <?php echo e($variantesStockBajo->count() - 5); ?> más...
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Acciones rápidas -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="<?php echo e(route('admin.inventario.movimientos')); ?>" 
               class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Ver Movimientos</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Historial de entradas y salidas</p>
                </div>
            </a>
            
            <a href="<?php echo e(route('admin.inventario.reporte')); ?>" 
               class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Generar Reporte</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Análisis completo del inventario</p>
                </div>
            </a>
            
            <a href="<?php echo e(route('admin.inventario.productos-mas-vendidos')); ?>" 
               class="flex items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <svg class="w-8 h-8 text-orange-600 dark:text-orange-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Productos Más Vendidos</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Análisis de demanda</p>
                </div>
            </a>
            
            <a href="<?php echo e(route('admin.inventario.valor-por-categoria')); ?>" 
               class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Valor por Categoría</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Distribución del inventario</p>
                </div>
            </a>
            
            <a href="<?php echo e(route('admin.inventario.exportar-reporte')); ?>" 
               class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Exportar Reporte</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">PDF y Excel</p>
                </div>
            </a>
            
            <a href="<?php echo e(route('admin.productos.listadoP')); ?>" 
               class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Gestionar Productos</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Editar productos y stock</p>
                </div>
            </a>
            
            <?php if(isset($reporteVariantes) && $reporteVariantes['total_variantes'] > 0): ?>
            <a href="<?php echo e(route('admin.inventario.variantes.dashboard')); ?>" 
               class="flex items-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/30 transition-colors">
                <svg class="w-8 h-8 text-teal-600 dark:text-teal-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Gestionar Variantes</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Stock por color/tamaño</p>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/pages/admin/inventario/dashboard.blade.php ENDPATH**/ ?>