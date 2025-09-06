<?php $__env->startSection('title', '4GMovil - Productos Destacados'); ?>
<?php $__env->startSection('meta-description',
    'Descubre nuestros productos destacados en 4GMovil. Encuentra lo mejor en tecnología móvil
    y accesorios.'); ?>
<?php $__env->startSection('content'); ?>

    <!-- Hero Section -->
    <section id="inicio" class="min-h-screen flex items-center relative overflow-hidden pt-16">
        <div class="absolute inset-0">
            <img src="<?php echo e(asset('img/slaider/image.png')); ?>" alt="Fondo corporativo"
                class="w-full h-full object-cover object-center">
        </div>

        <!-- Content Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-white">
                    <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        <?php echo e(__('messages.messages.welcome')); ?>

                    </h1>
                    <p class="text-xl lg:text-2xl mb-8 text-gray-100 leading-relaxed">
                        <?php echo e(__('messages.products.featured')); ?> - <?php echo e(__('messages.home.subtitle')); ?>

                    </p>

                    <!-- Features List -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-4g-blue mr-3 text-xl"></i>
                            <span class="text-lg"><?php echo e(__('messages.home.no_credit_check')); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-4g-blue mr-3 text-xl"></i>
                            <span class="text-lg"><?php echo e(__('messages.home.minimal_documents')); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-4g-blue mr-3 text-xl"></i>
                            <span class="text-lg"><?php echo e(__('messages.home.approval_24h')); ?></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6">
                        <button class="btn-primary text-lg px-4 py-2">
                            <i class="fas fa-rocket mr-2"></i><?php echo e(__('messages.home.request_credit')); ?>

                        </button>
                        <button class="btn-secondary text-lg px-4 py-2">
                            <i class="fas fa-info-circle mr-2"></i><?php echo e(__('messages.home.learn_more')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
            <i class="fas fa-chevron-down text-2xl"></i>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-center">
                <div class="space-y-2">
                    <div class="text-4xl md:text-5xl font-bold text-yellow-300">5000+</div>
                    <div class="text-lg font-medium"><?php echo e(__('messages.home.satisfied_clients')); ?></div>
                </div>
                <div class="space-y-2">
                    <div class="text-4xl md:text-5xl font-bold text-green-300">1000+</div>
                    <div class="text-lg font-medium"><?php echo e(__('messages.home.successful_repairs')); ?></div>
                </div>
                <div class="space-y-2">
                    <div class="text-4xl md:text-5xl font-bold text-purple-300">50+</div>
                    <div class="text-lg font-medium"><?php echo e(__('messages.home.available_brands')); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Section Carrusel -->
    <section class="py-16 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-4"><?php echo e(__('messages.home.trusted_brands')); ?></h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto"><?php echo e(__('messages.home.trusted_brands_desc')); ?></p>
            </div>

            <!-- Carrusel Container -->
            <div class="relative max-w-7xl mx-auto">
                <!-- Botones de navegación -->
                <button id="brands-prev"
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl text-gray-800 dark:text-white p-3 rounded-full transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600">
                    <i class="fas fa-chevron-left text-lg"></i>
                </button>
                <button id="brands-next"
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl text-gray-800 dark:text-white p-3 rounded-full transition-all duration-300 hover:scale-110 border border-gray-200 dark:border-gray-600">
                    <i class="fas fa-chevron-right text-lg"></i>
                </button>

                <!-- Contenedor del carrusel -->
                <div class="overflow-hidden">
                    <div id="brands-carousel" class="flex transition-transform duration-500 ease-in-out">
                                                 <!-- Página 1: Samsung, Apple, Xiaomi -->
                         <div class="flex gap-6 w-full flex-shrink-0">
                                                         <div
                                 class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                 <img src="<?php echo e(asset('img/marcas/samsung.png')); ?>" alt="Samsung"
                                    class="w-full h-16 sm:h-16 lg:h-20 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>SAMSUNG</div>';">
                            </div>
                                                         <div
                                 class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                 <img src="<?php echo e(asset('img/marcas/apple.png')); ?>" alt="Apple"
                                    class="w-full h-12 sm:h-16 lg:h-20 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>APPLE</div>';">
                            </div>
                                                         <div
                                 class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                 <img src="<?php echo e(asset('img/marcas/xiaomi.png')); ?>" alt="Xiaomi"
                                    class="w-full h-12 sm:h-16 lg:h-20 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>XIAOMI</div>';">
                            </div>
                        </div>

                                                 <!-- Página 2: Infinix, Motorola, Oppo -->
                         <div class="flex gap-6 w-full flex-shrink-0">
                                                         <div
                                 class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                 <img src="<?php echo e(asset('img/marcas/infinix.png')); ?>" alt="Infinix"
                                    class="w-full h-16 sm:h-20 lg:h-24 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-16 sm:h-20 lg:h-24 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>INFINIX</div>';">
                            </div>
                                                         <div
                                 class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                 <img src="<?php echo e(asset('img/marcas/motorola.png')); ?>" alt="Motorola"
                                    class="w-full h-16 sm:h-20 lg:h-24 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-16 sm:h-20 lg:h-24 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>MOTOROLA</div>';">
                            </div>
                                                         <div
                                 class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                 <img src="<?php echo e(asset('img/marcas/oppo.png')); ?>" alt="Oppo"
                                    class="w-full h-16 sm:h-20 lg:h-24 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-16 sm:h-20 lg:h-24 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>OPPO</div>';">
                            </div>
                        </div>

                                                 <!-- Página 3: ZTE -->
                         <div class="flex gap-6 w-full flex-shrink-0">
                                                         <div
                                 class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                 <img src="<?php echo e(asset('img/marcas/ZTE.png')); ?>" alt="ZTE"
                                    class="w-full h-10 sm:h-14 lg:h-18 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>ZTE</div>';">
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1 opacity-50">
                                <div
                                    class="w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-400 dark:text-gray-500 font-bold text-xs sm:text-sm lg:text-base">
                                    <i class="fas fa-plus text-3xl"></i>
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1 opacity-50">
                                <div
                                    class="w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-400 dark:text-gray-500 font-bold text-xs sm:text-sm lg:text-base">
                                    <i class="fas fa-plus text-3xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>


    <!-- Productos Destacados - Nueva Implementación -->
    <section class="py-16 bg-gradient-to-br from-gray-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header de la sección -->
            <div class="text-center mb-16">
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-800 dark:text-white mb-6">
                    <?php echo e(__('messages.home.featured_products')); ?>

                </h2>
                <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-300 max-w-4xl mx-auto leading-relaxed mb-8">
                    <?php echo e(__('messages.home.featured_products_desc')); ?>

                </p>
                
                <!-- Información de productos -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4 mb-8 inline-block">
                    <p class="text-sm text-blue-700 dark:text-blue-300 font-medium">
                        <i class="fas fa-info-circle mr-2"></i>
                        <?php echo e(__('messages.home.showing_products', ['count' => count($productosDestacados)])); ?>

                    </p>
                </div>
                
                <!-- Características destacadas -->
                <div class="flex flex-wrap justify-center items-center gap-6 sm:gap-8 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-green-500 mr-2 text-lg"></i>
                        <span class="font-medium"><?php echo e(__('messages.home.quality_guarantee')); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-truck text-blue-500 mr-2 text-lg"></i>
                        <span class="font-medium"><?php echo e(__('messages.home.fast_shipping')); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-headset text-purple-500 mr-2 text-lg"></i>
                        <span class="font-medium"><?php echo e(__('messages.home.customer_support')); ?></span>
                    </div>
                </div>
            </div>

            <!-- Carrusel de Productos -->
            <div class="w-full">

                <!-- Contenedor del carrusel -->
                <div class="overflow-x-auto rounded-2xl bg-white dark:bg-gray-800 shadow-lg dark:shadow-gray-900/50 mx-4 sm:mx-6 lg:mx-8 scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-gray-100 dark:scrollbar-track-gray-700 hover:scrollbar-thumb-blue-600 transition-all duration-300">
                    <div id="products-carousel" class="flex gap-4 sm:gap-6 px-4 sm:px-6 py-4 min-w-max">
                        <?php
                            $totalItems = count($productosDestacados);
                        ?>

                        <?php if($totalItems > 0): ?>
                            <?php $__currentLoopData = $productosDestacados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="min-w-[280px] sm:min-w-[300px] flex-shrink-0">
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
                        <?php else: ?>
                            <!-- Mensaje cuando no hay productos -->
                            <div class="w-full text-center py-16">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-box-open text-6xl mb-6"></i>
                                    <p class="text-xl font-medium"><?php echo e(__('messages.products.no_products')); ?></p>
                                    <p class="text-gray-400 mt-2"><?php echo e(__('messages.products.coming_soon')); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información de productos mostrados -->
                <div class="text-center mt-8">
                    <div class="flex items-center justify-center space-x-4">
                        <!-- Indicador de scroll visual -->
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                            <div class="w-2 h-2 bg-blue-300 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                            <i class="fas fa-arrows-alt-h mr-2"></i>
                            <?php echo e(__('messages.home.scroll_horizontally')); ?>

                        </p>
                        
                        <!-- Indicador de scroll visual -->
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-blue-300 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to action -->
            <div class="text-center mt-16">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-900 rounded-3xl p-8 shadow-2xl dark:shadow-gray-900/50">
                    <h3 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                        <?php echo e(__('messages.home.not_find_what_you_need')); ?>

                    </h3>
                    <p class="text-blue-100 mb-8 text-lg">
                        <?php echo e(__('messages.home.explore_catalog')); ?>

                    </p>
                    <a href="<?php echo e(route('productos.lista')); ?>"
                        class="inline-block bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-th-large mr-2"></i>
                        <?php echo e(__('messages.home.view_all')); ?>

                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Allies Section Mejorado -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-4"><?php echo e(__('messages.home.our_allies')); ?></h2>
                <p class="text-xl text-gray-700 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed"><?php echo e(__('messages.home.our_allies_desc')); ?></p>
            </div>
            <br>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                                 <!-- Ally 1 - Alquería -->
                 <div
                     class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                                         <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="https://yt3.googleusercontent.com/7oj-EkIyJE1hS06HKVpqz7Nzmg4wJEz3Lu9CD4JO39Mzf1k-1XYp-_KMlHJQYZocuBdRGKf7=s900-c-k-c0x00ffffff-no-rj"
                            alt="Alquería" class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">Alquería</h3>
                    <p class="text-gray-600 leading-relaxed">Productos lácteos de la más alta calidad para toda la familia
                    </p>
                </div>

                                 <!-- Ally 2 - Grupo Forma Íntimas -->
                 <div
                     class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                                         <div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="<?php echo e(asset('img/gfi.png')); ?>" alt="Grupo Forma Íntimas"
                            class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">Grupo Forma Íntimas</h3>
                    <p class="text-gray-600 leading-relaxed">Ropa interior femenina con diseños innovadores y cómodos</p>
                </div>

                                 <!-- Ally 3 - Centro Colombo Americano -->
                 <div
                     class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                                         <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="https://pbs.twimg.com/profile_images/827330867097980928/Rm4yC6YI_400x400.jpg"
                            alt="Centro Colombo Americano" class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">Centro Colombo Americano</h3>
                    <p class="text-gray-600 leading-relaxed">Educación y cultura para el desarrollo de habilidades
                        bilingües</p>
                </div>

                                 <!-- Ally 4 - Aderezos -->
                 <div
                     class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                                         <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="https://cdn.shopify.com/s/files/1/0559/1266/1155/files/Logo-aderezos-original_c6d4acd6-d8e7-4356-97f8-0f3eda5c0886.png?v=1625758391"
                            alt="Aderezos" class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">Aderezos</h3>
                    <p class="text-gray-600 leading-relaxed">Salsas y aderezos para realzar el sabor de tus comidas</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Mejorado -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-4"><?php echo e(__('messages.home.testimonials')); ?></h2>
                <p class="text-xl text-gray-700 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed"><?php echo e(__('messages.home.testimonials_desc')); ?></p>
            </div>
            <br>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                 <!-- Testimonial 1 -->
                 <div
                     class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-blue-100 dark:border-gray-600">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Cliente"
                            class="w-16 h-16 rounded-full mr-4 ring-4 ring-blue-200 shadow-lg">
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">María González</h4>
                            <div class="text-yellow-400 text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed text-lg italic">"Excelente servicio, repararon mi celular en
                        menos de una hora y quedó como nuevo. Muy recomendados!"</p>
                </div>

                                 <!-- Testimonial 2 -->
                 <div
                     class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-green-100 dark:border-gray-600">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Cliente"
                            class="w-16 h-16 rounded-full mr-4 ring-4 ring-green-200 shadow-lg">
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">Carlos Martínez</h4>
                            <div class="text-yellow-400 text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed text-lg italic">"Compré un iPhone 14 Pro y el precio fue el
                        mejor que encontré en el mercado. Además, la atención fue muy personalizada."</p>
                </div>

                                 <!-- Testimonial 3 -->
                 <div
                     class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-gray-800 dark:to-gray-700 p-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-purple-100 dark:border-gray-600">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Cliente"
                            class="w-16 h-16 rounded-full mr-4 ring-4 ring-purple-200 shadow-lg">
                        <div>
                            <h4 class="font-bold text-gray-900 text-lg">Laura Rodríguez</h4>
                            <div class="text-yellow-400 text-sm">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 leading-relaxed text-lg italic">"Recuperaron todas mis fotos de un celular que
                        se mojó. Estoy muy agradecida con el equipo de 4GMovil."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Mejorado -->
    <section
        class="py-16 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white relative overflow-hidden">
        <!-- Elementos decorativos -->
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-10 left-10 w-20 h-20 bg-white/10 rounded-full"></div>
            <div class="absolute top-20 right-20 w-32 h-32 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-10 left-1/4 w-16 h-16 bg-white/10 rounded-full"></div>
        </div>

        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="max-w-4xl mx-auto">
                <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                    alt="Contacto" class="w-full h-80 object-cover rounded-2xl mb-8 shadow-2xl">
                <h2 class="text-4xl font-bold mb-6 text-white"><?php echo e(__('messages.home.need_help')); ?></h2>
                <p class="text-xl mb-10 max-w-3xl mx-auto leading-relaxed text-white"><?php echo e(__('messages.home.need_help_desc')); ?></p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo e(route('contactanos')); ?>"
                        class="inline-block bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 hover:scale-105">
                        <i class="fas fa-phone mr-2"></i><?php echo e(__('messages.home.contact_us_now')); ?>

                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Estilos CSS optimizados -->
    <link rel="stylesheet" href="<?php echo e(asset('css/landing-optimized.css')); ?>">
    
    <!-- Estilos personalizados para el scrollbar -->
    <style>
        /* Scrollbar personalizado para Webkit (Chrome, Safari, Edge) */
        .scrollbar-thin::-webkit-scrollbar {
            height: 8px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            transform: scaleY(1.1);
        }
        
        /* Scrollbar personalizado para Firefox */
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #3b82f6 #f3f4f6;
        }
        
        /* Modo oscuro */
        .dark .scrollbar-thin::-webkit-scrollbar-track {
            background: #374151;
        }
        
        .dark .scrollbar-thin {
            scrollbar-color: #3b82f6 #374151;
        }
        
        /* Animación suave para el scroll */
        .scrollbar-thin {
            scroll-behavior: smooth;
        }
        
        /* Indicador de scroll visual */
        .scrollbar-thin::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .scrollbar-thin:hover::after {
            opacity: 1;
        }
    </style>

    <!-- JavaScript optimizado para carruseles -->
    <script src="<?php echo e(asset('js/carousel-manager.js')); ?>"></script>
    <script>
        // Verificación adicional para el carrusel
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Verificando elementos del carrusel...');

            // Verificar que los elementos existen
            const carousel = document.getElementById('products-carousel');
            const prevBtn = document.getElementById('products-prev');
            const nextBtn = document.getElementById('products-next');

            console.log('Carousel:', carousel);
            console.log('Prev button:', prevBtn);
            console.log('Next button:', nextBtn);

            // Si los elementos existen, verificar que el script se cargó
            if (carousel && prevBtn && nextBtn) {
                console.log('Elementos del carrusel encontrados correctamente');

                // Verificar que los event listeners están funcionando
                prevBtn.addEventListener('click', function() {
                    console.log('Botón prev clickeado');
                });

                nextBtn.addEventListener('click', function() {
                    console.log('Botón next clickeado');
                });
            } else {
                console.error('Algunos elementos del carrusel no se encontraron');
            }
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ACER\Documents\GitHub\4GMovil\resources\views/pages/landing/index.blade.php ENDPATH**/ ?>