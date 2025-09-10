@extends('layouts.landing')
@section('title', '4GMovil - Contáctanos')
@section('meta-description', 'Contáctanos en 4GMovil. Estamos aquí para ayudarte con tus necesidades tecnológicas. Llámanos, escríbenos o visítanos en Medellín.')

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
                        <span class="ml-1 text-sm font-medium text-gray-500 dark:text-gray-400 md:ml-2">{{ __('messages.nav.contact') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Hero Section -->
    <section
        class="min-h-[60vh] flex items-center relative overflow-hidden pt-16 bg-gradient-to-br from-blue-600 via-indigo-700 to-purple-800">
        <!-- Elementos decorativos -->
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-10 left-4 sm:left-10 w-16 h-16 sm:w-20 sm:h-20 bg-white/10 rounded-full"></div>
            <div class="absolute top-20 right-4 sm:right-20 w-24 h-24 sm:w-32 sm:h-32 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-10 left-1/4 w-12 h-12 sm:w-16 sm:h-16 bg-white/10 rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h1 class="text-3xl sm:text-4xl lg:text-6xl font-bold mb-4 sm:mb-6 text-white leading-tight">
                {{ __('messages.contact.title') }}
            </h1>
            <p class="text-lg sm:text-xl lg:text-2xl mb-6 sm:mb-8 text-blue-100 leading-relaxed max-w-3xl mx-auto px-4">
                {{ __('messages.contact.subtitle') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#contacto"
                    class="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i>{{ __('messages.contact.send_message') }}
                </a>
                <a href="#ubicacion"
                    class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-blue-600 dark:hover:text-blue-600 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-map-marker-alt mr-2"></i>{{ __('messages.contact.view_location') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Información de Contacto -->
    <section class="py-20 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-6">{{ __('messages.contact.multiple_ways') }}</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    {{ __('messages.contact.choose_option') }}
                </p>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Teléfono -->
                <div
                    class="text-center bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-8 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div
                        class="bg-white bg-opacity-20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-phone text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">{{ __('messages.contact.call_us') }}</h3>
                    <p class="text-lg mb-2">+57 302 597 0220</p>
                    <a href="tel:+573025970220"
                        class="inline-block mt-4 bg-white bg-opacity-20 px-4 py-2 rounded-lg hover:bg-opacity-30 transition-all duration-300">
                        <i class="fas fa-phone mr-2"></i>{{ __('messages.contact.call_now') }}
                    </a>
                </div>
                <!-- Email -->
                <div
                    class="text-center bg-gradient-to-br from-green-600 to-emerald-700 rounded-xl p-8 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                    <div
                        class="bg-white bg-opacity-20 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">{{ __('messages.contact.write_us') }}</h3>
                    <p class="text-lg mb-2">osmandavidgallego@gmail.com</p>
                    <a href="mailto:osmandavidgallego@gmail.com?subject=Consulta%204GMovil&body=Hola%20equipo%20de%204GMovil,%0A%0AMe%20gustaría%20obtener%20más%20información%20sobre%20sus%20productos%20y%20servicios.%0A%0ASaludos,%0A"
                        class="inline-block mt-4 bg-white bg-opacity-20 px-4 py-2 rounded-lg hover:bg-opacity-30 transition-all duration-300">
                        <i class="fas fa-envelope mr-2"></i>{{ __('messages.contact.send_email') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Formulario de Contacto -->
    <section id="contacto" class="py-20 bg-gray-50 dark:bg-gray-800">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-6">{{ __('messages.contact.send_us_message') }}</h2>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        {{ __('messages.contact.complete_form') }}
                    </p>
                </div>

                <div class="grid lg:grid-cols-2 gap-12">
                    <!-- Formulario -->
                    <div
                        class="bg-white dark:bg-gray-700 rounded-xl p-8 shadow-lg border border-gray-100 dark:border-gray-600">
                        <form id="contactForm" class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nombre"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.contact.name') }}
                                        *</label>
                                    <input type="text" id="nombre" name="nombre" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                        placeholder="{{ __('messages.contact.your_full_name') }}">
                                </div>
                                <div>
                                    <label for="apellido"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.contact.lastname') }}
                                        *</label>
                                    <input type="text" id="apellido" name="apellido" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                        placeholder="{{ __('messages.contact.your_lastname') }}">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.contact.email') }}
                                        *</label>
                                    <input type="email" id="email" name="email" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                        placeholder="{{ __('messages.contact.your_email') }}">
                                </div>
                                <div>
                                    <label for="telefono"
                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.contact.phone') }}
                                        *</label>
                                    <input type="tel" id="telefono" name="telefono" required
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                        placeholder="{{ __('messages.contact.your_phone') }}">
                                </div>
                            </div>

                            <div>
                                <label for="asunto"
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.contact.subject') }}
                                    *</label>
                                <select id="asunto" name="asunto" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-white dark:bg-gray-600 text-gray-900 dark:text-white">
                                    <option value="">{{ __('messages.contact.select_subject') }}</option>
                                    <option value="consulta-producto">{{ __('messages.contact.product_inquiry') }}</option>
                                    <option value="servicio-tecnico">{{ __('messages.contact.technical_service') }}</option>
                                    <option value="venta-corporativa">{{ __('messages.contact.corporate_sales') }}</option>
                                    <option value="soporte">{{ __('messages.contact.support') }}</option>
                                    <option value="otro">{{ __('messages.contact.other') }}</option>
                                </select>
                            </div>

                            <div>
                                <label for="mensaje"
                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.contact.message') }}
                                    *</label>
                                <textarea id="mensaje" name="mensaje" rows="5" required
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                    placeholder="{{ __('messages.contact.tell_us_help') }}"></textarea>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" id="terminos" name="terminos" required
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label for="terminos" class="ml-2 text-sm text-gray-600 dark:text-gray-300">
                                    {{ __('messages.contact.accept_terms') }} <a href="#"
                                        class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('messages.contact.terms_conditions') }}</a>
                                    *
                                </label>
                            </div>

                            <button type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-4 px-8 rounded-lg font-semibold hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                <i class="fas fa-paper-plane mr-2"></i>
                                {{ __('messages.contact.send_message') }}
                            </button>
                        </form>
                    </div>

                    <!-- Información Adicional -->
                    <div class="space-y-8">
                        <!-- Horarios -->
                        <div
                            class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-600">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-clock text-blue-600 dark:text-blue-400 mr-3"></i>
                                {{ __('messages.contact.opening_hours') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-300">{{ __('messages.contact.monday_friday') }}</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">8:00 AM - 6:00 PM</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-600 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-300">{{ __('messages.contact.saturday') }}</span>
                                    <span class="font-semibold text-gray-800 dark:text-white">9:00 AM - 4:00 PM</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                    <span class="text-gray-600 dark:text-gray-300">{{ __('messages.contact.sunday') }}</span>
                                    <span class="font-semibold text-red-500">{{ __('messages.contact.closed') }}</span>
                                </div>
                            </div>
                        </div>

                      

                        <!-- Ubicación -->
                        <div id="ubicacion"
                            class="bg-white dark:bg-gray-700 rounded-xl p-6 shadow-lg border border-gray-100 dark:border-gray-600">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt text-blue-600 dark:text-blue-400 mr-3"></i>
                                {{ __('messages.contact.our_location') }}
                            </h3>
                            <!-- Mapa interactivo - Solución 100% funcional -->
                            <div class="bg-gray-200 dark:bg-gray-600 rounded-lg h-64 flex items-center justify-center relative overflow-hidden group">
                                <!-- Mapa visual personalizado -->
                                <div id="mapContainer" class="w-full h-full relative bg-gradient-to-br from-blue-100 via-blue-50 to-indigo-100 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800 rounded-lg overflow-hidden">
                                    <!-- Fondo de mapa estilizado -->
                                    <div class="absolute inset-0">
                                        <!-- Líneas de calles simuladas -->
                                        <div class="absolute top-1/4 left-0 w-full h-1 bg-gray-300 dark:bg-gray-600 opacity-30"></div>
                                        <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-300 dark:bg-gray-600 opacity-30"></div>
                                        <div class="absolute top-3/4 left-0 w-full h-1 bg-gray-300 dark:bg-gray-600 opacity-30"></div>
                                        <div class="absolute top-0 left-1/4 h-full w-1 bg-gray-300 dark:bg-gray-600 opacity-30"></div>
                                        <div class="absolute top-0 left-1/2 h-full w-1 bg-gray-300 dark:bg-gray-600 opacity-30"></div>
                                        <div class="absolute top-0 left-3/4 h-full w-1 bg-gray-300 dark:bg-gray-600 opacity-30"></div>
                                    </div>
                                    
                                    <!-- Marcador de ubicación -->
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
                                        <div class="bg-red-500 w-8 h-8 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                                            <i class="fas fa-map-marker-alt text-white text-lg"></i>
                                        </div>
                                        <div class="bg-red-500 w-3 h-3 rounded-full absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 animate-ping"></div>
                                    </div>
                                    
                                    <!-- Información de ubicación flotante -->
                                    <div class="absolute top-4 left-4 bg-white dark:bg-gray-800 bg-opacity-95 dark:bg-opacity-95 rounded-xl px-4 py-3 shadow-xl border border-gray-200 dark:border-gray-600">
                                        <div class="text-center">
                                            <div class="flex items-center justify-center mb-1">
                                                <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ __('messages.contact.address_line1') }}</p>
                                            </div>
                                            <p class="text-xs text-gray-600 dark:text-gray-300">{{ __('messages.contact.address_line2') }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.contact.address_line3') }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Botón de pantalla completa -->
                                    <button onclick="toggleFullscreenMap()" class="absolute top-4 right-4 bg-white dark:bg-gray-800 bg-opacity-90 dark:bg-opacity-90 p-2 rounded-lg shadow-lg hover:bg-opacity-100 transition-all duration-300">
                                        <i class="fas fa-expand text-gray-600 dark:text-gray-300"></i>
                                    </button>
                                    
                                    <!-- Overlay clickeable para abrir Google Maps -->
                                    <div class="absolute inset-0 bg-transparent hover:bg-black hover:bg-opacity-5 transition-all duration-300 cursor-pointer" 
                                         onclick="openGoogleMaps()" 
                                         title="{{ __('messages.contact.click_google_maps') }}">
                                    </div>
                                </div>
                            </div>
                                <div id="mapLoading"
                                    class="absolute inset-0 bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                    <div class="text-center">
                                        <div
                                            class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2">
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('messages.contact.loading_map') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de navegación -->
                            <div class="mt-4 text-center space-y-2">
                                <button onclick="getDirections()"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors transform hover:scale-105">
                                    <i class="fas fa-route mr-2"></i>
                                    Obtener Direcciones
                                </button>
                               
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.getElementById('contactForm');
            const mapLoading = document.getElementById('mapLoading');

            // Manejo del formulario de contacto
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Mostrar loading
                    const submitButton = contactForm.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
                    submitButton.disabled = true;

                    // Obtener datos del formulario
                    const formData = new FormData(contactForm);
                    
                    // Enviar formulario
                    fetch('{{ route("contacto.enviar") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            nombre: formData.get('nombre'),
                            apellido: formData.get('apellido'),
                            email: formData.get('email'),
                            telefono: formData.get('telefono'),
                            asunto: formData.get('asunto'),
                            mensaje: formData.get('mensaje'),
                            terminos: formData.get('terminos') ? true : false
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Éxito
                            Swal.fire({
                                title: '{{ __('messages.contact.message_sent') }}',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: '{{ __('messages.contact.understood') }}',
                                confirmButtonColor: '#3B82F6'
                            });
                            
                            // Limpiar formulario
                            contactForm.reset();
                        } else {
                            // Error de validación
                            let errorMessage = data.message;
                            if (data.errors) {
                                errorMessage = '{{ __('messages.contact.fix_errors') }}\n';
                                Object.keys(data.errors).forEach(field => {
                                    errorMessage += `• ${data.errors[field][0]}\n`;
                                });
                            }
                            
                            Swal.fire({
                                title: '{{ __('messages.contact.error') }}',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: '{{ __('messages.contact.understood') }}',
                                confirmButtonColor: '#EF4444'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: '{{ __('messages.contact.error') }}',
                            text: '{{ __('messages.contact.send_error') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('messages.contact.understood') }}',
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

            // Ocultar indicador de carga inmediatamente (mapa visual)
            const mapContainer = document.getElementById('mapContainer');
            if (mapContainer) {
                console.log('✅ Mapa visual encontrado en el DOM');
                if (mapLoading) {
                    mapLoading.style.display = 'none';
                    console.log('✅ Indicador de carga ocultado');
                }
            } else {
                console.log('❌ No se encontró el elemento del mapa');
            }
        });

        // Funciones para el mapa interactivo
        function openGoogleMaps() {
            const address = encodeURIComponent('Cra 52 #49-100, La Candelaria, Medellín, Colombia');
            const url = `https://maps.google.com/?q=${address}`;
            window.open(url, '_blank');
        }

        function openWaze() {
    console.log('🚗 Abriendo Waze...');
    const lat = '6.2500';
    const lng = '-75.5700';
    const url = `https://waze.com/ul?ll=${lat},${lng}&navigate=yes`;
    console.log('🔗 URL de Waze:', url);
    window.open(url, '_blank');
}

        function toggleFullscreenMap() {
            const mapContainer = document.querySelector('.group');
            const mapVisual = document.getElementById('mapContainer');

            if (!document.fullscreenElement) {
                // Entrar en pantalla completa
                if (mapContainer.requestFullscreen) {
                    mapContainer.requestFullscreen();
                } else if (mapContainer.webkitRequestFullscreen) {
                    mapContainer.webkitRequestFullscreen();
                } else if (mapContainer.msRequestFullscreen) {
                    mapContainer.msRequestFullscreen();
                }

                // Cambiar el icono
                const expandIcon = document.querySelector('.fa-expand');
                if (expandIcon) {
                    expandIcon.className = 'fas fa-compress';
                }

                // Ajustar el tamaño del mapa visual
                if (mapVisual) {
                    mapVisual.style.height = '100vh';
                    mapVisual.style.width = '100vw';
                }
            } else {
                // Salir de pantalla completa
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }

                // Cambiar el icono
                const compressIcon = document.querySelector('.fa-compress');
                if (compressIcon) {
                    compressIcon.className = 'fas fa-expand';
                }

                // Restaurar el tamaño del mapa visual
                if (mapVisual) {
                    mapVisual.style.height = '100%';
                    mapVisual.style.width = '100%';
                }
            }
        }

        // Función para verificar el estado del mapa
function checkMapStatus() {
    const mapContainer = document.getElementById('mapContainer');
    if (mapContainer) {
        console.log('✅ Mapa visual funcionando correctamente');
        return true;
    } else {
        console.log('❌ Error: No se encontró el mapa');
        return false;
    }
}

        // Detectar cambios en pantalla completa
document.addEventListener('fullscreenchange', function() {
    const mapVisual = document.getElementById('mapContainer');
    const expandIcon = document.querySelector('.fa-expand, .fa-compress');
    
    if (!document.fullscreenElement) {
        // Restaurar icono y tamaño
        if (expandIcon) {
            expandIcon.className = 'fas fa-expand';
        }
        if (mapVisual) {
            mapVisual.style.height = '100%';
            mapVisual.style.width = '100%';
        }
    }
});

        // Función para obtener direcciones
        function getDirections() {
            const address = 'Cra 52 #49-100, La Candelaria, Medellín, Colombia';

            Swal.fire({
                title: '¿Cómo quieres llegar?',
                html: `
            <div class="space-y-3">
                <button onclick="openGoogleMaps()" class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-external-link-alt mr-2"></i>Google Maps
                </button>
                <button onclick="openWaze()" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-car mr-2"></i>Waze
                </button>
                <button onclick="copyAddress()" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-copy mr-2"></i>Copiar Dirección
                </button>
            </div>
        `,
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        function copyAddress() {
            const address = 'Cra 52 #49-100, La Candelaria, Medellín, Colombia';
            navigator.clipboard.writeText(address).then(() => {
                Swal.fire({
                    title: '{{ __('messages.contact.address_copied') }}',
                    text: '{{ __('messages.contact.address_copied_desc') }}',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    </script>
@endpush
