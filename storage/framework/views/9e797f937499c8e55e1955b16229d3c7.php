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

<div class="flex items-center space-x-2">
    <?php
        $stockDisponible = $producto->stock_disponible;
        $stockTotal = $producto->stock;
        $porcentajeDisponible = $stockTotal > 0 ? ($stockDisponible / $stockTotal) * 100 : 0;
    ?>
    
    <!-- Indicador visual -->
    <div class="flex items-center space-x-1">
        <?php if($stockDisponible > 10): ?>
            <!-- Stock alto -->
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-xs text-green-600 font-medium"><?php echo e($stockDisponible); ?> disponibles</span>
        <?php elseif($stockDisponible > 5): ?>
            <!-- Stock medio -->
            <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
            <span class="text-xs text-yellow-600 font-medium"><?php echo e($stockDisponible); ?> disponibles</span>
        <?php elseif($stockDisponible > 0): ?>
            <!-- Stock bajo -->
            <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
            <span class="text-xs text-red-600 font-medium">Solo <?php echo e($stockDisponible); ?> disponibles</span>
        <?php else: ?>
            <!-- Sin stock -->
            <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
            <span class="text-xs text-gray-500 font-medium">Agotado</span>
        <?php endif; ?>
    </div>
    
    <!-- Barra de progreso sutil -->
    <?php if($stockTotal > 0): ?>
        <div class="flex-1 max-w-16">
            <div class="w-full bg-gray-200 rounded-full h-1">
                <div class="h-1 rounded-full transition-all duration-300 
                    <?php echo e($porcentajeDisponible > 50 ? 'bg-green-500' : ($porcentajeDisponible > 20 ? 'bg-yellow-500' : 'bg-red-500')); ?>" 
                     style="width: <?php echo e($porcentajeDisponible); ?>%"></div>
            </div>
        </div>
    <?php endif; ?>
</div> <?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/components/stock-status.blade.php ENDPATH**/ ?>