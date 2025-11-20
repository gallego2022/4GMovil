<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-check" content="{{ Auth::check() ? 'true' : 'false' }}">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta-description')">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style_inicio.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js se carga desde Vite para mejor rendimiento -->
    <!-- CSS de animaciones de carga -->
    <link rel="stylesheet" href="{{ asset('css/loading-animations.css') }}">
    @stack('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Estilos para modo oscuro en landing */
        .dark body {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .dark .bg-white {
            background-color: #1e293b;
        }

        .dark .text-gray-800 {
            color: #e2e8f0;
        }

        .dark .text-gray-600 {
            color: #94a3b8;
        }

        .dark .bg-gray-100 {
            background-color: #334155;
        }

        .dark .bg-gray-900 {
            background-color: #0f172a;
        }

        .dark .border-gray-200 {
            border-color: #475569;
        }

        .dark .border-gray-300 {
            border-color: #475569;
        }

        .dark .border-gray-600 {
            border-color: #64748b;
        }

        .dark .border-gray-700 {
            border-color: #475569;
        }

        .dark .border-gray-800 {
            border-color: #374151;
        }

        .dark .hover\:bg-gray-50:hover {
            background-color: #334155;
        }

        .dark .hover\:bg-gray-100:hover {
            background-color: #475569;
        }

        .dark .hover\:bg-gray-800:hover {
            background-color: #1e293b;
        }

        .dark .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
        }

        .dark .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
        }

        .dark .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }

        /* Estilos específicos para elementos del landing en modo oscuro */
        .dark .bg-gradient-to-b.from-gray-400.to-gray-100 {
            background: linear-gradient(to bottom, #475569, #334155);
        }

        .dark .bg-gradient-to-b.from-gray-400.to-gray-100 h1 {
            color: #ffffff;
        }

        .dark .bg-gradient-to-b.from-gray-400.to-gray-100 p {
            color: #e2e8f0;
        }

        .dark .bg-gray-800 {
            background-color: #1e293b;
        }

        .dark .bg-gray-800 h2 {
            color: #ffffff;
        }

        .dark .bg-gray-800 input,
        .dark .bg-gray-800 textarea {
            background-color: #334155;
            border-color: #64748b;
            color: #e2e8f0;
        }

        .dark .bg-gray-800 input::placeholder,
        .dark .bg-gray-800 textarea::placeholder {
            color: #94a3b8;
        }

        .dark .bg-gray-800 input:focus,
        .dark .bg-gray-800 textarea:focus {
            border-color: #3b82f6;
            background-color: #475569;
        }

        .dark .bg-gray-300 {
            background-color: #475569;
        }

        .dark .bg-gray-300:hover {
            background-color: #64748b;
        }

        .dark .text-gray-900 {
            color: #ffffff;
        }

        .dark .text-gray-700 {
            color: #e2e8f0;
        }

        .dark .text-gray-500 {
            color: #94a3b8;
        }

        .dark .text-gray-400 {
            color: #94a3b8;
        }

        .dark .text-gray-300 {
            color: #cbd5e1;
        }

        .dark .text-gray-200 {
            color: #e2e8f0;
        }

        .dark .text-gray-100 {
            color: #f1f5f9;
        }

        .dark .text-white {
            color: #ffffff;
        }

        .dark .text-black {
            color: #ffffff;
        }

        .dark .bg-white {
            background-color: #1e293b;
        }

        .dark .bg-white:hover {
            background-color: #334155;
        }

        .dark .bg-gray-50 {
            background-color: #334155;
        }

        .dark .bg-gray-50:hover {
            background-color: #475569;
        }

        .dark .bg-gray-200 {
            background-color: #475569;
        }

        .dark .bg-gray-200:hover {
            background-color: #64748b;
        }

        .dark .bg-gray-300 {
            background-color: #64748b;
        }

        .dark .bg-gray-300:hover {
            background-color: #94a3b8;
        }

        .dark .bg-gray-400 {
            background-color: #94a3b8;
        }

        .dark .bg-gray-400:hover {
            background-color: #cbd5e1;
        }

        .dark .bg-gray-500 {
            background-color: #64748b;
        }

        .dark .bg-gray-500:hover {
            background-color: #94a3b8;
        }

        .dark .bg-gray-600 {
            background-color: #475569;
        }

        .dark .bg-gray-600:hover {
            background-color: #64748b;
        }

        .dark .bg-gray-700 {
            background-color: #374151;
        }

        .dark .bg-gray-700:hover {
            background-color: #4b5563;
        }

        .dark .bg-gray-800 {
            background-color: #1f2937;
        }

        .dark .bg-gray-800:hover {
            background-color: #374151;
        }

        .dark .bg-gray-900 {
            background-color: #111827;
        }

        .dark .bg-gray-900:hover {
            background-color: #1f2937;
        }

        /* Scroll Progress Indicator */
        .scroll-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(90deg, #088af5, #023dfd, #0a2bbe);
            z-index: 9999;
            transition: width 0.1s ease-out;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dark .scroll-indicator {
            background: linear-gradient(90deg, #088af5, #023dfd, #0a2bbe);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Footer Styles - Light and Dark Mode */
        footer {
            background: #ffffff;
        }

        .dark footer {
            background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #111827 100%);
        }

        /* Light mode styles */
        footer .bg-white\/5 {
            background-color: rgba(0, 0, 0, 0.05);
        }

        footer .border-white\/10 {
            border-color: rgba(0, 0, 0, 0.1);
        }

        footer .hover\:border-white\/20:hover {
            border-color: rgba(0, 0, 0, 0.2);
        }

        footer .hover\:bg-white\/10:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }

        footer .text-gray-300 {
            color: #6b7280;
        }

        footer .text-gray-400 {
            color: #9ca3af;
        }

        footer .text-white {
            color: #111827;
        }

        footer .border-gray-700\/50 {
            border-color: rgba(55, 65, 81, 0.5);
        }

        /* Dark mode overrides */
        .dark footer .bg-white\/5 {
            background-color: rgba(31, 41, 55, 0.5);
        }

        .dark footer .border-white\/10 {
            border-color: rgba(75, 85, 99, 0.5);
        }

        .dark footer .hover\:border-white\/20:hover {
            border-color: rgba(107, 114, 128, 0.5);
        }

        .dark footer .hover\:bg-white\/10:hover {
            background-color: rgba(31, 41, 55, 0.7);
        }

        .dark footer .text-gray-300 {
            color: #d1d5db;
        }

        .dark footer .text-gray-400 {
            color: #9ca3af;
        }

        .dark footer .text-white {
            color: #f9fafb;
        }

        .dark footer .border-gray-700\/50 {
            border-color: rgba(55, 65, 81, 0.5);
        }

        .dark footer .bg-blue-500\/20 {
            background-color: rgba(59, 130, 246, 0.2);
        }

        .dark footer .bg-green-500\/20 {
            background-color: rgba(16, 185, 129, 0.2);
        }

        .dark footer .bg-purple-500\/20 {
            background-color: rgba(139, 92, 246, 0.2);
        }

        .dark footer .bg-orange-500\/20 {
            background-color: rgba(245, 158, 11, 0.2);
        }

        .dark footer .group-hover\:bg-blue-500\/30:hover {
            background-color: rgba(59, 130, 246, 0.3);
        }

        .dark footer .group-hover\:bg-green-500\/30:hover {
            background-color: rgba(16, 185, 129, 0.3);
        }

        .dark footer .group-hover\:bg-purple-500\/30:hover {
            background-color: rgba(139, 92, 246, 0.3);
        }

        .dark footer .group-hover\:bg-orange-500\/30:hover {
            background-color: rgba(245, 158, 11, 0.3);
        }
    </style>
</head>

<body class="bg-white dark:bg-gray-900 transition-colors duration-300 {{ Auth::check() ? 'authenticated' : '' }}">
    <!-- Componente de Notificaciones -->
    <x-notifications />
    
    @if(Auth::check())
        <script>
            window.isAuthenticated = true;
        </script>
    @else
        <script>
            window.isAuthenticated = false;
        </script>
    @endif
    
    <!-- Scroll Progress Indicator -->
    <div class="scroll-indicator" id="scrollIndicator"></div>
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 sticky top-0 z-50 shadow-sm transition-colors duration-300" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('landing') }}" class="text-2xl font-bold text-blue-600">
                    <img src="{{ asset('img/Logo_2.png') }}" alt="4GMovil " class="h-12 sm:h-14 lg:h-16">
                </a>
            </div>

            <!-- Desktop Menu (desde md - 768px) -->
            <div class="hidden md:flex items-center space-x-3 lg:space-x-6">
                <a href="{{ route('landing') }}" data-loading-message="Cargando inicio..."
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 text-sm lg:text-base">{{ __('messages.nav.home') }}</a>

                <a href="{{ route('productos.lista') }}" data-loading-message="Cargando productos..."
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 text-sm lg:text-base">{{ __('messages.nav.products') }}</a>

                <a href="{{ route('servicios') }}" data-loading-message="Cargando servicio tecnico..."
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 text-sm lg:text-base">{{ __('messages.nav.technical_service') }}</a>
                <a href="{{ route('nosotros') }}" data-loading-message="Cargando nosotros..."
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 text-sm lg:text-base">{{ __('messages.nav.about') }}</a>
                <a href="{{ route('contactanos') }}" data-loading-message="Cargando contacto..."
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 text-sm lg:text-base">{{ __('messages.nav.contact') }}</a>
            </div>

            <!-- Desktop Actions (desde md - 768px) -->
            <div class="hidden md:flex items-center space-x-2 lg:space-x-4">
                <!-- Botón de cambio de tema -->
                <button @click="darkMode = !darkMode"
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 p-2 rounded-lg transition-colors duration-300"
                    title="Cambiar tema">
                    <!-- Ícono sol para modo claro -->
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Ícono luna para modo oscuro -->
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Selector de idioma/país/moneda -->
                @include('components.language-selector-modal')

                <x-search-autocomplete context="header" :placeholder="'Buscar...'" />
                @auth
                    <div class="relative group" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button @click="open = !open"
                            class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 flex items-center transition-colors duration-300">
                            <i class="fas fa-user mr-2"></i>
                            <span class="text-sm">Mi Perfil</span>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-50 transition-colors duration-300">
                            <a href="{{ route('perfil') }}" data-loading-message="Cargando perfil..."
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
                                <i class="fas fa-user-circle mr-2"></i> Ver Perfil
                            </a>
                            <a href="{{ route('pedidos.historial') }}" data-loading-message="Cargando pedidos..."
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
                                <i class="fas fa-shopping-bag mr-2"></i> Ver Pedidos
                            </a>
                            <form id="logout-form" 
                                  action="{{ route('logout') }}" 
                                  method="POST" 
                                  class="block confirm-action"
                                  data-title="¿Cerrar sesión?"
                                  data-message="¿Estás seguro de que deseas cerrar tu sesión?"
                                  data-confirm-text="Sí, cerrar sesión"
                                  data-cancel-text="Cancelar"
                                  data-confirm-color="blue"
                                  data-show-warning="false"
                                  data-loading-message="Cerrando sesión...">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" data-loading-message="Cargando login..."
                        class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300">
                        <i class="fas fa-user"></i>
                    </a>
                @endauth
                <a href="#" id="cart-btn" data-no-loading
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 relative transition-colors duration-300">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count"
                        class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>
            </div>

            <!-- Mobile Menu Button (solo < md - < 768px) -->
            <div class="md:hidden flex items-center space-x-3">
                <!-- Botón de cambio de tema móvil -->
                <button @click="darkMode = !darkMode"
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 p-2 rounded-lg transition-colors duration-300"
                    title="Cambiar tema">
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Carrito móvil -->
                <a href="#" id="cart-btn-mobile" data-no-loading
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 relative transition-colors duration-300">
                    <i class="fas fa-shopping-cart"></i>
                    <span id="cart-count-mobile"
                        class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                </a>

                <!-- Botón hamburguesa -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 p-2 rounded-lg transition-colors duration-300"
                    aria-label="Toggle mobile menu">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu (< 768px) -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform -translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-4"
             class="md:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 py-6 space-y-4">
                <!-- Enlaces principales -->
                <div class="space-y-3">
                    <a href="{{ route('landing') }}" data-loading-message="Cargando inicio..."
                        class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 py-2">
                        <i class="fas fa-home mr-3"></i>Inicio
                    </a>

                    <a href="{{ route('productos.lista') }}" data-loading-message="Cargando productos..."
                    class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 py-2">
                    <i class="fas fa-mobile-alt mr-3"></i>Productos
                </a>

                    <a href="{{ route('servicios') }}" data-loading-message="Cargando servicio tecnico..."
                        class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 py-2">
                        <i class="fas fa-cogs mr-3"></i>Servicio Tecnico
                    </a>
                    <a href="{{ route('nosotros') }}" data-loading-message="Cargando nosotros..."
                        class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 py-2">
                        <i class="fas fa-users mr-3"></i>Nosotros
                    </a>
                <a href="{{ route('contactanos') }}" data-loading-message="Cargando contacto..."
                        class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-300 py-2">
                        <i class="fas fa-envelope mr-3"></i>Contacto
                    </a>
                </div>

                <!-- Separador -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4"></div>

                <!-- Acciones móviles -->
                <div class="space-y-3">
                    @include('components.language-selector-modal')
                    <a href="#" data-no-loading
                        class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 py-2">
                        <i class="fas fa-search mr-3"></i>Buscar
                    </a>
                    @auth
                            <a href="{{ route('perfil') }}" data-loading-message="Cargando perfil..."
                            class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 py-2">
                            <i class="fas fa-user mr-3"></i>Mi Perfil
                        </a>
                        <form id="logout-form-mobile" 
                              action="{{ route('logout') }}" 
                              method="POST" 
                              class="block confirm-action"
                              data-title="¿Cerrar sesión?"
                              data-message="¿Estás seguro de que deseas cerrar tu sesión?"
                              data-confirm-text="Sí, cerrar sesión"
                              data-cancel-text="Cancelar"
                              data-confirm-color="blue"
                              data-show-warning="false"
                              data-loading-message="Cerrando sesión...">
                            @csrf
                            <button type="submit"
                                class="w-full text-left text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 py-2">
                                <i class="fas fa-sign-out-alt mr-3"></i>Cerrar Sesión
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="block text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-300 py-2">
                            <i class="fas fa-sign-in-alt mr-3"></i>Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')

        <footer class="py-16 bg-white dark:bg-gradient-to-br dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 text-gray-900 dark:text-white transition-colors duration-300 relative overflow-hidden">
            <!-- Elementos decorativos de fondo -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-azul1 via-azul2 to-azul3 "></div>
                <div class="absolute top-20 right-10 w-32 h-32 bg-blue-500/5 dark:bg-blue-400/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 left-10 w-40 h-40 bg-purple-500/5 dark:bg-purple-400/5 rounded-full blur-3xl"></div>
            </div>

            <div class="container mx-auto px-4 relative z-10">
                <!-- Sección principal del footer -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    <!-- Column 1: Sobre nosotros -->
                    <div class="lg:col-span-1">
                        <div class="flex items-center mb-6">
                            <img src="{{ asset('img/Logo_2.png') }}" alt="4GMovil" class="h-10 mr-3">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">4GMovil</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                            {{ __('messages.footer.about_us') }}
                        </p>
                        <div class="flex space-x-3">
                            <a href="https://www.facebook.com/cuatro.g.movil.2025/"
                                class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 shadow-lg"
                                target="_blank" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.instagram.com/tumovil4g/?fbclid=IwY2xjawJClENleHRuA2FlbQIxMAABHfgoRN_xkx-QlgRi5XJX_YY8IVnmcJTaee4R2UWXoOMJTTip9ml-DYoVXw_aem_-9MriOuo88DFcLIGBeizRw"
                                class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 shadow-lg"
                                target="_blank" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.tiktok.com/@tumovil4g"
                                class="bg-black hover:bg-gray-800 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 shadow-lg"
                                target="_blank" title="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Column 2: Enlaces rápidos -->
                    <div>
                        <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-link mr-3 text-blue-600 dark:text-blue-400"></i>
                            {{ __('messages.footer.quick_links') }}
                        </h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('landing') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-300 flex items-center group">
                                    <i class="fas fa-chevron-right mr-2 text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    {{ __('messages.footer.home') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('productos.lista') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-300 flex items-center group">
                                    <i class="fas fa-chevron-right mr-2 text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    {{ __('messages.footer.products') }}
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-300 flex items-center group">
                                    <i class="fas fa-chevron-right mr-2 text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    {{ __('messages.footer.services') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('nosotros') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-300 flex items-center group">
                                    <i class="fas fa-chevron-right mr-2 text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    {{ __('messages.footer.about') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contactanos') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors duration-300 flex items-center group">
                                    <i class="fas fa-chevron-right mr-2 text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform duration-300"></i>
                                    {{ __('messages.footer.contact') }}
                                </a>
                            </li>
                        </ul>
                    </div>


                    <!-- Column 3: Contacto -->
                    <div>
                        <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-address-card mr-3 text-blue-600 dark:text-blue-400"></i>
                            {{ __('messages.footer.contact') }}
                        </h3>
                        <ul class="space-y-4 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start group">
                                <div class="bg-blue-500/20 p-2 rounded-lg mr-3 group-hover:bg-blue-500/30 transition-colors duration-300">
                                    <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ __('messages.footer.address') }}</span>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{!! __('messages.footer.address_value') !!}</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="bg-green-500/20 p-2 rounded-lg mr-3 group-hover:bg-green-500/30 transition-colors duration-300">
                                    <i class="fas fa-phone-alt text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ __('messages.footer.phone') }}</span>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('messages.footer.phone_value') }}</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="bg-purple-500/20 p-2 rounded-lg mr-3 group-hover:bg-purple-500/30 transition-colors duration-300">
                                    <i class="fas fa-envelope text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ __('messages.footer.email') }}</span>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('messages.footer.email_value') }}</p>
                                </div>
                            </li>
                            <li class="flex items-start group">
                                <div class="bg-orange-500/20 p-2 rounded-lg mr-3 group-hover:bg-orange-500/30 transition-colors duration-300">
                                    <i class="fas fa-clock text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ __('messages.footer.hours') }}</span>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{!! __('messages.footer.hours_value') !!}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Sección de características destacadas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10 hover:border-white/20 transition-all duration-300 hover:bg-white/10">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-500/20 p-3 rounded-lg mr-4">
                                <i class="fas fa-shield-alt text-blue-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.footer.official_warranty') }}</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">{{ __('messages.footer.official_warranty_desc') }}</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10 hover:border-white/20 transition-all duration-300 hover:bg-white/10">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-500/20 p-3 rounded-lg mr-4">
                                <i class="fas fa-truck text-green-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.footer.free_shipping') }}</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">{{ __('messages.footer.free_shipping_desc') }}</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10 hover:border-white/20 transition-all duration-300 hover:bg-white/10">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-500/20 p-3 rounded-lg mr-4">
                                <i class="fas fa-headset text-purple-400 text-xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('messages.footer.support_24_7') }}</h4>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">{{ __('messages.footer.support_24_7_desc') }}</p>
                    </div>
                </div>

                <!-- Línea divisoria -->
                <div class="border-t border-gray-700/50"></div>

                <!-- Sección inferior -->
                <div class="pt-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-4 text-center md:text-left">
                        <!-- Marca -->
                        <div class="flex items-center justify-center md:justify-start gap-3">
                            <img src="{{ asset('img/Logo_2.png') }}" alt="Logo 4GMovil" class="h-16">
                           
                        </div>

                        <!-- Derechos reservados (centro) -->
                        <div class="order-3 md:order-none flex flex-col items-center justify-center">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('messages.footer.all_rights_reserved') }}</p>
                            <p class="mt-1 text-gray-900 dark:text-white font-bold">© 2025 4GMovil</p>
                        </div>

                        <!-- Enlaces legales (derecha, en columna) -->
                        <div class="flex flex-col items-center md:items-end gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <a href="#" class="hover:text-gray-900 dark:hover:text-white transition-colors duration-300">{{ __('messages.footer.privacy_policy') }}</a>
                            <a href="#" class="hover:text-gray-900 dark:hover:text-white transition-colors duration-300">{{ __('messages.footer.terms_of_service') }}</a>
                            <a href="#" class="hover:text-gray-900 dark:hover:text-white transition-colors duration-300">{{ __('messages.footer.cookie_policy') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Cart Modal Responsive -->
        <div id="cart-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 flex items-center justify-center p-2 sm:p-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl sm:rounded-3xl shadow-2xl w-full max-w-4xl max-h-[98vh] sm:max-h-[95vh] overflow-hidden transition-all duration-500 transform scale-95 opacity-0"
                    id="cart-container">
                    <!-- Header del carrito -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 sm:p-6 text-white relative">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="bg-white/20 p-2 sm:p-3 rounded-full">
                                    <i class="fas fa-shopping-cart text-lg sm:text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg sm:text-2xl font-bold">Tu Carrito</h2>
                                    <p class="text-blue-100 text-xs sm:text-sm">Revisa tus productos seleccionados</p>
                                </div>
                            </div>
                            <button
                                class="text-white/80 hover:text-white p-2 sm:p-3 hover:bg-white/20 rounded-full transition-all duration-300 transform hover:scale-110"
                                aria-label="Cerrar carrito" id="close-cart">
                                <i class="fas fa-times text-lg sm:text-xl"></i>
                            </button>
                        </div>
                        <!-- Indicador de productos -->
                        <div class="mt-3 sm:mt-4 flex items-center text-blue-100">
                            <i class="fas fa-box mr-2 text-sm sm:text-base"></i>
                            <span id="cart-items-count" class="text-sm sm:text-base">0 productos</span>
                        </div>
                    </div>

                    <!-- Contenido del carrito -->
                    <div class="flex flex-col lg:flex-row h-[calc(98vh-120px)] sm:h-[calc(95vh-140px)]">
                        <!-- Lista de productos -->
                        <div class="flex-1 p-3 sm:p-6 overflow-y-auto">
                            <div id="cart-items" class="space-y-3 sm:space-y-4">
                                <!-- Estado vacío -->
                                <div class="text-center py-8 sm:py-12" id="empty-cart">
                                    <div
                                        class="bg-gray-100 dark:bg-gray-800 w-16 h-16 sm:w-24 sm:h-24 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                        <i class="fas fa-shopping-bag text-2xl sm:text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Tu carrito está vacío</h3>
                                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 mb-4 sm:mb-6">
                                        Agrega algunos productos para comenzar</p>
                                    <button
                                        class="bg-blue-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-medium hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 text-sm sm:text-base"
                                        onclick="closeCart()">
                                        <i class="fas fa-shopping-bag mr-2"></i>Ver Productos
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen y checkout -->
                        <div
                            class="lg:w-96 bg-gray-50 dark:bg-gray-800 p-3 sm:p-6 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Resumen
                                de Compra</h3>

                            <!-- Detalles del resumen -->
                            <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                                <div
                                    class="flex justify-between items-center text-gray-600 dark:text-gray-300 text-sm sm:text-base">
                                    <span>Subtotal:</span>
                                    <span id="cart-subtotal" class="font-medium">$0</span>
                                </div>
                                <div
                                    class="flex justify-between items-center text-gray-600 dark:text-gray-300 text-sm sm:text-base">
                                    <span>Envío:</span>
                                    <span class="text-green-600 font-medium">Gratis</span>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-3 sm:pt-4">
                                    <div class="flex justify-between items-center">
                                        <span
                                            class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">Total:</span>
                                        <span id="cart-total"
                                            class="text-xl sm:text-2xl font-bold text-blue-600">$0</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Botón de checkout -->
                            <form id="checkout-form" action="{{ route('checkout.index') }}" method="POST">
                                @csrf
                                <input type="hidden" name="cart" id="cart-data">
                                <button type="submit" id="checkout-btn"
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 sm:py-4 rounded-xl font-bold text-base sm:text-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg"
                                    role="button" aria-disabled="true">
                                    <i class="fas fa-lock mr-2"></i>Finalizar Compra Segura
                                </button>
                            </form>

                            <!-- Información adicional -->
                            <div class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                <div
                                    class="flex flex-col sm:flex-row items-center justify-center space-y-1 sm:space-y-0 sm:space-x-4 mb-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                                        <span>{{ __('messages.product_show.secure_payment') }}</span>
                                    </div>
                                </div>
                                <p class="text-xs sm:text-sm">Protegido por encriptación SSL de 256 bits</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- WhatsApp flotante -->
        <div class="fixed bottom-6 left-6 z-50 group">
            <a id="btnWhatsApp" href="https://wa.me/573025970220" target="_blank" rel="noopener noreferrer"
                aria-label="Chatear por WhatsApp"
                class="bg-green-500 hover:bg-green-600 text-white w-14 h-14 flex items-center justify-center rounded-full shadow-lg transition-opacity duration-300 opacity-100 visible">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                    <path
                        d="M12.04 2.004a10 10 0 0 0-8.723 14.942l-1.34 4.888 5.014-1.313A10 10 0 1 0 12.04 2.004Zm.007 18.005a8.006 8.006 0 0 1-4.116-1.152l-.293-.174-2.974.78.792-2.905-.19-.3a7.992 7.992 0 1 1 6.781 3.75Zm4.294-5.893c-.234-.117-1.383-.683-1.596-.76-.213-.078-.368-.117-.523.117-.155.233-.6.76-.736.915-.135.156-.271.175-.506.058-.234-.117-.99-.364-1.885-1.161-.696-.618-1.165-1.38-1.3-1.615-.136-.234-.015-.36.102-.476.105-.104.234-.271.351-.406.118-.135.156-.233.234-.389.078-.156.039-.292-.02-.409-.058-.117-.523-1.262-.716-1.73-.188-.453-.38-.39-.523-.397l-.445-.007c-.155 0-.408.058-.622.292s-.816.797-.816 1.945.836 2.256.952 2.413c.117.155 1.643 2.507 3.982 3.516.557.24.991.383 1.33.489.559.178 1.067.152 1.47.093.448-.067 1.383-.565 1.577-1.112.194-.546.194-1.014.135-1.112-.058-.098-.213-.155-.448-.272Z" />
                </svg>
            </a>
            <!-- Tooltip -->
            <div
                class="absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 rounded bg-gray-800 text-white text-sm opacity-0 group-hover:opacity-100 transition duration-300 whitespace-nowrap">
                Chatea con nosotros
            </div>
        </div>

        <!-- Botón Volver Arriba -->
        <button id="btnUp" title="Volver arriba" aria-label="Volver al inicio"
            class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white text-xl p-3 rounded-full shadow-lg transition-all duration-500 ease-in-out opacity-0 invisible z-50 transform translate-y-2 hover:scale-110">
            ↑
        </button>
    </main>

    <!-- Script del carrito -->
    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            // DOM Content Loaded

            // Variables globales - inicializar desde localStorage primero
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Verificar si el usuario está autenticado
            const isAuthenticated = document.querySelector('meta[name="auth-check"]')?.getAttribute('content') === 'true' || 
                                   document.body.classList.contains('authenticated') ||
                                   (typeof window.isAuthenticated !== 'undefined' && window.isAuthenticated);
            
            // Si el usuario está autenticado, sincronizar con el servidor al cargar
            // Esto asegura que el carrito esté actualizado y sin duplicados
            if (isAuthenticated) {
                try {
                    const cartResponse = await fetch('/carrito/obtener', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (cartResponse.ok) {
                        const cartData = await cartResponse.json();
                        if (cartData.success && cartData.data && cartData.data.items && cartData.data.items.length > 0) {
                            // Reconstruir carrito desde el servidor para evitar duplicados
                            cart = [];
                            cartData.data.items.forEach(item => {
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
                                    
                                    cart.push(cartItem);
                                }
                            });
                            
                            // Actualizar localStorage con datos del servidor
                            localStorage.setItem('cart', JSON.stringify(cart));
                            console.log('Carrito sincronizado desde servidor al cargar:', cart);
                        } else if (cartData.success && cartData.data && (!cartData.data.items || cartData.data.items.length === 0)) {
                            // Si el carrito del servidor está vacío, limpiar localStorage también
                            cart = [];
                            localStorage.setItem('cart', JSON.stringify(cart));
                            console.log('Carrito del servidor vacío, localStorage limpiado');
                        }
                    }
                } catch (error) {
                    console.error('Error al sincronizar carrito al cargar:', error);
                    // Continuar con localStorage si hay error
                }
            } else {
                // Si no está autenticado, sincronizar localStorage con la sesión de Laravel
                // Esto asegura que cuando el usuario inicie sesión, los items estén en la sesión
                if (cart.length > 0) {
                    try {
                        console.log('Sincronizando localStorage con sesión de Laravel...');
                        
                        // Consolidar items del mismo producto+variante antes de sincronizar
                        const consolidatedCart = {};
                        cart.forEach(item => {
                            const key = item.variante_id ? `${item.id}-${item.variante_id}` : item.id.toString();
                            if (consolidatedCart[key]) {
                                consolidatedCart[key].quantity += (item.quantity || 1);
                            } else {
                                consolidatedCart[key] = {
                                    id: item.id,
                                    variante_id: item.variante_id || null,
                                    quantity: item.quantity || 1
                                };
                            }
                        });
                        
                        console.log('Items consolidados:', Object.values(consolidatedCart));
                        
                        // Enviar cada item consolidado al servidor para guardarlo en la sesión
                        for (const item of Object.values(consolidatedCart)) {
                            const formData = new FormData();
                            formData.append('producto_id', item.id);
                            formData.append('cantidad', item.quantity);
                            if (item.variante_id) {
                                formData.append('variante_id', item.variante_id);
                            }
                            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

                            const response = await fetch('/carrito/agregar', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: formData
                            });
                            
                            if (response.ok) {
                                const data = await response.json();
                                console.log('Item sincronizado:', item, 'Respuesta:', data);
                            }
                        }
                        console.log('localStorage sincronizado con sesión de Laravel');
                    } catch (error) {
                        console.warn('Error al sincronizar localStorage con sesión:', error);
                    }
                }
            }
            
            // Sincronizar window.cart con la variable local
            window.cart = cart;

            // Funciones del carrito
            function updateCartCount() {
                // Actualizando contador del carrito
                // Sincronizar con window.cart si existe
                const currentCart = window.cart || cart;
                
                const cartCount = document.getElementById('cart-count');
                const cartCountMobile = document.getElementById('cart-count-mobile');
                
                if (!cartCount && !cartCountMobile) {
                    console.error('Elementos cart-count no encontrados');
                    return;
                }
                
                const totalItems = currentCart.reduce((total, item) => total + (parseInt(item.quantity) || 0), 0);
                
                // Asegurar que sea un número entero sin formateo adicional
                const count = parseInt(totalItems) || 0;
                
                if (cartCount) {
                    cartCount.textContent = count.toString();
                }
                
                if (cartCountMobile) {
                    cartCountMobile.textContent = count.toString();
                }
                
                // Total items calculado
            }

            function numberFormat(number) {
                // Redondear al entero más cercano y formatear sin decimales
                return new Intl.NumberFormat('es-CO', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(Math.round(number));
            }

            function updateCartDisplay() {
                // Actualizando display del carrito
                // Sincronizar con window.cart si existe
                const currentCart = window.cart || cart;
                
                const cartItems = document.getElementById('cart-items');
                const cartSubtotal = document.getElementById('cart-subtotal');
                const cartTotal = document.getElementById('cart-total');
                const checkoutBtn = document.getElementById('checkout-btn');
                const cartData = document.getElementById('cart-data');
                const cartItemsCount = document.getElementById('cart-items-count');
                const emptyCart = document.getElementById('empty-cart');

                if (!cartItems || !cartSubtotal || !cartTotal || !checkoutBtn || !cartData) {
                    console.error('Elementos del carrito no encontrados');
                    return;
                }

                const totalItems = currentCart.reduce((total, item) => total + item.quantity, 0);
                if (cartItemsCount) {
                    cartItemsCount.textContent = `${totalItems} producto${totalItems !== 1 ? 's' : ''}`;
                }

                if (currentCart.length === 0) {
                    cartItems.innerHTML = '';
                    if (emptyCart) {
                        emptyCart.style.display = 'block';
                    }
                    cartSubtotal.textContent = '$0';
                    cartTotal.textContent = '$0';
                    checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    checkoutBtn.setAttribute('aria-disabled', 'true');
                    cartData.value = '';
                    return;
                }

                if (emptyCart) {
                    emptyCart.style.display = 'none';
                }
                // Actualizar el campo oculto con los datos del carrito
                cartData.value = JSON.stringify(currentCart);

                let html = '';
                let subtotal = 0;

                currentCart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;

                    // Mostrar información de variante si existe
                    const varianteInfo = item.variante_nombre ? `<p class="text-xs text-blue-600 dark:text-blue-400">Variante: ${item.variante_nombre}</p>` : '';
                    
                    html += `
                        <div class="bg-white dark:bg-gray-800 rounded-xl sm:rounded-2xl p-3 sm:p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                                <div class="flex items-center space-x-3 sm:flex-shrink-0">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg sm:rounded-xl flex items-center justify-center">
                                        <i class="fas fa-mobile-alt text-lg sm:text-2xl text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div class="flex-1 sm:hidden">
                                        <h4 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white truncate">${item.name}</h4>
                                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Precio unitario: $${numberFormat(item.price)}</p>
                                        ${varianteInfo}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0 hidden sm:block">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white truncate">${item.name}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Precio unitario: $${numberFormat(item.price)}</p>
                                    ${varianteInfo}
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                    <div class="flex items-center justify-between sm:justify-start">
                                        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg">
                                            <button onclick="updateQuantity(${item.id}, ${item.variante_id || 'null'}, -1)" class="p-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-l-lg transition-colors">
                                                <i class="fas fa-minus text-sm"></i>
                                            </button>
                                            <span class="px-3 sm:px-4 py-2 text-gray-900 dark:text-white font-medium text-sm sm:text-base">${item.quantity}</span>
                                            <button onclick="updateQuantity(${item.id}, ${item.variante_id || 'null'}, 1)" class="p-2 text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-r-lg transition-colors">
                                                <i class="fas fa-plus text-sm"></i>
                                            </button>
                                        </div>
                                        <button onclick="removeFromCart(${item.id}, ${item.variante_id || 'null'})" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-300 ml-2">
                                            <i class="fas fa-trash text-sm sm:text-base"></i>
                                        </button>
                                    </div>
                                    <div class="text-right sm:text-left">
                                        <div class="text-base sm:text-lg font-bold text-blue-600 dark:text-blue-400">$${numberFormat(itemTotal)}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                cartItems.innerHTML = html;

                // El total es igual al subtotal (sin impuestos)
                const total = subtotal;

                cartSubtotal.textContent = `$${numberFormat(subtotal)}`;
                cartTotal.textContent = `$${numberFormat(total)}`;
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                checkoutBtn.setAttribute('aria-disabled', 'false');
            }

            async function addToCart(product) {
                // Agregando al carrito

                // Asegurarse de que el ID sea un número
                const productId = parseInt(product.id);
                if (isNaN(productId)) {
                    console.error('ID de producto inválido:', product.id);
                    return;
                }

                // Verificar si el producto tiene variante seleccionada
                const varianteId = product.variante_id ? parseInt(product.variante_id) : null;
                const varianteNombre = product.variante_nombre || null;
                const precioAdicional = product.precio_adicional ? parseFloat(product.precio_adicional) : 0;

                // Si el usuario está autenticado, agregar al servidor primero
                const isAuthenticated = document.querySelector('meta[name="auth-check"]')?.getAttribute('content') === 'true' || 
                                       document.body.classList.contains('authenticated') ||
                                       (typeof window.isAuthenticated !== 'undefined' && window.isAuthenticated);

                if (isAuthenticated) {
                    try {
                        // El servidor maneja automáticamente el incremento si el producto ya existe
                        const formData = new FormData();
                        formData.append('producto_id', productId);
                        formData.append('cantidad', 1);
                        if (varianteId) {
                            formData.append('variante_id', varianteId);
                        }
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                        console.log('Agregando al carrito en servidor:', {
                            producto_id: productId,
                            variante_id: varianteId,
                            cantidad: 1
                        });

                        const response = await fetch('/carrito/agregar', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (response.ok) {
                            const data = await response.json();
                            console.log('Respuesta del servidor:', data);
                            
                            if (data.success) {
                                // Recargar el carrito desde el servidor
                                await reloadCartFromServer();
                                
                                // Mostrar notificación de éxito
                                if (typeof window.showNotification === 'function') {
                                    window.showNotification('¡Producto agregado! El producto se agregó al carrito exitosamente', 'success', 3);
                                }
                                return;
                            } else {
                                console.error('Error del servidor:', data.message);
                                // Continuar con método local si falla
                            }
                        } else {
                            const errorText = await response.text();
                            console.error('Error HTTP:', response.status, errorText);
                            // Continuar con método local si falla
                        }
                    } catch (error) {
                        console.error('Error al agregar al carrito en servidor:', error);
                        // Continuar con el método local si falla
                    }
                }

                // Si no está autenticado o falló la llamada al servidor, usar método local
                // Crear clave única para el producto (producto + variante)
                const itemKey = varianteId ? `${productId}-${varianteId}` : productId.toString();

                const existingProduct = cart.find(item => {
                    const itemKey2 = item.variante_id ? `${item.id}-${item.variante_id}` : item.id.toString();
                    return itemKey2 === itemKey;
                });

                if (existingProduct) {
                    existingProduct.quantity++;
                    console.log('Producto existente, cantidad aumentada a:', existingProduct.quantity);
                } else {
                    const newItem = {
                        id: productId,
                        name: product.name,
                        price: parseFloat(product.price),
                        quantity: 1
                    };

                    // Agregar información de variante si existe
                    if (varianteId) {
                        newItem.variante_id = varianteId;
                        newItem.variante_nombre = varianteNombre;
                        newItem.precio_adicional = precioAdicional;
                        newItem.name = `${product.name} (${varianteNombre})`;
                        newItem.price = parseFloat(product.price) + precioAdicional;
                    }

                    cart.push(newItem);
                    console.log('Nuevo producto agregado al carrito');
                }

                // Sincronizar con window.cart
                window.cart = cart;
                localStorage.setItem('cart', JSON.stringify(cart));
                console.log('Cart actualizado:', cart); // Debug
                updateCartCount();
                updateCartDisplay();

                // Guardar en la sesión de Laravel (aunque no esté autenticado)
                // IMPORTANTE: Siempre enviar cantidad 1, el servidor se encarga de sumar
                try {
                    // SIEMPRE enviar cantidad 1, sin importar la cantidad en localStorage
                    const cantidadAEnviar = 1;
                    
                    console.log('Enviando a sesión de Laravel:', {
                        producto_id: productId,
                        variante_id: varianteId,
                        cantidad: cantidadAEnviar, // SIEMPRE 1
                        cantidad_en_localStorage: existingProduct ? existingProduct.quantity : 1
                    });
                    
                    const formData = new FormData();
                    formData.append('producto_id', productId);
                    formData.append('cantidad', cantidadAEnviar); // SIEMPRE 1
                    if (varianteId) {
                        formData.append('variante_id', varianteId);
                    }
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    // Llamar al servidor para guardar en sesión (aunque no esté autenticado)
                    const sessionResponse = await fetch('/carrito/agregar', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (sessionResponse.ok) {
                        const sessionData = await sessionResponse.json();
                        console.log('Producto guardado en sesión de Laravel (cantidad 1 agregada):', sessionData);
                    } else {
                        const errorText = await sessionResponse.text();
                        console.error('Error al guardar en sesión:', errorText);
                    }
                } catch (error) {
                    console.warn('No se pudo guardar en sesión de Laravel (continuando con localStorage):', error);
                }

                // Mostrar notificación de éxito
                if (typeof window.showNotification === 'function') {
                    window.showNotification('¡Producto agregado! El producto se agregó al carrito exitosamente', 'success', 3);
                }
            }

            // Función para recargar el carrito desde el servidor
            async function reloadCartFromServer() {
                try {
                    const response = await fetch('/carrito/obtener', {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const cartData = await response.json();
                        if (cartData.success && cartData.data && cartData.data.items) {
                            // Reconstruir carrito desde el servidor
                            cart = [];
                            cartData.data.items.forEach(item => {
                                if (item.producto) {
                                    const cartItem = {
                                        id: item.producto.producto_id,
                                        name: item.producto.nombre_producto,
                                        price: parseFloat(item.producto.precio),
                                        quantity: item.cantidad,
                                        itemId: item.id
                                    };

                                    if (item.variante) {
                                        cartItem.variante_id = item.variante.variante_id;
                                        cartItem.variante_nombre = item.variante.nombre;
                                        cartItem.precio_adicional = parseFloat(item.variante.precio_adicional || 0);
                                        cartItem.name = `${item.producto.nombre_producto} (${item.variante.nombre})`;
                                        cartItem.price = parseFloat(item.producto.precio) + parseFloat(item.variante.precio_adicional || 0);
                                    }

                                    cart.push(cartItem);
                                }
                            });

                            // Actualizar localStorage
                            localStorage.setItem('cart', JSON.stringify(cart));
                            window.cart = cart;

                            // Actualizar UI
                            updateCartCount();
                            updateCartDisplay();
                        }
                    }
                } catch (error) {
                    console.error('Error al recargar carrito desde servidor:', error);
                }
            }

            async function updateQuantity(productId, varianteId, change) {
                console.log('Actualizando cantidad:', {
                    productId,
                    varianteId,
                    change
                }); // Debug
                
                const product = cart.find(item => {
                    if (varianteId && varianteId !== 'null') {
                        return item.id === productId && item.variante_id === parseInt(varianteId);
                    }
                    return item.id === productId && !item.variante_id;
                });
                
                if (product) {
                    const cantidadAnterior = product.quantity;
                    product.quantity += change;
                    
                    if (product.quantity <= 0) {
                        removeFromCart(productId, varianteId);
                    } else {
                        // Sincronizar con window.cart
                        window.cart = cart;
                        localStorage.setItem('cart', JSON.stringify(cart));
                        updateCartCount();
                        updateCartDisplay();
                        
                        // Si el usuario está autenticado, actualizar en el servidor
                        const isAuthenticated = document.querySelector('meta[name="auth-check"]')?.getAttribute('content') === 'true' || 
                                               document.body.classList.contains('authenticated') ||
                                               (typeof window.isAuthenticated !== 'undefined' && window.isAuthenticated);
                        
                        if (isAuthenticated && product.itemId) {
                            // Usuario autenticado: actualizar cantidad en el servidor
                            try {
                                const response = await fetch(`/carrito/actualizar/${product.itemId}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        cantidad: product.quantity
                                    })
                                });
                                
                                if (response.ok) {
                                    const data = await response.json();
                                    console.log('Cantidad actualizada en servidor:', data);
                                }
                            } catch (error) {
                                console.error('Error al actualizar cantidad en servidor:', error);
                            }
                        } else {
                            // Usuario no autenticado: sincronizar todo el carrito con la sesión de Laravel
                            // Esto asegura que la cantidad actualizada se guarde correctamente
                            try {
                                console.log('Sincronizando carrito completo con sesión después de actualizar cantidad...');
                                
                                // Consolidar items del mismo producto+variante antes de sincronizar
                                const consolidatedCart = {};
                                cart.forEach(item => {
                                    const key = item.variante_id ? `${item.id}-${item.variante_id}` : item.id.toString();
                                    if (consolidatedCart[key]) {
                                        consolidatedCart[key].quantity += (item.quantity || 1);
                                    } else {
                                        consolidatedCart[key] = {
                                            id: item.id,
                                            variante_id: item.variante_id || null,
                                            quantity: item.quantity || 1
                                        };
                                    }
                                });
                                
                                // Limpiar la sesión primero y luego agregar todos los items consolidados
                                // Para esto, necesitamos eliminar todos los items y luego agregarlos de nuevo
                                // O mejor: sincronizar item por item con la cantidad correcta
                                
                                // Sincronizar cada item consolidado
                                for (const item of Object.values(consolidatedCart)) {
                                    const formData = new FormData();
                                    formData.append('producto_id', item.id);
                                    formData.append('cantidad', item.quantity);
                                    if (item.variante_id) {
                                        formData.append('variante_id', item.variante_id);
                                    }
                                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

                                    await fetch('/carrito/agregar', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    });
                                }
                                
                                console.log('Carrito sincronizado con sesión después de actualizar cantidad');
                            } catch (error) {
                                console.warn('Error al sincronizar carrito con sesión:', error);
                            }
                        }
                    }
                }
            }

            async function removeFromCart(productId, varianteId) {
                console.log('Eliminando del carrito:', { productId, varianteId }); // Debug
                
                // Buscar el item en el carrito para obtener su itemId del servidor
                const itemToRemove = cart.find(item => {
                    if (varianteId) {
                        return item.id === productId && item.variante_id === varianteId;
                    }
                    return item.id === productId && !item.variante_id;
                });
                
                // Si el item tiene itemId del servidor, eliminarlo del servidor también
                if (itemToRemove && itemToRemove.itemId) {
                    try {
                        // Usar la ruta correcta según si el usuario está autenticado o no
                        // Ambas rutas funcionan, pero la ruta /carrito/eliminar/{itemId} funciona para ambos casos
                        const route = `/carrito/eliminar/${itemToRemove.itemId}`;
                        
                        const response = await fetch(route, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            console.log('Item eliminado del servidor:', data);
                        } else {
                            const errorData = await response.json().catch(() => ({ message: 'Error desconocido' }));
                            console.error('Error al eliminar del servidor:', response.status, errorData);
                        }
                    } catch (error) {
                        console.error('Error al eliminar del servidor:', error);
                    }
                }
                
                // Eliminar del carrito local
                cart = cart.filter(item => {
                    if (varianteId) {
                        return !(item.id === productId && item.variante_id === varianteId);
                    }
                    return !(item.id === productId && !item.variante_id);
                });
                
                // Sincronizar con window.cart
                window.cart = cart;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCartCount();
                updateCartDisplay();
            }

            // Configurar el modal del carrito
            const cartModal = document.getElementById('cart-modal');
            const cartBtn = document.getElementById('cart-btn');

            if (!cartModal || !cartBtn) {
                console.error('Elementos del modal no encontrados');
                return;
            }

            console.log('Elementos del modal:', {
                cartModal,
                cartBtn
            }); // Debug

            // Función para mostrar el modal
            function showModal() {
                cartModal.style.display = 'block';
                const cartContainer = document.getElementById('cart-container');
                setTimeout(() => {
                    cartContainer.classList.remove('scale-95', 'opacity-0');
                    cartContainer.classList.add('scale-100', 'opacity-100');
                }, 10);
                document.body.style.overflow = 'hidden';
                updateCartDisplay(); // Actualizar contenido al abrir
            }

            // Función para ocultar el modal
            function hideModal() {
                const cartContainer = document.getElementById('cart-container');
                cartContainer.classList.remove('scale-100', 'opacity-100');
                cartContainer.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    cartModal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }

            // Función para cerrar el carrito
            function closeCart() {
                hideModal();
            }

            // Botón para abrir el carrito
            cartBtn.addEventListener('click', function(e) {
                console.log('Click en botón del carrito'); // Debug
                e.preventDefault();
                showModal();
            });

            // Botón para abrir el carrito móvil
            const cartBtnMobile = document.getElementById('cart-btn-mobile');
            if (cartBtnMobile) {
                cartBtnMobile.addEventListener('click', function(e) {
                    console.log('Click en botón del carrito móvil'); // Debug
                    e.preventDefault();
                    showModal();
                });
            }

            // Botón para cerrar el carrito
            const closeCartBtn = document.getElementById('close-cart');
            if (closeCartBtn) {
                closeCartBtn.addEventListener('click', function() {
                    console.log('Click en botón cerrar'); // Debug
                    hideModal();
                });
            }

            // Cerrar al hacer clic fuera del carrito
            cartModal.addEventListener('click', function(e) {
                if (e.target === cartModal) {
                    console.log('Click fuera del modal'); // Debug
                    hideModal();
                }
            });

            // Event listener para los botones de agregar al carrito
            document.addEventListener('click', function(e) {
                const addButton = e.target.closest('.add-to-cart');
                if (addButton) {
                    console.log('Click en botón agregar al carrito:', addButton); // Debug
                    e.preventDefault();
                    e.stopPropagation();

                    // Obtener y validar los datos del producto
                    const id = parseInt(addButton.dataset.id);
                    const name = addButton.dataset.name;
                    const price = parseFloat(addButton.dataset.price);
                    const varianteId = addButton.dataset.varianteId ? parseInt(addButton.dataset.varianteId) : null;
                    const varianteNombre = addButton.dataset.varianteNombre || null;
                    const precioAdicional = addButton.dataset.precioAdicional ? parseFloat(addButton.dataset.precioAdicional) : 0;

                    console.log('Datos del producto:', {
                        id,
                        name,
                        price,
                        varianteId,
                        varianteNombre,
                        precioAdicional
                    }); // Debug

                    if (isNaN(id) || !name || isNaN(price)) {
                        console.error('Datos de producto inválidos:', {
                            id,
                            name,
                            price
                        });
                        return;
                    }

                    addToCart({
                        id: id,
                        name: name,
                        price: price,
                        variante_id: varianteId,
                        variante_nombre: varianteNombre,
                        precio_adicional: precioAdicional
                    });
                }
            });

            // El event listener con delegation ya maneja todos los clicks, no necesitamos duplicar

            // Inicializar carrito
            updateCartCount();
            updateCartDisplay();

            // Función para sincronizar cart desde window.cart
            window.syncCart = function() {
                if (window.cart) {
                    cart = window.cart;
                    // Actualizar localStorage también
                    localStorage.setItem('cart', JSON.stringify(cart));
                    // Actualizar UI después de sincronizar
                    updateCartCount();
                    updateCartDisplay();
                    console.log('Carrito sincronizado y UI actualizada:', cart);
                }
            };
            
            // Hacer las funciones accesibles globalmente
            window.addToCart = addToCart;
            window.updateQuantity = updateQuantity;
            window.removeFromCart = removeFromCart;
            window.updateCartCount = updateCartCount;
            window.updateCartDisplay = updateCartDisplay;
            window.cart = cart; // Exponer cart globalmente

            console.log('Inicialización del carrito completada'); // Debug
        });
    </script>

    <!-- Script para el botón Volver Arriba -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnUp = document.getElementById('btnUp');
            let isScrolling;

            // Mostrar/ocultar el botón según el scroll con debounce
            window.addEventListener('scroll', function() {
                // Limpiar el timeout anterior
                window.clearTimeout(isScrolling);

                // Establecer un timeout para manejar el scroll
                isScrolling = setTimeout(function() {
                    if (window.scrollY > 300) {
                        btnUp.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                        btnUp.classList.add('opacity-100', 'visible', 'translate-y-0');
                    } else {
                        btnUp.classList.remove('opacity-100', 'visible', 'translate-y-0');
                        btnUp.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    }
                }, 100);
            });

            // Funcionalidad del botón con scroll suave personalizado
            btnUp.addEventListener('click', function() {
                const duration = 1000; // Duración de la animación en ms
                const start = window.scrollY;
                const startTime = performance.now();

                function easeInOutCubic(t) {
                    return t < 0.5 ?
                        4 * t * t * t :
                        1 - Math.pow(-2 * t + 2, 3) / 2;
                }

                function scrollAnimation(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);

                    window.scrollTo(0, start * (1 - easeInOutCubic(progress)));

                    if (progress < 1) {
                        requestAnimationFrame(scrollAnimation);
                    }
                }

                requestAnimationFrame(scrollAnimation);
            });
        });
    </script>

    @include('components.google-password-modal', [
        'title' => __('profile.google_set_password'),
        'description' => __('profile.google_set_password_description'),
        'passwordLabel' => __('profile.google_password_label'),
        'confirmPasswordLabel' => __('profile.google_confirm_password_label'),
        'passwordPlaceholder' => __('profile.google_password_placeholder'),
        'confirmPasswordPlaceholder' => __('profile.google_confirm_password_placeholder'),
        'passwordRequirements' => __('profile.google_password_requirements'),
        'cancelButtonText' => __('profile.google_cancel_button'),
        'submitButtonText' => __('profile.google_submit_button'),
        'submitRoute' => route('google.set-password')
    ])

    <!-- Script para mostrar el modal automáticamente -->
    <script>
        // Verificar si debe mostrar el modal al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar si el usuario no tiene contraseña (es usuario de Google) y es cliente
            @if (Auth::check() && !Auth::user()->contrasena && Auth::user()->rol === 'cliente')
                showGooglePasswordModal();
            @endif
        });
    </script>

    <!-- Script para el carrusel de marcas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('brands-carousel');
            const prevBtn = document.getElementById('brands-prev');
            const nextBtn = document.getElementById('brands-next');
            const indicators = document.querySelectorAll('.brands-indicator');
            
            // Solo ejecutar si el carrusel existe (página del index)
            if (!carousel || !prevBtn || !nextBtn) {
                return; // Salir si no están los elementos necesarios
            }
            
            let currentPage = 0;
            const totalPages = 3;
            
            function updateCarousel() {
                const translateX = -currentPage * 100;
                carousel.style.transform = `translateX(${translateX}%)`;
                
                // Actualizar indicadores
                indicators.forEach((indicator, index) => {
                    if (index === currentPage) {
                        indicator.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                        indicator.classList.add('bg-blue-600');
                    } else {
                        indicator.classList.remove('bg-blue-600');
                        indicator.classList.add('bg-gray-300', 'dark:bg-gray-600');
                    }
                });
                
                // Actualizar botones
                prevBtn.disabled = currentPage === 0;
                nextBtn.disabled = currentPage === totalPages - 1;
                
                if (currentPage === 0) {
                    prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
                
                if (currentPage === totalPages - 1) {
                    nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
            
            // Event listeners para botones
            prevBtn.addEventListener('click', () => {
                if (currentPage > 0) {
                    currentPage--;
                    updateCarousel();
                }
            });
            
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages - 1) {
                    currentPage++;
                    updateCarousel();
                }
            });
            
            // Event listeners para indicadores
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => {
                    currentPage = index;
                    updateCarousel();
                });
            });
            
            // Inicializar carrusel
            updateCarousel();
            
            // Auto-play opcional (comentado por defecto)
            /*
            setInterval(() => {
                currentPage = (currentPage + 1) % totalPages;
                updateCarousel();
            }, 5000);
            */
        });
    </script>
    <!-- Script para el indicador de scroll -->
    <script>
        function initializeScrollIndicator() {
            const scrollIndicator = document.getElementById('scrollIndicator');
            
            if (scrollIndicator) {
                // Función para actualizar el indicador
                function updateScrollIndicator() {
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                    const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
                    
                    // Limitar el porcentaje entre 0 y 100
                    const clampedPercent = Math.min(Math.max(scrollPercent, 0), 100);
                    scrollIndicator.style.width = clampedPercent + '%';
                    
                    // Mostrar/ocultar el indicador según el scroll
                    if (scrollTop > 10) {
                        scrollIndicator.style.opacity = '1';
                    } else {
                        scrollIndicator.style.opacity = '0';
                    }
                }
                
                // Event listener para el scroll
                window.addEventListener('scroll', updateScrollIndicator, { passive: true });
                
                // Actualizar al cargar la página
                updateScrollIndicator();
                
                // Debug: verificar que el elemento existe
                console.log('Scroll indicator initialized:', scrollIndicator);
            } else {
                console.error('Scroll indicator element not found');
            }
        }

        // Inicializar el indicador de scroll
        document.addEventListener('DOMContentLoaded', function() {
            initializeScrollIndicator();
        });
    </script>

    <!-- Componente de modal de confirmación personalizado -->
    @include('components.confirm-modal-custom')

    <!-- Sistema de confirmación modal (debe cargarse antes que otros scripts) -->
    <script src="{{ asset('js/confirm-modal.js') }}"></script>
    
    @stack('scripts')
    
    <!-- Modal para selección de variantes -->
    <x-variant-selection-modal />
    
    <!-- JavaScript para manejo de variantes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('variantModal');
            const closeButtons = document.querySelectorAll('.close-modal');
            const selectVariantButtons = document.querySelectorAll('.select-variant');
            
            // Variables globales
            let currentProductId = null;
            let currentProductName = null;
            let currentProductPrice = null;
            
            // Función para formatear precios de manera consistente
            function formatearPrecio(precio) {
                if (typeof precio !== 'number' || isNaN(precio)) {
                    return '$0';
                }
                
                // Redondear y convertir a entero
                const precioEntero = Math.round(precio);
                
                // Formatear con separadores de miles
                return `$${precioEntero.toLocaleString('es-CO')}`;
            }
            
            // Función para abrir el modal
            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
            
            // Función para cerrar el modal
            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
                
                // Restaurar stock original si no se seleccionó variante
                if (window.stockSync) {
                    window.stockSync.resetStock();
                    console.log('Stock restaurado al cerrar modal');
                }
                
                // Limpiar contenido
                document.getElementById('variantsList').innerHTML = '';
                document.getElementById('loadingVariants').classList.add('hidden');
                document.getElementById('noVariantsMessage').classList.add('hidden');
            }
            
            // Event listeners para cerrar modal
            closeButtons.forEach(button => {
                button.addEventListener('click', closeModal);
            });
            
            // Cerrar modal al hacer click fuera
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
            
            // Cerrar modal con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
            
            // Función para cargar variantes
            async function loadVariants(productId) {
                const variantsList = document.getElementById('variantsList');
                const loadingVariants = document.getElementById('loadingVariants');
                const noVariantsMessage = document.getElementById('noVariantsMessage');
                
                // Mostrar loading
                variantsList.innerHTML = '';
                loadingVariants.classList.remove('hidden');
                noVariantsMessage.classList.add('hidden');
                
                try {
                    const response = await fetch(`/api/productos/${productId}/variantes`);
                    const data = await response.json();
                    
                    // Ocultar loading
                    loadingVariants.classList.add('hidden');
                    
                    if (data.success && data.variantes.length > 0) {
                        // Renderizar variantes
                        data.variantes.forEach(variante => {
                            const variantElement = createVariantElement(variante);
                            variantsList.appendChild(variantElement);
                        });
                    } else {
                        // Mostrar mensaje de no variantes
                        noVariantsMessage.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error cargando variantes:', error);
                    loadingVariants.classList.add('hidden');
                    noVariantsMessage.classList.remove('hidden');
                }
            }
            
            // Función para crear elemento de variante
            function createVariantElement(variante) {
                const template = document.getElementById('variantTemplate');
                const clone = template.content.cloneNode(true);
                
                // Configurar color preview
                const colorPreview = clone.querySelector('.color-preview');
                colorPreview.style.backgroundColor = variante.codigo_color || '#CCCCCC';
                
                // Configurar información de la variante
                clone.querySelector('.variant-name').textContent = variante.nombre;
                clone.querySelector('.variant-description').textContent = variante.descripcion || '';
                
                // Configurar precio con validación
                const precioBase = parseFloat(currentProductPrice) || 0;
                const precioAdicional = parseFloat(variante.precio_adicional) || 0;
                const precioTotal = precioBase + precioAdicional;
                
                // Usar función de formateo consistente
                clone.querySelector('.variant-price').textContent = formatearPrecio(precioTotal);
                
                // Configurar stock
                const stockElement = clone.querySelector('.variant-stock');
                if (variante.stock_disponible > 0) {
                    stockElement.textContent = `${variante.stock_disponible} disponibles`;
                    stockElement.className = 'text-sm text-green-600';
                } else {
                    stockElement.textContent = 'Sin stock';
                    stockElement.className = 'text-sm text-red-600';
                }
                
                // Configurar botón de agregar
                const addButton = clone.querySelector('.add-variant-to-cart');
                if (variante.stock_disponible > 0) {
                    addButton.addEventListener('click', async function() {
                        // Deshabilitar botón mientras se procesa
                        addButton.disabled = true;
                        const originalText = addButton.innerHTML;
                        addButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Agregando...';
                        
                        try {
                            await addVariantToCart(variante);
                            // Cerrar modal solo si se agregó exitosamente
                            closeModal();
                        } catch (error) {
                            console.error('Error al agregar variante:', error);
                            // Restaurar botón en caso de error
                            addButton.disabled = false;
                            addButton.innerHTML = originalText;
                        }
                    });
                } else {
                    addButton.disabled = true;
                    addButton.textContent = 'Sin stock';
                    addButton.className = 'w-full bg-gray-400 text-white py-2 px-4 rounded-lg cursor-not-allowed font-medium text-sm';
                }
                
                // Agregar evento de hover para sincronizar stock
                const variantItem = clone.querySelector('.variant-item');
                if (variantItem) {
                    variantItem.addEventListener('mouseenter', function() {
                        if (window.stockSync) {
                            window.stockSync.updateStockTemporary(variante.stock_disponible);
                            console.log('Stock sincronizado en hover:', variante.stock_disponible);
                        }
                    });
                    
                    variantItem.addEventListener('mouseleave', function() {
                        // Restaurar stock original al salir del hover
                        if (window.stockSync) {
                            window.stockSync.resetStock();
                        }
                    });
                }
                
                return clone;
            }
            
            // Función para agregar variante al carrito
            async function addVariantToCart(variante) {
                console.log('addVariantToCart llamada con variante:', variante);
                
                // Validar que tenemos los datos necesarios
                if (!currentProductId) {
                    console.error('Error: currentProductId no está definido');
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('Error: No se pudo obtener la información del producto', 'error', 5);
                    }
                    return;
                }
                
                const product = {
                    id: parseInt(currentProductId),
                    name: currentProductName || 'Producto',
                    price: parseFloat(currentProductPrice) || 0,
                    variante_id: variante.variante_id ? parseInt(variante.variante_id) : null,
                    variante_nombre: variante.nombre || null,
                    precio_adicional: variante.precio_adicional ? parseFloat(variante.precio_adicional) : 0
                };
                
                console.log('Producto preparado para agregar:', product);
                
                // Sincronizar stock con la variante seleccionada
                if (window.stockSync) {
                    window.stockSync.updateStock(variante.stock_disponible);
                    console.log('Stock sincronizado con variante:', variante.stock_disponible);
                }
                
                // Llamar a la función global addToCart y esperar su resultado
                if (typeof addToCart === 'function') {
                    try {
                        await addToCart(product);
                        console.log('Producto agregado exitosamente desde addVariantToCart');
                    } catch (error) {
                        console.error('Error al agregar producto desde addVariantToCart:', error);
                        if (typeof window.showNotification === 'function') {
                            window.showNotification('Error al agregar el producto al carrito. Por favor, intenta nuevamente.', 'error', 5);
                        }
                    }
                } else {
                    console.error('Función addToCart no encontrada');
                    if (typeof window.showNotification === 'function') {
                        window.showNotification('Error: No se pudo agregar el producto al carrito', 'error', 5);
                    }
                }
            }
            
            // Event listeners para botones de selección de variante
            selectVariantButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Obtener datos del producto
                    currentProductId = this.dataset.productoId;
                    currentProductName = this.dataset.productoNombre;
                    currentProductPrice = parseFloat(this.dataset.productoPrecio);
                    
                    // Actualizar información del modal
                    document.getElementById('productName').textContent = currentProductName;
                    document.getElementById('productPrice').textContent = `Precio base: ${formatearPrecio(currentProductPrice)}`;
                    
                    // Abrir modal y cargar variantes
                    openModal();
                    loadVariants(currentProductId);
                });
            });
            
            // Event delegation para botones dinámicos
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('select-variant')) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Obtener datos del producto
                    currentProductId = e.target.dataset.productoId;
                    currentProductName = e.target.dataset.productoNombre;
                    currentProductPrice = parseFloat(e.target.dataset.productoPrecio);
                    
                    // Actualizar información del modal
                    document.getElementById('productName').textContent = currentProductName;
                    document.getElementById('productPrice').textContent = `Precio base: ${formatearPrecio(currentProductPrice)}`;
                    
                    // Abrir modal y cargar variantes
                    openModal();
                    loadVariants(currentProductId);
                }
            });
        });
    </script>
    
    <!-- Script de sincronización de stock -->
    <script src="{{ asset('js/stock-sync.js') }}"></script>
</body>

</html>
