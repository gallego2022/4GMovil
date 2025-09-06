@extends('layouts.landing')

@section('title', 'Servicio Técnico - 4G Móvil')
@section('meta_description', 'Servicio técnico profesional para celulares, tablets y computadoras. Reparaciones con garantía, diagnóstico gratuito y repuestos originales en Medellín.')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    {{ __('messages.nav.home') }}
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-angle-right text-gray-400 mx-2"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">{{ __('messages.nav.technical_service') }}</span>
                </div>
            </li>
        </ol>
    </nav>
</div>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white overflow-hidden">
        <!-- Elementos decorativos -->
        <div class="absolute top-0 left-0 w-72 h-72 bg-white opacity-10 rounded-full -translate-x-36 -translate-y-36"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full translate-x-48 -translate-y-48"></div>
        <div class="absolute bottom-0 left-1/2 w-80 h-80 bg-white opacity-10 rounded-full -translate-x-40 translate-y-40"></div>
        
        <div class="container mx-auto px-4 py-20 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-medium mb-6 backdrop-blur-sm">
                    <i class="fas fa-screwdriver-wrench mr-2"></i>
                    {{ __('messages.technical_service.title') }}
                </div>
                
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    {{ __('messages.technical_service.subtitle') }}
                </h1>
                
                <p class="text-xl md:text-2xl mb-8 opacity-90 leading-relaxed">
                    {{ __('messages.technical_service.description') }}
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#servicios"
                        class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-tools mr-2"></i>{{ __('messages.technical_service.view_services') }}
                    </a>
                    <a href="#diagnostico"
                        class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-blue-600 dark:hover:text-blue-600 transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-stethoscope mr-2"></i>{{ __('messages.technical_service.free_diagnosis') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    

    <!-- Servicios Principales -->
    <section id="servicios" class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-sm font-medium mb-4">
                    <i class="fas fa-star mr-2"></i>{{ __('messages.technical_service.specialized_services') }}
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                    {{ __('messages.technical_service.integral_solutions') }}
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    {{ __('messages.technical_service.certified_technicians') }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Diagnóstico -->
                <div class="group bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-stethoscope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('messages.technical_service.free_diagnosis_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('messages.technical_service.free_diagnosis_desc') }}
                    </p>
                </div>

                <!-- Reparaciones -->
                <div class="group bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-tools text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('messages.technical_service.fast_repairs') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('messages.technical_service.fast_repairs_desc') }}
                    </p>
                </div>

                <!-- Software -->
                <div class="group bg-gradient-to-br from-purple-50 to-violet-100 dark:from-purple-900/20 dark:to-violet-900/20 rounded-xl p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="bg-gradient-to-br from-purple-500 to-violet-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-robot text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('messages.technical_service.software_system') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('messages.technical_service.software_system_desc') }}
                    </p>
                </div>

                <!-- Garantía -->
                <div class="group bg-gradient-to-br from-orange-50 to-red-100 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="bg-gradient-to-br from-orange-500 to-red-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-shield-halved text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('messages.technical_service.total_warranty') }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('messages.technical_service.total_warranty_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dispositivos que Reparamos -->
    <section class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                    {{ __('messages.technical_service.repair_all_devices') }}
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    {{ __('messages.technical_service.repair_all_devices_desc') }}
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Celulares -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-mobile-screen-button text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('messages.technical_service.smartphones') }}</h3>
                        <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-left">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Cambio de pantallas
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Reparación de baterías
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Módulos de cámara
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Conectores de carga
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Problemas de software
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tablets -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-tablet-screen-button text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('messages.technical_service.tablets') }}</h3>
                        <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-left">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Pantallas táctiles
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Baterías internas
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Cámaras frontales y traseras
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Altavoces y micrófonos
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Actualizaciones de sistema
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Computadoras -->
                <div class="bg-white dark:bg-gray-900 rounded-xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-gradient-to-br from-purple-500 to-violet-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-laptop text-white text-3xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ __('messages.technical_service.computers') }}</h3>
                        <ul class="text-gray-600 dark:text-gray-300 space-y-2 text-left">
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Mantenimiento preventivo
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Cambio de componentes
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Instalación de software
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Recuperación de datos
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                Optimización de rendimiento
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulario de Diagnóstico -->
    <section id="diagnostico" class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-12">
                    <span class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-clipboard-check mr-2"></i>Diagnóstico Gratuito
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                        Solicita tu diagnóstico sin costo
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Completa el formulario y te contactaremos para agendar tu cita de diagnóstico
                    </p>
                </div>

                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Información -->
                    <div class="space-y-8">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-clock text-blue-600 mr-3"></i>
                                Tiempo de Reparación
                            </h3>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Reparaciones simples:</strong> 1-2 horas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Cambio de pantalla:</strong> 30-60 minutos
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Problemas de software:</strong> 1-3 horas
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Reparaciones complejas:</strong> 24-48 horas
                                </li>
                            </ul>
                        </div>

                        <div class="bg-gradient-to-br from-green-50 to-emerald-100 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-shield-halved text-green-600 mr-3"></i>
                                Nuestra Garantía
                            </h3>
                            <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Garantía por escrito</strong> en todas las reparaciones
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Repuestos originales</strong> o de alta calidad
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Pruebas de funcionamiento</strong> antes de entregar
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <strong>Soporte post-reparación</strong> por 30 días
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl p-8 shadow-lg">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Solicita tu diagnóstico</h3>
                        <form id="serviceForm" class="space-y-6">
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="name">
                                        <i class="fas fa-user mr-2"></i>Nombre completo
                                    </label>
                                    <input id="name" name="name" type="text" required 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                        placeholder="Tu nombre completo">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="phone">
                                        <i class="fas fa-phone mr-2"></i>Teléfono
                                    </label>
                                    <input id="phone" name="phone" type="tel" required 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                        placeholder="+57 302 597 0220">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="email">
                                    <i class="fas fa-envelope mr-2"></i>Email (opcional)
                                </label>
                                <input id="email" name="email" type="email" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                    placeholder="tu@email.com">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Opcional: Para recibir confirmación por email
                                </p>
                            </div>
                            
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="device">
                                        <i class="fas fa-mobile-alt mr-2"></i>Tipo de dispositivo
                                    </label>
                                    <select id="device" name="device" required 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                                        <option value="">Selecciona...</option>
                                        <option value="celular">Celular</option>
                                        <option value="tablet">Tablet</option>
                                        <option value="computadora">Computadora</option>
                                        <option value="laptop">Laptop</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="model">
                                        <i class="fas fa-tag mr-2"></i>Marca y modelo
                                    </label>
                                    <input id="model" name="model" type="text" 
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                        placeholder="Ej. iPhone 12 / Samsung A54">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="problem">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Descripción del problema
                                </label>
                                <textarea id="problem" name="problem" rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white resize-none"
                                    placeholder="Describe el problema que presenta tu dispositivo..."></textarea>
                            </div>
                            
                            <button id="serviceSubmit" type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white py-4 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>Enviar Solicitud
                            </button>
                            
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Te contactaremos en las próximas 2 horas para agendar tu cita
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Proceso de Trabajo -->
    <section class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">
                    ¿Cómo funciona nuestro proceso?
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Un proceso simple y transparente para que sepas exactamente qué esperar
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <!-- Paso 1 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl font-bold">1</span>
                        </div>
                        <div class="absolute -top-2 -right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Contacto Inicial</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Nos contactas por WhatsApp, formulario o llamada para describir el problema
                    </p>
                </div>

                <!-- Paso 2 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl font-bold">2</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Diagnóstico</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Evaluamos tu dispositivo y te damos un diagnóstico detallado con cotización
                    </p>
                </div>

                <!-- Paso 3 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div class="bg-gradient-to-br from-purple-500 to-violet-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl font-bold">3</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Reparación</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Realizamos la reparación con repuestos de calidad y técnicas profesionales
                    </p>
                </div>

                <!-- Paso 4 -->
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div class="bg-gradient-to-br from-orange-500 to-red-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto group-hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl font-bold">4</span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Entrega</h3>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        Probamos todo el funcionamiento y te entregamos con garantía por escrito
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">¿Tu dispositivo necesita reparación?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">
                No esperes más, contáctanos ahora y recupera la funcionalidad de tu dispositivo
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/573025970220?text=Hola%20equipo%20de%204GMovil,%20necesito%20servicio%20técnico%20para%20mi%20dispositivo.%20¿Podrían%20ayudarme?"
                    target="_blank"
                    class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fab fa-whatsapp mr-2"></i>Chat WhatsApp
                </a>
                <a href="{{ route('landing') }}"
                    class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-home mr-2"></i>Volver al Inicio
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceForm = document.getElementById('serviceForm');
    const submitButton = document.getElementById('serviceSubmit');

    if (serviceForm) {
        serviceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validación básica
            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const device = document.getElementById('device').value;
            
            if (!name || !phone || !device) {
                Swal.fire({
                    title: 'Campos Requeridos',
                    text: 'Por favor, completa todos los campos obligatorios.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#3B82F6'
                });
                return;
            }

            // Mostrar loading
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
            submitButton.disabled = true;

            // Obtener datos del formulario
            const formData = new FormData(serviceForm);
            
            // Enviar formulario
            fetch('{{ route("servicio-tecnico.enviar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: formData.get('name'),
                    phone: formData.get('phone'),
                    email: formData.get('email'),
                    device: formData.get('device'),
                    model: formData.get('model'),
                    problem: formData.get('problem')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Éxito
                    Swal.fire({
                        title: '¡Solicitud Enviada!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#3B82F6'
                    });
                    
                    // Limpiar formulario
                    serviceForm.reset();
                } else {
                    // Error de validación
                    let errorMessage = data.message;
                    if (data.errors) {
                        errorMessage = 'Por favor, corrige los siguientes errores:\n';
                        Object.keys(data.errors).forEach(field => {
                            errorMessage += `• ${data.errors[field][0]}\n`;
                        });
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#EF4444'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un error al enviar la solicitud. Por favor, inténtalo de nuevo.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#EF4444'
                });
            })
            .finally(() => {
                // Restaurar botón
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });
    }

    // Animaciones de contadores (si los agregas)
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
            }
        });
    }, observerOptions);

    // Observar elementos para animaciones
    document.querySelectorAll('.group').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endpush