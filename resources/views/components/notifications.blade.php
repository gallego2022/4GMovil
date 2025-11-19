<!-- Contenedor de notificaciones -->
<div x-data="notificationManager()" x-init="init()" class="fixed top-4 right-4 z-[9999] space-y-2 max-w-sm w-full">
    <template x-for="(notification, index) in notifications" :key="index">
        <div 
            x-show="notification.show" 
            x-transition:enter="transform ease-out duration-300 transition" 
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0" 
            x-transition:leave="transition ease-in duration-100" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            @mouseenter="notification.autoClose = false"
            @mouseleave="notification.autoClose = true"
            class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden"
            :class="{
                'bg-green-50 dark:bg-green-900/20': notification.tipo === 'success',
                'bg-red-50 dark:bg-red-900/20': notification.tipo === 'error',
                'bg-yellow-50 dark:bg-yellow-900/20': notification.tipo === 'warning',
                'bg-blue-50 dark:bg-blue-900/20': notification.tipo === 'info'
            }">
            <!-- Barra de progreso para auto-cierre -->
            <div class="h-1 bg-gray-200 dark:bg-gray-700 overflow-hidden">
                <div class="h-full transition-all ease-linear" 
                     :class="{
                         'bg-green-500': notification.tipo === 'success',
                         'bg-red-500': notification.tipo === 'error',
                         'bg-yellow-500': notification.tipo === 'warning',
                         'bg-blue-500': notification.tipo === 'info'
                     }"
                     :style="`width: ${Math.max(0, Math.min(100, (notification.timeLeft / notification.duration) * 100))}%; transition-duration: 50ms;`"></div>
            </div>
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Ícono Success -->
                        <svg x-show="notification.tipo === 'success'" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <!-- Ícono Error -->
                        <svg x-show="notification.tipo === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <!-- Ícono Warning -->
                        <svg x-show="notification.tipo === 'warning'" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <!-- Ícono Info -->
                        <svg x-show="notification.tipo === 'info'" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium"
                           :class="{
                               'text-green-800 dark:text-green-200': notification.tipo === 'success',
                               'text-red-800 dark:text-red-200': notification.tipo === 'error',
                               'text-yellow-800 dark:text-yellow-200': notification.tipo === 'warning',
                               'text-blue-800 dark:text-blue-200': notification.tipo === 'info'
                           }"
                           x-text="notification.mensaje"></p>
                        <!-- Indicador de tiempo restante -->
                        <div class="mt-1 text-xs"
                             :class="{
                                 'text-green-600 dark:text-green-400': notification.tipo === 'success',
                                 'text-red-600 dark:text-red-400': notification.tipo === 'error',
                                 'text-yellow-600 dark:text-yellow-400': notification.tipo === 'warning',
                                 'text-blue-600 dark:text-blue-400': notification.tipo === 'info'
                             }">
                            <span x-show="notification.autoClose">Se cierra en <span x-text="Math.ceil(notification.timeLeft)"></span>s</span>
                            <span x-show="!notification.autoClose" class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Pausado
                            </span>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="removeNotification(index)" class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
// Variable global para el manager de notificaciones
let globalNotificationManager = null;
// Variable global para controlar si el listener ya fue agregado
let globalListenerAdded = false;
// Variable global para controlar si la notificación de sesión ya fue mostrada
let sessionNotificationShown = false;

function notificationManager() {
    return {
        notifications: [],
        
        init() {
            // Guardar referencia global solo si no existe otra
            if (!globalNotificationManager) {
                globalNotificationManager = this;
                
                // Cargar notificación de sesión si existe (solo una vez)
                @if(session('mensaje'))
                    if (!sessionNotificationShown) {
                        this.show('{{ session('mensaje') }}', '{{ session('tipo', 'success') }}');
                        sessionNotificationShown = true;
                    }
                @endif
            }
            
            // Escuchar eventos personalizados solo una vez globalmente
            if (!globalListenerAdded) {
                window.addEventListener('show-notification', (e) => {
                    if (globalNotificationManager) {
                        globalNotificationManager.show(e.detail.mensaje, e.detail.tipo || 'success', e.detail.duration || 5);
                    }
                });
                globalListenerAdded = true;
            }
        },
        
        show(mensaje, tipo = 'success', duration = 5) {
            const notificationIndex = this.notifications.length;
            const notification = {
                mensaje,
                tipo,
                duration,
                timeLeft: duration,
                autoClose: true,
                show: true,
                timer: null
            };
            
            this.notifications.push(notification);
            
            // Iniciar timer con reactividad de Alpine (actualización más fluida)
            const self = this;
            const updateInterval = 50; // Actualizar cada 50ms (20 veces por segundo) para mayor fluidez
            const decrementAmount = updateInterval / 1000; // 0.05 segundos por actualización
            
            notification.timer = setInterval(() => {
                const currentNotification = self.notifications[notificationIndex];
                if (!currentNotification) {
                    clearInterval(notification.timer);
                    return;
                }
                
                if (currentNotification.autoClose && currentNotification.timeLeft > 0) {
                    // Actualizar timeLeft de forma reactiva (actualizar cada 50ms para mayor fluidez)
                    const newTimeLeft = Math.max(0, currentNotification.timeLeft - decrementAmount);
                    // Reemplazar el objeto completo para forzar reactividad de Alpine
                    self.notifications[notificationIndex] = {
                        ...currentNotification,
                        timeLeft: newTimeLeft
                    };
                    
                    // Si llegó a 0, remover la notificación
                    if (newTimeLeft <= 0) {
                        clearInterval(notification.timer);
                        setTimeout(() => {
                            self.removeNotification(notificationIndex);
                        }, 50);
                    }
                } else if (currentNotification.timeLeft <= 0) {
                    clearInterval(notification.timer);
                    self.removeNotification(notificationIndex);
                }
            }, updateInterval);
        },
        
        removeNotification(index) {
            if (this.notifications[index] && this.notifications[index].timer) {
                clearInterval(this.notifications[index].timer);
            }
            this.notifications.splice(index, 1);
        }
    };
}

// Función global para mostrar notificaciones desde JavaScript
window.showNotification = function(mensaje, tipo = 'success', duration = 5) {
    // Si el manager está disponible, usarlo directamente para evitar duplicados
    if (globalNotificationManager) {
        globalNotificationManager.show(mensaje, tipo, duration);
    } else {
        // Si no está disponible aún, usar el evento
        window.dispatchEvent(new CustomEvent('show-notification', {
            detail: { mensaje, tipo, duration }
        }));
    }
};
</script>
