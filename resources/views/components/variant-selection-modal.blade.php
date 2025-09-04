<!-- Modal para selección de variantes -->
<div id="variantModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-600">
        <!-- Header del modal -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-600">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white" id="modalTitle">Seleccionar Variante</h3>
            <button type="button" class="close-modal text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Contenido del modal -->
        <div class="p-6">
            <!-- Información del producto -->
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600">
                <h4 class="font-semibold text-lg text-gray-800 dark:text-white mb-2" id="productName"></h4>
                <p class="text-gray-600 dark:text-gray-400 text-sm" id="productPrice"></p>
            </div>

            <!-- Lista de variantes -->
            <div id="variantsList" class="space-y-3">
                <!-- Las variantes se cargarán dinámicamente aquí -->
            </div>

            <!-- Mensaje cuando no hay variantes -->
            <div id="noVariantsMessage" class="hidden text-center py-8">
                <i class="fas fa-exclamation-triangle text-yellow-500 dark:text-yellow-400 text-3xl mb-3"></i>
                <p class="text-gray-600 dark:text-gray-400">No hay variantes disponibles para este producto.</p>
            </div>

            <!-- Loading spinner -->
            <div id="loadingVariants" class="hidden text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400 mx-auto mb-3"></div>
                <p class="text-gray-600 dark:text-gray-400">Cargando variantes...</p>
            </div>
        </div>

        <!-- Footer del modal -->
        <div class="flex items-center justify-end p-6 border-t border-gray-200 dark:border-gray-600 space-x-3">
            <button type="button" class="close-modal px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors rounded-lg font-medium">
                Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Template para variante individual -->
<template id="variantTemplate">
    <div class="variant-item border border-gray-200 dark:border-gray-600 rounded-xl p-4 hover:border-blue-300 dark:hover:border-blue-400 transition-all duration-200 cursor-pointer bg-white dark:bg-gray-700 hover:shadow-md dark:hover:shadow-gray-900/20">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Color preview -->
                <div class="color-preview w-8 h-8 rounded-full border-2 border-gray-200 dark:border-gray-500 flex-shrink-0 shadow-sm"></div>
                
                <!-- Información de la variante -->
                <div class="flex-1">
                    <h5 class="font-semibold text-gray-800 dark:text-white variant-name"></h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400 variant-description"></p>
                </div>
            </div>
            
            <!-- Precio y stock -->
            <div class="text-right">
                <div class="font-bold text-blue-600 dark:text-blue-400 variant-price"></div>
                <div class="text-sm text-gray-500 dark:text-gray-400 variant-stock"></div>
            </div>
        </div>
        
        <!-- Botón de agregar -->
        <div class="mt-3">
            <button type="button" class="add-variant-to-cart w-full bg-blue-600 dark:bg-blue-500 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors font-medium text-sm shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                <i class="fas fa-shopping-cart mr-2"></i>
                Agregar al carrito
            </button>
        </div>
    </div>
</template>

<style>
.variant-item.selected {
    border-color: #3B82F6;
    background-color: #EFF6FF;
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1), 0 2px 4px -1px rgba(59, 130, 246, 0.06);
}

.dark .variant-item.selected {
    border-color: #60A5FA;
    background-color: rgba(59, 130, 246, 0.1);
    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2), 0 2px 4px -1px rgba(59, 130, 246, 0.1);
}

.variant-item:not(.selected):hover {
    border-color: #93C5FD;
    background-color: #F8FAFC;
    transform: translateY(-1px);
}

.dark .variant-item:not(.selected):hover {
    border-color: #93C5FD;
    background-color: rgba(59, 130, 246, 0.05);
    transform: translateY(-1px);
}

/* Animaciones suaves */
.variant-item {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Mejoras en el botón de cerrar */
.close-modal {
    transition: all 0.2s ease-in-out;
}

.close-modal:hover {
    transform: scale(1.05);
}

/* Efecto de backdrop mejorado */
#variantModal {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Scrollbar personalizado para el modal */
#variantModal .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

#variantModal .overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

#variantModal .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #CBD5E1;
    border-radius: 3px;
}

.dark #variantModal .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #475569;
}

#variantModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94A3B8;
}

.dark #variantModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #64748B;
}
</style>
