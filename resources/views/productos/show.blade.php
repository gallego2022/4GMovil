@extends('layouts.landing')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', $producto->nombre_producto . ' - 4G Móvil')
@section('meta_description', $producto->descripcion ?? 'Descubre ' . $producto->nombre_producto . ' en 4G Móvil. Calidad
    garantizada y los mejores precios en Medellín.')

    @push('styles')
        <style>
            /* ===== ESTILO NEUTRO Y PROFESIONAL ===== */
            
            /* Variables CSS personalizadas */
            :root {
                --neutral-primary: #374151;
                --neutral-primary-dark: #1f2937;
                --neutral-primary-light: #6b7280;
                --neutral-secondary: #4b5563;
                --neutral-accent: #8b5cf6;
                --neutral-success: #059669;
                --neutral-warning: #d97706;
                --neutral-danger: #dc2626;
                --neutral-gray-light: #f9fafb;
                --neutral-gray-dark: #111827;
                --neutral-text-primary: #111827;
                --neutral-text-secondary: #6b7280;
                --neutral-border: #d1d5db;
                --neutral-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                --neutral-shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
            }

            /* Modo oscuro */
            .dark {
                --4g-gray-light: #1e293b;
                --4g-gray-dark: #0f172a;
                --4g-text-primary: #f1f5f9;
                --4g-text-secondary: #94a3b8;
                --4g-border: #475569;
            }

            /* ===== GALERÍA DE PRODUCTOS - ESTILOS COMPLETAMENTE NUEVOS ===== */
            
            /* Contenedor principal de la galería */
            .product-gallery-container {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2rem;
            }

            /* Contenedor de la imagen principal */
            .main-image-wrapper {
                position: relative;
                width: 100%;
                height: 300px;
                border-radius: 1rem;
                background: #f9fafb;
                border: 1px solid #d1d5db;
                overflow: hidden;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }

            .dark .main-image-wrapper {
                background: #1f2937;
                border-color: #475569;
            }

            /* Imagen principal */
            .main-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
                display: block;
            }

            /* Contenedor de thumbnails */
            .thumbnail-gallery-wrapper {
                width: 100%;
                padding: 1rem 0;
            }

            /* Grid de thumbnails */
            .thumbnail-grid {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                gap: 1rem;
                overflow-x: auto;
                padding: 0.5rem 0;
                align-items: center;
                min-height: 5rem;
            }

            /* Scrollbar personalizado */
            .thumbnail-grid::-webkit-scrollbar {
                height: 4px;
            }

            .thumbnail-grid::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 2px;
            }

            .thumbnail-grid::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 2px;
            }

            .thumbnail-grid::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            /* Items de thumbnail */
            .thumbnail-item {
                width: 5rem;
                height: 5rem;
                flex-shrink: 0;
                border-radius: 0.75rem;
                border: 2px solid transparent;
                cursor: pointer;
                background: #f9fafb;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .dark .thumbnail-item {
                background: #374151;
            }

            .thumbnail-item:hover {
                transform: scale(1.05);
                border-color: #374151;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .thumbnail-item.border-blue-500 {
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            }

            /* Imágenes dentro de thumbnails */
            .thumbnail-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            /* Puntos de paginación */
            .pagination-dots {
                display: flex;
                justify-content: center;
                gap: 0.5rem;
                margin-top: 1rem;
            }

            .pagination-dots > div {
                width: 0.5rem;
                height: 0.5rem;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .pagination-dots > div.bg-blue-600 {
                background-color: #3b82f6;
                transform: scale(1.1);
            }

            .pagination-dots > div.bg-gray-300 {
                background-color: #d1d5db;
            }

            .pagination-dots > div.bg-gray-600 {
                background-color: #6b7280;
            }

            .pagination-dots > div:hover {
                transform: scale(1.05);
            }

            /* Responsive */
            @media (max-width: 640px) {
                .main-image-wrapper {
                    height: 250px;
                }
                
                .thumbnail-item {
                    width: 4rem;
                    height: 4rem;
                }
            }

            @media (min-width: 641px) and (max-width: 768px) {
                .main-image-wrapper {
                    height: 280px;
                }
            }

            @media (min-width: 769px) and (max-width: 1024px) {
                .main-image-wrapper {
                    height: 320px;
                }
            }

            @media (min-width: 1025px) {
                .main-image-wrapper {
                    height: 350px;
                }
            }

            .pagination-dots > div.bg-blue-600 {
                background-color: var(--neutral-primary) !important;
                transform: scale(1.1) !important;
                box-shadow: 0 0 8px rgba(139, 92, 246, 0.3) !important;
            }

            .pagination-dots > div.bg-blue-600::before {
                width: 100% !important;
                height: 100% !important;
            }

            .pagination-dots > div.bg-gray-300 {
                background-color: var(--neutral-border) !important;
            }

            .pagination-dots > div.bg-gray-600 {
                background-color: var(--neutral-text-secondary) !important;
            }

            .pagination-dots > div:hover {
                transform: scale(1.05) !important;
                background-color: var(--neutral-primary-light) !important;
            }

            /* Responsive Design - Estilo Neutro */
            @media (max-width: 640px) {
                .main-image-wrapper {
                    height: 250px !important;
                    border-radius: 0.75rem !important;
                }
                
                .thumbnail-item {
                    width: 4rem !important;
                    height: 4rem !important;
                    border-radius: 0.5rem !important;
                    min-width: 4rem !important;
                    min-height: 4rem !important;
                    max-width: 4rem !important;
                    max-height: 4rem !important;
                }

                .thumbnail-grid {
                    gap: 0.75rem !important;
                    padding: 0.5rem 0 !important;
                }

                .pagination-dots > div {
                    width: 0.375rem !important;
                    height: 0.375rem !important;
                }
            }

            @media (min-width: 641px) and (max-width: 768px) {
                .main-image-wrapper {
                    height: 280px !important;
                }

                .thumbnail-grid {
                    gap: 1.25rem !important;
                }

                .pagination-dots > div {
                    width: 0.625rem !important;
                    height: 0.625rem !important;
                }
            }

            @media (min-width: 769px) and (max-width: 1024px) {
                .main-image-wrapper {
                    height: 320px !important;
                }

                .thumbnail-item {
                    width: 5.5rem !important;
                    height: 5.5rem !important;
                }
            }

            @media (min-width: 1025px) {
                .main-image-wrapper {
                    height: 350px !important;
                }

                .thumbnail-item {
                    width: 6rem !important;
                    height: 6rem !important;
                }
            }

            /* ===== ESTILOS ADICIONALES PARA LA GALERÍA ===== */
            
            /* Touch-friendly interactions for mobile */
            @media (max-width: 768px) {
                .thumbnail-grid {
                    -webkit-overflow-scrolling: touch;
                    scrollbar-width: none;
                }
                
                .thumbnail-grid::-webkit-scrollbar {
                    display: none;
                }
                
                .thumbnail-item {
                    touch-action: manipulation;
                }
                
                .main-image-wrapper {
                    touch-action: pan-x pan-y;
                }
            }

            /* Focus states for accessibility */
            .thumbnail-item:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
            }

            .pagination-dots > div:focus {
                outline: 2px solid #3b82f6;
                outline-offset: 2px;
            }

            /* ===== ESTILOS DEL MODAL DE ZOOM ===== */
            
            #imageZoomModal {
                backdrop-filter: blur(12px);
                background: rgba(15, 23, 42, 0.95);
            }

            #zoomImage {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                cursor: grab;
                border-radius: 1rem;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }

            #zoomImage:active {
                cursor: grabbing;
            }

            /* Botones del modal */
            #imageZoomModal button {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            #imageZoomModal button:hover {
                transform: scale(1.15);
                background: rgba(255, 255, 255, 0.2);
                border-color: #3b82f6;
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            }

            /* Responsive para el modal */
            @media (max-width: 768px) {
                #imageZoomModal .absolute.top-4.left-4 {
                    top: 1rem;
                    left: 1rem;
                }

                #imageZoomModal .absolute.top-4.right-4 {
                    top: 1rem;
                    right: 1rem;
                }

                #imageZoomModal .absolute.left-4 {
                    left: 1rem;
                }

                #imageZoomModal .absolute.right-4 {
                    right: 1rem;
                }

                #imageZoomModal button {
                    padding: 0.75rem;
                    font-size: 0.875rem;
                }

                #zoomImage {
                    border-radius: 0.75rem;
                }
            }


        </style>
    @endpush

