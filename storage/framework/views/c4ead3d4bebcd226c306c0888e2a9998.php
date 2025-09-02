<!-- Modal para selección de variantes -->
<div id="variantModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <!-- Header del modal -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Seleccionar Variante</h3>
            <button type="button" class="close-modal text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Contenido del modal -->
        <div class="p-6">
            <!-- Información del producto -->
            <div class="mb-6">
                <h4 class="font-semibold text-lg text-gray-800 mb-2" id="productName"></h4>
                <p class="text-gray-600 text-sm" id="productPrice"></p>
            </div>

            <!-- Lista de variantes -->
            <div id="variantsList" class="space-y-3">
                <!-- Las variantes se cargarán dinámicamente aquí -->
            </div>

            <!-- Mensaje cuando no hay variantes -->
            <div id="noVariantsMessage" class="hidden text-center py-8">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-3"></i>
                <p class="text-gray-600">No hay variantes disponibles para este producto.</p>
            </div>

            <!-- Loading spinner -->
            <div id="loadingVariants" class="hidden text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-3"></div>
                <p class="text-gray-600">Cargando variantes...</p>
            </div>
        </div>

        <!-- Footer del modal -->
        <div class="flex items-center justify-end p-6 border-t border-gray-200 space-x-3">
            <button type="button" class="close-modal px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                Cancelar
            </button>
        </div>
    </div>
</div>

<!-- Template para variante individual -->
<template id="variantTemplate">
    <div class="variant-item border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors cursor-pointer">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Color preview -->
                <div class="color-preview w-8 h-8 rounded-full border-2 border-gray-200 flex-shrink-0"></div>
                
                <!-- Información de la variante -->
                <div class="flex-1">
                    <h5 class="font-semibold text-gray-800 variant-name"></h5>
                    <p class="text-sm text-gray-600 variant-description"></p>
                </div>
            </div>
            
            <!-- Precio y stock -->
            <div class="text-right">
                <div class="font-bold text-blue-600 variant-price"></div>
                <div class="text-sm variant-stock"></div>
            </div>
        </div>
        
        <!-- Botón de agregar -->
        <div class="mt-3">
            <button type="button" class="add-variant-to-cart w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm">
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
}

.variant-item:not(.selected):hover {
    border-color: #93C5FD;
    background-color: #F8FAFC;
}
</style>
<?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/components/variant-selection-modal.blade.php ENDPATH**/ ?>