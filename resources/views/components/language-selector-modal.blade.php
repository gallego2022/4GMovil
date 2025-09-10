<!-- Modal de selección de idioma/país/moneda -->
<div id="languageSelectorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ __('messages.language_selector.title') }}
            </h3>
            <button type="button" id="closeLanguageModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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

            <form id="languageConfigForm">
                @csrf
                
                <!-- Región -->
                <div class="mb-4">
                    <label for="country_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('messages.language_selector.region') }}
                    </label>
                    <select id="country_code" name="country_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="CO">🇨🇴 Colombia</option>
                        <option value="MX">🇲🇽 México</option>
                        <option value="AR">🇦🇷 Argentina</option>
                        <option value="CL">🇨🇱 Chile</option>
                        <option value="PE">🇵🇪 Perú</option>
                        <option value="VE">🇻🇪 Venezuela</option>
                        <option value="EC">🇪🇨 Ecuador</option>
                        <option value="BO">🇧🇴 Bolivia</option>
                        <option value="UY">🇺🇾 Uruguay</option>
                        <option value="PY">🇵🇾 Paraguay</option>
                        <option value="ES">🇪🇸 España</option>
                        <option value="US">🇺🇸 Estados Unidos</option>
                    </select>
                </div>

                <!-- Idioma -->
                <div class="mb-4">
                    <label for="language_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('messages.language_selector.language') }}
                    </label>
                    <select id="language_code" name="language_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="es">Español Latinoamericano</option>
                        <option value="es-ES">Español (España)</option>
                        <option value="en">English</option>
                        <option value="pt">Português</option>
                    </select>
                </div>

                <!-- Moneda -->
                <div class="mb-6">
                    <label for="currency_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('messages.language_selector.currency') }}
                    </label>
                    <select id="currency_code" name="currency_code" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="COP">Peso colombiano (COP)</option>
                        <option value="USD">Dólar estadounidense (USD)</option>
                        <option value="BRL">Real brasileño (BRL)</option>
                        <option value="EUR">Euro (EUR)</option>
                        <option value="MXN">Peso mexicano (MXN)</option>
                        <option value="ARS">Peso argentino (ARS)</option>
                        <option value="CLP">Peso chileno (CLP)</option>
                        <option value="PEN">Sol peruano (PEN)</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="flex space-x-3">
                    <button type="button" id="cancelLanguageConfig" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        {{ __('messages.language_selector.cancel') }}
                    </button>
                    <button type="submit" id="saveLanguageConfig" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        {{ __('messages.language_selector.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Botón para abrir el modal -->
<button id="openLanguageModal" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
    </svg>
    <span class="text-sm font-medium" id="currentLanguageDisplay">Español Latinoamericano | COP</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('languageSelectorModal');
    const openBtn = document.getElementById('openLanguageModal');
    const closeBtn = document.getElementById('closeLanguageModal');
    const cancelBtn = document.getElementById('cancelLanguageConfig');
    const form = document.getElementById('languageConfigForm');
    const currentDisplay = document.getElementById('currentLanguageDisplay');

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
                    document.getElementById('country_code').value = data.config.country_code || 'CO';
                    document.getElementById('language_code').value = data.config.language_code || 'es';
                    document.getElementById('currency_code').value = data.config.currency_code || 'COP';
                    updateCurrentDisplay(data.config);
                }
            })
            .catch(error => {
                console.error('Error loading config:', error);
            });
    }

    // Actualizar display actual
    function updateCurrentDisplay(config) {
        const countryNames = {
            'CO': 'Colombia', 'MX': 'México', 'AR': 'Argentina', 'CL': 'Chile',
            'PE': 'Perú', 'VE': 'Venezuela', 'EC': 'Ecuador', 'BO': 'Bolivia',
            'UY': 'Uruguay', 'PY': 'Paraguay', 'ES': 'España', 'US': 'Estados Unidos'
        };
        
        const languageNames = {
            'es': 'Español Latinoamericano', 'es-ES': 'Español (España)',
            'en': 'English', 'pt': 'Português'
        };

        const countryName = countryNames[config.country_code] || 'Colombia';
        const languageName = languageNames[config.language_code] || 'Español Latinoamericano';
        
        currentDisplay.textContent = `${languageName} | ${config.currency_code}`;
    }

    // Enviar formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const languageCode = document.getElementById('language_code').value;
        const countryCode = document.getElementById('country_code').value;
        const currencyCode = document.getElementById('currency_code').value;
        
        console.log('Cambiando idioma a:', languageCode); // Debug
        
        // Mostrar mensaje de carga
        showNotification('Cambiando idioma...', 'info');
        
        // Usar la ruta simple de cambio de idioma
        window.location.href = `/change-lang/${languageCode}`;
    });

    // Función para mostrar notificaciones
    function showNotification(message, type) {
        // Crear elemento de notificación
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
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Cargar configuración inicial
    loadCurrentConfig();
});
</script>
