<!-- Pantalla de Carga Optimizada y Rápida para 4GMovil -->
<div id="pageLoadingScreen" class="fixed inset-0 bg-white dark:bg-gray-900 z-[9999] flex items-center justify-center transition-all duration-500">
    <div class="text-center relative">
        <!-- Logo de 4GMovil centrado - Optimizado -->
        <div class="mb-8">
            <div class="w-24 h-24 mx-auto bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center border border-gray-200/50 dark:border-gray-700/50 shadow-sm">
                <img src="{{ asset('img/Logo_2.png') }}" alt="4GMovil" class="w-16 h-16 object-contain opacity-90" loading="eager">
            </div>
        </div>
        
        <!-- Título de la marca - Simplificado -->
        <div class="mb-6">
            <h1 class="text-2xl font-light text-gray-700 dark:text-gray-300 mb-1 tracking-wide">
                4G Móvil
            </h1>
            <p class="text-gray-500 dark:text-gray-500 text-sm font-light">Tu tecnología, tu futuro</p>
        </div>
        
        <!-- Spinner simple y rápido -->
        <div class="mb-6">
            <div class="w-8 h-8 border-2 border-gray-200 dark:border-gray-600 border-t-blue-500 rounded-full animate-spin mx-auto mb-3"></div>
            <h3 class="text-sm font-normal text-gray-600 dark:text-gray-400" id="loadingMessage">Cargando...</h3>
        </div>
        
        <!-- Barra de progreso simplificada -->
        <div class="w-64 mx-auto">
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden">
                <div id="loadingProgressBar" class="bg-gradient-to-r from-blue-500 to-blue-600 h-1.5 rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Pantalla de Carga para Navegación (ultra-sutil) -->
<div id="navigationLoadingScreen" class="fixed inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm z-[9998] hidden flex items-center justify-center">
    <div class="bg-white/90 dark:bg-gray-800/90 rounded-xl shadow-lg p-5 border border-gray-200/40 dark:border-gray-700/40 max-w-sm mx-4 backdrop-blur-md">
        <div class="text-center">
            <!-- Logo pequeño para navegación -->
            <div class="mb-3">
                <div class="w-14 h-14 mx-auto bg-gray-50/80 dark:bg-gray-700/80 rounded-lg flex items-center justify-center border border-gray-200/40 dark:border-gray-600/40">
                    <img src="{{ asset('img/Logo_2.png') }}" alt="4GMovil" class="w-8 h-8 object-contain opacity-80">
                </div>
            </div>
            
            <!-- Spinner de navegación ultra-sutil -->
            <div class="relative mb-3">
                <div class="w-10 h-10 border-2 border-gray-200 dark:border-gray-600 border-t-blue-400/60 rounded-full animate-spin mx-auto"></div>
            </div>
            
            <!-- Texto -->
            <h3 class="text-sm font-normal text-gray-600 dark:text-gray-400 mb-1" id="navigationMessage">
                Navegando...
            </h3>
            <p class="text-gray-400 dark:text-gray-500 text-xs font-light mb-2">
                Redirigiendo a la nueva página
            </p>
            
            <!-- Barra de progreso pequeña -->
            <div class="mt-2">
                <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1 overflow-hidden">
                    <div id="navigationProgressBar" class="bg-blue-400/60 h-1 rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de Carga Optimizado y Rápido para 4GMovil

class PageLoadingManager {
    constructor() {
        this.isInitialLoad = true;
        this.isNavigating = false;
        this.progress = 0;
        this.progressInterval = null;
        this.init();
    }
    
    init() {
        // Mostrar pantalla de carga inicial
        this.showInitialLoading();
        
        // Carga rápida y eficiente
        this.simulateFastProgress();
        
        // Interceptar navegación
        this.interceptNavigation();
    }
    
    showInitialLoading() {
        const loadingScreen = document.getElementById('pageLoadingScreen');
        if (loadingScreen) {
            loadingScreen.style.display = 'flex';
            loadingScreen.classList.remove('hidden');
        }
    }
    
    hideInitialLoading() {
        const loadingScreen = document.getElementById('pageLoadingScreen');
        if (loadingScreen) {
            loadingScreen.style.opacity = '0';
            loadingScreen.style.transform = 'scale(0.98)';
            
            setTimeout(() => {
                loadingScreen.style.display = 'none';
                loadingScreen.classList.add('hidden');
                loadingScreen.style.opacity = '';
                loadingScreen.style.transform = '';
            }, 300); // Transición más rápida
        }
    }
    
