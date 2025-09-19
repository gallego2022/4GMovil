@props(['trigger' => 'button'])

<div x-data="localizationModal()" class="relative">
    <!-- Trigger Button -->
    @if($trigger === 'button')
        <button 
            @click="openModal()"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
            </svg>
            <span>{{ __('messages.language_selector.title') }}</span>
        </button>
    @else
        <div @click="openModal()" class="cursor-pointer">
            {{ $slot }}
        </div>
    @endif

    <!-- Modal -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="closeModal()"></div>
        
        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-xl"
            >
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('messages.language_selector.title') }}
                    </h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="saveConfig()" class="p-6 space-y-6">
                    

                    <!-- Idioma -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.language_selector.language') }}
                        </label>
                        <select 
                            x-model="config.language_code"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="es">🇨🇴 Español</option>
                            <option value="en">🇺🇸 English</option>
                            <option value="pt">🇧🇷 Português</option>
                        </select>
                    </div>

                    <!-- Moneda -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('messages.language_selector.currency') }}
                        </label>
                        <select 
                            x-model="config.currency_code"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="COP">Peso colombiano (COP)</option>
                            <option value="USD">Dólar estadounidense (USD)</option>
                            <option value="BRL">Real brasileño (BRL)</option>
                            <option value="EUR">Euro (EUR)</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button" 
                            @click="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200"
                        >
                            {{ __('messages.language_selector.cancel') }}
                        </button>
                        <button 
                            type="submit"
                            :disabled="loading"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors duration-200"
                        >
                            <span x-show="!loading">{{ __('messages.language_selector.save') }}</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Guardando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function localizationModal() {
    return {
        isOpen: false,
        loading: false,
        config: {
            country_code: 'CO',
            language_code: 'es',
            currency_code: 'COP'
        },

        async openModal() {
            this.isOpen = true;
            await this.loadCurrentConfig();
        },

        closeModal() {
            this.isOpen = false;
        },

        async loadCurrentConfig() {
            try {
                const response = await fetch('{{ route("localization.current") }}');
                const data = await response.json();
                
                if (data.config) {
                    this.config = {
                        language_code: data.config.language_code || 'es',
                        currency_code: data.config.currency_code || 'COP'
                    };
                }
            } catch (error) {
                console.error('Error loading config:', error);
            }
        },

        async saveConfig() {
            this.loading = true;
            
            try {
                const response = await fetch('{{ route("localization.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.config)
                });

                const data = await response.json();
                
                if (data.success) {
                    // Recargar la página para aplicar los cambios
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al guardar la configuración');
                }
            } catch (error) {
                console.error('Error saving config:', error);
                alert('Error al guardar la configuración');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
