<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['producto']));

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

foreach (array_filter((['producto']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="flex flex-col space-y-2">
    <!-- Barra de progreso visual -->
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
        <?php
            $totalStock = $producto->stock;
            $disponible = $producto->stock_disponible;
            $reservado = $producto->stock_reservado;
            
            $porcentajeDisponible = $totalStock > 0 ? ($disponible / $totalStock) * 100 : 0;
            $porcentajeReservado = $totalStock > 0 ? ($reservado / $totalStock) * 100 : 0;
        ?>
        
        <!-- Stock Disponible -->
        <div class="h-2 rounded-full transition-all duration-300 <?php echo e($disponible > 10 ? 'bg-green-500' : ($disponible > 5 ? 'bg-yellow-500' : 'bg-red-500')); ?>" 
             style="width: <?php echo e($porcentajeDisponible); ?>%"></div>
        
        <!-- Stock Reservado -->
        <?php if($reservado > 0): ?>
        <div class="h-2 bg-blue-500 rounded-full transition-all duration-300" 
             style="width: <?php echo e($porcentajeReservado); ?>%"></div>
        <?php endif; ?>
    </div>
    
    <!-- Información detallada -->
    <div class="grid grid-cols-3 gap-2 text-xs">
        <!-- Total -->
        <div class="text-center">
            <div class="font-medium text-gray-900 dark:text-gray-100"><?php echo e($totalStock); ?></div>
            <div class="text-gray-500 dark:text-gray-400">Total</div>
        </div>
        
        <!-- Disponible -->
        <div class="text-center">
            <div class="font-medium <?php echo e($disponible > 10 ? 'text-green-600 dark:text-green-400' : ($disponible > 5 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400')); ?>">
                <?php echo e($disponible); ?>

            </div>
            <div class="text-gray-500 dark:text-gray-400">Disponible</div>
        </div>
        
        <!-- Reservado -->
        <div class="text-center">
            <div class="font-medium <?php echo e($reservado > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'); ?>">
                <?php echo e($reservado); ?>

            </div>
            <div class="text-gray-500 dark:text-gray-400">Reservado</div>
        </div>
    </div>
    
    <!-- Indicadores de estado -->
    <?php if($disponible <= 0): ?>
        <div class="flex items-center space-x-1 text-xs text-red-600 dark:text-red-400">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>Sin stock disponible</span>
        </div>
    <?php elseif($reservado > $totalStock * 0.5): ?>
        <div class="flex items-center space-x-1 text-xs text-yellow-600 dark:text-yellow-400">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>Alto stock reservado</span>
        </div>
    <?php elseif($disponible <= 5): ?>
        <div class="flex items-center space-x-1 text-xs text-yellow-600 dark:text-yellow-400">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span>Stock bajo</span>
        </div>
    <?php else: ?>
        <div class="flex items-center space-x-1 text-xs text-green-600 dark:text-green-400">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>Stock óptimo</span>
        </div>
    <?php endif; ?>
</div> <?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views\components\stock-indicator.blade.php ENDPATH**/ ?>