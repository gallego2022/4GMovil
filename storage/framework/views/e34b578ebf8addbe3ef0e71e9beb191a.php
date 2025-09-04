<!-- Pantalla de Carga Ultra-Suave y Minimalista para 4GMovil -->
<div id="pageLoadingScreen" class="fixed inset-0 bg-white dark:bg-gray-900 z-[9999] flex items-center justify-center transition-all duration-1500">
    <div class="text-center relative">
        <!-- Logo de 4GMovil centrado -->
        <div class="mb-12">
            <div class="w-32 h-32 mx-auto bg-gray-50 dark:bg-gray-800 rounded-3xl flex items-center justify-center border border-gray-200/50 dark:border-gray-700/50 shadow-sm">
                <img src="<?php echo e(asset('img/Logo_2.png')); ?>" alt="4GMovil" class="w-24 h-24 object-contain opacity-90">
            </div>
        </div>
        
        <!-- Título de la marca -->
        <div class="mb-8">
            <h1 class="text-3xl font-light text-gray-700 dark:text-gray-300 mb-2 tracking-wide">
                4G Móvil
            </h1>
            <p class="text-gray-500 dark:text-gray-500 text-base font-light">Tu tecnología, tu futuro</p>
        </div>
        
        <!-- Mensaje de carga dinámico -->
        <div class="mb-8">
            <h3 class="text-lg font-normal text-gray-600 dark:text-gray-400 mb-2" id="loadingMessage">Inicializando sistema...</h3>
            <p class="text-gray-400 dark:text-gray-500 text-sm font-light">Preparando tu experiencia digital</p>
        </div>
        
        <!-- Barra de progreso centrada -->
        <div class="w-80 mx-auto mb-8">
            <!-- Contenedor de la barra -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden border border-gray-200/30 dark:border-gray-600/30">
                <!-- Barra de progreso ultra-suave -->
                <div id="loadingProgressBar" class="bg-gradient-to-r from-blue-400/80 to-blue-500/80 h-2 rounded-full transition-all duration-1000 ease-out shadow-none" style="width: 0%"></div>
            </div>
            
            <!-- Porcentaje de progreso -->
            <div class="mt-4 text-center">
                <span id="loadingProgressText" class="text-lg font-light text-gray-600 dark:text-gray-400">0%</span>
                <span class="text-gray-400 dark:text-gray-500 text-sm ml-2 font-light">completado</span>
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
                    <img src="<?php echo e(asset('img/Logo_2.png')); ?>" alt="4GMovil" class="w-8 h-8 object-contain opacity-80">
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
// Sistema de Carga Ultra-Suave y Minimalista para 4GMovil

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
        
        // Simular progreso de carga inicial ultra-suave
        this.simulateInitialProgress();
        
        // Interceptar navegación
        this.interceptNavigation();
        
        // Interceptar recarga de página
        this.interceptPageReload();
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
            loadingScreen.style.transform = 'scale(0.99)';
            
            setTimeout(() => {
                loadingScreen.style.display = 'none';
                loadingScreen.classList.add('hidden');
                loadingScreen.style.opacity = '';
                loadingScreen.style.transform = '';
            }, 1500);
        }
    }
    
    simulateInitialProgress() {
        const progressBar = document.getElementById('loadingProgressBar');
        const progressText = document.getElementById('loadingProgressText');
        const messages = [
            'Inicializando sistema...',
            'Cargando componentes...',
            'Preparando interfaz...',
            'Conectando servicios...',
            'Sincronizando datos...',
            'Finalizando carga...'
        ];
        
        let currentStep = 0;
        
        this.progressInterval = setInterval(() => {
            this.progress += Math.random() * 6 + 4; // Incremento ultra-suave entre 4-10
            
            if (this.progress >= 100) {
                this.progress = 100;
                clearInterval(this.progressInterval);
                
                // Simular tiempo final de carga
                setTimeout(() => {
                    this.hideInitialLoading();
                    this.isInitialLoad = false;
                }, 800);
            }
            
            // Actualizar barra de progreso ultra-suave
            if (progressBar) {
                progressBar.style.width = `${this.progress}%`;
            }
            
            if (progressText) {
                progressText.textContent = `${Math.round(this.progress)}%`;
            }
            
            // Cambiar mensaje según el progreso
            if (currentStep < messages.length && this.progress > (currentStep + 1) * 16) {
                const messageElement = document.getElementById('loadingMessage');
                if (messageElement) {
                    messageElement.style.opacity = '0';
                    setTimeout(() => {
                        messageElement.textContent = messages[currentStep];
                        messageElement.style.opacity = '1';
                    }, 200);
                    currentStep++;
                }
            }
        }, 400); // Intervalo más lento para efecto ultra-suave
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
            
            // Simular progreso de navegación ultra-suave
            this.simulateNavigationProgress();
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
    
    simulateNavigationProgress() {
        const progressBar = document.getElementById('navigationProgressBar');
        let progress = 0;
        
        const interval = setInterval(() => {
            progress += Math.random() * 15 + 10; // Progreso más suave
            
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                
                // Ocultar después de completar
                setTimeout(() => {
                    this.hideNavigationLoading();
                }, 300);
            }
            
            if (progressBar) {
                progressBar.style.width = `${progress}%`;
            }
        }, 150); // Intervalo más lento
    }
    
    interceptNavigation() {
        // Interceptar clicks en enlaces internos
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && this.shouldShowNavigationLoading(link)) {
                e.preventDefault();
                
                const href = link.getAttribute('href');
                const message = link.getAttribute('data-loading-message') || 'Navegando...';
                
                this.showNavigationLoading(message);
                
                // Simular tiempo de navegación
                setTimeout(() => {
                    window.location.href = href;
                }, 1000);
            }
        });
        
        // Interceptar navegación programática
        const originalPushState = history.pushState;
        const originalReplaceState = history.replaceState;
        
        history.pushState = function(...args) {
            originalPushState.apply(history, args);
            if (!window.loadingManager.isInitialLoad) {
                window.loadingManager.showNavigationLoading('Actualizando página...');
                setTimeout(() => window.loadingManager.hideNavigationLoading(), 800);
            }
        };
        
        history.replaceState = function(...args) {
            originalReplaceState.apply(history, args);
            if (!window.loadingManager.isInitialLoad) {
                window.loadingManager.showNavigationLoading('Actualizando página...');
                setTimeout(() => window.loadingManager.hideNavigationLoading(), 800);
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
    
    interceptPageReload() {
        window.addEventListener('beforeunload', () => {
            if (!this.isInitialLoad) {
                this.showNavigationLoading('Recargando página...');
            }
        });
    }
}

// Inicializar el sistema de carga cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.loadingManager = new PageLoadingManager();
});

// Función global para mostrar loading de navegación manualmente
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

// Función para mostrar loading en enlaces específicos
function addNavigationLoadingToLink(selector, message = 'Navegando...') {
    const links = document.querySelectorAll(selector);
    links.forEach(link => {
        link.setAttribute('data-loading-message', message);
    });
}

// Función para excluir enlaces del loading automático
function excludeLinkFromLoading(selector) {
    const links = document.querySelectorAll(selector);
    links.forEach(link => {
        link.setAttribute('data-no-loading', 'true');
    });
}
</script>

<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/components/loading-screen.blade.php ENDPATH**/ ?>