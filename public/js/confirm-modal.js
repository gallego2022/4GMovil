/**
 * Sistema de confirmación modal para formularios con clase 'confirm-action'
 * Usa el componente de modal personalizado con Alpine.js
 */

(function() {
    'use strict';
    
    // Conjunto para rastrear formularios que ya tienen listeners
    const processedForms = new WeakSet();
    
    // Función para inicializar los listeners de confirmación
    function initConfirmActions() {
        // Obtener todos los formularios con la clase 'confirm-action'
        const confirmForms = document.querySelectorAll('form.confirm-action');
        
        confirmForms.forEach(form => {
            // Evitar procesar el mismo formulario múltiples veces
            if (processedForms.has(form)) {
                return;
            }
            
            // Marcar el formulario como procesado
            processedForms.add(form);
            
            // Agregar listener solo una vez
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Obtener los datos del formulario desde los atributos data-*
                const title = form.getAttribute('data-title') || '¿Confirmar acción?';
                const message = form.getAttribute('data-message') || '¿Estás seguro de realizar esta acción?';
                const confirmText = form.getAttribute('data-confirm-text') || 'Sí, confirmar';
                const cancelText = form.getAttribute('data-cancel-text') || 'Cancelar';
                const confirmColor = form.getAttribute('data-confirm-color') || 'red';
                const showWarning = form.getAttribute('data-show-warning') !== 'false';
                
                // Función para enviar el formulario cuando se confirme
                const submitForm = function() {
                    // Crear un nuevo formulario sin el listener para evitar bucle infinito
                    const formToSubmit = form.cloneNode(true);
                    formToSubmit.style.display = 'none';
                    document.body.appendChild(formToSubmit);
                    formToSubmit.submit();
                };
                
                // Mostrar el modal de confirmación personalizado
                if (typeof window.showConfirmModal === 'function') {
                    window.showConfirmModal({
                        title: title,
                        message: message,
                        confirmText: confirmText,
                        cancelText: cancelText,
                        color: confirmColor,
                        showWarning: showWarning,
                        onConfirm: submitForm,
                        form: form
                    });
                } else {
                    // Fallback si el modal no está disponible
                    if (confirm(message)) {
                        submitForm();
                    }
                }
            }, { once: false });
        });
    }
    
    // Esperar a que Alpine.js esté listo con límite de intentos
    function waitForAlpine(callback) {
        let attempts = 0;
        const maxAttempts = 100; // Máximo 5 segundos (100 * 50ms)
        
        function checkAlpine() {
            attempts++;
            
            if (window.Alpine && window.Alpine.version) {
                // Alpine está completamente cargado
                setTimeout(callback, 200);
            } else if (attempts < maxAttempts) {
                // Seguir esperando
                setTimeout(checkAlpine, 50);
            } else {
                // Timeout: ejecutar de todas formas (fallback)
                console.warn('Alpine.js no se cargó a tiempo, inicializando confirmaciones sin Alpine');
                callback();
            }
        }
        
        checkAlpine();
    }
    
    // Inicializar cuando el DOM esté listo
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                waitForAlpine(initConfirmActions);
            });
        } else {
            waitForAlpine(initConfirmActions);
        }
    }
    
    // Inicializar
    init();
    
    // También inicializar para elementos agregados dinámicamente
    // Usar un debounce para evitar múltiples llamadas
    let observerTimeout = null;
    const observer = new MutationObserver(function(mutations) {
        // Limpiar timeout anterior
        if (observerTimeout) {
            clearTimeout(observerTimeout);
        }
        
        // Esperar un poco antes de procesar para evitar múltiples llamadas
        observerTimeout = setTimeout(function() {
            const hasNewForms = Array.from(mutations).some(mutation => {
                return Array.from(mutation.addedNodes).some(node => {
                    if (node.nodeType !== 1) return false;
                    return node.classList?.contains('confirm-action') ||
                           (node.tagName === 'FORM' && node.classList?.contains('confirm-action')) ||
                           node.querySelector?.('form.confirm-action');
                });
            });
            
            if (hasNewForms) {
                initConfirmActions();
            }
        }, 100);
    });
    
    // Observar cambios en el DOM solo después de que el DOM esté listo
    function setupObserver() {
        if (document.body) {
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupObserver);
    } else {
        setupObserver();
    }
})();

