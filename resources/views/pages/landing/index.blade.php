@extends('layouts.landing')
@section('title', '4GMovil - Productos Destacados')
@section('meta-description',
    'Descubre nuestros productos destacados en 4GMovil. Encuentra lo mejor en tecnología móvil
    y accesorios.')
@section('content')
    @include('components.loading-screen')
    <!-- Hero Section (inspirado en popcorn.space) -->
    <section id="inicio" class="min-h-screen flex items-center relative overflow-hidden pt-16 bg-gradient-to-b from-white to-gray-100 dark:from-gray-900 dark:to-black">

        <!-- Contenido -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-6 items-center">
                <!-- Columna izquierda -->
                <div id="hero_container" class="lg:col-span-11 text-gray-900 dark:text-white group">
                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-gray-900/5 dark:bg-white/10 border border-gray-900/10 dark:border-white backdrop-blur mb-5 motion-safe:animate-pulse group-hover:[animation-play-state:paused]">
                        <span class="text-sm">{{ __('landing.hero.badge') }}</span>
                    </div>
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight tracking-tight">
                        <span class="sr-only">Tecnología a tu alcance</span>
                        <div class="relative">
                            <svg class="block w-full max-w-none h-auto" viewBox="0 0 1600 260" aria-hidden="true" preserveAspectRatio="xMinYMid meet" style="overflow: visible;">
                                <defs>
                                    <linearGradient id="heroTitleGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#9CA3AF"/>
                                        <stop offset="50%" stop-color="#6B7280"/>
                                        <stop offset="100%" stop-color="#374151"/>
                                    </linearGradient>
                                </defs>
                                <text x="0" y="180" fill="transparent" stroke="url(#heroTitleGradient)" stroke-width="4" stroke-linejoin="round" font-weight="800" font-family="'Manrope', 'Inter', 'Segoe UI', ui-sans-serif, system-ui, -apple-system, Roboto, Helvetica, Arial, Noto Sans, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji'" style="letter-spacing:-0.02em; font-size:132px;">
                                    {{ __('landing.hero.title') }}
                                </text>
                            </svg>
                        </div>
                    </h1>
                    <p class="mt-5 text-lg sm:text-xl text-gray-700 dark:text-gray-100/90 max-w-4xl">
                        <span id="hero_subtitle_text">{{ __('landing.hero.phrases')[0] ?? 'Encuentra los mejores productos y servicios de tecnología a tu alcance.' }}</span>
                        <span class="inline-block w-3 align-baseline ml-0.5 bg-gray-700 dark:bg-gray-100 animate-pulse" style="height:1.2em"></span>
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 sm:items-center">
                        <a href="{{ route('productos.lista') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold shadow-lg shadow-blue-600/30 hover:shadow-xl hover:shadow-indigo-600/40 transform hover:-translate-y-1 hover:scale-150 transition-all duration-500 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 hover:ring-4 hover:ring-blue-400/30 dark:hover:ring-indigo-400/30 border-2 border-blue-500/40 hover:border-blue-300/70 dark:border-indigo-400/40 dark:hover:border-indigo-300/70">
                            {{ __('landing.hero.cta_explore') }}
                        </a>
                        
                        
                </div>
            </div>
        </div>

       
        
    </section>
    @push('scripts')
    <script>
    (function(){
        var phrases = @json(trans('landing.hero.phrases'));
        var el = document.getElementById('hero_subtitle_text');
        var container = document.getElementById('hero_container');
        if (!el) return;
        var idx = 0, pos = 0, dir = 1, typing,
            delayTypingBase = 55,   // ms por carácter al escribir
            delayEraseBase = 28,    // ms por carácter al borrar
            delayPause = 5100,      // pausa al final de cada frase
            randomness = 0.25,      // ±25% aleatoriedad en el ritmo
            paused = false;
        function tick(){
            if (paused) { return; }
            var full = phrases[idx];
            if (dir === 1){
                pos++;
                if (pos > full.length){
                    dir = -1;
                    return setTimeout(tick, delayPause);
                }
            } else {
                pos--;
                if (pos < 0){
                    dir = 1; idx = (idx + 1) % phrases.length; pos = 0;
                }
            }
            el.textContent = full.substring(0, Math.max(0, pos));
            // Pausa adicional tras signos de puntuación al escribir
            var extraPause = 0;
            if (dir === 1 && pos > 0) {
                var ch = full.charAt(pos - 1);
                if (/[\.,;:!?]/.test(ch)) {
                    extraPause = 250 + Math.floor(Math.random() * 450); // 250–700ms
                }
            }
            var base = dir === 1 ? delayTypingBase : delayEraseBase;
            var jitter = 1 + ((Math.random() - 0.5) * 2 * randomness);
            var nextDelay = Math.max(8, Math.floor(base * jitter)) + extraPause;
            typing = setTimeout(tick, nextDelay);
        }
        function pause(){ paused = true; if (typing) { clearTimeout(typing); typing = null; } }
        function resume(){ if (!paused) return; paused = false; tick(); }
        if (container){
            container.addEventListener('mouseenter', pause);
            container.addEventListener('mouseleave', resume);
        }
        try { tick(); } catch(e){}
    })();
    </script>
    @endpush

    <!-- Stats Section -->
    <section class="py-12 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-center">
                <div class="space-y-2">
                    <div class="text-4xl md:text-5xl font-bold text-yellow-300">5000+</div>
                    <div class="text-lg font-medium">{{ __('messages.home.satisfied_clients') }}</div>
                </div>
                <div class="space-y-2">
                    <div class="text-4xl md:text-5xl font-bold text-green-300">1000+</div>
                    <div class="text-lg font-medium">{{ __('messages.home.successful_repairs') }}</div>
                </div>
                <div class="space-y-2">
                    <div class="text-4xl md:text-5xl font-bold text-purple-300">50+</div>
                    <div class="text-lg font-medium">{{ __('messages.home.available_brands') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Section Carrusel -->
    <section class="py-16 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-4">{{ __('messages.home.trusted_brands') }}
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    {{ __('messages.home.trusted_brands_desc') }}</p>
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
                                <img src="{{ asset('img/marcas/samsung.png') }}" alt="Samsung"
                                    class="w-full h-16 sm:h-16 lg:h-20 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>SAMSUNG</div>';">
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                <img src="{{ asset('img/marcas/apple.png') }}" alt="Apple"
                                    class="w-full h-12 sm:h-16 lg:h-20 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>APPLE</div>';">
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                <img src="{{ asset('img/marcas/xiaomi.png') }}" alt="Xiaomi"
                                    class="w-full h-12 sm:h-16 lg:h-20 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-12 sm:h-16 lg:h-20 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>XIAOMI</div>';">
                            </div>
                        </div>

                        <!-- Página 2: Infinix, Motorola, Oppo -->
                        <div class="flex gap-6 w-full flex-shrink-0">
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                <img src="{{ asset('img/marcas/infinix.png') }}" alt="Infinix"
                                    class="w-full h-16 sm:h-20 lg:h-24 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-16 sm:h-20 lg:h-24 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>INFINIX</div>';">
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                <img src="{{ asset('img/marcas/motorola.png') }}" alt="Motorola"
                                    class="w-full h-16 sm:h-20 lg:h-24 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-16 sm:h-20 lg:h-24 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>MOTOROLA</div>';">
                            </div>
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                <img src="{{ asset('img/marcas/oppo.png') }}" alt="Oppo"
                                    class="w-full h-16 sm:h-20 lg:h-24 object-contain opacity-70 hover:opacity-100 transition-all duration-300 dark:filter dark:brightness-0 dark:invert"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-16 sm:h-20 lg:h-24 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-xs sm:text-sm lg:text-base\'>OPPO</div>';">
                            </div>
                        </div>

                        <!-- Página 3: ZTE -->
                        <div class="flex gap-6 w-full flex-shrink-0">
                            <div
                                class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-xl sm:rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 flex-1">
                                <img src="{{ asset('img/marcas/ZTE.png') }}" alt="ZTE"
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
    <section
        class="py-16 bg-gradient-to-br from-gray-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header de la sección -->
            <div class="text-center mb-16">
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-800 dark:text-white mb-6">
                    {{ __('messages.home.featured_products') }}
                </h2>
                <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-300 max-w-4xl mx-auto leading-relaxed mb-8">
                    {{ __('messages.home.featured_products_desc') }}
                </p>

                <!-- Información de productos -->
                <div
                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4 mb-8 inline-block">
                    <p class="text-sm text-blue-700 dark:text-blue-300 font-medium">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ __('messages.home.showing_products', ['count' => count($productosDestacados)]) }}
                    </p>
                </div>

                <!-- Características destacadas -->
                <div
                    class="flex flex-wrap justify-center items-center gap-6 sm:gap-8 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-green-500 mr-2 text-lg"></i>
                        <span class="font-medium">{{ __('messages.featured_products.warranty_info') }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-credit-card text-blue-500 mr-2 text-lg"></i>
                        <span class="font-medium">{{ __('messages.featured_products.payment_info') }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-undo text-orange-500 mr-2 text-lg"></i>
                        <span class="font-medium">{{ __('messages.featured_products.return_info') }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-truck text-purple-500 mr-2 text-lg"></i>
                        <span class="font-medium">{{ __('messages.featured_products.shipping_info') }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-headset text-indigo-500 mr-2 text-lg"></i>
                        <span class="font-medium">{{ __('messages.featured_products.support_info') }}</span>
                    </div>
                </div>
            </div>

            <!-- Carrusel de Productos -->
            <div class="w-full">

                <!-- Contenedor del carrusel -->
                <div
                    class="overflow-x-auto rounded-2xl bg-white dark:bg-gray-800 shadow-lg dark:shadow-gray-900/50 mx-4 sm:mx-6 lg:mx-8 scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-gray-100 dark:scrollbar-track-gray-700 hover:scrollbar-thumb-blue-600 transition-all duration-300">
                    <div id="products-carousel" class="flex gap-4 sm:gap-6 px-4 sm:px-6 py-4 min-w-max">
                        @php
                            $totalItems = count($productosDestacados);
                        @endphp

                        @if ($totalItems > 0)
                            @foreach ($productosDestacados as $producto)
                                <div class="min-w-[280px] sm:min-w-[300px] flex-shrink-0">
                                    <x-product-card :producto="$producto" />
                                </div>
                            @endforeach
                        @else
                            <!-- Mensaje cuando no hay productos -->
                            <div class="w-full text-center py-16">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-box-open text-6xl mb-6"></i>
                                    <p class="text-xl font-medium">{{ __('messages.products.no_products') }}</p>
                                    <p class="text-gray-400 mt-2">{{ __('messages.products.coming_soon') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información de productos mostrados -->
                <div class="text-center mt-8">
                    <div class="flex items-center justify-center space-x-4">
                        <!-- Indicador de scroll visual -->
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.2s;">
                            </div>
                            <div class="w-2 h-2 bg-blue-300 rounded-full animate-pulse" style="animation-delay: 0.4s;">
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                            <i class="fas fa-arrows-alt-h mr-2"></i>
                            {{ __('messages.home.scroll_horizontally') }}
                        </p>

                        <!-- Indicador de scroll visual -->
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-blue-300 rounded-full animate-pulse" style="animation-delay: 0.4s;">
                            </div>
                            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.2s;">
                            </div>
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Call to action -->
            <div class="text-center mt-16">
                <div
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-800 dark:to-indigo-900 rounded-3xl p-8 shadow-2xl dark:shadow-gray-900/50">
                    <h3 class="text-2xl sm:text-3xl font-bold text-white mb-4">
                        {{ __('messages.home.not_find_what_you_need') }}
                    </h3>
                    <p class="text-blue-100 mb-8 text-lg">
                        {{ __('messages.home.explore_catalog') }}
                    </p>
                    <a href="{{ route('productos.lista') }}"
                        class="inline-block bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-th-large mr-2"></i>
                        {{ __('messages.home.view_all') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Allies Section Mejorado -->
    <section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 dark:text-white mb-4">{{ __('messages.home.our_allies') }}
                </h2>
                <p class="text-xl text-gray-700 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    {{ __('messages.home.our_allies_desc') }}</p>
            </div>
            <br>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Ally 1 - Alquería -->
                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                    <div
                        class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="https://yt3.googleusercontent.com/7oj-EkIyJE1hS06HKVpqz7Nzmg4wJEz3Lu9CD4JO39Mzf1k-1XYp-_KMlHJQYZocuBdRGKf7=s900-c-k-c0x00ffffff-no-rj"
                            alt="Alquería" class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">{{ __('messages.allies.alqueria.name') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.allies.alqueria.description') }}</p>
                </div>

                <!-- Ally 2 - Grupo Forma Íntimas -->
                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                    <div
                        class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="{{ asset('img/gfi.png') }}" alt="Grupo Forma Íntimas"
                            class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">{{ __('messages.allies.grupo_forma_intimas.name') }}
                    </h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.allies.grupo_forma_intimas.description') }}
                    </p>
                </div>

                <!-- Ally 3 - Centro Colombo Americano -->
                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                    <div
                        class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="https://pbs.twimg.com/profile_images/827330867097980928/Rm4yC6YI_400x400.jpg"
                            alt="Centro Colombo Americano" class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">
                        {{ __('messages.allies.centro_colombo_americano.name') }}</h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ __('messages.allies.centro_colombo_americano.description') }}</p>
                </div>

                <!-- Ally 4 - Aderezos -->
                <div
                    class="bg-white dark:bg-gray-800 p-8 rounded-2xl text-center shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 border border-gray-100 dark:border-gray-700">
                    <div
                        class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-6 rounded-2xl mb-6">
                        <img src="https://cdn.shopify.com/s/files/1/0559/1266/1155/files/Logo-aderezos-original_c6d4acd6-d8e7-4356-97f8-0f3eda5c0886.png?v=1625758391"
                            alt="Aderezos" class="w-full h-20 object-contain">
                    </div>
                    <h3 class="text-xl font-bold mb-4 text-gray-900">{{ __('messages.allies.aderezos.name') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.allies.aderezos.description') }}</p>
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
                <h2 class="text-4xl font-bold mb-6 text-white">{{ __('messages.home.need_help') }}</h2>
                <p class="text-xl mb-10 max-w-3xl mx-auto leading-relaxed text-white">
                    {{ __('messages.home.need_help_desc') }}</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('contactanos') }}"
                        class="inline-block bg-white text-blue-600 px-8 py-4 rounded-xl font-bold hover:bg-gray-100 transition-all duration-300 shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 hover:scale-105">
                        <i class="fas fa-phone mr-2"></i>{{ __('messages.home.contact_us_now') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Estilos CSS optimizados -->
    <link rel="stylesheet" href="{{ asset('css/landing-optimized.css') }}">

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
    <script src="{{ asset('js/carousel-manager.js') }}"></script>
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

@endsection