    simulateFastProgress() {
        const progressBar = document.getElementById('loadingProgressBar');
        const messageElement = document.getElementById('loadingMessage');
        
        // Progreso rápido y realista
        const steps = [
            { progress: 20, message: 'Cargando...' },
            { progress: 50, message: 'Preparando...' },
            { progress: 80, message: 'Finalizando...' },
            { progress: 100, message: 'Listo!' }
        ];
        
        let currentStep = 0;
        
        this.progressInterval = setInterval(() => {
            if (currentStep < steps.length) {
                const step = steps[currentStep];
                this.progress = step.progress;
                
                // Actualizar barra de progreso
                if (progressBar) {
                    progressBar.style.width = `${this.progress}%`;
                }
                
                // Actualizar mensaje
                if (messageElement) {
                    messageElement.textContent = step.message;
                }
                
                currentStep++;
            } else {
                clearInterval(this.progressInterval);
                
                // Ocultar rápidamente
                setTimeout(() => {
                    this.hideInitialLoading();
                    this.isInitialLoad = false;
                }, 200); // Tiempo mínimo
            }
        }, 150); // Intervalo más rápido
    }
    
    showNavigationLoading(message = 'Navegando...') {
        if (this.isNavigating) return;
        
        this.isNavigating = true;
        const navigationScreen = document.getElementById('navigationLoadingScreen');
        const navigationMessage = document.getElementById('navigationMessage');
        
        if (navigationScreen && navigationMessage) {
            navigationMessage.textContent = message;
            navigationScreen.classList.remove('hidden');
            navigationScreen.classList.add('flex');
            
            // Progreso rápido de navegación
            this.simulateFastNavigationProgress();
        }
    }
    
    hideNavigationLoading() {
        this.isNavigating = false;
        const navigationScreen = document.getElementById('navigationLoadingScreen');
        
        if (navigationScreen) {
            navigationScreen.classList.add('hidden');
            navigationScreen.classList.remove('flex');
        }
    }
    
    simulateFastNavigationProgress() {
        const progressBar = document.getElementById('navigationProgressBar');
        let progress = 0;
        
        const interval = setInterval(() => {
            progress += 25; // Progreso más rápido
            
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                
                // Ocultar rápidamente
                setTimeout(() => {
                    this.hideNavigationLoading();
                }, 100);
            }
            
            if (progressBar) {
                progressBar.style.width = `${progress}%`;
            }
        }, 50); // Intervalo muy rápido
    }
    
    interceptNavigation() {
        // Interceptar clicks en enlaces internos - Optimizado
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && this.shouldShowNavigationLoading(link)) {
                e.preventDefault();
                
                const href = link.getAttribute('href');
                const message = link.getAttribute('data-loading-message') || 'Navegando...';
                
                this.showNavigationLoading(message);
                
                // Navegación más rápida
                setTimeout(() => {
                    window.location.href = href;
                }, 300); // Reducido de 1000ms a 300ms
            }
        });
        
        // Interceptar navegación programática - Optimizado
        const originalPushState = history.pushState;
        const originalReplaceState = history.replaceState;
        
        history.pushState = function(...args) {
            originalPushState.apply(history, args);
            if (!window.loadingManager.isInitialLoad) {
                window.loadingManager.showNavigationLoading('Actualizando...');
                setTimeout(() => window.loadingManager.hideNavigationLoading(), 200);
            }
        };
        
        history.replaceState = function(...args) {
            originalReplaceState.apply(history, args);
            if (!window.loadingManager.isInitialLoad) {
                window.loadingManager.showNavigationLoading('Actualizando...');
                setTimeout(() => window.loadingManager.hideNavigationLoading(), 200);
            }
        };
    }
    
    shouldShowNavigationLoading(link) {
        const href = link.getAttribute('href');
        
        // Solo mostrar loading para enlaces internos
        return href && 
               !href.startsWith('#') && 
               !href.startsWith('javascript:') && 
               !href.startsWith('mailto:') && 
               !href.startsWith('tel:') &&
               !link.target &&
               !link.hasAttribute('data-no-loading');
    }
}

// Inicializar el sistema de carga cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.loadingManager = new PageLoadingManager();
});

// Funciones globales optimizadas
function showNavigationLoading(message = 'Navegando...') {
    if (window.loadingManager) {
        window.loadingManager.showNavigationLoading(message);
    }
}

function hideNavigationLoading() {
    if (window.loadingManager) {
        window.loadingManager.hideNavigationLoading();
    }
}

// Función para excluir enlaces del loading automático
function excludeLinkFromLoading(selector) {
    const links = document.querySelectorAll(selector);
    links.forEach(link => {
        link.setAttribute('data-no-loading', 'true');
    });
}
</script>

