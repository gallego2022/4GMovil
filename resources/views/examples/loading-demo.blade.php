@extends('layouts.app')

@section('title', 'Demo - Sistema de Carga')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Encabezado -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Sistema de Carga - Demo
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Prueba las diferentes funcionalidades del sistema de carga
            </p>
        </div>

        <!-- Tarjetas de demostración -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            
            <!-- Carga de Navegación -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Navegación
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Prueba la carga automática al navegar
                    </p>
                    <a href="{{ route('landing') }}" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                       data-loading-message="Navegando a inicio...">
                        Ir al Inicio
                    </a>
                </div>
            </div>

            <!-- Carga Manual -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Carga Manual
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Activa la pantalla de carga manualmente
                    </p>
                    <button onclick="showNavigationLoading('Procesando solicitud...')" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Mostrar Loading
                    </button>
                </div>
            </div>

            <!-- Carga con Progreso -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Con Progreso
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Simula una operación con progreso
                    </p>
                    <button onclick="simulateProgressOperation()" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Simular Operación
                    </button>
                </div>
            </div>

            <!-- Excluir del Loading -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Sin Loading
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Enlace que no muestra loading
                    </p>
                    <a href="#" 
                       class="inline-block bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                       data-no-loading="true">
                        Enlace Excluido
                    </a>
                </div>
            </div>

            <!-- Carga con Mensaje Personalizado -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Mensaje Personalizado
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Loading con mensaje específico
                    </p>
                    <button onclick="showNavigationLoading('Sincronizando datos...')" 
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Sincronizar
                    </button>
                </div>
            </div>

            <!-- Carga con Timeout -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Con Timeout
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Loading que se oculta automáticamente
                    </p>
                    <button onclick="showLoadingWithTimeout()" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Timeout 3s
                    </button>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                Características del Sistema
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        ✅ Funcionalidades
                    </h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>• Carga inicial automática de página</li>
                        <li>• Interceptación automática de navegación</li>
                        <li>• Mensajes personalizables</li>
                        <li>• Barra de progreso animada</li>
                        <li>• Modo oscuro compatible</li>
                        <li>• Responsive design</li>
                        <li>• Transiciones suaves</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        🎯 Casos de Uso
                    </h3>
                    <ul class="space-y-2 text-gray-600 dark:text-gray-400">
                        <li>• Carga inicial de aplicación</li>
                        <li>• Navegación entre páginas</li>
                        <li>• Recarga de página</li>
                        <li>• Operaciones AJAX</li>
                        <li>• Subida de archivos</li>
                        <li>• Procesos largos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para simular operación con progreso
function simulateProgressOperation() {
    showNavigationLoading('Iniciando operación...');
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15 + 5;
        
        if (progress >= 100) {
            progress = 100;
            clearInterval(interval);
            
            setTimeout(() => {
                hideNavigationLoading();
                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación completada!',
                    text: 'La simulación se ejecutó correctamente',
                    confirmButtonColor: '#3b82f6'
                });
            }, 500);
        }
        
        // Actualizar mensaje según progreso
        let message = 'Procesando...';
        if (progress > 25) message = 'Analizando datos...';
        if (progress > 50) message = 'Aplicando cambios...';
        if (progress > 75) message = 'Finalizando...';
        
        showNavigationLoading(`${message} ${Math.round(progress)}%`);
    }, 200);
}

// Función para mostrar loading con timeout
function showLoadingWithTimeout() {
    showNavigationLoading('Operación temporal...');
    
    setTimeout(() => {
        hideNavigationLoading();
    }, 3000);
}

// Agregar loading personalizado a enlaces específicos
document.addEventListener('DOMContentLoaded', function() {
    // Ejemplo: agregar loading a enlaces de categorías
    addNavigationLoadingToLink('.category-link', 'Cargando categoría...');
    
    // Ejemplo: excluir enlaces de descarga
    excludeLinkFromLoading('.download-link');
});
</script>
@endsection
