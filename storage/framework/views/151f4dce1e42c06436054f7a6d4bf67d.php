<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['showFlags' => true, 'showText' => true, 'size' => 'sm']));

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

foreach (array_filter((['showFlags' => true, 'showText' => true, 'size' => 'sm']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $currentLocale = app()->getLocale();
    $currentCurrency = session('currency', 'COP');
    
    $languageConfigs = [
        'es' => [
            'name' => 'Español', 
            'flag' => '🇨🇴', 
            'code' => 'es',
            'country' => 'CO',
            'currency' => 'COP'
        ],
        'en' => [
            'name' => 'English', 
            'flag' => '🇺🇸', 
            'code' => 'en',
            'country' => 'US',
            'currency' => 'USD'
        ],
        'pt' => [
            'name' => 'Português', 
            'flag' => '🇧🇷', 
            'code' => 'pt',
            'country' => 'BR',
            'currency' => 'BRL'
        ],
    ];
    
    $sizeClasses = [
        'xs' => 'text-xs px-2 py-1',
        'sm' => 'text-sm px-3 py-1.5',
        'md' => 'text-base px-4 py-2',
        'lg' => 'text-lg px-5 py-2.5',
    ];
?>

<div class="relative inline-block" x-data="{ open: false }">
    <!-- Botón del selector -->
    <button 
        @click="open = !open"
        class="flex items-center gap-2 <?php echo e($sizeClasses[$size]); ?> bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
        type="button"
    >
        <?php if($showFlags): ?>
            <span class="text-lg"><?php echo e($languageConfigs[$currentLocale]['flag'] ?? '🌐'); ?></span>
        <?php endif; ?>
        
        <?php if($showText): ?>
            <span class="font-medium"><?php echo e($languageConfigs[$currentLocale]['name'] ?? 'Idioma'); ?></span>
        <?php endif; ?>
        
        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown menu -->
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
        style="display: none;"
    >
        <div class="py-1">
            <?php $__currentLoopData = $languageConfigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a 
                    href="<?php echo e(route('localization.change', $code)); ?>"
                    class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 <?php echo e($currentLocale === $code ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300'); ?>"
                >
                    <?php if($showFlags): ?>
                        <span class="text-lg"><?php echo e($config['flag']); ?></span>
                    <?php endif; ?>
                    <div class="flex-1">
                        <div class="font-medium"><?php echo e($config['name']); ?></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400"><?php echo e($config['currency']); ?></div>
                    </div>
                    <?php if($currentLocale === $code): ?>
                        <svg class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    <?php endif; ?>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\ACER\Documents\GitHub\4GMovil\resources\views/components/language-selector.blade.php ENDPATH**/ ?>