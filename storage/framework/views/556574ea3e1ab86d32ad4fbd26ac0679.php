<?php if($productos->count() > 0): ?>
    <!-- Grid responsivo para productos -->
    <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-4 sm:gap-6 mb-8">
        <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="product-item min-w-[300px] flex-shrink-0 animate-fadeInUp">
                <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['producto' => $producto]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['producto' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($producto)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Paginación -->
    <?php if($productos->hasPages()): ?>
        <!-- Paginación para móvil -->
        <div class="mt-10 flex justify-center md:hidden">
            <nav class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700" aria-label="Paginación móvil">
                <!-- Botón Anterior -->
                <?php if($productos->onFirstPage()): ?>
                    <span class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-xl cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                <?php else: ?>
                    <a href="<?php echo e($productos->previousPageUrl()); ?>"
                        class="pagination-link px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <!-- Indicador de página actual -->
                <span class="px-4 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium">
                    <?php echo e($productos->currentPage()); ?> de <?php echo e($productos->lastPage()); ?>

                </span>

                <!-- Botón Siguiente -->
                <?php if($productos->hasMorePages()): ?>
                    <a href="<?php echo e($productos->nextPageUrl()); ?>"
                        class="pagination-link px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-xl cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Paginación para tablet y escritorio -->
        <div class="mt-10 hidden md:flex justify-center">
            <nav class="inline-flex rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden" aria-label="Paginación">
                <!-- Botón Anterior -->
                <?php if($productos->onFirstPage()): ?>
                    <span class="px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                <?php else: ?>
                    <a href="<?php echo e($productos->previousPageUrl()); ?>"
                        class="pagination-link px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <!-- Números de página -->
                <?php $__currentLoopData = $productos->getUrlRange(1, $productos->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $productos->currentPage()): ?>
                        <span class="px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold">
                            <?php echo e($page); ?>

                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($url); ?>"
                            class="pagination-link px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                            <?php echo e($page); ?>

                        </a>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Botón Siguiente -->
                <?php if($productos->hasMorePages()): ?>
                    <a href="<?php echo e($productos->nextPageUrl()); ?>"
                        class="pagination-link px-4 py-3 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Información de paginación -->
        <div class="mt-6 text-center">
            <div class="inline-block bg-blue-50 dark:bg-blue-900/20 px-6 py-3 rounded-2xl border border-blue-200 dark:border-blue-700">
                <span class="text-sm text-blue-800 dark:text-blue-300 font-medium">
                    Mostrando <?php echo e($productos->firstItem() ?? 0); ?> a <?php echo e($productos->lastItem() ?? 0); ?> de <?php echo e($productos->total()); ?> productos
                </span>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <!-- Estado vacío -->
    <div class="text-center py-16">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-12">
            <i class="fas fa-search text-6xl text-gray-300 dark:text-gray-600 mb-6"></i>
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">No se encontraron productos</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Intenta ajustar los filtros o realizar una búsqueda diferente
            </p>
            <button id="resetFiltersBtn" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-undo mr-2"></i>Limpiar Filtros
            </button>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/components/productos-grid.blade.php ENDPATH**/ ?>