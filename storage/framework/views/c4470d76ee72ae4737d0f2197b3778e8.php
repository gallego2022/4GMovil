<?php $__env->startSection('title', 'Dashboard de Variantes - 4GMovil'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard de Variantes</h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-300">Gestión y control del inventario de variantes por color/tamaño</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('admin.inventario.variantes.movimientos')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Movimientos
                </a>
                <a href="<?php echo e(route('admin.inventario.variantes.reporte')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de variantes -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Variantes</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['total_variantes'] ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Variantes con stock -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Con Stock</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['variantes_con_stock'] ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Variantes sin stock -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sin Stock</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['variantes_sin_stock'] ?? 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Necesitan reposición -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Necesitan Reposición</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white"><?php echo e($reporteVariantes['variantes_necesitan_reposicion'] ?? 0); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Valor total del inventario de variantes -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Valor Total del Inventario</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Valor de todas las variantes en stock</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                    $<?php echo e(number_format($reporteVariantes['valor_total_inventario'] ?? 0, 0, ',', '.')); ?>

                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Stock total: <?php echo e(number_format($reporteVariantes['stock_total'] ?? 0, 0, ',', '.')); ?> unidades
                </p>
            </div>
        </div>
    </div>

    <!-- Variantes con alertas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Variantes con stock bajo -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stock Bajo</h3>
                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                    <?php echo e($variantesStockBajo->count()); ?> variantes
                </span>
            </div>
            
            <?php if($variantesStockBajo->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $variantesStockBajo->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <?php if($variante->imagenes->isNotEmpty()): ?>
                                    <img src="<?php echo e(asset('storage/' . $variante->imagenes[0]->ruta_imagen)); ?>" 
                                         class="w-10 h-10 rounded-md object-cover" 
                                         alt="<?php echo e($variante->nombre); ?>">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo e($variante->producto->nombre_producto); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo e($variante->nombre); ?> - ID: <?php echo e($variante->variante_id); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-red-600 dark:text-red-400">
                                    <?php echo e($variante->stock_disponible); ?>

                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Mín: <?php echo e($variante->stock_minimo); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($variantesStockBajo->count() > 5): ?>
                        <div class="text-center">
                            <a href="#" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                                Ver <?php echo e($variantesStockBajo->count() - 5); ?> más...
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                    No hay variantes con stock bajo
                </div>
            <?php endif; ?>
        </div>

        <!-- Variantes sin stock -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sin Stock</h3>
                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded-full">
                    <?php echo e($variantesSinStock->count()); ?> variantes
                </span>
            </div>
            
            <?php if($variantesSinStock->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $variantesSinStock->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <?php if($variante->imagenes->isNotEmpty()): ?>
                                    <img src="<?php echo e(asset('storage/' . $variante->imagenes[0]->ruta_imagen)); ?>" 
                                         class="w-10 h-10 rounded-md object-cover" 
                                         alt="<?php echo e($variante->nombre); ?>">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?php echo e($variante->producto->nombre_producto); ?>

                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo e($variante->nombre); ?> - ID: <?php echo e($variante->variante_id); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    0
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Sin stock
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($variantesSinStock->count() > 5): ?>
                        <div class="text-center">
                            <a href="#" class="text-sm text-purple-600 dark:text-purple-400 hover:underline">
                                Ver <?php echo e($variantesSinStock->count() - 5); ?> más...
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400">
                    No hay variantes sin stock
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="<?php echo e(route('admin.inventario.variantes.movimientos')); ?>" 
               class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Ver Movimientos</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Historial de entradas y salidas</p>
                </div>
            </a>
            
            <a href="<?php echo e(route('admin.inventario.variantes.reporte')); ?>" 
               class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Generar Reporte</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Análisis completo de variantes</p>
                </div>
            </a>
            
            <a href="#" 
               class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors">
                <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Registrar Entrada</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Agregar stock a variantes</p>
                </div>
            </a>
            
            <a href="#" 
               class="flex items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors">
                <svg class="w-8 h-8 text-orange-600 dark:text-orange-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Registrar Salida</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Descontar stock de variantes</p>
                </div>
            </a>
            
            <a href="#" 
               class="flex items-center p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-900/30 transition-colors">
                <svg class="w-8 h-8 text-teal-600 dark:text-teal-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Ajustar Stock</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Corregir cantidades</p>
                </div>
            </a>
            
            <a href="<?php echo e(route('admin.inventario.dashboard')); ?>" 
               class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Volver al Inventario</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dashboard principal</p>
                </div>
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\pages\admin\inventario\variantes\dashboard.blade.php ENDPATH**/ ?>