<!DOCTYPE html>
<html lang="es" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      :class="{ 'dark': darkMode }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel de Administración - 4GMovil')</title>
    <meta name="description" content="@yield('meta-description', 'Panel de administración de 4GMovil')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    @stack('styles')
    
    <!-- Configuración de Tailwind -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0efff',
                            200: '#b8dfff',
                            300: '#7cc3ff',
                            400: '#36a5ff',
                            500: '#0088ff', // Color principal
                            600: '#006ee6',
                            700: '#0055b3',
                            800: '#004494',
                            900: '#003575',
                        },
                        secondary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316', // Naranja
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Estilos para modo oscuro */
        .dark body { @apply bg-gray-900; }
        .dark .bg-white { @apply bg-gray-800; }
        .dark .text-gray-700 { @apply text-gray-200; }
        .dark .text-gray-900 { @apply text-white; }
        .dark .text-gray-500 { @apply text-gray-400; }
        .dark .bg-gray-50 { @apply bg-gray-700; }
        .dark .bg-gray-100 { @apply bg-gray-900; }
        .dark .ring-gray-300 { @apply ring-gray-700; }
        .dark .hover\:bg-gray-50:hover { @apply hover:bg-gray-700; }
        .dark .divide-gray-200 > * + * { @apply divide-gray-700; }
        .dark .shadow-sm { @apply shadow-none; }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen" x-data="{ sidebarOpen: false }">
        <!-- Sidebar Móvil (offcanvas) -->
        <div x-show="sidebarOpen" 
             class="fixed inset-0 z-40 lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            
            <!-- Overlay de fondo -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" 
                 @click="sidebarOpen = false">
            </div>

            <!-- Panel lateral -->
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-brand-800">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="sidebarOpen = false"
                            class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Cerrar sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center justify-center px-4 py-3">
                <a href="{{ route('landing') }}">
                            <img src="{{ asset('storage/imagenes/Logo_2.png') }}" alt="4GMovil" class="h-22">
                          </a>
                </div>

                <!-- Menú lateral -->
                <div class="flex-1 h-0 overflow-y-auto">
                    @include('layouts.partials.sidebar-menu')
                </div>
            </div>
        </div>

        <!-- Sidebar Escritorio -->
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            <div class="flex-1 flex flex-col min-h-0 bg-brand-800">
                <div class="flex-1 flex flex-col overflow-y-auto">
                    <!-- Logo -->
                    <div class="flex items-center justify-center h-24 px-4 bg-brand-900">
                        <a href="{{ route('landing') }}">
                            <img src="{{ asset('storage/imagenes/Logo_2.png') }}" alt="4GMovil" class="h-20">
                          </a>
                    </div>
                    <!-- Menú lateral -->
                    @include('layouts.partials.sidebar-menu')
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <!-- Barra superior -->
            <nav class="bg-white shadow-sm">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Botón menú móvil -->
                        <button @click="sidebarOpen = true" 
                                class="lg:hidden px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500">
                            <span class="sr-only">Abrir menú</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Menú usuario -->
                        @auth
                        <div class="ml-4 flex items-center space-x-4">
                            <!-- Botón de cambio de tema -->
                            <button @click="darkMode = !darkMode" 
                                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <span class="sr-only">Cambiar tema</span>
                                <!-- Ícono sol para modo claro -->
                                <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <!-- Ícono luna para modo oscuro -->
                                <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </button>

                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">
                                    <span class="sr-only">Abrir menú usuario</span>
                                    @if(Auth::user()->foto_perfil)
                                        <img class="h-8 w-8 rounded-full object-cover" 
                                             src="{{ asset('storage/' . Auth::user()->foto_perfil) }}" 
                                             alt="{{ Auth::user()->nombre_usuario }}">
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-brand-200 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-brand-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="ml-3 text-gray-700">{{ Auth::user()->nombre_usuario }}</span>
                                </button>

                                <!-- Menú desplegable -->
                                <div x-show="open" 
                                     @click.away="open = false"
                                     class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5"
                                     role="menu"
                                     x-cloak>
                                    <a href="{{ url('/perfil') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50" 
                                       role="menuitem">
                                        <i class="fa-solid fa-user mr-2"></i>Mi perfil
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-brand-50" 
                                                role="menuitem">
                                            <i class="fa-solid fa-right-from-bracket mr-2"></i>Cerrar sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 mr-4">
            <a href="https://www.facebook.com/cuatro.g.movil.2025/" class="text-black hover:text-warning"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/tumovil4g/?fbclid=IwY2xjawJClENleHRuA2FlbQIxMAABHfgoRN_xkx-QlgRi5XJX_YY8IVnmcJTaee4R2UWXoOMJTTip9ml-DYoVXw_aem_-9MriOuo88DFcLIGBeizRw" class="text-black hover:text-warning"><i class="fab fa-instagram"></i></a>
            <a href="https://api.whatsapp.com/send/?phone=573117337272&text&type=phone_number&app_absent=0" class="text-black hover:text-warning"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.tiktok.com/@tumovil4g" class="text-black hover:text-warning"><i class="fab fa-tiktok"></i></a>
        </div>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Contenido de la página -->
            <main class="flex-1">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- SweetAlert2 Scripts -->
    @include('layouts.partials.sweet-alerts')
    
    @stack('scripts')
</body>
</html> 