@section('content')
  
    
    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 py-3 bg-gray-100 dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('landing') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-angle-right text-gray-400 mx-2"></i>
                        <a href="{{ route('productos.lista') }}"
                            class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 md:ml-2 transition-colors duration-200">
                            Catálogo
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-angle-right text-gray-400 mx-2"></i>
                        <span
                            class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">{{ $producto->nombre_producto }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Product Details Section - Diseño Completamente Nuevo -->
    <section id="producto" class="py-12 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Product Main Container -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    
                    <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 p-6 sm:p-8 lg:p-12">
                        <!-- Left Section - Product Images -->
                        <div class="space-y-6">
                            <!-- Main Image Container -->
                            <div class="relative group bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-600 shadow-inner">
                                <div class="aspect-square w-full flex items-center justify-center p-8">
                                    @if ($producto->imagenes->count() > 0)
                                        <img id="mainImage"
                                            src="{{ $producto->imagenes->first()->url_completa }}"
                                            alt="{{ $producto->nombre_producto }}"
                                            class="w-full h-full object-contain transition-all duration-500 group-hover:scale-110"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="hidden w-full h-full flex items-center justify-center">
                                            <i class="fas fa-image text-6xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-image text-6xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Navigation Arrows -->
                                @if ($producto->imagenes->count() > 1)
                                    <button id="prevImage" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white dark:bg-gray-800 p-3 rounded-full shadow-xl border border-gray-200 dark:border-gray-600 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:scale-110 z-10">
                                        <i class="fas fa-chevron-left text-gray-700 dark:text-gray-300 text-lg"></i>
                                    </button>
                                    <button id="nextImage" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white dark:bg-gray-800 p-3 rounded-full shadow-xl border border-gray-200 dark:border-gray-600 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:scale-110 z-10">
                                        <i class="fas fa-chevron-right text-gray-700 dark:text-gray-300 text-lg"></i>
                                    </button>
                                @endif

                                <!-- Zoom Button -->
                                <button onclick="openImageZoom()" class="absolute top-4 right-4 bg-white dark:bg-gray-800 px-4 py-2 rounded-full text-sm font-medium shadow-lg border border-gray-200 dark:border-gray-600 opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-gray-50 dark:hover:bg-gray-700 z-10">
                                    <i class="fas fa-search-plus mr-2 text-gray-700 dark:text-gray-300"></i>
                                    <span class="text-gray-700 dark:text-gray-300">Zoom</span>
                                </button>
                            </div>

                            <!-- Thumbnail Gallery -->
                            @if ($producto->imagenes->count() > 1)
                                <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                                    @foreach ($producto->imagenes->take(5) as $index => $imagen)
                                        <button class="thumbnail-btn flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 transition-all duration-300 {{ $index === 0 ? 'border-blue-600 ring-4 ring-blue-200 dark:ring-blue-800 shadow-lg scale-105' : 'border-gray-200 dark:border-gray-600 hover:border-blue-400 hover:scale-105' }}"
                                                data-image-index="{{ $index }}"
                                                data-image-url="{{ $imagen->url_completa }}">
                                            <img src="{{ $imagen->url_completa }}" 
                                                 alt="{{ $producto->nombre_producto }} - Vista {{ $index + 1 }}"
                                                 class="w-full h-full object-cover"
                                                 loading="lazy"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="hidden w-full h-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 dark:text-gray-500"></i>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Right Section - Product Details -->
                        <div class="space-y-6 lg:space-y-8">
                            <!-- Product Title and Badges -->
                            <div class="space-y-4">
                                <div class="flex items-start justify-between gap-4">
                                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight flex-1">
                                        {{ $producto->nombre_producto }}
                                    </h1>
                                    <!-- Action Icons -->
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <button class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group border border-gray-200 dark:border-gray-600">
                                            <i class="fas fa-heart text-gray-600 dark:text-gray-400 group-hover:text-red-500 transition-colors duration-200"></i>
                                        </button>
                                        <button class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group border border-gray-200 dark:border-gray-600">
                                            <i class="fas fa-share-alt text-gray-600 dark:text-gray-400 group-hover:text-blue-500 transition-colors duration-200"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Badges -->
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold rounded-lg border border-gray-200 dark:border-gray-600">
                                        <i class="fas fa-barcode mr-2 text-gray-600 dark:text-gray-400"></i>
                                        SKU: <span class="ml-1 font-mono">{{ $producto->sku ?? 'N/A' }}</span>
                                    </span>
                                    <span class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-sm font-semibold rounded-lg border border-green-200 dark:border-green-700">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        {{ __('messages.product_show.available') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Pricing Section -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border-2 border-blue-200 dark:border-blue-800">
                                <div class="space-y-4">
                                    <div class="flex items-baseline gap-4 flex-wrap">
                                        <span class="text-5xl sm:text-6xl font-black text-gray-900 dark:text-white">
                                            {{ \App\Helpers\CurrencyHelper::formatPrice($producto->precio ?? 0) }}
                                        </span>
                                        @if (isset($producto->precio_anterior) && $producto->precio_anterior > $producto->precio)
                                            <div class="flex flex-col gap-1">
                                                <span class="text-xl text-gray-500 dark:text-gray-400 line-through font-semibold">
                                                    {{ \App\Helpers\CurrencyHelper::formatPrice($producto->precio_anterior) }}
                                                </span>
                                                <span class="bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold shadow-lg">
                                                    -{{ round((($producto->precio_anterior - $producto->precio) / $producto->precio_anterior) * 100) }}% OFF
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Quick Info -->
                                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-blue-200 dark:border-blue-700">
                                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-blue-100 dark:border-blue-800">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Envío</span>
                                            <span class="text-sm font-bold text-green-600 dark:text-green-400">GRATIS</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-blue-100 dark:border-blue-800">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('messages.product_show.warranty') }}</span>
                                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('messages.product_show.warranty_period') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Variant Info -->
                            @if ($producto->variantes->count() > 0)
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 border-2 border-purple-200 dark:border-purple-800">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-2xl border-4 border-purple-500 shadow-xl flex-shrink-0" id="selectedColorPreview"
                                            style="background-color: {{ $producto->variantes->first()->codigo_color ?? '#000000' }};">
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-palette text-purple-600 dark:text-purple-400 text-lg"></i>
                                                <p class="text-base font-bold text-gray-900 dark:text-white">
                                                    {{ __('messages.product_show.color') }}: <span id="selectedColorText" class="text-purple-700 dark:text-purple-300">{{ $producto->variantes->first()->nombre }}</span>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-boxes text-green-600 dark:text-green-400"></i>
                                                <p class="text-sm text-gray-700 dark:text-gray-300" id="selectedColorStock">
                                                    {{ __('messages.product_show.stock_available') }}: <span class="font-bold text-green-700 dark:text-green-400">{{ $producto->variantes->first()->stock }} {{ __('messages.product_show.stock_units') }}</span>
                                                </p>
                                            </div>
                                            @if ($producto->variantes->first()->precio_adicional > 0)
                                                <p class="text-sm text-purple-700 dark:text-purple-400 font-semibold" id="selectedColorPrice">
                                                    <i class="fas fa-plus-circle mr-1"></i>
                                                    {{ __('messages.product_show.additional_price') }}: <span id="precioAdicional" class="font-bold">{{ \App\Helpers\CurrencyHelper::formatPrice($producto->variantes->first()->precio_adicional) }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Color Variants -->
                            @if ($producto->variantes->count() > 0)
                                <div class="space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <i class="fas fa-palette text-white"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('messages.product_show.select_color') }}</h3>
                                    </div>
                                    
                                    <div class="flex flex-wrap items-center gap-3">
                                        @foreach ($producto->variantes as $index => $variante)
                                            <div class="relative group">
                                                <button class="color-variant w-12 h-12 sm:w-14 sm:h-14 rounded-full border-2 transition-all duration-300 {{ $index === 0 ? 'border-blue-600 ring-2 ring-blue-200 dark:ring-blue-800 shadow-lg scale-110' : 'border-gray-300 dark:border-gray-600 hover:border-blue-500 hover:scale-110 hover:shadow-md' }} {{ !$variante->disponible ? 'opacity-50 cursor-not-allowed grayscale' : '' }}"
                                                        style="background-color: {{ $variante->codigo_color ?? '#CCCCCC' }};"
                                                        data-variante-id="{{ $variante->variante_id }}"
                                                        data-variant-stock="{{ $variante->stock }}"
                                                        data-color="{{ $variante->nombre }}"
                                                        data-precio-adicional="{{ $variante->precio_adicional }}"
                                                        data-descripcion="{{ $variante->descripcion }}"
                                                        data-codigo-color="{{ $variante->codigo_color }}"
                                                        data-has-images="{{ $variante->imagenes->count() > 0 ? 'true' : 'false' }}"
                                                        title="{{ $variante->nombre }} - Stock: {{ $variante->stock }}">
                                                </button>

                                                <!-- Stock indicator badge -->
                                                @if ($variante->stock <= 5 && $variante->stock > 0)
                                                    <div class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-lg border-2 border-white dark:border-gray-800 z-10">
                                                        {{ $variante->stock }}
                                                    </div>
                                                @elseif($variante->stock == 0)
                                                    <div class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center shadow-lg border-2 border-white dark:border-gray-800 z-10">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Color Description -->
                            @if ($producto->variantes->count() > 0 && $producto->variantes->first()->descripcion)
                                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-2xl p-5 border-2 border-indigo-200 dark:border-indigo-800">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                            <i class="fas fa-info-circle text-white"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-bold text-indigo-900 dark:text-indigo-100 mb-2">{{ __('messages.product_show.color_description') }}</h4>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" id="colorDescription">
                                                {{ $producto->variantes->first()->descripcion }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Quantity Selector and Add to Cart -->
                            <div class="space-y-5">
                                @php
                                    $tieneVariantes = $producto->variantes && $producto->variantes->count() > 0;
                                    $stockDisponible = $producto->stock_disponible;
                                    $primeraVariante = $producto->variantes->first();
                                @endphp

                                @if ($stockDisponible > 0)
                                    <!-- Quantity Selector -->
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-5 border-2 border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center justify-between">
                                            <label class="text-base font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                                <i class="fas fa-shopping-bag text-gray-600 dark:text-gray-400"></i>
                                                Cantidad:
                                            </label>
                                            <div class="flex items-center gap-3">
                                                <button type="button" 
                                                        id="decrementQuantity" 
                                                        class="quantity-btn w-12 h-12 rounded-xl bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-blue-500 transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-gray-300 shadow-md"
                                                        disabled>
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" 
                                                       id="productQuantity" 
                                                       name="cantidad" 
                                                       value="1" 
                                                       min="1" 
                                                       max="{{ $tieneVariantes ? ($primeraVariante->stock ?? 100) : ($producto->stock_disponible ?? 100) }}" 
                                                       class="w-24 text-center text-xl font-bold text-gray-900 dark:text-white bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-md">
                                                <button type="button" 
                                                        id="incrementQuantity" 
                                                        class="quantity-btn w-12 h-12 rounded-xl bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-blue-500 transition-all duration-200 flex items-center justify-center shadow-md">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
<br>

                                    <!-- Add to Cart Button -->
                                    <button type="button"
                                            id="addToCartBtn"
                                            class="add-to-cart w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white py-5 px-6 rounded-2xl font-bold text-lg transition-all duration-300 hover:shadow-2xl hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 shadow-xl border-2 border-green-700 dark:border-green-600"
                                            data-id="{{ $producto->producto_id }}" 
                                            data-name="{{ $producto->nombre_producto }}" 
                                            data-price="{{ $producto->precio }}"
                                            data-producto-id="{{ $producto->producto_id }}" 
                                            data-producto-nombre="{{ $producto->nombre_producto }}" 
                                            data-producto-precio="{{ $producto->precio }}"
                                            @if($tieneVariantes) data-variante-id="{{ $primeraVariante->variante_id }}" @endif>
                                        <i class="fas fa-shopping-cart mr-3"></i>
                                        {{ __('messages.product_show.add_to_cart') }}
                                    </button>
                                @else
                                    <button type="button"
                                        class="w-full bg-gray-400 dark:bg-gray-600 text-white py-5 px-6 rounded-2xl font-bold text-lg cursor-not-allowed opacity-75 shadow-lg" disabled>
                                        <i class="fas fa-times mr-3"></i>
                                        {{ __('messages.product_show.out_of_stock') }}
                                    </button>
                                @endif
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex flex-wrap gap-3 pt-2">
                                <a href="#reseñas" class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-yellow-100 to-orange-100 dark:from-yellow-900/30 dark:to-orange-900/30 text-yellow-800 dark:text-yellow-300 rounded-xl hover:from-yellow-200 hover:to-orange-200 dark:hover:from-yellow-800/30 dark:hover:to-orange-800/30 transition-all duration-200 font-semibold border-2 border-yellow-200 dark:border-yellow-800 shadow-md">
                                    <i class="fas fa-star mr-2 text-yellow-600 dark:text-yellow-400"></i>
                                    <span>{{ __('messages.product_show.view_reviews') }} ({{ $producto->resenas->where('activa', true)->count() }})</span>
                                </a>
                                <button onclick="openReviewModal()" class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 text-orange-800 dark:text-orange-300 rounded-xl hover:from-orange-200 hover:to-red-200 dark:hover:from-orange-800/30 dark:hover:to-red-800/30 transition-all duration-200 font-semibold border-2 border-orange-200 dark:border-orange-800 shadow-md">
                                    <i class="fas fa-edit mr-2 text-orange-600 dark:text-orange-400"></i>
                                    <span>Escribir Reseña</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Info -->
                    <div class="px-6 sm:px-8 lg:px-12 pb-8 lg:pb-12">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-6 lg:p-8 border-2 border-gray-200 dark:border-gray-600">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-shield-halved text-white"></i>
                                </div>
                                Información Adicional
                            </h3>
                            <div class="grid md:grid-cols-3 gap-5">
                                <div class="flex items-start gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border-2 border-green-200 dark:border-green-800 shadow-md hover:shadow-lg transition-shadow duration-200">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <i class="fas fa-check text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-base font-bold text-gray-800 dark:text-gray-200 block mb-1">{{ __('messages.product_show.warranty_official') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ __('messages.product_show.warranty_description') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border-2 border-blue-200 dark:border-blue-800 shadow-md hover:shadow-lg transition-shadow duration-200">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <i class="fas fa-undo text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-base font-bold text-gray-800 dark:text-gray-200 block mb-1">{{ __('messages.product_show.return_policy') }}</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ __('messages.product_show.return_description') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border-2 border-purple-200 dark:border-purple-800 shadow-md hover:shadow-lg transition-shadow duration-200">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                        <i class="fas fa-headset text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <span class="text-base font-bold text-gray-800 dark:text-gray-200 block mb-1">Soporte Técnico</span>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Incluido 24/7</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Specifications -->
    <section id="especificaciones" class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <span
                        class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-cogs mr-2"></i>Especificaciones Técnicas
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ __('messages.product_show.product_features') }}
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Conoce todos los detalles técnicos y especificaciones de {{ $producto->nombre_producto }}
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-600 p-8">
                    
                    @if($producto->especificaciones && $producto->especificaciones->count() > 0)
                        <!-- Dynamic Specifications Grid -->
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($producto->especificaciones as $especProducto)
                                @php
                                    $espec = $especProducto->especificacionCategoria;
                                    $valor = $especProducto->valor;
                                    $iconClass = '';
                                    $bgClass = '';
                                    
                                    // Asignar iconos y colores según el tipo de especificación
                                    switch($espec->nombre_campo) {
                                        case 'pantalla':
                                        case 'resolucion':
                                            $iconClass = 'fas fa-mobile-alt';
                                            $bgClass = 'from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20';
                                            break;
                                        case 'ram':
                                        case 'almacenamiento':
                                        case 'procesador':
                                            $iconClass = 'fas fa-microchip';
                                            $bgClass = 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20';
                                            break;
                                        case 'camara_principal':
                                        case 'camara_frontal':
                                        case 'bateria':
                                            $iconClass = 'fas fa-camera';
                                            $bgClass = 'from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20';
                                            break;
                                        case 'sistema_operativo':
                                        case 'version_os':
                                            $iconClass = 'fas fa-desktop';
                                            $bgClass = 'from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20';
                                            break;
                                        case 'conectividad':
                                        case 'wifi':
                                        case 'bluetooth':
                                            $iconClass = 'fas fa-wifi';
                                            $bgClass = 'from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20';
                                            break;
                                        default:
                                            $iconClass = 'fas fa-info-circle';
                                            $bgClass = 'from-gray-50 to-gray-100 dark:from-gray-900/20 dark:to-gray-800/20';
                                    }
                                @endphp
                                
                                <div class="bg-gradient-to-r {{ $bgClass }} rounded-xl p-6 border border-gray-200 dark:border-gray-600 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center mb-3">
                                        <div class="w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                                            <i class="{{ $iconClass }} text-gray-600 dark:text-gray-400"></i>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg">
                                            {{ $espec->etiqueta }}
                                        </h3>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Valor:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                @if($espec->tipo_campo === 'checkbox')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $valor == '1' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                                        <i class="fas {{ $valor == '1' ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                                        {{ $valor == '1' ? 'Sí' : 'No' }}
                                                    </span>
                                                @else
                                                    {{ $valor }}
                                                    @if($espec->unidad)
                                                        <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">{{ $espec->unidad }}</span>
                                                    @endif
                                                @endif
                                            </span>
                                        </div>
                                        
                                        @if($espec->descripcion)
                                            <div class="mt-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    {{ $espec->descripcion }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Specifications Summary -->
                        <div class="mt-8 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-chart-bar text-blue-600 mr-3"></i>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Resumen de Especificaciones</h3>
                                </div>
                                <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $producto->especificaciones->count() }} características
                                </span>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">
                                Este producto cuenta con {{ $producto->especificaciones->count() }} especificaciones técnicas detalladas que garantizan su calidad y rendimiento.
                            </p>
                        </div>
                        
                    @else
                        <!-- No Specifications Available -->
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-info-circle text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                Especificaciones no disponibles
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                                Las especificaciones técnicas de este producto aún no han sido configuradas. 
                                Contacta con nuestro equipo para obtener más información.
                            </p>
                        </div>
                    @endif

                    <!-- Product Information -->
                    <div class="mt-8 grid md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
                            <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                Información Básica
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Categoría</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $producto->categoria->nombre ?? 'No especificado' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Marca</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $producto->marca->nombre ?? 'No especificado' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Estado</span>
                                    <span class="text-gray-600 dark:text-gray-400 capitalize">{{ $producto->estado ?? 'No especificado' }}</span>
                                </div>
                                @if($producto->sku)
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">SKU</span>
                                    <span class="text-gray-600 dark:text-gray-400 font-mono">{{ $producto->sku }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Physical Information -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6">
                            <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-ruler-combined text-green-600 mr-2"></i>
                                Información Física
                            </h3>
                            <div class="space-y-3">
                                @if($producto->peso)
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Peso</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $producto->peso }} kg</span>
                                </div>
                                @endif
                                @if($producto->dimensiones)
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Dimensiones</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $producto->dimensiones }}</span>
                                </div>
                                @endif
                                @if($producto->codigo_barras)
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Código de Barras</span>
                                    <span class="text-gray-600 dark:text-gray-400 font-mono">{{ $producto->codigo_barras }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.product_show.stock_available') }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $producto->stock_disponible }} {{ __('messages.product_show.stock_units') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reseñas" class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <span
                        class="inline-flex items-center px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-star mr-2"></i>{{ __('messages.product_show.customer_reviews') }}
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        Lo que dicen nuestros clientes
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Descubre las experiencias de otros usuarios con {{ $producto->nombre_producto }}
                    </p>
                </div>

                <!-- Reviews Summary -->
                @php
                    $resenasActivas = $producto->resenas->where('activa', true);
                    $promedioCalificacion = $resenasActivas->avg('calificacion') ?? 0;
                    $totalResenas = $resenasActivas->count();
                @endphp
                <div
                    class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-2xl p-8 mb-12">
                    <div class="grid md:grid-cols-3 gap-8 items-center">
                        <!-- Overall Rating -->
                        <div class="text-center">
                            <div class="text-5xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">
                                {{ number_format($promedioCalificacion, 1) }}
                            </div>
                            <div class="flex justify-center mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fas fa-star {{ $i <= $promedioCalificacion ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Basado en {{ $totalResenas }} {{ $totalResenas === 1 ? 'reseña' : 'reseñas' }}
                            </p>
                            @if($resenasActivas->where('verificada', true)->count() > 0)
                                <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ $resenasActivas->where('verificada', true)->count() }} {{ $resenasActivas->where('verificada', true)->count() === 1 ? 'verificada' : 'verificadas' }}
                                </p>
                            @endif
                        </div>

                        <!-- Rating Breakdown -->
                        <div class="space-y-2">
                            @for ($rating = 5; $rating >= 1; $rating--)
                                @php
                                    $count = $resenasActivas->where('calificacion', $rating)->count();
                                    $percentage =
                                        $totalResenas > 0
                                            ? ($count / $totalResenas) * 100
                                            : 0;
                                @endphp
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-600 dark:text-gray-400 w-8">{{ $rating }}★</span>
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 w-12">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>

                        <!-- Write Review Button -->
                        <div class="text-center">
                            <button onclick="openReviewModal()"
                                class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-edit mr-2"></i>Escribir Reseña
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="space-y-6">
                    @forelse ($producto->resenas->where('activa', true)->take(5) as $resena)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 p-6 {{ $resena->verificada ? 'ring-2 ring-purple-200 dark:ring-purple-800' : '' }}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold relative">
                                        {{ strtoupper(substr($resena->usuario->nombre ?? 'U', 0, 1)) }}
                                        @if($resena->verificada)
                                            <div class="absolute -top-1 -right-1 bg-purple-600 dark:bg-purple-500 rounded-full p-1" title="Reseña verificada">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold text-gray-900 dark:text-white">
                                                {{ $resena->usuario->nombre ?? 'Usuario' }}
                                            </h4>
                                            @if($resena->verificada)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200" title="Reseña verificada">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Verificada
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-1 mt-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star {{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }} text-sm"></i>
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                                {{ $resena->calificacion }}/5
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end space-y-1">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $resena->created_at->diffForHumans() }}
                                    </span>
                                    @if($resena->pedido_id)
                                        <span class="text-xs text-gray-400 dark:text-gray-500" title="Reseña de pedido confirmado">
                                            <i class="fas fa-shopping-bag mr-1"></i>
                                            Pedido #{{ $resena->pedido_id }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($resena->comentario)
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ $resena->comentario }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center pprey-12">
                            <i class="fas fa-comments text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                No hay reseñas aún
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Sé el primero en compartir tu experiencia con este producto
                            </p>
                            <button onclick="openReviewModal()"
                                class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-edit mr-2"></i>Escribir Primera Reseña
                            </button>
                        </div>
                    @endforelse
                </div>

                @php
                    $totalResenasActivas = $producto->resenas->where('activa', true)->count();
                @endphp
                @if ($totalResenasActivas > 5)
                    <div class="text-center mt-8">
                        <button
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors duration-200">
                            Ver todas las {{ $totalResenasActivas }} reseñas
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Image Zoom Modal -->
    <div id="imageZoomModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center">
        <div class="relative w-full h-full flex items-center justify-center p-4">
            <!-- Close Button -->
            <button onclick="closeImageZoom()"
                class="absolute top-4 right-4 z-10 bg-white dark:bg-gray-800 p-3 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <i class="fas fa-times text-gray-600 dark:text-gray-300 text-xl"></i>
            </button>

            <!-- Previous Button -->
            <button onclick="previousZoomImage()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-white dark:bg-gray-800 p-4 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <i class="fas fa-chevron-left text-gray-600 dark:text-gray-300 text-xl"></i>
            </button>

            <!-- Next Button -->
            <button onclick="nextZoomImage()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 bg-white dark:bg-gray-800 p-4 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <i class="fas fa-chevron-right text-gray-600 dark:text-gray-300 text-xl"></i>
            </button>

            <!-- Image Container -->
            <div class="relative max-w-4xl max-h-full">
                <img id="zoomImage" src="" alt=""
                    class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">

                <!-- Image Counter -->
                <div
                    class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-sm">
                    <span id="zoomImageCounter">1</span> {{ __('messages.product_show.zoom_image_counter') }} <span id="zoomTotalImages">1</span>
                </div>

                <!-- Zoom Level Indicator -->
                <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                    <span id="zoomLevelIndicator">100%</span>
                </div>

                <!-- Zoom Controls -->
                <div class="absolute top-4 left-4 flex space-x-2">
                    <button onclick="zoomIn()"
                        class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 relative group"
                        title="Acercar (Ctrl + +)">
                        <i class="fas fa-search-plus text-gray-600 dark:text-gray-300"></i>
                        <span
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-black text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Acercar</span>
                    </button>
                    <button onclick="zoomOut()"
                        class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 relative group"
                        title="Alejar (Ctrl + -)">
                        <i class="fas fa-search-minus text-gray-600 dark:text-gray-300"></i>
                        <span
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-black text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Alejar</span>
                    </button>
                    <button onclick="resetZoom()"
                        class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 relative group"
                        title="Restablecer (0)">
                        <i class="fas fa-expand-arrows-alt text-gray-600 dark:text-gray-300"></i>
                        <span
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-black text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Restablecer</span>
                    </button>
                    <button onclick="rotateImage()"
                        class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 relative group"
                        title="Rotar (R)">
                        <i class="fas fa-redo text-gray-600 dark:text-gray-300"></i>
                        <span
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-black text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Rotar</span>
                    </button>
                    <button onclick="downloadImage()"
                        class="bg-white dark:bg-gray-800 p-2 rounded-full shadow-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 relative group"
                        title="Descargar">
                        <i class="fas fa-download text-gray-600 dark:text-gray-300"></i>
                        <span
                            class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-black text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Descargar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Escribir Reseña
                    </h3>
                    <button onclick="closeReviewModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="reviewForm" class="space-y-6">
                    @guest
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tu Nombre
                        </label>
                        <input type="text" name="nombre_usuario" id="nombreUsuario"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="Ingresa tu nombre para la reseña" required>
                    </div>
                    @endguest

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Calificación
                        </label>
                        <div class="flex items-center space-x-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button"
                                    class="star-rating text-2xl text-gray-300 dark:text-gray-600 hover:text-yellow-400 transition-colors duration-200"
                                    data-rating="{{ $i }}">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endfor
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Comentario
                        </label>
                        <textarea name="comentario" id="comentarioReview" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                            placeholder="Comparte tu experiencia con este producto..."></textarea>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" onclick="closeReviewModal()"
                            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="submit" id="submitReviewBtn"
                            class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:from-yellow-500 disabled:hover:to-orange-500"
                            disabled>
                            Enviar Reseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span
                    class="inline-flex items-center px-4 py-2 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-full text-sm font-medium mb-4">
                    <i class="fas fa-star mr-2"></i>Productos Relacionados
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                    También te puede interesar
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                    Descubre otros productos de nuestra colección que podrían ser perfectos para ti
                </p>
            </div>

                         <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                 @forelse ($productosRelacionados as $productoRelacionado)
                     <div
                         class="group bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                         <div class="relative overflow-hidden">
                                                           @if ($productoRelacionado->imagenes->first())
                                  <img src="{{ $productoRelacionado->imagenes->first()->url_completa }}"
                                      alt="{{ $productoRelacionado->nombre_producto }}"
                                                                              class="w-full h-32 sm:h-36 md:h-40 lg:h-44 xl:h-48 object-cover bg-gray-50 dark:bg-gray-800 group-hover:scale-110 transition-transform duration-300">
                              @else
                                  <div class="w-full h-32 sm:h-36 md:h-40 lg:h-44 xl:h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                      <i class="fas fa-mobile-alt text-2xl sm:text-3xl md:text-4xl text-gray-400 dark:text-gray-500"></i>
                                  </div>
                              @endif
                             
                             @if (isset($productoRelacionado->precio_anterior) && $productoRelacionado->precio_anterior > $productoRelacionado->precio)
                                 <div
                                     class="absolute top-4 right-4 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                     -{{ round((($productoRelacionado->precio_anterior - $productoRelacionado->precio) / $productoRelacionado->precio_anterior) * 100) }}%
                                 </div>
                             @endif
                             
                             @if ($productoRelacionado->stock <= 5 && $productoRelacionado->stock > 0)
                                 <div
                                     class="absolute top-4 left-4 bg-orange-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                     Solo {{ $productoRelacionado->stock }}
                                 </div>
                             @elseif($productoRelacionado->stock == 0)
                                 <div
                                     class="absolute top-4 left-4 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                     Agotado
                                 </div>
                             @endif
                         </div>
                         <div class="p-6">
                             <div class="flex items-center space-x-2 mb-2">
                                 @if ($productoRelacionado->categoria)
                                     <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-full">
                                         {{ $productoRelacionado->categoria->nombre }}
                                     </span>
                                 @endif
                                 @if ($productoRelacionado->marca)
                                     <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 px-2 py-1 rounded-full">
                                         {{ $productoRelacionado->marca->nombre }}
                                     </span>
                                 @endif
                             </div>
                             
                             <h3
                                 class="font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300 line-clamp-2">
                                 {{ $productoRelacionado->nombre_producto }}
                             </h3>
                             
                             <!-- Rating -->
                             @if ($productoRelacionado->resenas->count() > 0)
                                 <div class="flex items-center mb-3">
                                     <div class="flex text-yellow-400 text-sm">
                                         @for ($i = 1; $i <= 5; $i++)
                                             <i class="fas fa-star {{ $i <= $productoRelacionado->resenas->avg('calificacion') ? '' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                         @endfor
                                     </div>
                                     <span class="text-gray-500 text-sm ml-2">({{ $productoRelacionado->resenas->count() }})</span>
                                 </div>
                             @endif
                             
                             <div class="flex items-center space-x-2 mb-3">
                                 <span
                                     class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ \App\Helpers\CurrencyHelper::formatPrice($productoRelacionado->precio ?? 0) }}</span>
                                 @if (isset($productoRelacionado->precio_anterior) && $productoRelacionado->precio_anterior > $productoRelacionado->precio)
                                     <span
                                         class="text-sm text-gray-500 line-through">{{ \App\Helpers\CurrencyHelper::formatPrice($productoRelacionado->precio_anterior) }}</span>
                                 @endif
                             </div>
                             
                             <a href="{{ route('productos.show', $productoRelacionado->producto_id) }}"
                                 class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-2 px-4 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 text-center">
                                 Ver Producto
                             </a>
                         </div>
                     </div>
                 @empty
                     <div class="col-span-full text-center py-12">
                         <i class="fas fa-box-open text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                         <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                             No hay productos relacionados
                         </h3>
                         <p class="text-gray-600 dark:text-gray-400 mb-6">
                             No encontramos productos similares en este momento
                         </p>
                         <a href="{{ route('productos.lista') }}"
                             class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                             <i class="fas fa-th-large mr-2"></i>Ver Todos los Productos
                         </a>
                     </div>
                 @endforelse
            </div>
        </div>
    </section>

   

