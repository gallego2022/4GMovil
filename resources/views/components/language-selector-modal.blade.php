@php($modalId = uniqid('langModal_'))
<!-- Modal de selección de idioma/país/moneda -->
<div id="{{ $modalId }}" class="language-selector-modal fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('messages.language_selector.title') }}
            </h3>
            <button type="button" class="close-language-modal text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                {{ __('messages.language_selector.subtitle') }}
            </p>

            <form class="language-config-form">
                @csrf
                
                

                <!-- Idioma -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('messages.language_selector.language') }}
                    </label>
                    <select name="language_code" class="language-code-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="es">Español</option>
                        <option value="en">English</option>
                        <option value="pt">Português</option>
                    </select>
                </div>

                <!-- Moneda -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('messages.language_selector.currency') }}
                    </label>
                    <select name="currency_code" class="currency-code-select w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="COP">Peso colombiano (COP)</option>
                        <option value="USD">Dólar estadounidense (USD)</option>
                        <option value="BRL">Real brasileño (BRL)</option>
                        <option value="EUR">Euro (EUR)</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="flex space-x-3">
                    <button type="button" class="cancel-language-config flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        {{ __('messages.language_selector.cancel') }}
                    </button>
                    <button type="submit" class="save-language-config flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('messages.language_selector.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Botón para abrir el modal -->
<button class="open-language-modal flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
    </svg>
    <span class="text-sm font-medium current-language-display">Español Latinoamericano | COP</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.language-selector-modal');
    modals.forEach(function(modal) {
        const openBtn = modal.parentElement.querySelector('.open-language-modal');
        const closeBtn = modal.querySelector('.close-language-modal');
        const cancelBtn = modal.querySelector('.cancel-language-config');
        const form = modal.querySelector('.language-config-form');
        const currentDisplay = modal.parentElement.querySelector('.current-language-display');

        if (!openBtn || !closeBtn || !cancelBtn || !form || !currentDisplay) return;
        
        // Abrir modal
        openBtn.addEventListener('click', function() {
            loadCurrentConfig();
            modal.classList.remove('hidden');
        });

        // Cerrar modal
        function closeModal() {
            modal.classList.add('hidden');
        }

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Cerrar al hacer clic fuera del modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });

        // Cargar configuración actual
        function loadCurrentConfig() {
            fetch('/localization/current')
                .then(response => response.json())
                .then(data => {
                    if (data.config) {
                        modal.querySelector('.language-code-select').value = data.config.language_code || 'es';
                        modal.querySelector('.currency-code-select').value = data.config.currency_code || 'COP';
                        updateCurrentDisplay(data.config);
                    }
                })
                .catch(error => {
                    console.error('Error loading config:', error);
                });
        }

        // Actualizar display actual
        function updateCurrentDisplay(config) {
            const languageNames = {
                'es': 'Español', 'es-ES': 'Español (España)',
                'en': 'English', 'pt': 'Português'
            };

            const languageName = languageNames[config.language_code] || 'Español';
            currentDisplay.textContent = `${languageName} | ${config.currency_code}`;
        }

        // Función para mostrar notificaciones
        function showNotification(message, type) {
            const notification = document.createElement('div');
            let bgColor = 'bg-gray-500';
            
            if (type === 'success') {
                bgColor = 'bg-green-500';
            } else if (type === 'error') {
                bgColor = 'bg-red-500';
            } else if (type === 'info') {
                bgColor = 'bg-blue-500';
            }
            
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${bgColor} text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Enviar formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const languageCode = modal.querySelector('.language-code-select').value;
            const currencyCode = modal.querySelector('.currency-code-select').value;
            
            console.log('Cambiando idioma a:', languageCode);
            
            showNotification('Cambiando idioma...', 'info');
            
            window.location.href = `/change-lang/${languageCode}`;
        });

        // Cargar configuración inicial
        loadCurrentConfig();
    });
});
</script>
