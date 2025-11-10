/**
 * Sistema de confirmación modal reutilizable
 * 
 * Uso:
 * 1. Agregar la clase 'confirm-action' al formulario
 * 2. Agregar atributos data-* para personalizar el modal:
 *    - data-modal-id: ID del modal (opcional, por defecto 'confirmModal')
 *    - data-title: Título del modal
 *    - data-message: Mensaje del modal
 *    - data-confirm-text: Texto del botón de confirmar (opcional)
 *    - data-cancel-text: Texto del botón de cancelar (opcional)
 *    - data-confirm-color: Color del modal (red, blue, yellow, etc.) (opcional)
 *    - data-show-warning: Mostrar advertencia (true/false) (opcional)
 *    - data-method: Método HTTP (POST, DELETE, etc.) (opcional)
 * 
 * Ejemplo:
 * <form action="/delete" method="POST" class="confirm-action" 
 *       data-title="¿Eliminar?" 
 *       data-message="¿Estás seguro de eliminar este elemento?"
 *       data-method="DELETE">
 *   @csrf
 *   <button type="submit">Eliminar</button>
 * </form>
 */

// Ejecutar inmediatamente cuando el script se carga
(function() {
    'use strict';
    
    // Función para inicializar el sistema de confirmación
    function initConfirmModal() {
        // Inicializar todos los formularios con confirmación
        const confirmForms = document.querySelectorAll('.confirm-action');
        
        confirmForms.forEach((form) => {
            const newHandler = function(e) {
                // Verificar nuevamente que el formulario tenga la clase
                if (!form.classList.contains('confirm-action')) {
                    return;
                }
                
                // Prevenir el envío inmediato
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const modalId = form.dataset.modalId || 'confirmModal';
                const title = form.dataset.title || '¿Confirmar acción?';
                const message = form.dataset.message || '¿Estás seguro de realizar esta acción?';
                const confirmText = form.dataset.confirmText || 'Sí, confirmar';
                const cancelText = form.dataset.cancelText || 'Cancelar';
                const confirmColor = form.dataset.confirmColor || 'red';
                const showWarning = form.dataset.showWarning !== 'false';
                const method = form.dataset.method || form.method || 'POST';
                
                // Crear o actualizar el modal
                let modal = document.getElementById(modalId);
                
                if (!modal) {
                    modal = createModal(modalId, title, message, confirmText, cancelText, confirmColor, showWarning);
                    document.body.appendChild(modal);
                } else {
                    updateModal(modal, title, message, confirmText, cancelText, confirmColor, showWarning);
                }
                
                // Configurar el formulario de confirmación
                const confirmForm = document.getElementById(modalId + '_form');
                if (!confirmForm) {
                    return;
                }
                
                const confirmBtn = confirmForm.querySelector('button[type="submit"]');
                const cancelBtn = document.getElementById(modalId + '_cancel');
                const messageEl = document.getElementById(modalId + '_message');
                
                // Actualizar action y method
                confirmForm.action = form.action;
                confirmForm.method = form.method || 'POST';
                
                // Copiar el método si es DELETE, PUT, PATCH
                if (method === 'DELETE' || method === 'PUT' || method === 'PATCH') {
                    let methodInput = confirmForm.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        confirmForm.appendChild(methodInput);
                    }
                    methodInput.value = method;
                }
                
                // Limpiar inputs previos (excepto _method)
                const existingInputs = confirmForm.querySelectorAll('input[type="hidden"]:not([name="_method"])');
                existingInputs.forEach(input => input.remove());
                
                // Copiar todos los inputs del formulario original
                const originalInputs = form.querySelectorAll('input[type="hidden"]');
                originalInputs.forEach(input => {
                    if (input.name === '_method') {
                        return; // Ya lo manejamos arriba
                    }
                    const newInput = input.cloneNode(true);
                    confirmForm.appendChild(newInput);
                });
                
                // Actualizar mensaje si hay data adicional
                if (form.dataset.details && messageEl) {
                    messageEl.innerHTML = message + '<br><strong class="text-gray-900 dark:text-white">' + form.dataset.details + '</strong>';
                } else if (messageEl) {
                    messageEl.textContent = message;
                }
                
                // Mostrar el modal
                modal.classList.remove('hidden');
                
                // Configurar eventos - remover listeners previos primero
                if (confirmBtn) {
                    // Remover cualquier listener previo clonando el botón
                    const newConfirmBtn = confirmBtn.cloneNode(true);
                    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                    
                    // Agregar el nuevo listener
                    newConfirmBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        
                        // Ocultar el modal primero
                        modal.classList.add('hidden');
                        
                        // Pequeño delay para que el modal se oculte antes de enviar
                        setTimeout(function() {
                            // Enviar el formulario del modal (que tiene todos los datos copiados)
                            if (confirmForm && confirmForm.action) {
                                confirmForm.submit();
                            }
                        }, 100);
                    }, { once: false });
                }
                
                if (cancelBtn) {
                    // Remover listener previo clonando el botón
                    const newCancelBtn = cancelBtn.cloneNode(true);
                    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
                    
                    newCancelBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        modal.classList.add('hidden');
                    }, { once: false });
                }
                
                // Cerrar al hacer clic fuera - remover listener previo
                const newModalClickHandler = function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                    }
                };
                
                // Remover listener previo si existe
                modal.removeEventListener('click', modal._clickHandler);
                modal._clickHandler = newModalClickHandler;
                modal.addEventListener('click', newModalClickHandler);
            };
            
            // Agregar el listener con capture: true para que se ejecute primero
            form.addEventListener('submit', newHandler, { capture: true, once: false });
        });
    }
    
    // Ejecutar inmediatamente
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConfirmModal);
    } else {
        // DOM ya está cargado, ejecutar inmediatamente
        initConfirmModal();
    }
})();

function createModal(id, title, message, confirmText, cancelText, confirmColor, showWarning) {
    const modal = document.createElement('div');
    modal.id = id;
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden';
    
    const warningText = showWarning ? '<p class="text-sm text-' + confirmColor + '-600 dark:text-' + confirmColor + '-400 mt-2 font-medium">Esta acción no se puede deshacer.</p>' : '';
    
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-${confirmColor}-100 dark:bg-${confirmColor}-900/30">
                    <svg class="h-6 w-6 text-${confirmColor}-600 dark:text-${confirmColor}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-5">
                    ${title}
                </h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 dark:text-gray-300" id="${id}_message">
                        ${message}
                    </p>
                    ${warningText}
                </div>
                <div class="items-center px-4 py-3">
                    <form id="${id}_form" method="POST" class="inline">
                        <button type="submit" 
                                class="px-4 py-2 bg-${confirmColor}-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-${confirmColor}-700 focus:outline-none focus:ring-2 focus:ring-${confirmColor}-500 transition-colors duration-200">
                            ${confirmText}
                        </button>
                    </form>
                    <button id="${id}_cancel" 
                            class="mt-2 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors duration-200">
                        ${cancelText}
                    </button>
                </div>
            </div>
        </div>
    `;
    
    return modal;
}

function updateModal(modal, title, message, confirmText, cancelText, confirmColor, showWarning) {
    const titleEl = modal.querySelector('h3');
    const messageEl = modal.querySelector(`#${modal.id}_message`);
    const confirmBtn = modal.querySelector('form button[type="submit"]');
    const cancelBtn = modal.querySelector(`#${modal.id}_cancel`);
    
    if (titleEl) titleEl.textContent = title;
    if (messageEl) messageEl.textContent = message;
    if (confirmBtn) confirmBtn.textContent = confirmText;
    if (cancelBtn) cancelBtn.textContent = cancelText;
}