@endsection

@push('scripts')
    <script>
        // Función para formatear moneda según la configuración actual
        function formatCurrency(amount, fromCurrency = 'COP') {
            // Obtener la configuración de moneda desde el servidor
            const currencySymbol = '{{ \App\Helpers\CurrencyHelper::getCurrencySymbol() }}';
            const currentCurrency = '{{ \App\Helpers\CurrencyHelper::getCurrentCurrency() }}';
            
            // Tasas de cambio (deben coincidir con las del servidor)
            const rates = {
                'COP': 1,
                'USD': 0.00025,
                'BRL': 0.0012,
                'EUR': 0.00023,
                'MXN': 0.0045,
                'ARS': 0.25,
                'CLP': 0.4,
                'PEN': 0.0009
            };
            
            // Convertir moneda si es necesario
            let convertedAmount = amount;
            if (fromCurrency !== currentCurrency) {
                const amountInCOP = amount / (rates[fromCurrency] || 1);
                convertedAmount = amountInCOP * (rates[currentCurrency] || 1);
            }
            
            // Configuraciones de formateo por moneda
            const formats = {
                'COP': { decimals: 0, separator: '.', decimal: ',' },
                'USD': { decimals: 2, separator: ',', decimal: '.' },
                'BRL': { decimals: 2, separator: '.', decimal: ',' },
                'EUR': { decimals: 2, separator: '.', decimal: ',' },
                'MXN': { decimals: 2, separator: ',', decimal: '.' },
                'ARS': { decimals: 0, separator: '.', decimal: ',' },
                'CLP': { decimals: 0, separator: '.', decimal: ',' },
                'PEN': { decimals: 2, separator: ',', decimal: '.' }
            };
            
            const format = formats[currentCurrency] || formats['COP'];
            
            // Formatear el número
            const formattedAmount = new Intl.NumberFormat('es-CO', {
                minimumFractionDigits: format.decimals,
                maximumFractionDigits: format.decimals
            }).format(convertedAmount);
            
            // Retornar con el símbolo de moneda
            return currencySymbol + ' ' + formattedAmount;
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== DOM CONTENT LOADED - INITIALIZING ===');
            
            // Variables globales para el zoom de imágenes
            let zoomLevel = 1;
            let rotationAngle = 0;
            let zoomImages = [];
            let currentZoomIndex = 0;

            // ===== FUNCIONALIDAD DE LA GALERÍA =====
            
            const mainImage = document.getElementById('mainImage');

            function changeMainImage(thumbnail, newSrc) {
                // Actualizar imagen principal
                mainImage.src = newSrc;
                mainImage.alt = thumbnail.alt;

                // Actualizar thumbnail activo
                const allThumbnails = document.querySelectorAll('.thumbnail-item');
                allThumbnails.forEach(thumb => {
                    thumb.classList.remove('border-blue-500');
                });
                thumbnail.classList.add('border-blue-500');

                // Actualizar punto activo
                const index = Array.from(allThumbnails).indexOf(thumbnail);
                const allDots = document.querySelectorAll('.pagination-dots > div');
                allDots.forEach((dot, i) => {
                    if (i === index) {
                        dot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                        dot.classList.add('bg-blue-600');
                    } else {
                        dot.classList.remove('bg-blue-600');
                        dot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                    }
                });
            }

            // Agregar event listeners a thumbnails
            function addThumbnailClickHandlers() {
                const allThumbnails = document.querySelectorAll('.thumbnail-item');
                allThumbnails.forEach((thumbnail) => {
                    thumbnail.addEventListener('click', function() {
                        const imageUrl = this.dataset.imageUrl || this.src;
                        changeMainImage(this, imageUrl);
                    });
                });
            }

            // Agregar event listeners a puntos de paginación
            function addDotClickHandlers() {
                const allDots = document.querySelectorAll('.pagination-dots > div');
                allDots.forEach((dot, index) => {
                    dot.addEventListener('click', function() {
                        const allThumbnails = document.querySelectorAll('.thumbnail-item');
                        if (allThumbnails[index]) {
                            const imageUrl = allThumbnails[index].dataset.imageUrl || allThumbnails[index].src;
                            changeMainImage(allThumbnails[index], imageUrl);
                        }
                    });
                });
            }

            // Function to update gallery with variant images
            function updateGalleryWithVariantImages(imagenes) {
                console.log('updateGalleryWithVariantImages called with:', imagenes);
                
                // Buscar la galería de thumbnails correcta
                const thumbnailGallery = document.querySelector('.thumbnail-grid');
                const mainImage = document.getElementById('mainImage');

                console.log('thumbnailGallery:', thumbnailGallery);
                console.log('mainImage:', mainImage);

                if (!thumbnailGallery || !mainImage) {
                    console.error('Required elements not found');
                    return;
                }

                // Clear existing thumbnails
                thumbnailGallery.innerHTML = '';

                // Add new thumbnails for variant images
                imagenes.forEach((imagenUrl, index) => {
                    console.log(`Creating thumbnail ${index + 1} with URL:`, imagenUrl);
                    
                    const thumbnail = document.createElement('img');
                    thumbnail.src = imagenUrl;
                    thumbnail.alt = `Vista ${index + 1}`;
                    thumbnail.className = `thumbnail-item ${index === 0 ? 'border-blue-500' : ''}`;
                    
                    // Force thumbnail dimensions
                    thumbnail.style.width = '5rem';
                    thumbnail.style.height = '5rem';
                    thumbnail.style.objectFit = 'cover';
                    thumbnail.style.borderRadius = '0.75rem';
                    
                    // Add error handling for image loading
                    thumbnail.onerror = function() {
                        console.error('Error loading thumbnail image:', imagenUrl);
                        this.style.display = 'none';
                    };
                    
                    thumbnail.onload = function() {
                        console.log('Thumbnail loaded successfully:', imagenUrl);
                    };
                    
                    thumbnail.onclick = function() {
                        console.log('Thumbnail clicked, changing main image to:', imagenUrl);
                        changeMainImage(this, imagenUrl);
                    };

                    thumbnailGallery.appendChild(thumbnail);
                });

                // Update main image to first variant image
                if (imagenes.length > 0) {
                    console.log('Updating main image to:', imagenes[0]);
                    
                    // Ensure main image maintains fixed dimensions
                    mainImage.style.width = '100%';
                    mainImage.style.height = '100%';
                    mainImage.style.objectFit = 'cover';
                    mainImage.style.objectPosition = 'center';
                    
                    // Add error handling for main image
                    mainImage.onerror = function() {
                        console.error('Error loading main image:', imagenes[0]);
                        // Show placeholder if image fails to load
                        this.style.display = 'none';
                        const placeholderDiv = document.createElement('div');
                        placeholderDiv.className = 'w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl shadow-lg flex items-center justify-center';
                        placeholderDiv.innerHTML = '<i class="fas fa-image text-6xl text-gray-400 dark:text-gray-500"></i>';
                        this.parentNode.appendChild(placeholderDiv);
                    };
                    
                    mainImage.onload = function() {
                        console.log('Main image loaded successfully:', imagenes[0]);
                    };
                    
                    mainImage.src = imagenes[0];
                    
                    // Update zoom images array for zoom functionality
                    window.zoomImages = imagenes;
                    window.currentZoomIndex = 0;
                }

                // Update pagination dots
                updatePaginationDots(imagenes.length);
                
                // Add click handlers to new thumbnails and dots
                addThumbnailClickHandlers();
                addDotClickHandlers();
            }

            // Function to update pagination dots
            function updatePaginationDots(count) {
                const paginationContainer = document.querySelector('.pagination-dots');
                if (!paginationContainer) return;

                paginationContainer.innerHTML = '';

                for (let i = 1; i <= Math.min(count, 5); i++) {
                    const dot = document.createElement('div');
                    dot.className =
                        `w-2 h-2 rounded-full ${i === 1 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'} cursor-pointer hover:bg-blue-500 transition-colors duration-200`;
                    dot.onclick = function() {
                        const thumbnails = document.querySelectorAll('.thumbnail-item');
                        if (thumbnails[i - 1]) {
                            const imageUrl = thumbnails[i - 1].dataset.imageUrl || thumbnails[i - 1].src;
                            changeMainImage(thumbnails[i - 1], imageUrl);
                        }
                    };
                    paginationContainer.appendChild(dot);
                }
            }

            // Function to restore original product images
            function restoreOriginalImages() {
                const thumbnailGallery = document.querySelector('.thumbnail-grid');
                const mainImage = document.getElementById('mainImage');

                if (!thumbnailGallery || !mainImage) return;

                // Clear existing thumbnails
                thumbnailGallery.innerHTML = '';

                // Restore original thumbnails
                @php
                    $imagenes = $producto->imagenes;
                    $totalImagenes = $imagenes->count();
                @endphp

                @for ($i = 0; $i < 5; $i++)
                    @if ($i < $totalImagenes)
                        @php
                            $imagen = $imagenes[$i];
                            $imagenUrl = $imagen->url_completa;
                            $imagenPrincipal = $imagen->url_completa;
                        @endphp
                        const thumbnail{{ $i }} = document.createElement('img');
                        thumbnail{{ $i }}.src = '{{ $imagenUrl }}';
                        thumbnail{{ $i }}.alt =
                            '{{ $producto->nombre_producto }} - Vista {{ $i + 1 }}';
                        thumbnail{{ $i }}.className = 'thumbnail-item {{ $i === 0 ? 'border-blue-500' : '' }}';
                        
                        // Force thumbnail dimensions
                        thumbnail{{ $i }}.style.width = '5rem';
                        thumbnail{{ $i }}.style.height = '5rem';
                        thumbnail{{ $i }}.style.objectFit = 'cover';
                        thumbnail{{ $i }}.style.borderRadius = '0.75rem';
                        
                        thumbnail{{ $i }}.onclick = function() {
                            changeMainImage(this, '{{ $imagenPrincipal }}');
                        };
                        thumbnailGallery.appendChild(thumbnail{{ $i }});
                    @else
                        const thumbnail{{ $i }} = document.createElement('div');
                        thumbnail{{ $i }}.className = 'thumbnail-item flex items-center justify-center';
                        thumbnail{{ $i }}.style.width = '5rem';
                        thumbnail{{ $i }}.style.height = '5rem';
                        
                        thumbnail{{ $i }}.innerHTML = '<i class="fas fa-image text-gray-400 dark:text-gray-500 text-sm"></i>';
                        thumbnail{{ $i }}.onclick = function() {
                            // No action for placeholder thumbnails
                        };
                        thumbnailGallery.appendChild(thumbnail{{ $i }});
                    @endif
                @endfor

                // Restore main image
                @if ($producto->imagenes->first())
                    // Ensure main image maintains fixed dimensions
                    mainImage.style.width = '100%';
                    mainImage.style.height = '100%';
                    mainImage.style.objectFit = 'cover';
                    mainImage.style.objectPosition = 'center';
                    mainImage.src = '{{ $producto->imagenes->first()->url_completa }}';
                @else
                    // If no images, show a placeholder div instead of an image
                    mainImage.style.display = 'none';
                    const placeholderDiv = document.createElement('div');
                    placeholderDiv.className = 'w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl shadow-lg flex items-center justify-center';
                    placeholderDiv.innerHTML = '<i class="fas fa-image text-6xl text-gray-400 dark:text-gray-500"></i>';
                    this.parentNode.appendChild(placeholderDiv);
                @endif

                // Update zoom images array
                window.zoomImages = [
                    @foreach ($producto->imagenes as $imagen)
                        '{{ $imagen->url_completa }}',
                    @endforeach
                ];
                window.currentZoomIndex = 0;

                // Update pagination dots
                updatePaginationDots({{ $totalImagenes }});
                
                // Add click handlers to restored thumbnails and dots
                addThumbnailClickHandlers();
                addDotClickHandlers();
            }

            // Smooth scroll for anchor links (exclude links with only "#" as href)
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    // Skip if href is only "#" (like cart buttons)
                    if (href === '#' || href === '#!') {
                        return; // Let the default handler manage it
                    }
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Initialize thumbnail and dot click handlers for original images
            addThumbnailClickHandlers();
            addDotClickHandlers();

            // ===== SISTEMA MODERNO DE SELECTOR DE CANTIDAD =====
            
            // Clase para manejar el selector de cantidad
            class QuantitySelector {
                constructor() {
                    this.input = document.getElementById('productQuantity');
                    this.incrementBtn = document.getElementById('incrementQuantity');
                    this.decrementBtn = document.getElementById('decrementQuantity');
                    this.init();
                }

                init() {
                    if (!this.input) {
                        console.error('QuantitySelector: No se encontró el input #productQuantity');
                        return;
                    }
                    
                    if (!this.incrementBtn) {
                        console.error('QuantitySelector: No se encontró el botón #incrementQuantity');
                    }
                    
                    if (!this.decrementBtn) {
                        console.error('QuantitySelector: No se encontró el botón #decrementQuantity');
                    }
                    
                    this.setupButtons();
                    this.updateButtons();
                }

                setupButtons() {
                    if (this.incrementBtn) {
                        // Remover cualquier listener previo para evitar duplicados
                        const newIncrementBtn = this.incrementBtn.cloneNode(true);
                        this.incrementBtn.parentNode.replaceChild(newIncrementBtn, this.incrementBtn);
                        this.incrementBtn = newIncrementBtn;
                        
                        this.incrementBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Increment button clicked');
                            this.increment();
                        });
                        console.log('Increment button listener added');
                    } else {
                        console.error('QuantitySelector: incrementBtn no encontrado');
                    }

                    if (this.decrementBtn) {
                        // Remover cualquier listener previo para evitar duplicados
                        const newDecrementBtn = this.decrementBtn.cloneNode(true);
                        this.decrementBtn.parentNode.replaceChild(newDecrementBtn, this.decrementBtn);
                        this.decrementBtn = newDecrementBtn;
                        
                        this.decrementBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Decrement button clicked');
                            this.decrement();
                        });
                        console.log('Decrement button listener added');
                    } else {
                        console.error('QuantitySelector: decrementBtn no encontrado');
                    }

                    // Permitir cambio manual del input
                    if (this.input) {
                        this.input.addEventListener('change', () => this.validate());
                        this.input.addEventListener('input', () => this.updateButtons());
                    }
                }

                increment() {
                    if (!this.input) {
                        console.error('QuantitySelector: input no encontrado en increment()');
                        return;
                    }
                    
                    const currentValue = parseInt(this.input.value) || 1;
                    const maxValue = parseInt(this.input.max) || 100;
                    
                    console.log('Increment:', { currentValue, maxValue });
                    
                    if (currentValue < maxValue) {
                        this.input.value = currentValue + 1;
                        this.updateButtons();
                        this.animateChange();
                        console.log('Cantidad incrementada a:', this.input.value);
                    } else {
                        console.log('No se puede incrementar: ya está en el máximo');
                    }
                }

                decrement() {
                    if (!this.input) {
                        console.error('QuantitySelector: input no encontrado en decrement()');
                        return;
                    }
                    
                    const currentValue = parseInt(this.input.value) || 1;
                    const minValue = parseInt(this.input.min) || 1;
                    
                    console.log('Decrement:', { currentValue, minValue });
                    
                    if (currentValue > minValue) {
                        this.input.value = currentValue - 1;
                        this.updateButtons();
                        this.animateChange();
                        console.log('Cantidad decrementada a:', this.input.value);
                    } else {
                        console.log('No se puede decrementar: ya está en el mínimo');
                    }
                }

                validate() {
                    const currentValue = parseInt(this.input.value) || 1;
                    const maxValue = parseInt(this.input.max) || 100;
                    const minValue = parseInt(this.input.min) || 1;
                    
                    if (currentValue > maxValue) {
                        this.input.value = maxValue;
                    } else if (currentValue < minValue) {
                        this.input.value = minValue;
                    }
                    
                    this.updateButtons();
                }

                updateButtons() {
                    const currentValue = parseInt(this.input.value) || 1;
                    const maxValue = parseInt(this.input.max) || 100;
                    const minValue = parseInt(this.input.min) || 1;

                    if (this.decrementBtn) {
                        this.decrementBtn.disabled = currentValue <= minValue;
                        if (currentValue <= minValue) {
                            this.decrementBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            this.decrementBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }

                    if (this.incrementBtn) {
                        this.incrementBtn.disabled = currentValue >= maxValue;
                        if (currentValue >= maxValue) {
                            this.incrementBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            this.incrementBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }
                }

                animateChange() {
                    this.input.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        this.input.style.transform = 'scale(1)';
                    }, 200);
                }

                setMax(maxValue) {
                    if (this.input) {
                        this.input.max = maxValue;
                        this.validate();
                    }
                }
            }

            // Inicializar selector de cantidad
            let quantitySelector;
            
            // Intentar inicializar inmediatamente si el DOM ya está listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeQuantitySelector);
            } else {
                // DOM ya está listo
                initializeQuantitySelector();
            }
            
            function initializeQuantitySelector() {
                // Esperar un poco para asegurar que el DOM esté completamente cargado
                setTimeout(() => {
                    console.log('Inicializando QuantitySelector...');
                    const input = document.getElementById('productQuantity');
                    const incrementBtn = document.getElementById('incrementQuantity');
                    const decrementBtn = document.getElementById('decrementQuantity');
                    
                    console.log('Elementos encontrados:', {
                        input: !!input,
                        incrementBtn: !!incrementBtn,
                        decrementBtn: !!decrementBtn
                    });
                    
                    if (input && incrementBtn && decrementBtn) {
                        quantitySelector = new QuantitySelector();
                        window.quantitySelector = quantitySelector; // Hacerlo global para debugging
                        console.log('QuantitySelector inicializado correctamente');
                    } else {
                        console.error('QuantitySelector: No se pudieron encontrar todos los elementos necesarios');
                    }
                    
                    // Función global para compatibilidad
                    window.updateQuantityButtons = function() {
                        if (quantitySelector) {
                            quantitySelector.updateButtons();
                        }
                    };
                }, 100);
            }

            // Add to cart functionality (must execute before landing.blade.php handler)
            if (addToCartBtn) {
                // Use capture phase to execute before landing.blade.php handler
                addToCartBtn.addEventListener('click', async function(e) {
                    // Prevent default behavior and stop propagation to avoid conflicts with landing.blade.php
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    // Get product data from button attributes
                    const productoId = this.getAttribute('data-producto-id') || this.dataset.productoId;
                    const varianteId = this.getAttribute('data-variante-id') || this.dataset.varianteId || null;
                    
                    // Get quantity from the quantity input selector
                    const qtyInput = document.getElementById('productQuantity');
                    const cantidad = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                    
                    console.log('Agregando al carrito:', {
                        productoId,
                        varianteId,
                        cantidad,
                        quantityInputValue: qtyInput ? qtyInput.value : 'no encontrado'
                    });

                    // Validate product data
                    if (!productoId || isNaN(parseInt(productoId))) {
                        console.error('Error: productoId inválido', productoId);
                        // Mostrar notificación de error personalizada
                        if (typeof window.showNotification === 'function') {
                            window.showNotification('Error al obtener los datos del producto. Por favor, recarga la página.', 'error', 5);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al obtener los datos del producto. Por favor, recarga la página.',
                                icon: 'error',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#3B82F6'
                            });
                        } else {
                            alert('Error al obtener los datos del producto');
                        }
                        return;
                    }

                    // Disable button during request
                    this.disabled = true;
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i> Agregando...';

                    try {
                        const formData = new FormData();
                        formData.append('producto_id', productoId);
                        formData.append('cantidad', cantidad);
                        if (varianteId) {
                            formData.append('variante_id', varianteId);
                        }
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                        console.log('Enviando petición al servidor:', {
                            producto_id: productoId,
                            variante_id: varianteId,
                            cantidad: cantidad
                        });

                        const response = await fetch('/carrito/agregar', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        console.log('Respuesta del servidor:', {
                            status: response.status,
                            statusText: response.statusText,
                            ok: response.ok
                        });

                        // Check if response is ok before parsing JSON
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('Error en la respuesta del servidor:', errorText);
                            throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
                        }

                        const data = await response.json();
                        console.log('Datos de respuesta:', data);

                        if (data.success) {
                            // Mostrar notificación personalizada
                            if (typeof window.showNotification === 'function') {
                                window.showNotification('¡Producto agregado al carrito exitosamente!', 'success', 5);
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: '¡Producto Agregado!',
                                    text: 'El producto se ha agregado al carrito exitosamente.',
                                    icon: 'success',
                                    confirmButtonText: '{{ __('messages.product_show.continue_shopping') }}',
                                    confirmButtonColor: '#3B82F6',
                                    timer: 2000,
                                    showConfirmButton: true
                                });
                            } else {
                                alert('Producto agregado al carrito');
                            }

                            // Update cart counter if exists (for admin/authenticated users)
                            if (window.carritoController) {
                                await window.carritoController.cargarCarrito();
                            }

                            // Update client-side cart (localStorage) for landing page
                            console.log('Verificando funciones del carrito:', {
                                updateCartCount: typeof updateCartCount,
                                updateCartDisplay: typeof updateCartDisplay,
                                windowCart: typeof window.cart
                            });
                            
                            // Reload cart from server to sync with localStorage
                            try {
                                const cartResponse = await fetch('/carrito/obtener', {
                                    method: 'GET',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json'
                                    }
                                });
                                
                                console.log('Respuesta del carrito:', cartResponse.status);
                                
                                if (cartResponse.ok) {
                                    const cartData = await cartResponse.json();
                                    console.log('Datos del carrito completos:', cartData);
                                    console.log('Items del carrito:', cartData.data?.items);
                                    
                                    if (cartData.success && cartData.data && cartData.data.items) {
                                        // Clear and rebuild cart from server data to avoid duplicates
                                        window.cart = [];
                                        
                                        // Add items from server to client cart
                                        const items = Array.isArray(cartData.data.items) ? cartData.data.items : [];
                                        console.log('Procesando items:', items.length);
                                        
                                        items.forEach(item => {
                                            console.log('Procesando item:', item);
                                            if (item.producto) {
                                                const cartItem = {
                                                    id: item.producto.producto_id,
                                                    name: item.producto.nombre_producto,
                                                    price: parseFloat(item.producto.precio),
                                                    quantity: item.cantidad,
                                                    itemId: item.id // Guardar el ID del item del servidor para poder eliminarlo
                                                };
                                                
                                                if (item.variante) {
                                                    cartItem.variante_id = item.variante.variante_id;
                                                    cartItem.variante_nombre = item.variante.nombre;
                                                    cartItem.precio_adicional = parseFloat(item.variante.precio_adicional || 0);
                                                    cartItem.name = `${item.producto.nombre_producto} (${item.variante.nombre})`;
                                                    cartItem.price = parseFloat(item.producto.precio) + parseFloat(item.variante.precio_adicional || 0);
                                                }
                                                
                                                window.cart.push(cartItem);
                                                console.log('Item agregado al carrito cliente:', cartItem);
                                            }
                                        });
                                        
                                        // Update localStorage first
                                        localStorage.setItem('cart', JSON.stringify(window.cart));
                                        console.log('Carrito actualizado en localStorage:', window.cart);
                                        console.log('Total items en carrito:', window.cart.length);
                                        
                                        // Force sync with landing.blade.php cart variable if it exists
                                        if (typeof window.syncCart === 'function') {
                                            console.log('Sincronizando carrito con landing.blade.php');
                                            window.syncCart();
                                        } else {
                                            // Si syncCart no existe, actualizar UI directamente
                                            const updateCountFn = window.updateCartCount || (typeof updateCartCount !== 'undefined' ? updateCartCount : null);
                                            const updateDisplayFn = window.updateCartDisplay || (typeof updateCartDisplay !== 'undefined' ? updateCartDisplay : null);
                                            
                                            if (updateCountFn && typeof updateCountFn === 'function') {
                                                console.log('Llamando updateCartCount()');
                                                updateCountFn();
                                            }
                                            
                                            if (updateDisplayFn && typeof updateDisplayFn === 'function') {
                                                console.log('Llamando updateCartDisplay()');
                                                updateDisplayFn();
                                            }
                                        }
                                    } else {
                                        console.warn('Estructura de datos del carrito no válida:', cartData);
                                    }
                                } else {
                                    console.error('Error al obtener carrito:', cartResponse.status);
                                    const errorText = await cartResponse.text();
                                    console.error('Error detallado:', errorText);
                                }
                            } catch (error) {
                                console.error('Error al sincronizar carrito:', error);
                                console.error('Stack trace:', error.stack);
                            }
                        } else {
                            // Mostrar notificación de error personalizada
                            if (typeof window.showNotification === 'function') {
                                window.showNotification(data.message || 'Error al agregar el producto al carrito', 'error', 5);
                            } else if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.message || 'Error al agregar el producto al carrito',
                                    icon: 'error',
                                    confirmButtonText: 'Entendido',
                                    confirmButtonColor: '#3B82F6'
                                });
                            } else {
                                alert(data.message || 'Error al agregar el producto al carrito');
                            }
                        }
                    } catch (error) {
                        console.error('Error al agregar al carrito:', error);
                        // Mostrar notificación de error personalizada
                        if (typeof window.showNotification === 'function') {
                            window.showNotification('Error al agregar el producto al carrito. Por favor, intenta nuevamente.', 'error', 5);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al agregar el producto al carrito. Por favor, intenta nuevamente.',
                                icon: 'error',
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#3B82F6'
                            });
                        } else {
                            alert('Error al agregar el producto al carrito');
                        }
                    } finally {
                        // Re-enable button
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }
                }, { capture: true }); // Execute in capture phase to run before landing.blade.php handler
            }

            // Buy now functionality - Not implemented yet, so we'll remove this for now
            // const buyNowBtn = document.querySelector('button:contains("Comprar Ahora")');
            // if (buyNowBtn) {
            //     buyNowBtn.addEventListener('click', function() {
            //         if (typeof Swal !== 'undefined') {
            //             Swal.fire({
            //                 title: '¡Compra Exitosa!',
            //                 text: 'Gracias por tu compra. Te contactaremos pronto.',
            //                 icon: 'success',
            //                 confirmButtonText: 'Entendido',
            //                 confirmButtonColor: '#3B82F6'
            //             });
            //         } else {
            //             alert('Compra exitosa');
            //         }
            //     });
            // }

            // Wishlist functionality
            const wishlistBtn = document.querySelector('button:has(i.fa-heart)');
            if (wishlistBtn) {
                wishlistBtn.addEventListener('click', function() {
                    const icon = this.querySelector('.fa-heart');
                    icon.classList.toggle('text-red-500');

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Lista de Deseos',
                            text: icon.classList.contains('text-red-500') ?
                                'Producto agregado a favoritos' : 'Producto removido de favoritos',
                            icon: 'success',
                            confirmButtonText: '{{ __('messages.product_show.understood') }}',
                            confirmButtonColor: '#3B82F6'
                        });
                    } else {
                        alert(icon.classList.contains('text-red-500') ? 'Producto agregado a favoritos' :
                            'Producto removido de favoritos');
                    }
                });
            }

          

            // Función mejorada para limpiar todos los botones de variantes
            function cleanAllVariantButtons() {
                const colorVariants = document.querySelectorAll('.color-variant');
                colorVariants.forEach(variant => {
                    // Limpiar cualquier texto que contenga "disponibles" o números
                    const textNodes = Array.from(variant.childNodes).filter(node => {
                        if (node.nodeType === Node.TEXT_NODE) {
                            const text = node.textContent.trim();
                            // Eliminar cualquier texto que contenga el stock o "disponibles"
                            const stockValue = variant.dataset.variantStock;
                            return text && (
                                text.includes('disponibles') || 
                                /^\d+$/.test(text) || 
                                text.match(/\d+\s*disponibles?/i) ||
                                (stockValue && text.includes(stockValue))
                            );
                        }
                        return false;
                    });
                    textNodes.forEach(node => node.remove());
                    
                    // Si el botón está disponible y no tiene el icono de deshabilitado, asegurar que esté vacío
                    if (variant.dataset.available === 'true') {
                        const hasDisabledIcon = variant.querySelector('i.fa-times');
                        if (!hasDisabledIcon) {
                            // Limpiar cualquier contenido que no sea necesario
                            const children = Array.from(variant.children);
                            children.forEach(child => {
                                // Solo mantener el icono de deshabilitado si existe
                                if (!child.classList.contains('fa-times')) {
                                    // Verificar si el contenido del hijo contiene el stock
                                    const childText = child.textContent || '';
                                    const stockValue = variant.dataset.variantStock;
                                    if (childText.includes('disponibles') || 
                                        childText.match(/\d+\s*disponibles?/i) ||
                                        (stockValue && childText.includes(stockValue))) {
                                        child.remove();
                                    }
                                }
                            });
                        }
                    }
                });
            }

            // Limpiar cuando el DOM esté listo
            document.addEventListener('DOMContentLoaded', function() {
                cleanAllVariantButtons();
                
                // Limpiar después de un pequeño delay para asegurar que cualquier otro código haya terminado
                setTimeout(cleanAllVariantButtons, 500);
                setTimeout(cleanAllVariantButtons, 1000);
            });

            // Limpiar cuando se detecten cambios en los botones (MutationObserver)
            document.addEventListener('DOMContentLoaded', function() {
                const colorVariants = document.querySelectorAll('.color-variant');
                colorVariants.forEach(variant => {
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'childList' || mutation.type === 'characterData') {
                                // Limpiar cualquier texto que se haya agregado
                                const textNodes = Array.from(variant.childNodes).filter(node => {
                                    if (node.nodeType === Node.TEXT_NODE) {
                                        const text = node.textContent.trim();
                                        const stockValue = variant.dataset.variantStock;
                                        return text && (
                                            text.includes('disponibles') || 
                                            /^\d+$/.test(text) || 
                                            text.match(/\d+\s*disponibles?/i) ||
                                            (stockValue && text.includes(stockValue))
                                        );
                                    }
                                    return false;
                                });
                                textNodes.forEach(node => node.remove());
                            }
                        });
                    });
                    
                    observer.observe(variant, {
                        childList: true,
                        characterData: true,
                        subtree: true
                    });
                });
            });

        // Review Modal functionality
        let selectedRating = 0;

        // Star rating functionality
        console.log('=== INICIALIZANDO ESTRELLAS DE CALIFICACIÓN ===');
        const starRatings = document.querySelectorAll('.star-rating');
        console.log('Estrellas encontradas:', starRatings.length);
        
        starRatings.forEach((star, index) => {
            console.log(`Estrella ${index + 1}:`, star);
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                selectedRating = rating;
                console.log('⭐ Calificación seleccionada:', rating);

                // Update all stars
                document.querySelectorAll('.star-rating').forEach((s, index) => {
                    const starIcon = s.querySelector('i');
                    if (index < rating) {
                        starIcon.style.color = '#fbbf24'; // yellow-400
                    } else {
                        starIcon.style.color = '#d1d5db'; // gray-300
                    }
                });
                
                // Validar formulario después de seleccionar calificación
                if (typeof validateReviewForm === 'function') {
                    validateReviewForm();
                }
            });
        });

        // Review form submission
        console.log('=== INICIALIZANDO FORMULARIO DE RESEÑAS ===');
        const reviewForm = document.getElementById('reviewForm');
        console.log('Formulario encontrado:', reviewForm);
        
        if (!reviewForm) {
            console.error('❌ No se encontró el formulario de reseñas');
            return;
        }
        
        // Función para validar el formulario de reseñas
        function validateReviewForm() {
            const submitBtn = document.getElementById('submitReviewBtn');
            if (!submitBtn) return;
            
            // Validar calificación
            const hasRating = selectedRating > 0;
            
            // Validar comentario (al menos 3 letras)
            const comentarioInput = document.getElementById('comentarioReview');
            const comentario = comentarioInput ? comentarioInput.value.trim() : '';
            const letrasComentario = comentario.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]/g, '').length;
            const hasValidComment = letrasComentario >= 3;
            
            // Validar nombre de usuario (solo si no está autenticado)
            @guest
            const nombreUsuarioInput = document.getElementById('nombreUsuario');
            const nombreUsuario = nombreUsuarioInput ? nombreUsuarioInput.value.trim() : '';
            const hasValidName = nombreUsuario.length >= 2;
            @else
            const hasValidName = true; // Usuario autenticado no necesita nombre
            @endguest
            
            // Habilitar/deshabilitar botón según validación
            const isValid = hasRating && hasValidComment && hasValidName;
            
            if (isValid) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            
            console.log('Validación del formulario:', {
                hasRating,
                hasValidComment,
                hasValidName,
                isValid
            });
        }
        
        // Event listeners para validación en tiempo real
        const comentarioInput = document.getElementById('comentarioReview');
        if (comentarioInput) {
            comentarioInput.addEventListener('input', validateReviewForm);
            comentarioInput.addEventListener('keyup', validateReviewForm);
        }
        
        @guest
        const nombreUsuarioInput = document.getElementById('nombreUsuario');
        if (nombreUsuarioInput) {
            nombreUsuarioInput.addEventListener('input', validateReviewForm);
            nombreUsuarioInput.addEventListener('keyup', validateReviewForm);
        }
        @endguest
        
        // Validar inicialmente (el botón debe estar deshabilitado)
        validateReviewForm();
        
        reviewForm.addEventListener('submit', function(e) {
            console.log('=== ENVIANDO RESEÑA ===');
            e.preventDefault();

            console.log('Calificación seleccionada:', selectedRating);
            if (selectedRating === 0) {
                console.log('❌ No se seleccionó calificación');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'Por favor selecciona una calificación',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert('Por favor selecciona una calificación');
                }
                return;
            }

            const comentario = this.querySelector('textarea[name="comentario"]').value.trim();
            console.log('Comentario:', comentario);
            if (!comentario) {
                console.log('❌ No se escribió comentario');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'Por favor escribe un comentario',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert('Por favor escribe un comentario');
                }
                return;
            }
            
            // Validar que el comentario tenga al menos 3 letras
            const letrasComentario = comentario.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]/g, '').length;
            if (letrasComentario < 3) {
                console.log('❌ El comentario debe tener al menos 3 letras');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'El comentario debe tener al menos 3 letras',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert('El comentario debe tener al menos 3 letras');
                }
                return;
            }

            // Validar nombre de usuario para usuarios no autenticados
            @guest
            const nombreUsuario = this.querySelector('input[name="nombre_usuario"]').value.trim();
            if (!nombreUsuario) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'Por favor ingresa tu nombre',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert('Por favor ingresa tu nombre');
                }
                return;
            }
            @endguest

            // Enviar la reseña al backend
            console.log('Preparando datos para enviar...');
            const formData = new FormData();
            formData.append('calificacion', selectedRating);
            formData.append('comentario', comentario);
            formData.append('_token', '{{ csrf_token() }}');
            
            @guest
            formData.append('nombre_usuario', nombreUsuario);
            @endguest

            const url = '{{ route("productos.resenas.store", $producto->producto_id) }}';
            console.log('URL de envío:', url);
            console.log('Datos a enviar:', {
                calificacion: selectedRating,
                comentario: comentario,
                _token: '{{ csrf_token() }}'
                @guest
                , nombre_usuario: nombreUsuario
                @endguest
            });

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Respuesta recibida:', response);
                return response.json();
            })
            .then(data => {
                console.log('Datos de respuesta:', data);
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '¡Reseña Enviada!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: '{{ __('messages.product_show.understood') }}',
                            confirmButtonColor: '#3B82F6'
                        }).then(() => {
                            closeReviewModal();
                            // Recargar la página para mostrar la nueva reseña después de un breve delay
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        });
                    } else {
                        alert(data.message);
                        closeReviewModal();
                        location.reload();
                    }
                } else {
                    throw new Error(data.message || 'Error al enviar la reseña');
                }
            })
            .catch(error => {
                console.error('❌ Error en fetch:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: error.message || 'Error al enviar la reseña',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#ef4444'
                    });
                } else {
                    alert('Error al enviar la reseña: ' + error.message);
                }
            });
        });
        
        console.log('=== INICIALIZACIÓN COMPLETA DEL SCRIPT ===');
        console.log('✅ Modal de reseñas configurado');
        console.log('✅ Formulario de reseñas configurado');
        console.log('✅ Estrellas de calificación configuradas');
    });

        // Review Modal functions
        function openReviewModal() {
            console.log('=== ABRIENDO MODAL DE RESEÑAS ===');
            const modal = document.getElementById('reviewModal');
            console.log('Modal encontrado:', modal);
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Asegurar que el botón esté deshabilitado al abrir
            const submitBtn = document.getElementById('submitReviewBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            
            // Validar formulario después de abrir (por si hay valores pre-existentes)
            if (typeof validateReviewForm === 'function') {
                setTimeout(() => {
                    validateReviewForm();
                }, 100);
            }
            
            console.log('✅ Modal abierto');
        }

        function closeReviewModal() {
            console.log('=== CERRANDO MODAL DE RESEÑAS ===');
            const modal = document.getElementById('reviewModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Reset form
            const form = document.getElementById('reviewForm');
            form.reset();
            selectedRating = 0;

            // Reset stars
            document.querySelectorAll('.star-rating i').forEach(star => {
                star.style.color = '#d1d5db';
            });
            
            // Deshabilitar botón de envío
            const submitBtn = document.getElementById('submitReviewBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            
            console.log('✅ Modal cerrado y formulario reseteado');
        }

        // Close modal when clicking outside
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });

        // Image Zoom Functions
        function openImageZoom() {
            const modal = document.getElementById('imageZoomModal');
            const zoomImage = document.getElementById('zoomImage');
            const mainImage = document.getElementById('mainImage');

            // Get all available images (current variant or product images)
            zoomImages = [];
            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(thumb => {
                zoomImages.push(thumb.src);
            });

            if (zoomImages.length === 0) {
                zoomImages = [mainImage.src];
            }

            currentZoomIndex = 0;
            zoomLevel = 1;
            rotationAngle = 0;
            translateX = 0;
            translateY = 0;

            // Preload all images for smooth navigation
            preloadImages(zoomImages);

            // Load initial image with indicator
            loadImageWithIndicator(zoomImage, zoomImages[currentZoomIndex]);
            zoomImage.alt = mainImage.alt;

            // Update counters
            document.getElementById('zoomImageCounter').textContent = currentZoomIndex + 1;
            document.getElementById('zoomTotalImages').textContent = zoomImages.length;

            // Show modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Apply initial transform
            applyImageTransform();
            updateZoomIndicator();

            // Initialize drag functionality
            setTimeout(() => initImageDrag(), 100);

            // Add mouse wheel zoom
            zoomImage.addEventListener('wheel', handleWheelZoom);
        }

        function handleWheelZoom(e) {
            e.preventDefault();

            const delta = e.deltaY > 0 ? -1 : 1;
            const zoomFactor = 1.2;

            if (delta > 0) {
                zoomLevel = Math.min(zoomLevel * zoomFactor, 5);
            } else {
                zoomLevel = Math.max(zoomLevel / zoomFactor, 0.5);
            }

            applyImageTransform();
            updateZoomIndicator();
        }

        function closeImageZoom() {
            const modal = document.getElementById('imageZoomModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';

            // Reset transforms
            zoomLevel = 1;
            rotationAngle = 0;
            applyImageTransform();
        }

        function previousZoomImage() {
            if (zoomImages.length <= 1) return;

            currentZoomIndex = (currentZoomIndex - 1 + zoomImages.length) % zoomImages.length;
            updateZoomImage();
        }

        function nextZoomImage() {
            if (zoomImages.length <= 1) return;

            currentZoomIndex = (currentZoomIndex + 1) % zoomImages.length;
            updateZoomImage();
        }

        function updateZoomImage() {
            const zoomImage = document.getElementById('zoomImage');

            // Load new image with indicator
            loadImageWithIndicator(zoomImage, zoomImages[currentZoomIndex]);

            // Reset transforms for new image
            zoomLevel = 1;
            rotationAngle = 0;
            translateX = 0;
            translateY = 0;
            applyImageTransform();

            // Update counter
            document.getElementById('zoomImageCounter').textContent = currentZoomIndex + 1;
            updateZoomIndicator();
        }

        function zoomIn() {
            zoomLevel = Math.min(zoomLevel * 1.5, 5);
            applyImageTransform();
            updateZoomIndicator();
        }

        function zoomOut() {
            zoomLevel = Math.max(zoomLevel / 1.5, 0.5);
            applyImageTransform();
            updateZoomIndicator();
        }

        function updateZoomIndicator() {
            const indicator = document.getElementById('zoomLevelIndicator');
            if (indicator) {
                indicator.textContent = Math.round(zoomLevel * 100) + '%';
            }
        }

        function resetZoom() {
            zoomLevel = 1;
            rotationAngle = 0;
            translateX = 0;
            translateY = 0;
            applyImageTransform();
            updateZoomIndicator();
        }

        function rotateImage() {
            rotationAngle = (rotationAngle + 90) % 360;
            applyImageTransform();
        }

        function downloadImage() {
            const zoomImage = document.getElementById('zoomImage');
            const link = document.createElement('a');
            link.download = `producto_${currentZoomIndex + 1}.jpg`;
            link.href = zoomImage.src;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function applyImageTransform() {
            const zoomImage = document.getElementById('zoomImage');
            if (zoomImage) {
                zoomImage.style.transform =
                    `scale(${zoomLevel}) rotate(${rotationAngle}deg) translate(${translateX}px, ${translateY}px)`;
                zoomImage.style.transition = 'transform 0.3s ease-in-out';
            }
        }

        // Image drag functionality
        let isDragging = false;
        let startX, startY, translateX = 0,
            translateY = 0;

        function initImageDrag() {
            const zoomImage = document.getElementById('zoomImage');
            if (!zoomImage) return;

            zoomImage.addEventListener('mousedown', startDrag);
            zoomImage.addEventListener('touchstart', startDrag);
            document.addEventListener('mousemove', drag);
            document.addEventListener('touchmove', drag);
            document.addEventListener('mouseup', endDrag);
            document.addEventListener('touchend', endDrag);
        }

        function startDrag(e) {
            if (zoomLevel <= 1) return;

            isDragging = true;
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;

            startX = clientX - translateX;
            startY = clientY - translateY;

            const zoomImage = document.getElementById('zoomImage');
            zoomImage.style.cursor = 'grabbing';
            zoomImage.style.transition = 'none';
        }

        function drag(e) {
            if (!isDragging) return;

            e.preventDefault();
            const clientX = e.clientX || e.touches[0].clientX;
            const clientY = e.clientY || e.touches[0].clientY;

            translateX = clientX - startX;
            translateY = clientY - startY;

            const zoomImage = document.getElementById('zoomImage');
            zoomImage.style.transform =
                `scale(${zoomLevel}) rotate(${rotationAngle}deg) translate(${translateX}px, ${translateY}px)`;
        }

        function endDrag() {
            isDragging = false;
            const zoomImage = document.getElementById('zoomImage');
            if (zoomImage) {
                zoomImage.style.cursor = 'grab';
                zoomImage.style.transition = 'transform 0.3s ease-in-out';
            }
        }

        // Optimize image loading
        function preloadImages(imageUrls) {
            imageUrls.forEach(url => {
                const img = new Image();
                img.src = url;
            });
        }

        // Enhanced image loading with loading indicator
        function loadImageWithIndicator(imageElement, src) {
            imageElement.classList.add('image-loading');

            const img = new Image();
            img.onload = function() {
                imageElement.src = src;
                imageElement.classList.remove('image-loading');
            };
            img.onerror = function() {
                imageElement.classList.remove('image-loading');
                console.error('Error loading image:', src);
            };
            img.src = src;
        }

        // Keyboard navigation for zoom modal
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageZoomModal');
            if (modal.classList.contains('hidden')) return;

            switch (e.key) {
                case 'Escape':
                    closeImageZoom();
                    break;
                case 'ArrowLeft':
                    previousZoomImage();
                    break;
                case 'ArrowRight':
                    nextZoomImage();
                    break;
                case '+':
                case '=':
                    if (e.ctrlKey) {
                        e.preventDefault();
                        zoomIn();
                    }
                    break;
                case '-':
                    if (e.ctrlKey) {
                        e.preventDefault();
                        zoomOut();
                    }
                    break;
                case '0':
                    resetZoom();
                    break;
                case 'r':
                case 'R':
                    rotateImage();
                    break;
            }
        });

        // Close zoom modal when clicking outside
        document.getElementById('imageZoomModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageZoom();
            }
        });
        
        // Debug: Verificar que los botones de variantes estén funcionando
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== PRODUCTO SHOW PAGE INITIALIZATION ===');
            
            // Verificar botones de agregar al carrito
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            
            console.log('Botones add-to-cart encontrados:', addToCartButtons.length);
            
            // Verificar datos del producto
            const producto = {
                id: {{ $producto->producto_id }},
                nombre: '{{ $producto->nombre_producto }}',
                precio: {{ $producto->precio }},
                variantes: {{ $producto->variantes->count() }},
                stock: {{ $producto->stock_disponible }}
            };
            
            console.log('Datos del producto:', producto);
            
            // Verificar si tiene variantes
            const tieneVariantes = {{ $producto->variantes && $producto->variantes->count() > 0 ? 'true' : 'false' }};
            console.log('¿Tiene variantes?', tieneVariantes);
            
            if (tieneVariantes) {
                console.log('Variantes disponibles:');
                @foreach($producto->variantes as $variante)
                    console.log('- {{ $variante->nombre }}: Stock {{ $variante->stock_disponible }}, Precio adicional ${{ $variante->precio_adicional }}');
                @endforeach
            }
        });
        
        console.log('=== SCRIPT INITIALIZATION COMPLETE ===');

        // Función para actualizar el indicador de stock en la tarjeta del producto
        function updateProductCardStock(stock) {
            console.log('Actualizando stock de la tarjeta del producto:', stock);
            
            // Buscar todos los indicadores de stock en la página
            const stockIndicators = document.querySelectorAll('.stock-status');
            
            stockIndicators.forEach(indicator => {
                // Actualizar el texto del stock
                const stockText = indicator.querySelector('.stock-text');
                if (stockText) {
                    if (stock > 10) {
                        stockText.textContent = `${stock} disponibles`;
                        stockText.className = stockText.className.replace(/text-(red|yellow|gray)-600/, 'text-green-600');
                    } else if (stock > 5) {
                        stockText.textContent = `${stock} disponibles`;
                        stockText.className = stockText.className.replace(/text-(red|green|gray)-600/, 'text-yellow-600');
                    } else if (stock > 0) {
                        stockText.textContent = `Solo ${stock} disponibles`;
                        stockText.className = stockText.className.replace(/text-(green|yellow|gray)-600/, 'text-red-600');
                    } else {
                        stockText.textContent = 'Agotado';
                        stockText.className = stockText.className.replace(/text-(red|yellow|green)-600/, 'text-gray-500');
                    }
                }
                
                // Actualizar el indicador visual (punto de color)
                const stockDot = indicator.querySelector('.stock-dot');
                if (stockDot) {
                    stockDot.className = stockDot.className.replace(/bg-(red|yellow|green|gray)-500/, '');
                    if (stock > 10) {
                        stockDot.classList.add('bg-green-500');
                    } else if (stock > 5) {
                        stockDot.classList.add('bg-yellow-500');
                    } else if (stock > 0) {
                        stockDot.classList.add('bg-red-500');
                    } else {
                        stockDot.classList.add('bg-gray-400');
                    }
                }
                
                // Actualizar la barra de progreso si existe
                const progressBar = indicator.querySelector('.stock-progress');
                if (progressBar) {
                    const totalStock = {{ $producto->stock ?? 0 }};
                    const percentage = totalStock > 0 ? (stock / totalStock) * 100 : 0;
                    progressBar.style.width = `${percentage}%`;
                    
                    // Actualizar el color de la barra
                    progressBar.className = progressBar.className.replace(/bg-(red|yellow|green)-500/, '');
                    if (stock > 10) {
                        progressBar.classList.add('bg-green-500');
                    } else if (stock > 5) {
                        progressBar.classList.add('bg-yellow-500');
                    } else {
                        progressBar.classList.add('bg-red-500');
                    }
                }
            });
            
            // También actualizar cualquier elemento con clase específica de stock (excluyendo botones de variantes)
            const stockElements = document.querySelectorAll('.stock-available, .stock-disponible');
            stockElements.forEach(element => {
                // Verificar que no sea un botón de variante
                if (!element.classList.contains('color-variant') && 
                    (element.textContent.includes('disponibles') || element.textContent.includes('Stock'))) {
                    if (stock > 0) {
                        element.textContent = `${stock} disponibles`;
                    } else {
                        element.textContent = 'Agotado';
                    }
                }
            });
        }

        // Función para restaurar el stock original del producto
        function restoreOriginalStock() {
            console.log('Restaurando stock original del producto');
            const originalStock = {{ $producto->stock_disponible ?? 0 }};
            updateProductCardStock(originalStock);
        }

        // Agregar evento para restaurar stock cuando se cierra el modal de variantes
        document.addEventListener('DOMContentLoaded', function() {
            // Escuchar cuando se cierra el modal de variantes
            const variantModal = document.getElementById('variantSelectionModal');
            if (variantModal) {
                variantModal.addEventListener('hidden.bs.modal', function() {
                    // Restaurar stock original si no hay variante seleccionada
                    const selectedVariant = document.querySelector('.color-variant.border-blue-500');
                    if (!selectedVariant) {
                        restoreOriginalStock();
                    }
                });
            }
        });

        // ===== SISTEMA MODERNO DE GALERÍA DE IMÁGENES =====
        
        // Clase para manejar la galería de imágenes del producto
        class ProductImageGallery {
            constructor() {
                this.currentIndex = 0;
                this.images = [
                    @foreach ($producto->imagenes as $imagen)
                        '{{ $imagen->url_completa }}',
                    @endforeach
                ];
                this.mainImage = document.getElementById('mainImage');
                this.thumbnails = [];
                this.init();
            }

            init() {
                if (!this.mainImage || this.images.length === 0) return;
                
                this.setupThumbnails();
                this.setupNavigation();
                this.setupKeyboardNavigation();
                this.updateDisplay();
            }

            setupThumbnails() {
                const thumbnailContainer = document.querySelector('.thumbnail-btn')?.parentElement;
                if (!thumbnailContainer) return;

                this.thumbnails = Array.from(document.querySelectorAll('.thumbnail-btn'));
                this.thumbnails.forEach((btn, index) => {
                    btn.addEventListener('click', () => this.selectImage(index));
                    btn.addEventListener('mouseenter', () => {
                        btn.style.transform = 'scale(1.05)';
                    });
                    btn.addEventListener('mouseleave', () => {
                        if (index !== this.currentIndex) {
                            btn.style.transform = 'scale(1)';
                        }
                    });
                });
            }

            setupNavigation() {
                const prevBtn = document.getElementById('prevImage');
                const nextBtn = document.getElementById('nextImage');

                if (prevBtn) {
                    prevBtn.addEventListener('click', () => this.navigate('prev'));
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', () => this.navigate('next'));
                }
            }

            setupKeyboardNavigation() {
                document.addEventListener('keydown', (e) => {
                    if (this.images.length <= 1) return;
                    
                    if (e.key === 'ArrowLeft') {
                        this.navigate('prev');
                    } else if (e.key === 'ArrowRight') {
                        this.navigate('next');
                    }
                });
            }

            selectImage(index) {
                if (index < 0 || index >= this.images.length) return;
                
                this.currentIndex = index;
                this.updateDisplay();
            }

            navigate(direction) {
                if (this.images.length <= 1) return;
                
                if (direction === 'next') {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                } else {
                    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                }
                
                this.updateDisplay();
            }

            updateDisplay() {
                if (!this.mainImage || this.images.length === 0) return;

                const currentImageUrl = this.images[this.currentIndex];
                
                // Actualizar imagen principal con efecto de transición
                this.mainImage.style.opacity = '0';
                this.mainImage.style.transition = 'opacity 0.3s ease-in-out';
                
                setTimeout(() => {
                    this.mainImage.src = currentImageUrl;
                    this.mainImage.onload = () => {
                        this.mainImage.style.opacity = '1';
                    };
                    this.mainImage.onerror = () => {
                        console.error('Error loading image:', currentImageUrl);
                        this.mainImage.style.opacity = '1';
                    };
                }, 150);

                // Actualizar thumbnails
                this.updateThumbnails();
            }

            updateThumbnails() {
                this.thumbnails.forEach((btn, index) => {
                    if (index === this.currentIndex) {
                        // Thumbnail activo
                        btn.classList.remove('border-gray-200', 'dark:border-gray-600');
                        btn.classList.add('border-blue-600', 'ring-4', 'ring-blue-200', 'dark:ring-blue-800', 'shadow-lg', 'scale-105');
                    } else {
                        // Thumbnails inactivos
                        btn.classList.remove('border-blue-600', 'ring-4', 'ring-blue-200', 'dark:ring-blue-800', 'shadow-lg', 'scale-105');
                        btn.classList.add('border-gray-200', 'dark:border-gray-600');
                        btn.style.transform = 'scale(1)';
                    }
                });
            }

            updateWithVariantImages(imagenes) {
                if (!imagenes || imagenes.length === 0) return;
                
                this.images = imagenes;
                this.currentIndex = 0;
                this.updateDisplay();
            }

            restoreOriginalImages() {
                this.images = [
                    @foreach ($producto->imagenes as $imagen)
                        '{{ $imagen->url_completa }}',
                    @endforeach
                ];
                this.currentIndex = 0;
                this.updateDisplay();
            }
        }

        // Inicializar galería cuando el DOM esté listo
        let productGallery;
        document.addEventListener('DOMContentLoaded', function() {
            productGallery = new ProductImageGallery();
        });

        // Función mejorada para abrir zoom de imagen
        function openImageZoom() {
            if (!productGallery || productGallery.images.length === 0) return;
            
            const currentImage = productGallery.images[productGallery.currentIndex];
            
            // Crear modal de zoom moderno
            const zoomModal = document.createElement('div');
            zoomModal.className = 'fixed inset-0 bg-black bg-opacity-95 backdrop-blur-sm flex items-center justify-center z-50';
            zoomModal.style.transition = 'opacity 0.3s ease-in-out';
            zoomModal.style.opacity = '0';
            
            zoomModal.innerHTML = `
                <div class="relative max-w-6xl max-h-full p-4 w-full h-full flex items-center justify-center">
                    <button onclick="this.closest('.fixed').remove()" 
                            class="absolute top-4 right-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 p-3 rounded-full shadow-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 hover:scale-110 z-10">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <img src="${currentImage}" 
                         alt="Zoom" 
                         class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl"
                         style="animation: fadeIn 0.3s ease-in-out;">
                </div>
            `;
            
            document.body.appendChild(zoomModal);
            
            // Animación de entrada
            setTimeout(() => {
                zoomModal.style.opacity = '1';
            }, 10);
            
            // Cerrar con ESC o click fuera
            const closeModal = () => {
                zoomModal.style.opacity = '0';
                setTimeout(() => zoomModal.remove(), 300);
            };
            
            zoomModal.addEventListener('click', (e) => {
                if (e.target === zoomModal) {
                    closeModal();
                }
            });
            
            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', escHandler);
                }
            });
        }
        
        // Hacer la función globalmente accesible
        window.openImageZoom = openImageZoom;

        // ===== SISTEMA MODERNO DE VARIANTES DE COLOR =====
        
        // Clase para manejar la selección de variantes de color
        class ColorVariantSelector {
            constructor() {
                this.variants = [];
                this.selectedVariant = null;
                this.colorData = {};
                this.init();
            }

            init() {
                this.setupVariants();
                this.loadVariantData();
                this.selectFirstAvailable();
            }

            setupVariants() {
                this.variants = Array.from(document.querySelectorAll('.color-variant'));
                
                this.variants.forEach((variant, index) => {
                    // Limpiar cualquier texto que pueda estar dentro del botón
                    this.cleanVariantButton(variant);
                    
                    variant.addEventListener('click', () => this.selectVariant(variant, index));
                    
                    // Efectos hover mejorados
                    variant.addEventListener('mouseenter', () => {
                        if (variant.dataset.available === 'true' && !variant.classList.contains('border-blue-600')) {
                            variant.style.transform = 'scale(1.05)';
                        }
                    });
                    
                    variant.addEventListener('mouseleave', () => {
                        if (!variant.classList.contains('border-blue-600')) {
                            variant.style.transform = 'scale(1)';
                        }
                    });
                });
            }

            cleanVariantButton(variant) {
                // Remover todos los nodos de texto que contengan "disponibles" o números
                const textNodes = Array.from(variant.childNodes).filter(node => {
                    if (node.nodeType === Node.TEXT_NODE) {
                        const text = node.textContent.trim();
                        // Eliminar cualquier texto que contenga el stock o "disponibles"
                        const stockValue = variant.dataset.variantStock;
                        return text && (
                            text.includes('disponibles') || 
                            /^\d+$/.test(text) || 
                            text.match(/\d+\s*disponibles?/i) ||
                            (stockValue && text.includes(stockValue))
                        );
                    }
                    return false;
                });
                textNodes.forEach(node => node.remove());
                
                // Si el botón está disponible y no tiene el icono de deshabilitado, asegurar que esté vacío
                if (variant.dataset.available === 'true') {
                    const hasDisabledIcon = variant.querySelector('i.fa-times');
                    if (!hasDisabledIcon) {
                        // Limpiar cualquier contenido que no sea necesario
                        const children = Array.from(variant.children);
                        children.forEach(child => {
                            // Solo mantener el icono de deshabilitado si existe
                            if (!child.classList.contains('fa-times')) {
                                // Verificar si el contenido del hijo contiene el stock
                                const childText = child.textContent || '';
                                const stockValue = variant.dataset.variantStock;
                                if (childText.includes('disponibles') || 
                                    childText.match(/\d+\s*disponibles?/i) ||
                                    (stockValue && childText.includes(stockValue))) {
                                    child.remove();
                                }
                            }
                        });
                    }
                }
            }

            loadVariantData() {
                this.variants.forEach((variant) => {
                    const colorName = variant.dataset.color;
                    if (!colorName) return;

                    this.colorData[colorName] = {
                        description: variant.dataset.descripcion || '{{ __('messages.product_show.color_default_description') }}',
                        code: variant.dataset.codigoColor || '#CCCCCC',
                        stock: parseInt(variant.dataset.variantStock) || 0,
                        precioAdicional: parseFloat(variant.dataset.precioAdicional) || 0,
                        imagenes: []
                    };
                });

                // Cargar imágenes de variantes desde el servidor
                @foreach ($producto->variantes as $variante)
                    if (this.colorData['{{ $variante->nombre }}']) {
                        this.colorData['{{ $variante->nombre }}'].imagenes = [
                            @foreach ($variante->imagenes as $imagen)
                                '{{ $imagen->url_completa }}',
                            @endforeach
                        ];
                    }
                @endforeach
            }

            selectFirstAvailable() {
                const firstAvailable = this.variants.find(v => v.dataset.available === 'true');
                if (firstAvailable) {
                    const index = this.variants.indexOf(firstAvailable);
                    this.selectVariant(firstAvailable, index, false);
                }
            }

            selectVariant(variant, index, showMessage = true) {
                if (variant.dataset.available === 'false') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '{{ __('messages.product_show.color_not_available_title') }}',
                            text: '{{ __('messages.product_show.color_not_available_text') }}',
                            icon: 'warning',
                            confirmButtonText: '{{ __('messages.product_show.understood') }}',
                            confirmButtonColor: '#3B82F6',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        alert('Este color no está disponible actualmente.');
                    }
                    return;
                }

                // Limpiar el botón antes de seleccionarlo
                this.cleanVariantButton(variant);

                // Remover selección anterior con animación
                this.variants.forEach(v => {
                    // Limpiar cada variante también
                    this.cleanVariantButton(v);
                    v.classList.remove('border-blue-600', 'ring-4', 'ring-blue-200', 'dark:ring-blue-800', 'shadow-2xl', 'scale-110');
                    v.classList.add('border-gray-300', 'dark:border-gray-600');
                    v.style.transform = 'scale(1)';
                });

                // Agregar selección actual con animación
                variant.classList.remove('border-gray-300', 'dark:border-gray-600');
                variant.classList.add('border-blue-600', 'ring-4', 'ring-blue-200', 'dark:ring-blue-800', 'shadow-2xl', 'scale-110');
                variant.style.transform = 'scale(1.1)';

                // Actualizar variante seleccionada
                this.selectedVariant = variant;
                const colorName = variant.dataset.color;
                const colorInfo = this.colorData[colorName];

                if (colorInfo) {
                    this.updateColorInfo(colorName, colorInfo);
                    this.updateQuantitySelector(colorInfo.stock);
                    this.updateAddToCartButton(variant.dataset.varianteId);
                    
                    // Actualizar galería con imágenes de la variante
                    if (colorInfo.imagenes && colorInfo.imagenes.length > 0 && productGallery) {
                        productGallery.updateWithVariantImages(colorInfo.imagenes);
                    }
                }
            }

            updateColorInfo(colorName, colorData) {
                // Actualizar preview del color
                const selectedColorPreview = document.getElementById('selectedColorPreview');
                if (selectedColorPreview && colorData.code) {
                    selectedColorPreview.style.backgroundColor = colorData.code;
                    selectedColorPreview.style.transition = 'background-color 0.3s ease';
                }

                // Actualizar texto del color
                const selectedColorText = document.getElementById('selectedColorText');
                if (selectedColorText) {
                    selectedColorText.textContent = colorName;
                    selectedColorText.style.transition = 'color 0.3s ease';
                }

                // Actualizar stock
                const selectedColorStock = document.getElementById('selectedColorStock');
                if (selectedColorStock) {
                    selectedColorStock.innerHTML = 
                        `{{ __('messages.product_show.stock_available') }}: <span class="font-bold text-green-700 dark:text-green-400">${colorData.stock} {{ __('messages.product_show.stock_units') }}</span>`;
                }

                // Actualizar descripción
                const colorDescription = document.getElementById('colorDescription');
                if (colorDescription) {
                    colorDescription.textContent = colorData.description || '{{ __('messages.product_show.color_default_description') }}';
                    colorDescription.style.transition = 'opacity 0.3s ease';
                }

                // Actualizar precio adicional
                const selectedColorPrice = document.getElementById('selectedColorPrice');
                const precioAdicional = document.getElementById('precioAdicional');
                if (selectedColorPrice && precioAdicional) {
                    if (colorData.precioAdicional > 0) {
                        const formattedPrice = new Intl.NumberFormat('es-CO', {
                            style: 'currency',
                            currency: 'COP',
                            minimumFractionDigits: 0
                        }).format(colorData.precioAdicional);
                        precioAdicional.textContent = formattedPrice;
                        selectedColorPrice.style.display = 'block';
                    } else {
                        selectedColorPrice.style.display = 'none';
                    }
                }
            }

            updateQuantitySelector(stock) {
                if (typeof quantitySelector !== 'undefined' && quantitySelector) {
                    quantitySelector.setMax(stock);
                } else {
                    // Fallback si quantitySelector no está disponible
                    const quantityInput = document.getElementById('productQuantity');
                    if (quantityInput) {
                        const currentValue = parseInt(quantityInput.value) || 1;
                        quantityInput.max = stock;
                        
                        if (currentValue > stock) {
                            quantityInput.value = stock;
                        }
                        
                        if (typeof window.updateQuantityButtons === 'function') {
                            window.updateQuantityButtons();
                        }
                    }
                }
            }

            updateAddToCartButton(varianteId) {
                const addToCartBtn = document.getElementById('addToCartBtn');
                if (addToCartBtn && varianteId) {
                    addToCartBtn.dataset.varianteId = varianteId;
                    addToCartBtn.setAttribute('data-variante-id', varianteId);
                }
            }

        }

        // Inicializar selector de variantes cuando el DOM esté listo
        let colorVariantSelector;
        document.addEventListener('DOMContentLoaded', function() {
            colorVariantSelector = new ColorVariantSelector();
        });
    </script>
@endpush

<!-- Incluir modal de selección de variantes -->
<x-variant-selection-modal />

