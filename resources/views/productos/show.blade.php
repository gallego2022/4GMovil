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

    <!-- Product Details Section - Estilo Neutro -->
    <section id="producto" class="py-20 bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-800">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <!-- Product Card -->
                <div
                    class="bg-gradient-to-br from-white via-gray-50 to-gray-100 dark:from-gray-800 dark:via-gray-700 dark:to-gray-700 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-600 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:scale-[1.01] relative">
                    
                    <!-- Elemento decorativo superior sutil -->
                    <div class="absolute top-0 left-0 w-24 h-24 bg-gradient-to-br from-gray-400 to-gray-500 opacity-5 rounded-full -translate-x-12 -translate-y-12"></div>
                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-gray-400 to-gray-500 opacity-5 rounded-full translate-x-10 -translate-y-10"></div>
                    <div class="grid lg:grid-cols-2 gap-12 p-8">

                        <!-- Left Section - Product Images -->
                        <div class="product-gallery-container group">
                            <!-- Main Image Container -->
                            <div class="main-image-wrapper">
                                @if ($producto->imagenes->first())
                                    <img id="mainImage"
                                        src="{{ $producto->imagenes->first()->url_completa }}"
                                        alt="{{ $producto->nombre_producto }}"
                                        class="main-image bg-gray-50 dark:bg-gray-800 shadow-lg group-hover:scale-105">
                                @else
                                    <div id="mainImage" class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-xl shadow-lg flex items-center justify-center">
                                        <i class="fas fa-image text-6xl text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                @endif

                                <!-- Navigation Arrows -->
                                <button
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white dark:bg-gray-800 p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-chevron-left text-gray-600 dark:text-gray-300"></i>
                                </button>
                                <button
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white dark:bg-gray-800 p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-chevron-right text-gray-600 dark:text-gray-300"></i>
                                </button>

                                <!-- Zoom Indicator -->
                                <div class="absolute top-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer"
                                    onclick="openImageZoom()">
                                    <i class="fas fa-search-plus mr-1"></i>Zoom
                                </div>
                            </div>

                            <!-- Thumbnail Gallery - Fixed below main image -->
                            <div class="thumbnail-gallery-wrapper">
                                <div class="thumbnail-grid">
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
                                            <img src="{{ $imagenUrl }}"
                                                alt="{{ $producto->nombre_producto }} - Vista {{ $i + 1 }}"
                                                class="thumbnail-item {{ $i === 0 ? 'border-blue-500' : '' }}"
                                                data-image-url="{{ $imagenPrincipal }}">
                                        @else
                                            <div class="thumbnail-item flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400 dark:text-gray-500 text-sm"></i>
                                            </div>
                                        @endif
                                    @endfor
                                </div>
                            </div>

                            <!-- Pagination Dots -->
                            <div class="pagination-dots">
                                @for ($i = 1; $i <= 5; $i++)
                                    <div
                                        class="w-2 h-2 rounded-full {{ $i === 1 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600' }} cursor-pointer hover:bg-blue-500 transition-colors duration-200"
                                        >
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Right Section - Product Details -->
                        <div class="space-y-4">

                            <!-- Action Icons - Estilo Neutro -->
                            <div class="flex items-center space-x-4">
                                <button
                                    class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-600">
                                    <i
                                        class="fas fa-heart text-gray-600 dark:text-gray-400 group-hover:text-red-500 transition-colors duration-200"></i>
                                </button>
                                <button
                                    class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 group shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-600">
                                    <i
                                        class="fas fa-share-alt text-gray-600 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200 transition-colors duration-200"></i>
                                </button>           
                            </div>
                            <!-- Product Title - Estilo Neutro -->
                            <div class="relative">
                                <div class="absolute -left-3 top-0 w-0.5 h-full bg-gray-400 rounded-full"></div>
                                <div class="pl-6">
                                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-3">
                                        {{ $producto->nombre_producto }}
                                    </h1>
                                    <div class="flex items-center space-x-4">
                                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-full border border-gray-200 dark:border-gray-600">
                                            <i class="fas fa-barcode mr-2"></i>
                                            SKU: {{ $producto->sku ?? 'N/A' }}
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-300 text-sm font-medium rounded-full border border-green-200 dark:border-green-600">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Disponible
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing - Estilo Neutro -->
                            <div class="space-y-4">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-baseline space-x-4">
                                        <div class="relative">
                                            <span class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white">
                                                ${{ number_format($producto->precio ?? 0, 0, ',', '.') }}
                                            </span>
                                            <div class="absolute -top-2 -right-2 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                        </div>
                                        @if (isset($producto->precio_anterior) && $producto->precio_anterior > $producto->precio)
                                            <div class="flex flex-col items-start space-y-2">
                                                <span class="text-xl text-gray-500 line-through">
                                                    ${{ number_format($producto->precio_anterior, 0, ',', '.') }}
                                                </span>
                                                <span
                                                    class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                                    -{{ round((($producto->precio_anterior - $producto->precio) / $producto->precio_anterior) * 100) }}% OFF
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Información adicional de precio -->
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Envío:</span>
                                            <span class="text-green-600 dark:text-green-400 font-semibold">GRATIS</span>
                                        </div>
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Garantía:</span>
                                            <span class="text-gray-700 dark:text-gray-300 font-semibold">1 Año</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Variant Info - Modern Corporate Design -->
                            <div
                                class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 rounded-2xl p-6 border-2 border-blue-200 dark:border-blue-700/30 shadow-lg relative overflow-hidden">
                                
                                <!-- Elemento decorativo -->
                                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-500 opacity-10 rounded-full translate-x-10 -translate-y-10"></div>
                                
                                <div class="flex items-center space-x-4 relative z-10">
                                    <div class="relative">
                                        <div class="w-8 h-8 rounded-full border-3 border-blue-500 shadow-lg" id="selectedColorPreview"
                                            style="background-color: {{ $producto->variantes->first() ? $producto->variantes->first()->codigo_color ?? '#000000' : '#000000' }};">
                                        </div>
                                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white animate-pulse"></div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <i class="fas fa-palette text-blue-500"></i>
                                            <p class="text-sm font-bold text-blue-900 dark:text-blue-100">
                                                Color: <span
                                                    id="selectedColorText" class="text-indigo-700 dark:text-indigo-300">{{ $producto->variantes->first() ? $producto->variantes->first()->nombre : 'No disponible' }}</span>
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <i class="fas fa-boxes text-green-500"></i>
                                            <p class="text-xs text-green-700 dark:text-green-300" id="selectedColorStock">
                                                Stock: <span class="font-bold">{{ $producto->variantes->first() ? $producto->variantes->first()->stock : 0 }} unidades</span>
                                            </p>
                                        </div>
                                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium" id="selectedColorPrice"
                                            style="display: {{ $producto->variantes->first() && $producto->variantes->first()->precio_adicional > 0 ? 'block' : 'none' }};">
                                            <i class="fas fa-plus-circle mr-1"></i>
                                            Precio adicional: <span
                                                id="precioAdicional" class="font-bold">${{ number_format($producto->variantes->first() ? $producto->variantes->first()->precio_adicional : 0, 0, ',', '.') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Color Variants - Modern Corporate Design -->
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-palette text-white text-sm"></i>
                                        </div>
                                        <h3 class="text-xl font-bold bg-gradient-to-r from-gray-900 to-blue-800 dark:from-white dark:to-blue-200 bg-clip-text text-transparent">Selecciona tu Color</h3>
                                    </div>
                                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/30 px-3 py-1 rounded-full border border-blue-200 dark:border-blue-700/30"
                                        id="selectedColorName">{{ $producto->variantes->first() ? $producto->variantes->first()->nombre : 'No disponible' }}</span>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @if ($producto->variantes->count() > 0)
                                        @foreach ($producto->variantes as $index => $variante)
                                            <div class="relative group">
                                                <button
                                                    class="color-variant w-16 h-16 rounded-2xl border-3 transition-all duration-300 {{ $index === 0 ? 'border-blue-500 ring-4 ring-blue-200 shadow-xl' : 'border-gray-300 dark:border-gray-600' }} {{ !$variante->disponible ? 'opacity-50 cursor-not-allowed' : 'hover:scale-110 hover:shadow-2xl hover:border-blue-400' }}"
                                                    style="background-color: {{ $variante->codigo_color ?? '#CCCCCC' }};"
                                                    data-color="{{ $variante->nombre }}"
                                                    data-available="{{ $variante->disponible ? 'true' : 'false' }}"
                                                    data-stock="{{ $variante->stock }}"
                                                    data-precio-adicional="{{ $variante->precio_adicional }}"
                                                    data-descripcion="{{ $variante->descripcion }}"
                                                    data-codigo-color="{{ $variante->codigo_color }}"
                                                    data-has-images="{{ $variante->imagenes->count() > 0 ? 'true' : 'false' }}"
                                                    {{ !$variante->disponible ? 'disabled' : '' }}
                                                    title="{{ $variante->nombre }} - Stock: {{ $variante->stock }} {{ !$variante->disponible ? '(No disponible)' : '' }} {{ $variante->imagenes->count() > 0 ? '(Con imágenes)' : '(Sin imágenes)' }}">
                                                    
                                                    <!-- Efecto de brillo en hover -->
                                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                    
                                                    @if (!$variante->disponible)
                                                        <div class="w-full h-full flex items-center justify-center relative z-10">
                                                            <i class="fas fa-times text-gray-400 text-lg"></i>
                                                        </div>
                                                    @endif
                                                </button>

                                                <!-- Stock indicator - Modern Corporate Design -->
                                                @if ($variante->stock <= 5 && $variante->stock > 0)
                                                    <div
                                                        class="absolute -top-2 -right-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold shadow-lg border-2 border-white animate-pulse">
                                                        {{ $variante->stock }}
                                                    </div>
                                                @elseif($variante->stock == 0)
                                                    <div
                                                        class="absolute -top-2 -right-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center shadow-lg border-2 border-white">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </div>
                                                @endif

                                                <!-- Image indicator - Modern Corporate Design -->
                                                @if ($variante->imagenes->count() > 0)
                                                    <div
                                                        class="absolute -bottom-2 -left-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center shadow-lg border-2 border-white">
                                                        <i class="fas fa-image text-xs"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Fallback: Mostrar mensaje si no hay variantes -->
                                        <div class="text-center w-full py-4">
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">
                                                No hay variantes de color disponibles para este producto
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Color Description - Modern Corporate Design -->
                            <div class="bg-gradient-to-r from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-800 dark:via-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-700/30 shadow-lg relative overflow-hidden">
                                
                                <!-- Elemento decorativo -->
                                <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-400 to-indigo-500 opacity-10 rounded-full translate-x-8 -translate-y-8"></div>
                                
                                <div class="flex items-start space-x-3 relative z-10">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-info-circle text-white text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-bold text-blue-900 dark:text-blue-100 mb-2">Descripción del Color</h4>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" id="colorDescription">
                                            {{ $producto->variantes->first() ? $producto->variantes->first()->descripcion ?? 'Color elegante y versátil que combina con cualquier estilo y se adapta perfectamente a tu personalidad.' : 'No hay descripción disponible para este color.' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons - Modern Corporate Design -->
                            <div class="space-y-4">
                                @php
                                    $tieneVariantes = $producto->variantes && $producto->variantes->count() > 0;
                                    $stockDisponible = $producto->stock_disponible;
                                @endphp

                                @if ($stockDisponible > 0)
                                    @if ($tieneVariantes)
                                        <!-- Si tiene variantes, mostrar botón que abra modal de selección -->
                                        <button type="button"
                                            class="select-variant w-full bg-gray-800 hover:bg-gray-900 text-white py-4 px-8 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl shadow-lg group relative overflow-hidden"
                                            data-producto-id="{{ $producto->producto_id }}" 
                                            data-producto-nombre="{{ $producto->nombre_producto }}" 
                                            data-producto-precio="{{ $producto->precio }}">
                                            
                                            <!-- Efecto de brillo sutil -->
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                            
                                            <div class="relative z-10 flex items-center justify-center">
                                                <i class="fas fa-palette mr-3 text-xl group-hover:scale-110 transition-transform duration-300"></i>
                                                <span>Seleccionar Variante</span>
                                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                                            </div>
                                        </button>
                                    @else
                                        <!-- Si no tiene variantes, agregar directamente al carrito -->
                                        <button type="button"
                                            class="add-to-cart w-full bg-green-600 hover:bg-green-700 text-white py-4 px-8 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 hover:shadow-xl shadow-lg group relative overflow-hidden"
                                            data-id="{{ $producto->producto_id }}" 
                                            data-name="{{ $producto->nombre_producto }}" 
                                            data-price="{{ $producto->precio }}">
                                            
                                            <!-- Efecto de brillo sutil -->
                                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                                            
                                            <div class="relative z-10 flex items-center justify-center">
                                                <i class="fas fa-shopping-cart mr-3 text-xl group-hover:scale-110 transition-transform duration-300"></i>
                                                <span>Agregar al Carrito</span>
                                                <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                                            </div>
                                        </button>
                                    @endif
                                @else
                                    <button type="button"
                                        class="w-full bg-gradient-to-r from-gray-400 to-gray-500 text-white py-5 px-8 rounded-2xl font-bold text-lg cursor-not-allowed opacity-75" disabled>
                                        <i class="fas fa-times mr-3 text-xl"></i>
                                        Sin stock disponible
                                    </button>
                                @endif
                            </div>

                            <!-- Quick Actions - Modern Corporate Design -->
                            <div class="flex flex-wrap gap-4">
                                <a href="#reseñas"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-100 via-orange-100 to-yellow-200 dark:from-yellow-900/30 dark:via-orange-900/30 dark:to-yellow-800/30 text-yellow-700 dark:text-yellow-300 rounded-xl hover:from-yellow-200 hover:via-orange-200 hover:to-yellow-300 dark:hover:from-yellow-800/30 dark:hover:via-orange-800/30 dark:hover:to-yellow-700/30 transition-all duration-300 border border-yellow-200 dark:border-yellow-700/30 shadow-lg hover:shadow-xl group">
                                    <i class="fas fa-star mr-3 text-yellow-500 group-hover:scale-110 transition-transform duration-300"></i>
                                    <span class="font-semibold">Ver Reseñas ({{ $producto->resenas->count() }})</span>
                                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                                </a>
                                <button onclick="openReviewModal()"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-100 via-red-100 to-orange-200 dark:from-orange-900/30 dark:via-red-900/30 dark:to-orange-800/30 text-orange-700 dark:text-orange-300 rounded-xl hover:from-orange-200 hover:via-red-200 hover:to-orange-300 dark:hover:from-orange-800/30 dark:hover:via-red-800/30 dark:hover:to-orange-700/30 transition-all duration-300 border border-orange-200 dark:border-orange-700/30 shadow-lg hover:shadow-xl group">
                                    <i class="fas fa-edit mr-3 text-orange-500 group-hover:scale-110 transition-transform duration-300"></i>
                                    <span class="font-semibold">Escribir Reseña</span>
                                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Info - Full Width - Estilo Neutro -->
                    <div class="px-8 pb-8">
                        <div
                            class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 w-full border border-gray-200 dark:border-gray-600 shadow-lg relative overflow-hidden">
                            
                            <!-- Elementos decorativos sutiles -->
                            <div class="absolute top-0 left-0 w-20 h-20 bg-gray-400 opacity-5 rounded-full -translate-x-10 -translate-y-10"></div>
                            <div class="absolute bottom-0 right-0 w-16 h-16 bg-gray-400 opacity-5 translate-x-8 translate-y-8"></div>
                            
                            <div class="relative z-10">
                                <h3 class="font-bold text-2xl text-gray-900 dark:text-white mb-6 flex items-center">
                                    <div class="w-10 h-10 bg-gray-600 rounded-xl flex items-center justify-center mr-4">
                                        <i class="fas fa-shield-halved text-white text-lg"></i>
                                    </div>
                                    <span class="text-gray-900 dark:text-white">
                                        Información Adicional
                                    </span>
                                </h3>
                                <div class="grid md:grid-cols-3 gap-8">
                                    <div class="flex items-center space-x-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                        <div
                                            class="bg-green-600 w-12 h-12 rounded-xl flex items-center justify-center shadow-md">
                                            <i class="fas fa-check text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <span class="text-gray-700 dark:text-gray-300 font-semibold">Garantía Oficial</span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">1 año completo</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                        <div
                                            class="bg-gray-600 w-12 h-12 rounded-xl flex items-center justify-center shadow-md">
                                            <i class="fas fa-undo text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <span class="text-gray-700 dark:text-gray-300 font-semibold">Devolución</span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">30 días sin preguntas</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600 shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                                        <div
                                            class="bg-gray-600 w-12 h-12 rounded-xl flex items-center justify-center shadow-md">
                                            <i class="fas fa-headset text-white text-lg"></i>
                                        </div>
                                        <div>
                                            <span class="text-gray-700 dark:text-gray-300 font-semibold">Soporte Técnico</span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Incluido 24/7</p>
                                        </div>
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
                        Características del Producto
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
                                    <span class="text-gray-600 dark:text-gray-400">{{ $producto->marca->nombre_marca ?? 'No especificado' }}</span>
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
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Stock Disponible</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $producto->stock }} unidades</span>
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
                        <i class="fas fa-star mr-2"></i>Reseñas de Clientes
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        Lo que dicen nuestros clientes
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Descubre las experiencias de otros usuarios con {{ $producto->nombre_producto }}
                    </p>
                </div>

                <!-- Reviews Summary -->
                <div
                    class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-2xl p-8 mb-12">
                    <div class="grid md:grid-cols-3 gap-8 items-center">
                        <!-- Overall Rating -->
                        <div class="text-center">
                            <div class="text-5xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">
                                {{ number_format($producto->resenas->avg('calificacion') ?? 0, 1) }}
                            </div>
                            <div class="flex justify-center mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fas fa-star {{ $i <= ($producto->resenas->avg('calificacion') ?? 0) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 dark:text-gray-400">
                                Basado en {{ $producto->resenas->count() }} reseñas
                            </p>
                        </div>

                        <!-- Rating Breakdown -->
                        <div class="space-y-2">
                            @for ($rating = 5; $rating >= 1; $rating--)
                                @php
                                    $count = $producto->resenas->where('calificacion', $rating)->count();
                                    $percentage =
                                        $producto->resenas->count() > 0
                                            ? ($count / $producto->resenas->count()) * 100
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
                    @forelse ($producto->resenas->take(5) as $resena)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($resena->usuario->nombre ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $resena->usuario->nombre ?? 'Usuario' }}
                                        </h4>
                                        <div class="flex items-center space-x-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star {{ $i <= $resena->calificacion ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }} text-sm"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $resena->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ $resena->comentario }}
                            </p>
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

                @if ($producto->resenas->count() > 5)
                    <div class="text-center mt-8">
                        <button
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors duration-200">
                            Ver todas las {{ $producto->resenas->count() }} reseñas
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
                    <span id="zoomImageCounter">1</span> de <span id="zoomTotalImages">1</span>
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
                        <textarea name="comentario" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white resize-none"
                            placeholder="Comparte tu experiencia con este producto..."></textarea>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" onclick="closeReviewModal()"
                            class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
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
                                     class="text-lg font-bold text-blue-600 dark:text-blue-400">${{ number_format($productoRelacionado->precio ?? 0, 0, ',', '.') }}</span>
                                 @if (isset($productoRelacionado->precio_anterior) && $productoRelacionado->precio_anterior > $productoRelacionado->precio)
                                     <span
                                         class="text-sm text-gray-500 line-through">${{ number_format($productoRelacionado->precio_anterior, 0, ',', '.') }}</span>
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

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
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

            // Add to cart functionality
            const addCartBtn = document.querySelector('button:has(i.fa-shopping-cart)');
            if (addCartBtn) {
                addCartBtn.addEventListener('click', function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: '¡Producto Agregado!',
                            text: 'El producto se ha agregado al carrito exitosamente.',
                            icon: 'success',
                            confirmButtonText: 'Continuar Comprando',
                            confirmButtonColor: '#3B82F6'
                        });
                    } else {
                        alert('Producto agregado al carrito');
                    }
                });
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
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#3B82F6'
                        });
                    } else {
                        alert(icon.classList.contains('text-red-500') ? 'Producto agregado a favoritos' :
                            'Producto removido de favoritos');
                    }
                });
            }

            // Color Variants functionality - More robust initialization
            console.log('=== INITIALIZING COLOR VARIANT SELECTOR ===');
            
            // Wait a bit to ensure DOM is fully loaded
            setTimeout(() => {
                const colorVariants = document.querySelectorAll('.color-variant');
                const selectedColorName = document.getElementById('selectedColorName');
                const colorDescription = document.getElementById('colorDescription');

                console.log('Color variants found:', colorVariants.length);
                console.log('Selected color name element:', selectedColorName);
                console.log('Color description element:', colorDescription);

                if (colorVariants.length === 0) {
                    console.error('No color variants found!');
                    return;
                }

                // Color data will be populated dynamically from the database
                const colorData = {};

                // Populate color data from the DOM
                colorVariants.forEach((variant, index) => {
                    const colorName = variant.dataset.color;
                    console.log(`Processing variant ${index + 1}:`, colorName, 'Dataset:', variant.dataset);
                    
                    if (!colorName) {
                        console.error('Variant has no color name!', variant);
                        return;
                    }
                    
                    colorData[colorName] = {
                        description: variant.dataset.descripcion || 'Color elegante y versátil.',
                        code: variant.dataset.codigoColor || '#CCCCCC',
                        stock: parseInt(variant.dataset.stock) || 0,
                        precioAdicional: parseFloat(variant.dataset.precioAdicional) || 0,
                        imagenes: [] // Will be populated from database
                    };
                });

                console.log('Initial colorData:', colorData);

                // Load images for each variant from the database
                @foreach ($producto->variantes as $variante)
                    console.log('Loading images for variant: {{ $variante->nombre }}');
                    if (colorData['{{ $variante->nombre }}']) {
                        colorData['{{ $variante->nombre }}'].imagenes = [
                            @foreach ($variante->imagenes as $imagen)
                                '{{ $imagen->url_completa }}',
                            @endforeach
                        ];
                        console.log('Images loaded for {{ $variante->nombre }}:', colorData['{{ $variante->nombre }}'].imagenes);
                        console.log('Image URLs for {{ $variante->nombre }}:');
                        @foreach ($variante->imagenes as $imagen)
                            console.log('  - {{ $imagen->url_completa }}');
                        @endforeach
                    } else {
                        console.log('No colorData found for variant: {{ $variante->nombre }}');
                    }
                @endforeach

                console.log('Final colorData with images:', colorData);

                // Initialize with first variant if available
                const firstVariant = colorVariants[0];
                console.log('First variant:', firstVariant);
                if (firstVariant && firstVariant.dataset.available === 'true') {
                    const firstColorName = firstVariant.dataset.color;
                    const firstColorInfo = colorData[firstColorName];
                    console.log('First color name:', firstColorName);
                    console.log('First color info:', firstColorInfo);

                    if (firstColorInfo && firstColorInfo.imagenes && firstColorInfo.imagenes.length > 0) {
                        console.log('Updating gallery with first variant images');
                        // Update gallery with first variant images
                        updateGalleryWithVariantImages(firstColorInfo.imagenes);
                    } else {
                        console.log('No images found for first variant');
                    }
                } else {
                    console.log('First variant not available or not found');
                }

                colorVariants.forEach(variant => {
                    variant.addEventListener('click', function() {
                        console.log('Variant clicked:', this.dataset.color);
                        console.log('Available:', this.dataset.available);
                        
                        if (this.dataset.available === 'false') {
                            console.log('Variant not available');
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    title: 'Color No Disponible',
                                    text: 'Este color no está disponible actualmente.',
                                    icon: 'warning',
                                    confirmButtonText: 'Entendido',
                                    confirmButtonColor: '#3B82F6'
                                });
                            } else {
                                alert('Este color no está disponible actualmente.');
                            }
                            return;
                        }

                        // Update selected color
                        const colorName = this.dataset.color;
                        const colorInfo = colorData[colorName];
                        console.log('Selected color name:', colorName);
                        console.log('Color info:', colorInfo);

                        selectedColorName.textContent = colorName;

                        // Update color description
                        colorDescription.textContent = colorInfo.description;

                        // Update selected color preview and info
                        const selectedColorPreview = document.getElementById('selectedColorPreview');
                        const selectedColorText = document.getElementById('selectedColorText');
                        const selectedColorStock = document.getElementById('selectedColorStock');

                        if (selectedColorPreview) {
                            selectedColorPreview.style.backgroundColor = colorInfo.code;
                        }
                        if (selectedColorText) {
                            selectedColorText.textContent = colorName;
                        }
                        if (selectedColorStock) {
                            selectedColorStock.textContent =
                                `Stock disponible: ${colorInfo.stock} unidades`;
                        }

                        // Update price additional info
                        const selectedColorPrice = document.getElementById('selectedColorPrice');
                        const precioAdicional = document.getElementById('precioAdicional');
                        if (selectedColorPrice && precioAdicional) {
                            if (colorInfo.precioAdicional > 0) {
                                precioAdicional.textContent =
                                    `$${colorInfo.precioAdicional.toLocaleString('es-CO')}`;
                                selectedColorPrice.style.display = 'block';
                            } else {
                                selectedColorPrice.style.display = 'none';
                            }
                        }

                        // Update price if there's an additional cost
                        const priceElement = document.querySelector(
                        '.text-4xl.font-bold.text-blue-600');
                        if (priceElement) {
                            const basePrice = parseFloat('{{ $producto->precio ?? 0 }}');
                            const totalPrice = basePrice + colorInfo.precioAdicional;
                            priceElement.textContent = `$${totalPrice.toLocaleString('es-CO')}`;
                        }

                        // Update visual selection
                        colorVariants.forEach(v => {
                            v.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                            v.classList.add('border-gray-300', 'dark:border-gray-600');
                        });

                        this.classList.remove('border-gray-300', 'dark:border-gray-600');
                        this.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');

                        // Update gallery with variant images if available
                        console.log('Checking variant images:', colorInfo.imagenes);
                        if (colorInfo.imagenes && colorInfo.imagenes.length > 0) {
                            console.log('Updating gallery with variant images');
                            updateGalleryWithVariantImages(colorInfo.imagenes);
                        } else {
                            console.log('No variant images, keeping original product images');
                            // Keep original product images if no variant images
                            // No need to restore, just keep current state
                        }

                        // Show success message with stock info and image status
                        const stock = parseInt(this.dataset.stock);
                        const hasImages = this.dataset.hasImages === 'true';
                        let stockMessage = `Has seleccionado el color ${colorName}`;

                        if (stock <= 5 && stock > 0) {
                            stockMessage += ` (Solo quedan ${stock} disponibles)`;
                        }

                        if (!hasImages) {
                            stockMessage += ` - Se muestran las imágenes generales del producto`;
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Color Seleccionado',
                                text: stockMessage,
                                icon: stock <= 3 ? 'warning' : (hasImages ? 'success' : 'info'),
                                timer: 3000,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });
                        }
                    });
                });

            console.log('=== COLOR VARIANT SELECTOR INITIALIZATION COMPLETE ===');
        }, 100); // Small delay to ensure DOM is fully loaded

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
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#3B82F6'
                        }).then(() => {
                            closeReviewModal();
                            // Recargar la página para mostrar la nueva reseña
                            location.reload();
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
            
            // Verificar botones de variantes
            const selectVariantButtons = document.querySelectorAll('.select-variant');
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            
            console.log('Botones select-variant encontrados:', selectVariantButtons.length);
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
    </script>
@endpush

<!-- Incluir modal de selección de variantes -->
<x-variant-selection-modal />

