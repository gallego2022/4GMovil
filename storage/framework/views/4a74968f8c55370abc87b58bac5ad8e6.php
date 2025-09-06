<?php $__env->startSection('title', '4GMovil - ' . __('messages.products.featured')); ?>
<?php $__env->startSection('meta-description',
    __('messages.products.featured') . ' - 4GMovil. ' . __('messages.welcome')); ?>

    <?php $__env->startPush('head'); ?>
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Breadcrumb -->
<div class="container mx-auto px-4 py-3 bg-gray-100 dark:bg-gray-800">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?php echo e(route('landing')); ?>"
                    class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>
                    <?php echo e(__('messages.nav.home')); ?>

                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-angle-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2"><?php echo e(__('messages.nav.products')); ?></span>
                </div>
            </li>
        </ol>
    </nav>
</div>

    <!-- Hero Section para Productos -->
    <section
        class="min-h-[50vh] sm:min-h-[60vh] flex items-center relative overflow-hidden pt-16 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800">
        <!-- Elementos decorativos -->
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-10 left-4 sm:left-10 w-16 h-16 sm:w-20 sm:h-20 bg-white/10 rounded-full"></div>
            <div class="absolute top-20 right-4 sm:right-20 w-24 h-24 sm:w-32 sm:h-32 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-10 left-1/4 w-12 h-12 sm:w-16 sm:h-16 bg-white/10 rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-6xl font-bold mb-4 sm:mb-6 text-white leading-tight">
                <?php echo e(__('messages.products.featured')); ?>

            </h1>
            <p class="text-lg sm:text-xl lg:text-2xl mb-6 sm:mb-8 text-blue-100 leading-relaxed max-w-3xl mx-auto px-4">
                <?php echo e(__('messages.welcome')); ?> - <?php echo e(__('messages.products.featured')); ?>

            </p>

            <!-- Búsqueda en tiempo real -->
            <div class="max-w-2xl mx-auto px-4">
                <div class="relative">
                    <input type="text" id="searchProducts"
                        class="w-full px-4 sm:px-6 py-3 sm:py-4 text-base sm:text-lg rounded-2xl border-0 shadow-2xl focus:ring-4 focus:ring-blue-300 focus:outline-none transition-all duration-300"
                        placeholder="<?php echo e(__('messages.products.search_placeholder')); ?>">
                    <i
                        class="fas fa-search absolute right-4 sm:right-6 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
    </section>



    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 sm:py-12">
        <div class="flex flex-col lg:flex-row gap-6 sm:gap-8">

            <!-- Filters Sidebar -->
            <aside class="w-full lg:w-1/4 mb-8 lg:mb-0">
                <div
                    class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 h-fit lg:sticky lg:top-4">
                    <h2
                        class="text-lg sm:text-xl font-bold mb-4 sm:mb-6 text-gray-800 dark:text-white border-b border-gray-200 dark:border-gray-600 pb-2 sm:pb-3">
                        <i class="fas fa-filter text-blue-500 mr-2"></i><?php echo e(__('messages.products.filters')); ?>

                    </h2>

                    <!-- Categories -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm sm:text-base"><?php echo e(__('messages.products.categories')); ?></h3>
                        <div class="space-y-2">
                            <button
                                class="category-btn w-full text-left px-3 sm:px-4 py-2 sm:py-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 active bg-blue-500 text-white font-medium transition-all duration-200 text-sm sm:text-base"
                                data-category="all">
                                <i class="fas fa-boxes mr-2"></i><?php echo e(__('messages.products.all_products')); ?>

                            </button>
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button
                                    class="category-btn w-full text-left px-3 sm:px-4 py-2 sm:py-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-700 dark:text-gray-300 font-medium transition-all duration-200 text-sm sm:text-base"
                                    data-category="<?php echo e($categoria->categoria_id); ?>">
                                    <i class="fas fa-box mr-2"></i><?php echo e($categoria->nombre); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm sm:text-base"><?php echo e(__('messages.products.price_range')); ?></h3>
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-3 sm:p-4">
                            <div class="mb-3">
                                <input type="range" min="<?php echo e($precioMinimo); ?>" max="<?php echo e($precioMaximo); ?>"
                                    value="<?php echo e($precioMaximo); ?>" step="10000" class="w-full price-range accent-blue-500"
                                    id="priceRange">
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span><?php echo e(__('messages.products.min')); ?>: <?php echo e(\App\Helpers\CurrencyHelper::formatPrice($precioMinimo)); ?></span>
                                <span><?php echo e(__('messages.products.max')); ?>: <?php echo e(\App\Helpers\CurrencyHelper::formatPrice($precioMaximo)); ?></span>
                            </div>
                            <div class="text-center">
                                <span class="text-sm sm:text-lg font-bold text-blue-600 dark:text-blue-400">
                                    <?php echo e(__('messages.products.max_price')); ?>: <span
                                        id="maxPrice"><?php echo e(\App\Helpers\CurrencyHelper::formatPrice($precioMaximo)); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>



                    <!-- Filtros Dinámicos por Categoría -->
                    <div id="dynamicFilters" class="mb-6">
                        <!-- Los filtros dinámicos se cargarán aquí según la categoría seleccionada -->
                    </div>

                    <!-- Marcas -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm sm:text-base"><?php echo e(__('messages.products.brands')); ?></h3>
                        <div class="space-y-2">
                            <button
                                class="brand-btn w-full text-left px-3 sm:px-4 py-2 sm:py-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 active bg-blue-500 text-white font-medium transition-all duration-200 text-sm sm:text-base"
                                data-brand="all">
                                <i class="fas fa-tags mr-2"></i><?php echo e(__('messages.products.all_brands')); ?>

                            </button>
                            <?php $__currentLoopData = $marcas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $marca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button
                                    class="brand-btn w-full text-left px-3 sm:px-4 py-2 sm:py-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-700 dark:text-gray-300 font-medium transition-all duration-200 text-sm sm:text-base"
                                    data-brand="<?php echo e($marca->marca_id); ?>">
                                    <i class="fas fa-tag mr-2"></i><?php echo e($marca->nombre); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <!-- Estado del producto -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm sm:text-base"><?php echo e(__('messages.products.condition')); ?></h3>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="filter-checkbox mr-3 rounded text-blue-500" value="nuevo">
                                <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300"><?php echo e(__('messages.products.new')); ?></span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="filter-checkbox mr-3 rounded text-blue-500" value="usado">
                                <span class="text-xs sm:text-sm text-gray-700 dark:text-gray-300"><?php echo e(__('messages.products.used')); ?></span>
                            </label>
                        </div>
                    </div>

                    <button id="resetFilters"
                        class="w-full text-blue-600 dark:text-blue-400 py-2 sm:py-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300 font-medium border border-blue-200 dark:border-blue-700 relative overflow-hidden group text-sm sm:text-base">
                        <span class="relative z-10 flex items-center justify-center">
                            <i class="fas fa-undo mr-2 transition-transform duration-300 group-hover:rotate-12"></i>
                            <?php echo e(__('messages.products.clear_filters')); ?>

                        </span>
                        <div
                            class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                    </button>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="w-full lg:w-3/4">
                <!-- Header con ordenamiento -->
                <div
                    class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white"><?php echo e(__('messages.products.our_products')); ?></h2>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mt-1"><?php echo e(__('messages.products.find_perfect_device')); ?></p>
                            <div id="productosContador" class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                <!-- El contador se actualizará dinámicamente -->
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                            <span class="text-sm text-gray-600 dark:text-gray-400"><?php echo e(__('messages.products.sort_by')); ?></span>
                            <select id="sortProducts"
                                class="w-full sm:w-auto border border-gray-200 dark:border-gray-600 rounded-xl px-3 sm:px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <option value="recommended"><?php echo e(__('messages.products.recommended')); ?></option>
                                <option value="price_low"><?php echo e(__('messages.products.price_low_high')); ?></option>
                                <option value="price_high"><?php echo e(__('messages.products.price_high_low')); ?></option>
                                <option value="rating"><?php echo e(__('messages.products.best_rated')); ?></option>
                                <option value="newest"><?php echo e(__('messages.products.newest')); ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
                <div id="activeFilters" class="mb-4 sm:mb-6 hidden">
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 p-3 sm:p-4 rounded-2xl border border-blue-200 dark:border-blue-700">
                        <h3 class="font-semibold text-blue-800 dark:text-blue-300 mb-2 sm:mb-3 text-sm sm:text-base">
                            <?php echo e(__('messages.products.active_filters')); ?>:</h3>
                        <div class="flex flex-wrap gap-2" id="activeFiltersList">
                            <!-- Filtros activos aparecerán aquí -->
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div id="loadingState" class="hidden">
                    <div class="flex flex-col sm:flex-row items-center justify-center py-8 sm:py-12 gap-3 sm:gap-0">
                        <div class="animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-blue-500"></div>
                        <span class="text-sm sm:text-base text-gray-600 dark:text-gray-400 sm:ml-3"><?php echo e(__('messages.products.loading_products')); ?></span>
                    </div>
                </div>

                <!-- Catálogo de productos -->
                <section id="productsSection">
                    <!-- Grid responsivo para productos -->
                    <div id="productsGrid"
                        class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-8">
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

                    <!-- Paginación responsiva -->
                    <?php if($productos->hasPages()): ?>
                        <!-- Paginación para móvil -->
                        <div class="mt-10 flex justify-center md:hidden">
                            <nav class="flex items-center space-x-2 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700"
                                aria-label="Paginación móvil">
                                <!-- Botón Anterior -->
                                <?php if($productos->onFirstPage()): ?>
                                    <span
                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-xl cursor-not-allowed">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo e($productos->previousPageUrl()); ?>"
                                        class="px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <!-- Indicador de página actual -->
                                <span
                                    class="px-4 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium">
                                    <?php echo e($productos->currentPage()); ?> de <?php echo e($productos->lastPage()); ?>

                                </span>

                                <!-- Botón Siguiente -->
                                <?php if($productos->hasMorePages()): ?>
                                    <a href="<?php echo e($productos->nextPageUrl()); ?>"
                                        class="px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php else: ?>
                                    <span
                                        class="px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-xl cursor-not-allowed">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                <?php endif; ?>
                            </nav>
                        </div>

                        <!-- Paginación para tablet y escritorio -->
                        <div class="mt-10 hidden md:flex justify-center">
                            <nav class="inline-flex rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden"
                                aria-label="Paginación">
                                <!-- Botón Anterior -->
                                <?php if($productos->onFirstPage()): ?>
                                    <span
                                        class="px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed">
                                        <i class="fas fa-chevron-left"></i>
                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo e($productos->previousPageUrl()); ?>"
                                        class="px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <!-- Números de página -->
                                <?php $__currentLoopData = $productos->getUrlRange(1, $productos->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($page == $productos->currentPage()): ?>
                                        <span
                                            class="px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold">
                                            <?php echo e($page); ?>

                                        </span>
                                    <?php else: ?>
                                        <a href="<?php echo e($url); ?>"
                                            class="px-4 py-3 border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
                                            <?php echo e($page); ?>

                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <!-- Botón Siguiente -->
                                <?php if($productos->hasMorePages()): ?>
                                    <a href="<?php echo e($productos->nextPageUrl()); ?>"
                                        class="px-4 py-3 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300">
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
                            <div
                                class="inline-block bg-blue-50 dark:bg-blue-900/20 px-6 py-3 rounded-2xl border border-blue-200 dark:border-blue-700">
                                <span class="text-sm text-blue-800 dark:text-blue-300 font-medium">
                                    Mostrando <?php echo e($productos->firstItem() ?? 0); ?> a <?php echo e($productos->lastItem() ?? 0); ?> de
                                    <?php echo e($productos->total()); ?> productos
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </main>

    <!-- Estilos CSS para el nuevo diseño -->
    <style>
        /* Animaciones de entrada */
        .animate-fadeInUp {
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para filtros activos */
        .filter-chip {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .filter-chip.active {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .filter-chip:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.2);
        }

        .filter-chip::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease-out;
        }

        .filter-chip:hover::before {
            left: 100%;
        }

        .category-btn.active,
        .brand-btn.active {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px) scale(1.02);
        }

        .category-btn,
        .brand-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .category-btn::before,
        .brand-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .category-btn:hover::before,
        .brand-btn:hover::before {
            left: 100%;
        }

        .category-btn:hover,
        .brand-btn:hover {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }



        /* Efectos para botones de filtros */
        #resetFilters {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #resetFilters:hover {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
        }

        /* Efecto de ripple para botones */
        .group:hover .group-hover\:scale-110 {
            animation: iconPop 0.3s ease-out;
        }

        .group:hover .group-hover\:rotate-12 {
            animation: iconRotate 0.3s ease-out;
        }

        @keyframes iconPop {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1.1);
            }
        }

        @keyframes iconRotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(12deg);
            }
        }

        /* Efectos hover mejorados */
        .product-item {
            transition: all 0.3s ease;
        }

        .product-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Responsive design mejorado */
        @media (max-width: 768px) {
            .products-container .grid {
                grid-template-columns: 1fr;
            }

            .product-card {
                max-width: 100%;
                margin: 0 auto;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .products-container .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1025px) {
            .products-container .grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Estilos para modo oscuro */
        .dark .bg-gradient-to-r.from-blue-600.to-indigo-600 {
            background: linear-gradient(135deg, #1e40af, #3730a3);
        }

        .dark .bg-gradient-to-r.from-blue-600.via-indigo-700.to-purple-800 {
            background: linear-gradient(135deg, #1e40af, #4338ca, #6b21a8);
        }

        /* Mejoras para la paginación */
        nav.inline-flex a:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        nav.inline-flex a,
        nav.inline-flex span {
            transition: all 0.3s ease-in-out;
        }

        /* Efecto de carga */
        .product-card.loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Estilos para el slider de precio */
        input[type="range"] {
            -webkit-appearance: none;
            height: 10px;
            border-radius: 5px;
            background: linear-gradient(to right, #e5e7eb 0%, #e5e7eb 100%);
            outline: none;
            cursor: pointer;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        /* Estilos para la búsqueda */
        #searchProducts:focus {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Estilos para checkboxes de estado */
        .filter-checkbox {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: scale(1.1);
        }

        .filter-checkbox:checked {
            transform: scale(1.2);
            animation: checkboxPop 0.3s ease-out;
        }

        @keyframes checkboxPop {
            0% {
                transform: scale(1.1);
            }

            50% {
                transform: scale(1.3);
            }

            100% {
                transform: scale(1.2);
            }
        }

        /* Transiciones suaves */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>

    <!-- JavaScript para funcionalidades básicas -->
    <script>
        // Variables globales
        let currentView = 'grid';
        let currentPage = 1;
        let precioUsuarioModificado = false; // Nueva variable para rastrear si el usuario movió el slider

        // Función auxiliar para sanitizar mensajes de error
        function sanitizeErrorMessage(error) {
            let errorMessage = 'Error desconocido';
            
            if (error && error.message) {
                // Limpiar el mensaje de error de HTML y caracteres especiales
                errorMessage = error.message
                    .replace(/<[^>]*>/g, '') // Remover tags HTML
                    .replace(/&/g, '&amp;') // Escapar caracteres especiales
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#x27;')
                    .substring(0, 200); // Limitar longitud
            }
            
            return errorMessage;
        }

        // Función de prueba para AJAX
        function testAjax() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('/test-ajax', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({test: 'data'})
            })
            .then(response => {
                console.log('Test AJAX - Status:', response.status);
                console.log('Test AJAX - Content-Type:', response.headers.get('content-type'));
                return response.json();
            })
            .then(data => {
                console.log('Test AJAX - Success:', data);
            })
            .catch(error => {
                console.error('Test AJAX - Error:', error);
            });
        }

        // Esperar a que el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Probar AJAX primero
            testAjax();
            
            // Inicializar filtros
            initializeFilters();

            // Inicializar paginación
            initializePagination();

            // Cargar todos los productos al inicio
            setTimeout(() => {
                cargarTodosLosProductos();
            }, 100);
        });

        // Inicializar filtros
        function initializeFilters() {

            // Filtros de categoría
            const categoryBtns = document.querySelectorAll('.category-btn');
            if (categoryBtns.length > 0) {
                categoryBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        console.log('=== CLICK EN CATEGORÍA ===');
                        console.log('Botón clickeado:', this);
                        console.log('Dataset:', this.dataset);
                        console.log('Categoría ID:', this.dataset.category);
                        
                        categoryBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Cargar filtros dinámicos para la categoría seleccionada
                        const categoriaId = this.dataset.category;
                        console.log('Categoría ID para filtros dinámicos:', categoriaId);
                        
                        if (categoriaId && categoriaId !== 'all') {
                            cargarFiltrosDinamicos(categoriaId);
                        } else {
                            limpiarFiltrosDinamicos();
                        }
                        
                        aplicarFiltros();
                    });
                });
            }

            // Filtros de marca
            const brandBtns = document.querySelectorAll('.brand-btn');
            if (brandBtns.length > 0) {
                brandBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        brandBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        aplicarFiltros();
                    });
                });
            }

            // Checkboxes de estado
            const checkboxes = document.querySelectorAll('.filter-checkbox');
            if (checkboxes.length > 0) {
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        aplicarFiltros();
                    });
                });
            }

            // Slider de precio
            const priceRange = document.getElementById('priceRange');
            if (priceRange) {
                priceRange.addEventListener('input', function() {
                    const precio = parseInt(this.value);
                    const maxPrice = document.getElementById('maxPrice');
                    if (maxPrice) {
                        // Usar el formato de moneda actual
                        const currency = '<?php echo e(session("currency", "COP")); ?>';
                        if (currency === 'USD') {
                            maxPrice.textContent = `$${precio.toLocaleString('en-US')}`;
                        } else if (currency === 'BRL') {
                            maxPrice.textContent = `R$ ${precio.toLocaleString('pt-BR')}`;
                        } else {
                            maxPrice.textContent = `$${precio.toLocaleString('es-CO')}`;
                        }
                    }
                    // Marcar que el usuario ha modificado el precio
                    precioUsuarioModificado = true;
                });

                // Aplicar filtros cuando se suelte el slider
                priceRange.addEventListener('change', function() {
                    aplicarFiltros();
                });
            }

            // Ordenamiento
            const sortSelect = document.getElementById('sortProducts');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    aplicarFiltros();
                });
            }

            // Búsqueda en tiempo real
            const searchInput = document.getElementById('searchProducts');
            if (searchInput) {
                let searchTimeout;

                searchInput.addEventListener('input', function() {
                    const searchValue = this.value.trim();

                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (searchValue.length >= 2 || searchValue.length === 0) {
                            aplicarFiltros();
                        }
                    }, 500);
                });

                // También aplicar filtros al hacer Enter
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        aplicarFiltros();
                    }
                });
            }

            // Botón de resetear filtros
            const resetFiltersBtn = document.getElementById('resetFilters');
            if (resetFiltersBtn) {
                resetFiltersBtn.addEventListener('click', function() {
                    resetearFiltros();
                });
            }


        }

        // Función para cargar filtros dinámicos según la categoría
        function cargarFiltrosDinamicos(categoriaId) {
            console.log('=== INICIO: cargarFiltrosDinamicos ===');
            console.log('Categoría ID:', categoriaId);
            console.log('Tipo de categoría ID:', typeof categoriaId);
            console.log('URL actual:', window.location.href);
            console.log('Origin:', window.location.origin);
            
            const dynamicFiltersContainer = document.getElementById('dynamicFilters');
            if (!dynamicFiltersContainer) {
                console.error('Contenedor de filtros dinámicos no encontrado');
                return;
            }

            // Mostrar estado de carga
            dynamicFiltersContainer.innerHTML = `
                <div class="flex items-center justify-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Cargando filtros...</span>
                </div>
            `;

            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Usar URL absoluta para evitar problemas de resolución
            const baseUrl = window.location.origin;
            const apiUrl = `${baseUrl}/api/especificaciones/${categoriaId}/valores`;
            
            console.log('Llamando a API:', apiUrl);
            
            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('=== RESPUESTA FETCH ===');
                console.log('Status:', response.status);
                console.log('Status Text:', response.statusText);
                console.log('OK:', response.ok);
                console.log('URL:', response.url);
                console.log('Headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status} - ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('=== DATOS RECIBIDOS ===');
                console.log('Filtros dinámicos cargados:', data);
                console.log('Tipo de datos:', typeof data);
                console.log('Es objeto:', typeof data === 'object');
                renderizarFiltrosDinamicos(data);
            })
            .catch(error => {
                console.error('=== ERROR EN FETCH ===');
                console.error('Error completo:', error);
                console.error('Mensaje de error:', error.message);
                console.error('Stack trace:', error.stack);
                dynamicFiltersContainer.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-sm text-red-500 dark:text-red-400">Error al cargar filtros: ${error.message}</p>
                        <button onclick="cargarFiltrosDinamicos(${categoriaId})" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Reintentar
                        </button>
                    </div>
                `;
            });
            
            console.log('=== FIN: cargarFiltrosDinamicos ===');
        }

        // Función para renderizar los filtros dinámicos
        function renderizarFiltrosDinamicos(especificaciones) {
            const dynamicFiltersContainer = document.getElementById('dynamicFilters');
            if (!dynamicFiltersContainer) return;

            if (!especificaciones || Object.keys(especificaciones).length === 0) {
                dynamicFiltersContainer.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay filtros disponibles para esta categoría</p>
                    </div>
                `;
                return;
            }

            let html = '';
            
            Object.entries(especificaciones).forEach(([nombreCampo, datos]) => {
                // Obtener icono según el tipo de especificación
                const icono = obtenerIconoEspecificacion(nombreCampo);
                
                html += `
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 text-sm sm:text-base flex items-center">
                            <i class="${icono} mr-2 text-blue-500"></i>
                            ${datos.etiqueta}
                        </h3>
                        <div class="flex flex-wrap gap-2">
                `;

                // Ordenar valores numéricamente si es posible
                const valores = datos.valores.sort((a, b) => {
                    const numA = parseFloat(a);
                    const numB = parseFloat(b);
                    if (!isNaN(numA) && !isNaN(numB)) {
                        return numA - numB;
                    }
                    return a.localeCompare(b);
                });

                valores.forEach(valor => {
                    const valorMostrar = datos.unidad ? `${valor} ${datos.unidad}` : valor;
                    html += `
                        <button
                            class="dynamic-filter-chip px-3 sm:px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-blue-100 dark:hover:bg-blue-900/20 text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 transition-all duration-200"
                            data-filter="${nombreCampo}" 
                            data-value="${valor}"
                            title="${datos.etiqueta}: ${valorMostrar}">
                            ${valorMostrar}
                        </button>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });

            dynamicFiltersContainer.innerHTML = html;

            // Agregar event listeners a los nuevos filtros dinámicos
            const dynamicFilterChips = document.querySelectorAll('.dynamic-filter-chip');
            dynamicFilterChips.forEach(chip => {
                chip.addEventListener('click', function() {
                    this.classList.toggle('active');
                    aplicarFiltros();
                });
            });
        }

        // Función para limpiar filtros dinámicos
        function limpiarFiltrosDinamicos() {
            const dynamicFiltersContainer = document.getElementById('dynamicFilters');
            if (dynamicFiltersContainer) {
                dynamicFiltersContainer.innerHTML = '';
            }
        }

        // Función para obtener icono según el tipo de especificación
        function obtenerIconoEspecificacion(nombreCampo) {
            const iconos = {
                'ram': 'fas fa-memory',
                'almacenamiento': 'fas fa-hdd',
                'pantalla': 'fas fa-mobile-alt',
                'resolucion': 'fas fa-tv',
                'procesador': 'fas fa-microchip',
                'camara_principal': 'fas fa-camera',
                'camara_frontal': 'fas fa-camera',
                'bateria': 'fas fa-battery-full',
                'sistema_operativo': 'fas fa-desktop',
                'version_os': 'fas fa-code-branch',
                'conectividad': 'fas fa-wifi',
                'wifi': 'fas fa-wifi',
                'bluetooth': 'fas fa-bluetooth',
                'gpu': 'fas fa-gamepad',
                'peso': 'fas fa-weight-hanging',
                'dimensiones': 'fas fa-ruler-combined'
            };
            
            return iconos[nombreCampo] || 'fas fa-cog';
        }

        // Función para inicializar la paginación
        function initializePagination() {
            // Agregar event listeners a los enlaces de paginación
            const paginationLinks = document.querySelectorAll('.pagination-link');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    if (url) {
                        // Si hay filtros activos, mantenerlos en la URL
                        const currentFilters = getCurrentFilters();
                        const separator = url.includes('?') ? '&' : '?';
                        const filtersUrl = Object.keys(currentFilters).length > 0 ?
                            url + separator + new URLSearchParams(currentFilters).toString() :
                            url;

                        // Navegar a la nueva página
                        window.location.href = filtersUrl;
                    }
                });
            });
        }

        // Función para obtener los filtros actuales
        function getCurrentFilters() {
            const filters = {};

            // Categoría activa
            const categoriaActiva = document.querySelector('.category-btn.active');
            if (categoriaActiva && categoriaActiva.dataset.category !== 'all') {
                filters.categoria = categoriaActiva.dataset.category;
            }

            // Marca activa
            const marcaActiva = document.querySelector('.brand-btn.active');
            if (marcaActiva && marcaActiva.dataset.brand !== 'all') {
                filters.marca = marcaActiva.dataset.brand;
            }

            // Estados marcados
            const checkboxesMarcados = document.querySelectorAll('.filter-checkbox:checked');
            if (checkboxesMarcados.length > 0) {
                filters.estado = Array.from(checkboxesMarcados).map(cb => cb.value);
            }

            // Precio máximo - solo si está realmente filtrado (menor al máximo)
            const priceRange = document.getElementById('priceRange');
            const precioMaximo = <?php echo e($precioMaximo); ?>;
            if (priceRange && precioUsuarioModificado && parseInt(priceRange.value) < precioMaximo) {
                filters.precio_max = priceRange.value;
            }

            // Orden
            const sortSelect = document.getElementById('sortProducts');
            if (sortSelect && sortSelect.value !== 'recommended') {
                filters.orden = sortSelect.value;
            }

            // Búsqueda
            const searchInput = document.getElementById('searchProducts');
            if (searchInput && searchInput.value.trim()) {
                filters.buscar = searchInput.value.trim();
            }

            // Filtros de especificaciones (solo dinámicos)
            const activeDynamicFilterChips = document.querySelectorAll('.dynamic-filter-chip.active');
            
            if (activeDynamicFilterChips.length > 0) {
                const especificaciones = {};
                
                // Procesar filtros dinámicos
                activeDynamicFilterChips.forEach(chip => {
                    const filterType = chip.dataset.filter;
                    const filterValue = chip.dataset.value;
                    
                    if (!especificaciones[filterType]) {
                        especificaciones[filterType] = [];
                    }
                    especificaciones[filterType].push(filterValue);
                });
                
                if (Object.keys(especificaciones).length > 0) {
                    filters.especificaciones = especificaciones;
                }
            }

            return filters;
        }

        // Función para mostrar filtros activos
        function mostrarFiltrosActivos() {
            const activeFiltersContainer = document.getElementById('activeFilters');
            const activeFiltersList = document.getElementById('activeFiltersList');
            
            if (!activeFiltersContainer || !activeFiltersList) {
                console.error('Contenedores de filtros activos no encontrados');
                return;
            }

            const filtros = getCurrentFilters();
            const filtrosActivos = [];

            // Agregar categoría si está filtrada
            if (filtros.categoria) {
                const categoriaBtn = document.querySelector(`[data-category="${filtros.categoria}"]`);
                if (categoriaBtn) {
                    filtrosActivos.push({
                        tipo: 'categoria',
                        valor: filtros.categoria,
                        texto: categoriaBtn.textContent.trim(),
                        icono: 'fas fa-tag'
                    });
                }
            }

            // Agregar marca si está filtrada
            if (filtros.marca) {
                const marcaBtn = document.querySelector(`[data-brand="${filtros.marca}"]`);
                if (marcaBtn) {
                    filtrosActivos.push({
                        tipo: 'marca',
                        valor: filtros.marca,
                        texto: marcaBtn.textContent.trim(),
                        icono: 'fas fa-copyright'
                    });
                }
            }

            // Agregar estados si están filtrados
            if (filtros.estado && filtros.estado.length > 0) {
                filtros.estado.forEach(estado => {
                    const checkbox = document.querySelector(`input[value="${estado}"]`);
                    if (checkbox) {
                        const label = checkbox.closest('label') || checkbox.nextElementSibling;
                        const texto = label ? label.textContent.trim() : estado;
                        filtrosActivos.push({
                            tipo: 'estado',
                            valor: estado,
                            texto: texto,
                            icono: 'fas fa-check-circle'
                        });
                    }
                });
            }

            // Agregar precio si está filtrado (solo si es menor al máximo)
            if (filtros.precio_max) {
                const precioMaximo = <?php echo e($precioMaximo); ?>;
                const precioActual = parseInt(filtros.precio_max);
                if (precioActual < precioMaximo) {
                    const precioFormateado = '$' + precioActual.toLocaleString('es-CO');
                    filtrosActivos.push({
                        tipo: 'precio',
                        valor: filtros.precio_max,
                        texto: `Hasta ${precioFormateado}`,
                        icono: 'fas fa-dollar-sign'
                    });
                }
            }

            // Agregar orden si está filtrado
            if (filtros.orden && filtros.orden !== 'recommended') {
                const sortSelect = document.getElementById('sortProducts');
                if (sortSelect) {
                    const option = sortSelect.querySelector(`option[value="${filtros.orden}"]`);
                    if (option) {
                        filtrosActivos.push({
                            tipo: 'orden',
                            valor: filtros.orden,
                            texto: option.textContent.trim(),
                            icono: 'fas fa-sort'
                        });
                    }
                }
            }

            // Agregar búsqueda si está filtrada
            if (filtros.buscar && filtros.buscar.trim()) {
                filtrosActivos.push({
                    tipo: 'buscar',
                    valor: filtros.buscar,
                    texto: `"${filtros.buscar}"`,
                    icono: 'fas fa-search'
                });
            }

            // Agregar filtros de especificaciones si están filtrados
            if (filtros.especificaciones && Object.keys(filtros.especificaciones).length > 0) {
                Object.entries(filtros.especificaciones).forEach(([tipo, valores]) => {
                    valores.forEach(valor => {
                        let texto = '';
                        let icono = 'fas fa-cog';
                        
                        switch (tipo) {
                            case 'ram':
                                texto = `RAM ${valor}`;
                                icono = 'fas fa-memory';
                                break;
                            case 'almacenamiento':
                                texto = `Almacenamiento ${valor}`;
                                icono = 'fas fa-hdd';
                                break;
                            case 'pantalla':
                                texto = `Pantalla ${valor}"`;
                                icono = 'fas fa-mobile-alt';
                                break;
                            default:
                                texto = `${tipo.charAt(0).toUpperCase() + tipo.slice(1)} ${valor}`;
                                break;
                        }
                        
                        filtrosActivos.push({
                            tipo: 'especificacion',
                            subtipo: tipo,
                            valor: `${tipo}:${valor}`,
                            texto: texto,
                            icono: icono
                        });
                    });
                });
            }

            // Mostrar u ocultar contenedor según si hay filtros activos
            if (filtrosActivos.length > 0) {
                activeFiltersContainer.classList.remove('hidden');
                
                // Generar HTML de filtros activos
                const filtrosHTML = filtrosActivos.map(filtro => `
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full text-sm font-medium">
                        <i class="${filtro.icono} text-xs"></i>
                        <span>${filtro.texto}</span>
                        <button onclick="removerFiltro('${filtro.tipo}', '${filtro.valor}')" 
                                class="ml-1 text-blue-600 dark:text-blue-300 hover:text-blue-800 dark:hover:text-blue-100 transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                `).join('');
                
                activeFiltersList.innerHTML = filtrosHTML;
            } else {
                activeFiltersContainer.classList.add('hidden');
                activeFiltersList.innerHTML = '';
            }
        }

        // Función para remover un filtro específico
        function removerFiltro(tipo, valor) {
            console.log('=== REMOVIENDO FILTRO ===');
            console.log('Tipo:', tipo);
            console.log('Valor:', valor);
            switch (tipo) {
                case 'categoria':
                    // Resetear a "Todas las categorías"
                    const categoryBtns = document.querySelectorAll('.category-btn');
                    categoryBtns.forEach(btn => btn.classList.remove('active'));
                    const allCategoryBtn = document.querySelector('[data-category="all"]');
                    if (allCategoryBtn) {
                        allCategoryBtn.classList.add('active');
                    }
                    
                    // Limpiar filtros dinámicos cuando se remueve la categoría
                    limpiarFiltrosDinamicos();
                    
                    // Remover filtros dinámicos activos
                    const dynamicFilterChips = document.querySelectorAll('.dynamic-filter-chip.active');
                    dynamicFilterChips.forEach(chip => {
                        chip.classList.remove('active');
                    });
                    break;
                    
                case 'marca':
                    // Resetear a "Todas las marcas"
                    const brandBtns = document.querySelectorAll('.brand-btn');
                    brandBtns.forEach(btn => btn.classList.remove('active'));
                    const allBrandBtn = document.querySelector('[data-brand="all"]');
                    if (allBrandBtn) {
                        allBrandBtn.classList.add('active');
                    }
                    break;
                    
                case 'estado':
                    // Desmarcar checkbox específico
                    const checkbox = document.querySelector(`input[value="${valor}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                    break;
                    
                case 'especificacion':
                    // Remover filtro de especificación específico
                    console.log('Removiendo especificación:', valor);
                    
                    // Parsear el valor que viene en formato "tipo:valor"
                    const partes = valor.split(':');
                    const tipoEspecificacion = partes[0];
                    const valorEspecificacion = partes[1];
                    
                    console.log('Tipo especificación:', tipoEspecificacion);
                    console.log('Valor especificación:', valorEspecificacion);
                    
                    // Buscar en filtros dinámicos
                    const chip = document.querySelector(`[data-filter="${tipoEspecificacion}"][data-value="${valorEspecificacion}"]`);
                    if (chip) {
                        console.log('Encontrado filtro dinámico:', chip);
                        chip.classList.remove('active');
                    } else {
                        console.log('No se encontró el filtro para remover');
                    }
                    break;
                    
                case 'precio':
                    // Resetear precio
                    const priceRange = document.getElementById('priceRange');
                    if (priceRange) {
                        priceRange.value = <?php echo e($precioMaximo); ?>;
                        const maxPrice = document.getElementById('maxPrice');
                        if (maxPrice) {
                            maxPrice.textContent = '<?php echo e(\App\Helpers\CurrencyHelper::formatPrice($precioMaximo)); ?>';
                        }
                    }
                    // Resetear la variable de control del precio
                    precioUsuarioModificado = false;
                    break;
                    
                case 'orden':
                    // Resetear orden
                    const sortSelect = document.getElementById('sortProducts');
                    if (sortSelect) {
                        sortSelect.value = 'recommended';
                    }
                    break;
                    
                case 'buscar':
                    // Limpiar búsqueda
                    const searchInput = document.getElementById('searchProducts');
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    break;
            }
            
            // Aplicar filtros después de remover
            console.log('=== FIN REMOVIENDO FILTRO ===');
            aplicarFiltros();
        }

        // Función para aplicar filtros
        function aplicarFiltros() {

            const loadingState = document.getElementById('loadingState');
            const productsSection = document.getElementById('productsSection');

            if (!loadingState || !productsSection) {
                console.error('Elementos de carga no encontrados');
                return;
            }

            // Mostrar estado de carga
            loadingState.classList.remove('hidden');
            productsSection.classList.add('hidden');

            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                loadingState.classList.add('hidden');
                productsSection.classList.remove('hidden');
                return;
            }

            // Recopilar filtros
            const categoriaActiva = document.querySelector('.category-btn.active');
            const marcaActiva = document.querySelector('.brand-btn.active');
            const checkboxesMarcados = document.querySelectorAll('.filter-checkbox:checked');
            const priceRange = document.getElementById('priceRange');
            const precioMaximoOriginal = <?php echo e($precioMaximo); ?>;
            const precioActual = priceRange ? parseInt(priceRange.value) : precioMaximoOriginal;
            const precioEstaFiltrado = precioUsuarioModificado && precioActual < precioMaximoOriginal;
            const sortSelect = document.getElementById('sortProducts');
            const ordenSeleccionado = sortSelect ? sortSelect.value : 'recommended';
            const searchInput = document.getElementById('searchProducts');
            const textoBusqueda = searchInput ? searchInput.value.trim() : '';

            // Verificar filtros de especificaciones (solo dinámicos ahora)
            const activeDynamicFilterChips = document.querySelectorAll('.dynamic-filter-chip.active');
            const hayFiltrosEspecificaciones = activeDynamicFilterChips.length > 0;

            // Verificar si realmente hay filtros activos
            const hayFiltrosActivos = (
                (categoriaActiva && categoriaActiva.dataset.category !== 'all') ||
                (marcaActiva && marcaActiva.dataset.brand !== 'all') ||
                checkboxesMarcados.length > 0 ||
                precioEstaFiltrado ||
                (textoBusqueda && textoBusqueda.length > 0) ||
                (ordenSeleccionado && ordenSeleccionado !== 'recommended') ||
                hayFiltrosEspecificaciones
            );

            const filtros = {
                categoria: categoriaActiva ? categoriaActiva.dataset.category : 'all',
                marca: marcaActiva ? marcaActiva.dataset.brand : 'all',
                estado: Array.from(checkboxesMarcados).map(cb => cb.value),
                orden: ordenSeleccionado,
                buscar: textoBusqueda
            };

            // Agregar filtros de especificaciones
            if (hayFiltrosEspecificaciones) {
                const especificaciones = {};
                
                // Procesar filtros dinámicos
                activeDynamicFilterChips.forEach(chip => {
                    const filterType = chip.dataset.filter;
                    const filterValue = chip.dataset.value;
                    
                    if (!especificaciones[filterType]) {
                        especificaciones[filterType] = [];
                    }
                    especificaciones[filterType].push(filterValue);
                });
                
                filtros.especificaciones = especificaciones;
            }

            // Solo agregar precio_max si está realmente filtrado
            if (precioEstaFiltrado) {
                filtros.precio_max = precioActual;
            }

            // Si no hay filtros activos, cargar todos los productos
            if (!hayFiltrosActivos) {
                cargarTodosLosProductos();
                return;
            }

            // Realizar petición AJAX solo cuando hay filtros activos
            console.log('=== ENVIANDO FILTROS ===');
            console.log('Filtros a enviar:', filtros);
            console.log('Hay filtros activos:', hayFiltrosActivos);
            
            fetch('<?php echo e(route('productos.filtrados')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(filtros)
                })
                .then(response => {
                    console.log('Respuesta recibida:', response.status); // Debug
                    console.log('Headers:', response.headers);
                    
                    // Verificar si la respuesta es JSON
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);
                    
                    if (!contentType || !contentType.includes('application/json')) {
                        // Si no es JSON, obtener el texto para debug
                        return response.text().then(text => {
                            console.error('Respuesta no es JSON:', text.substring(0, 500));
                            throw new Error('La respuesta del servidor no es JSON válido. Posible error del servidor.');
                        });
                    }
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('=== RESPUESTA RECIBIDA ===');
                    console.log('Datos recibidos:', data);
                    console.log('Success:', data.success);
                    console.log('HTML recibido:', data.html ? data.html.substring(0, 200) + '...' : 'No HTML');
                    
                    if (data.success) {
                        // Actualizar la sección de productos
                        productsSection.innerHTML = data.html;

                        // Reinicializar la paginación después de aplicar filtros
                        initializePagination();

                        // Mostrar filtros activos
                        mostrarFiltrosActivos();
                        
                        // Mostrar indicador de filtros aplicados
                        mostrarIndicadorFiltros();
                        
                        // Actualizar contador de productos
                        actualizarContadorProductos(data.productos);

                        // Ocultar estado de carga
                        loadingState.classList.add('hidden');
                        productsSection.classList.remove('hidden');
                    } else {
                        throw new Error(data.message || 'Error al filtrar productos');
                    }
                })
                .catch(error => {
                    console.error('Error al aplicar filtros:', error);
                    loadingState.classList.add('hidden');
                    productsSection.classList.remove('hidden');

                    // Mostrar mensaje de error al usuario
                    productsSection.innerHTML = `
                    <div class="text-center py-12">
                        <div class="text-red-500 text-xl mb-4">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                            Error al cargar productos
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            ${sanitizeErrorMessage(error)}
                        </p>
                        <button onclick="aplicarFiltros()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Reintentar
                        </button>
                    </div>
                `;
                });
        }



        // Función para cargar todos los productos (sin filtros)
        function cargarTodosLosProductos() {
            const loadingState = document.getElementById('loadingState');
            const productsSection = document.getElementById('productsSection');

            if (!loadingState || !productsSection) {
                console.error('Elementos de carga no encontrados');
                return;
            }

            // Mostrar estado de carga
            loadingState.classList.remove('hidden');
            productsSection.classList.add('hidden');

            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('Token CSRF no encontrado');
                loadingState.classList.add('hidden');
                productsSection.classList.remove('hidden');
                return;
            }

            // Realizar petición AJAX para obtener todos los productos
            fetch('<?php echo e(route('productos.filtrados')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    categoria: 'all',
                    marca: 'all',
                    estado: [],
                    orden: 'recommended',
                    buscar: ''
                    // No incluir precio_max cuando no está filtrado
                })
            })
                            .then(response => {
                    console.log('Respuesta recibida:', response.status);
                    console.log('Headers:', response.headers);
                    
                    // Verificar si la respuesta es JSON
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);
                    
                    if (!contentType || !contentType.includes('application/json')) {
                        // Si no es JSON, obtener el texto para debug
                        return response.text().then(text => {
                            console.error('Respuesta no es JSON:', text.substring(0, 500));
                            throw new Error('La respuesta del servidor no es JSON válido. Posible error del servidor.');
                        });
                    }
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
            .then(data => {
                if (data.success) {
                    // Actualizar la sección de productos
                    productsSection.innerHTML = data.html;

                    // Reinicializar la paginación
                    initializePagination();

                                            // Mostrar filtros activos (ocultará la sección si no hay filtros)
                        mostrarFiltrosActivos();
                        
                        // Mostrar indicador de filtros aplicados
                        mostrarIndicadorFiltros();
                        
                        // Actualizar contador de productos
                        actualizarContadorProductos(data.productos);

                    // Ocultar estado de carga
                    loadingState.classList.add('hidden');
                    productsSection.classList.remove('hidden');
                } else {
                    throw new Error(data.message || 'Error al cargar productos');
                }
            })
            .catch(error => {
                console.error('Error al cargar productos:', error);
                loadingState.classList.add('hidden');
                productsSection.classList.remove('hidden');

                // Mostrar mensaje de error al usuario
                productsSection.innerHTML = `
                <div class="text-center py-12">
                    <div class="text-red-500 text-xl mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                        Error al cargar productos
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        ${sanitizeErrorMessage(error)}
                    </p>
                    <button onclick="cargarTodosLosProductos()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Reintentar
                    </button>
                </div>
            `;
            });
        }

        // Función para actualizar contador de productos
        function actualizarContadorProductos(productos) {
            const contadorElement = document.getElementById('productosContador');
            if (!contadorElement) return;
            
            const total = productos.total || 0;
            const enPagina = productos.data ? productos.data.length : 0;
            const paginaActual = productos.current_page || 1;
            const ultimaPagina = productos.last_page || 1;
            
            let texto = '';
            if (total === 0) {
                texto = '<?php echo e(__("messages.products.no_results")); ?>';
            } else if (ultimaPagina === 1) {
                texto = `${total} producto${total > 1 ? 's' : ''} encontrado${total > 1 ? 's' : ''}`;
            } else {
                texto = `Mostrando ${enPagina} de ${total} productos (página ${paginaActual} de ${ultimaPagina})`;
            }
            
            contadorElement.textContent = texto;
        }

        // Función para mostrar indicador de filtros aplicados
        function mostrarIndicadorFiltros() {
            const categoriaActiva = document.querySelector('.category-btn.active');
            const marcaActiva = document.querySelector('.brand-btn.active');
            const checkboxesMarcados = document.querySelectorAll('.filter-checkbox:checked');
            const activeDynamicFilterChips = document.querySelectorAll('.dynamic-filter-chip.active');
            
            // Verificar si hay filtros activos
            const hayFiltrosActivos = (
                (categoriaActiva && categoriaActiva.dataset.category !== 'all') ||
                (marcaActiva && marcaActiva.dataset.brand !== 'all') ||
                checkboxesMarcados.length > 0 ||
                activeDynamicFilterChips.length > 0
            );
            
            // Buscar o crear el indicador de filtros
            let indicadorFiltros = document.getElementById('indicadorFiltros');
            if (!indicadorFiltros) {
                indicadorFiltros = document.createElement('div');
                indicadorFiltros.id = 'indicadorFiltros';
                indicadorFiltros.className = 'fixed top-4 right-4 z-50 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg transform transition-all duration-300';
                indicadorFiltros.style.display = 'none';
                document.body.appendChild(indicadorFiltros);
            }
            
            if (hayFiltrosActivos) {
                // Contar filtros activos
                let contadorFiltros = 0;
                if (categoriaActiva && categoriaActiva.dataset.category !== 'all') contadorFiltros++;
                if (marcaActiva && marcaActiva.dataset.brand !== 'all') contadorFiltros++;
                contadorFiltros += checkboxesMarcados.length;
                contadorFiltros += activeDynamicFilterChips.length;
                
                indicadorFiltros.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-filter"></i>
                        <span>${contadorFiltros} filtro${contadorFiltros > 1 ? 's' : ''} activo${contadorFiltros > 1 ? 's' : ''}</span>
                    </div>
                `;
                indicadorFiltros.style.display = 'block';
                
                // Animación de entrada
                setTimeout(() => {
                    indicadorFiltros.style.transform = 'translateY(0) scale(1)';
                }, 100);
            } else {
                // Ocultar indicador
                indicadorFiltros.style.transform = 'translateY(-100%) scale(0.8)';
                setTimeout(() => {
                    indicadorFiltros.style.display = 'none';
                }, 300);
            }
        }

        // Función para resetear filtros
        function resetearFiltros() {

            // Resetear botones de categoría
            const categoryBtns = document.querySelectorAll('.category-btn');
            if (categoryBtns.length > 0) {
                categoryBtns.forEach(btn => btn.classList.remove('active'));
                const allCategoryBtn = document.querySelector('[data-category="all"]');
                if (allCategoryBtn) {
                    allCategoryBtn.classList.add('active');
                }
            }

            // Resetear botones de marca
            const brandBtns = document.querySelectorAll('.brand-btn');
            if (brandBtns.length > 0) {
                brandBtns.forEach(btn => btn.classList.remove('active'));
                const allBrandBtn = document.querySelector('[data-brand="all"]');
                if (allBrandBtn) {
                    allBrandBtn.classList.add('active');
                }
            }

            // Resetear checkboxes de estado
            const checkboxes = document.querySelectorAll('.filter-checkbox');
            if (checkboxes.length > 0) {
                checkboxes.forEach(cb => {
                    cb.checked = false;
                });
            }

            // Resetear slider de precio
            const priceRange = document.getElementById('priceRange');
            if (priceRange) {
                priceRange.value = <?php echo e($precioMaximo); ?>;
                const maxPrice = document.getElementById('maxPrice');
                if (maxPrice) {
                    maxPrice.textContent = '<?php echo e(\App\Helpers\CurrencyHelper::formatPrice($precioMaximo)); ?>';
                }
            }
            
            // Resetear la variable de control del precio
            precioUsuarioModificado = false;

            // Resetear búsqueda
            const searchInput = document.getElementById('searchProducts');
            if (searchInput) {
                searchInput.value = '';
            }

            // Resetear ordenamiento
            const sortSelect = document.getElementById('sortProducts');
            if (sortSelect) {
                sortSelect.value = 'recommended';
            }



            // Resetear filtros dinámicos
            const dynamicFilterChips = document.querySelectorAll('.dynamic-filter-chip');
            if (dynamicFilterChips.length > 0) {
                dynamicFilterChips.forEach(chip => {
                    chip.classList.remove('active');
                });
            }

            // Limpiar contenedor de filtros dinámicos
            limpiarFiltrosDinamicos();

            // Ocultar filtros activos
            const activeFiltersContainer = document.getElementById('activeFilters');
            if (activeFiltersContainer) {
                activeFiltersContainer.classList.add('hidden');
            }
            
            // Ocultar indicador de filtros
            mostrarIndicadorFiltros();

            // Cargar todos los productos después de resetear
            setTimeout(() => {
                cargarTodosLosProductos();
            }, 100);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ACER\Documents\GitHub\4GMovil\resources\views/pages/landing/productos.blade.php ENDPATH**/ ?>