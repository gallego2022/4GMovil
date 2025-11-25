/**
 * Sistema de Sincronización de Stock
 * Maneja la sincronización entre variantes de productos y indicadores de stock
 */

class StockSync {
    constructor() {
        this.currentStock = null;
        this.isVariantSelected = false;
        this.activeProductId = null;
        this.init();
    }

    init() {
        console.log('StockSync inicializado');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Escuchar cambios en selección de variantes (botones de color)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('color-variant')) {
                this.handleVariantSelection(e.target);
            }
        });

        // Escuchar cierre de modales (para otros sistemas de modales)
        document.addEventListener('hidden.bs.modal', (e) => {
            if (e.target.id === 'variantSelectionModal') {
                this.handleModalClose();
            }
        });
    }

    handleVariantSelection(variantElement) {
        const stockValue = variantElement.dataset.variantStock ?? variantElement.dataset.stock;
        const stock = parseInt(stockValue) || 0;
        const productId = variantElement.dataset.productId || null;
        console.log('Variante seleccionada, stock:', stock, 'producto:', productId);
        
        this.currentStock = stock;
        this.isVariantSelected = true;
        this.activeProductId = productId;
        this.updateAllStockIndicators(stock, productId);
    }

    handleModalClose() {
        // Siempre restaurar stock original al cerrar modal
        this.restoreOriginalStock(this.activeProductId);
        this.activeProductId = null;
    }

    updateAllStockIndicators(stock, productId = null) {
        console.log('Actualizando indicadores de stock:', stock, 'para producto:', productId);
        
        const selector = productId ? `.stock-status[data-product-id="${productId}"]` : '.stock-status';
        let stockIndicators = document.querySelectorAll(selector);

        // Fallback: si no se encontró el producto específico, actualizar todos (caso legacy)
        if (stockIndicators.length === 0 && productId) {
            stockIndicators = document.querySelectorAll('.stock-status');
        }
        
        stockIndicators.forEach(indicator => {
            this.updateStockIndicator(indicator, stock);
        });

        const stockElementsSelector = productId ? `[data-stock][data-product-id="${productId}"]` : '[data-stock]';
        const stockElements = document.querySelectorAll(stockElementsSelector);
        stockElements.forEach(element => {
            element.textContent = stock > 0 ? `${stock} disponibles` : 'Agotado';
        });
    }

    updateStockIndicator(indicator, stock) {
        // Actualizar texto
        const stockText = indicator.querySelector('.stock-text');
        if (stockText) {
            this.updateStockText(stockText, stock);
        }

        // Actualizar punto de color
        const stockDot = indicator.querySelector('.stock-dot');
        if (stockDot) {
            this.updateStockDot(stockDot, stock);
        }

        // Actualizar barra de progreso
        const progressBar = indicator.querySelector('.stock-progress');
        if (progressBar) {
            const totalStock = parseInt(indicator.dataset.totalStock) || 0;
            this.updateProgressBar(progressBar, stock, totalStock);
        }
    }

    updateStockText(element, stock) {
        if (stock > 10) {
            element.textContent = `${stock} disponibles`;
            element.className = element.className.replace(/text-(red|yellow|gray)-600/, 'text-green-600');
        } else if (stock > 5) {
            element.textContent = `${stock} disponibles`;
            element.className = element.className.replace(/text-(red|green|gray)-600/, 'text-yellow-600');
        } else if (stock > 0) {
            element.textContent = `Solo ${stock} disponibles`;
            element.className = element.className.replace(/text-(green|yellow|gray)-600/, 'text-red-600');
        } else {
            element.textContent = 'Agotado';
            element.className = element.className.replace(/text-(red|yellow|green)-600/, 'text-gray-500');
        }
    }

    updateStockDot(element, stock) {
        element.className = element.className.replace(/bg-(red|yellow|green|gray)-500/, '');
        
        if (stock > 10) {
            element.classList.add('bg-green-500');
        } else if (stock > 5) {
            element.classList.add('bg-yellow-500');
        } else if (stock > 0) {
            element.classList.add('bg-red-500');
        } else {
            element.classList.add('bg-gray-400');
        }
    }

    updateProgressBar(element, stock, totalStock) {
        const percentage = totalStock > 0 ? (stock / totalStock) * 100 : 0;
        element.style.width = `${percentage}%`;
        
        element.className = element.className.replace(/bg-(red|yellow|green)-500/, '');
        
        if (stock > 10) {
            element.classList.add('bg-green-500');
        } else if (stock > 5) {
            element.classList.add('bg-yellow-500');
        } else {
            element.classList.add('bg-red-500');
        }
    }

    restoreOriginalStock(productId = null) {
        console.log('Restaurando stock original para producto:', productId);
        this.isVariantSelected = false;
        
        const selector = productId ? `.stock-status[data-product-id="${productId}"]` : '.stock-status';
        const indicators = document.querySelectorAll(selector);
        if (indicators.length === 0) {
            return;
        }

        indicators.forEach(indicator => {
            const originalStock = parseInt(indicator.dataset.originalStock) || 0;
            this.updateStockIndicator(indicator, originalStock);
        });
    }

    // Método público para actualizar stock desde código externo
    updateStock(stock, productId = null) {
        this.currentStock = stock;
        this.isVariantSelected = true;
        this.activeProductId = productId ?? this.activeProductId;
        this.updateAllStockIndicators(stock, this.activeProductId);
    }

    // Método público para restaurar stock original
    resetStock(productId = null) {
        this.restoreOriginalStock(productId ?? this.activeProductId);
    }

    // Método para actualización temporal (hover)
    updateStockTemporary(stock, productId = null) {
        this.currentStock = stock;
        this.updateAllStockIndicators(stock, productId ?? this.activeProductId);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.stockSync = new StockSync();
});

// Exportar para uso global
window.StockSync = StockSync;
