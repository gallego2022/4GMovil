<!-- Script debe ejecutarse primero para registrar confirmModal -->
<script>
(function() {
    'use strict';
    
    // Definir la función confirmModal inmediatamente
    function confirmModal() {
        return {
            show: false,
            title: '¿Confirmar acción?',
            message: '¿Estás seguro de realizar esta acción?',
            confirmText: 'Sí, confirmar',
            cancelText: 'Cancelar',
            color: 'red',
            showWarning: true,
            warningText: 'Esta acción no se puede deshacer.',
            onConfirm: null,
            form: null,
            
            open(data) {
                console.log('confirmModal.open llamado con:', data);
                if (data) {
                    this.title = data.title || '¿Confirmar acción?';
                    this.message = data.message || '¿Estás seguro de realizar esta acción?';
                    this.confirmText = data.confirmText || 'Sí, confirmar';
                    this.cancelText = data.cancelText || 'Cancelar';
                    this.color = data.color || 'red';
                    this.showWarning = data.showWarning !== false;
                    this.warningText = data.warningText || 'Esta acción no se puede deshacer.';
                    this.onConfirm = data.onConfirm || null;
                    this.form = data.form || null;
                }
                console.log('Estableciendo show = true');
                this.show = true;
                document.body.style.overflow = 'hidden';
                console.log('Modal debería estar visible ahora, show =', this.show);
            },
            
            close() {
                this.show = false;
                document.body.style.overflow = '';
            },
            
            confirm() {
                if (this.onConfirm && typeof this.onConfirm === 'function') {
                    this.onConfirm();
                } else if (this.form) {
                    const form = this.form;
                    const formClone = form.cloneNode(true);
                    formClone.style.display = 'none';
                    document.body.appendChild(formClone);
                    formClone.submit();
                }
                this.close();
            }
        };
    }
    
    // Registrar inmediatamente para que Alpine.js pueda encontrarlo
    window.confirmModal = confirmModal;
    
    // También registrar con Alpine.data si Alpine está disponible
    if (window.Alpine && typeof window.Alpine.data === 'function') {
        window.Alpine.data('confirmModal', confirmModal);
    }
    
    // Función global para mostrar el modal de confirmación
    window.showConfirmModal = function(data) {
        console.log('showConfirmModal llamado con:', data);
        
        // Intentar usar la referencia global del componente
        if (window.$confirmModal && typeof window.$confirmModal.open === 'function') {
            console.log('Abriendo modal usando referencia global');
            window.$confirmModal.open(data);
            return;
        }
        
        // Intentar encontrar el componente Alpine directamente
        const modalElement = document.querySelector('[x-data*="confirmModal"]');
        if (modalElement && window.Alpine) {
            try {
                const alpineData = window.Alpine.$data(modalElement);
                if (alpineData && typeof alpineData.open === 'function') {
                    console.log('Abriendo modal directamente desde Alpine');
                    alpineData.open(data);
                    return;
                }
            } catch (e) {
                console.error('Error accediendo a Alpine data:', e);
            }
        }
        
        // Fallback: usar evento
        console.log('Usando evento como fallback');
        const event = new CustomEvent('show-confirm-modal', {
            detail: data || {}
        });
        window.dispatchEvent(event);
        console.log('Evento show-confirm-modal disparado');
    };
    
    // Verificar que todo esté listo
    console.log('confirmModal registrado:', typeof window.confirmModal);
    console.log('showConfirmModal registrado:', typeof window.showConfirmModal);
})();
</script>

<!-- Modal de confirmación personalizado usando Alpine.js -->
<div x-data="confirmModal()" 
     x-ref="confirmModal"
     x-show="show" 
     x-cloak
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[9999]"
     @click.self="close()"
     @show-confirm-modal.window="console.log('Evento recibido en Alpine:', $event.detail); open($event.detail)"
     x-init="console.log('Modal Alpine inicializado, show =', show); window.$confirmModal = $data"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800"
         @click.stop
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <div class="mt-3 text-center">
            <!-- Ícono -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full"
                 :class="{
                     'bg-red-100 dark:bg-red-900/30': color === 'red',
                     'bg-blue-100 dark:bg-blue-900/30': color === 'blue',
                     'bg-yellow-100 dark:bg-yellow-900/30': color === 'yellow',
                     'bg-green-100 dark:bg-green-900/30': color === 'green'
                 }">
                <svg class="h-6 w-6"
                     :class="{
                         'text-red-600 dark:text-red-400': color === 'red',
                         'text-blue-600 dark:text-blue-400': color === 'blue',
                         'text-yellow-600 dark:text-yellow-400': color === 'yellow',
                         'text-green-600 dark:text-green-400': color === 'green'
                     }"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <!-- Título -->
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-5" x-text="title"></h3>
            
            <!-- Mensaje -->
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-300" x-html="message"></p>
                <p x-show="showWarning" 
                   class="text-sm mt-2 font-medium"
                   :class="{
                       'text-red-600 dark:text-red-400': color === 'red',
                       'text-blue-600 dark:text-blue-400': color === 'blue',
                       'text-yellow-600 dark:text-yellow-400': color === 'yellow',
                       'text-green-600 dark:text-green-400': color === 'green'
                   }"
                   x-text="warningText"></p>
            </div>
            
            <!-- Botones -->
            <div class="items-center px-4 py-3">
                <button type="button"
                        @click="confirm()"
                        class="px-4 py-2 text-base font-medium rounded-md w-full shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200"
                        :class="{
                            'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white': color === 'red',
                            'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 text-white': color === 'blue',
                            'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500 text-white': color === 'yellow',
                            'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white': color === 'green'
                        }"
                        x-text="confirmText">
                </button>
                <button type="button"
                        @click="close()"
                        class="mt-2 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200"
                        x-text="cancelText">
                </button>
            </div>
        </div>
    </div>
</div>

