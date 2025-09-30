/**
 * Sistema de Sincronización de Stock
 * Maneja la sincronización entre variantes de productos y indicadores de stock
 */

class StockSync {
    constructor() {
        this.originalStock = null;
        this.currentStock = null;
        this.isVariantSelected = false;
        this.init();
    }

    init() {
        console.log('StockSync inicializado');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Escuchar cambios en selección de variantes
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('color-variant')) {
                this.handleVariantSelection(e.target);
            }
        });

        // Escuchar cierre de modales
        document.addEventListener('hidden.bs.modal', (e) => {
            if (e.target.id === 'variantSelectionModal') {
                this.handleModalClose();
            }
        });
    }

    handleVariantSelection(variantElement) {
        const stock = parseInt(variantElement.dataset.stock) || 0;
        console.log('Variante seleccionada, stock:', stock);
        
        this.currentStock = stock;
        this.isVariantSelected = true;
        this.updateAllStockIndicators(stock);
    }

    handleModalClose() {
        // Siempre restaurar stock original al cerrar modal
        this.restoreOriginalStock();
    }

    updateAllStockIndicators(stock) {
        console.log('Actualizando indicadores de stock:', stock);
        
        // Buscar todos los indicadores de stock
        const stockIndicators = document.querySelectorAll('.stock-status');
        
        stockIndicators.forEach(indicator => {
            this.updateStockIndicator(indicator, stock);
        });

        // Actualizar elementos con data-stock
        const stockElements = document.querySelectorAll('[data-stock]');
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
            this.updateProgressBar(progressBar, stock);
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

    updateProgressBar(element, stock) {
        const totalStock = this.getTotalStock();
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

    getTotalStock() {
        // Intentar obtener el stock total del producto
        const stockData = document.querySelector('[data-total-stock]');
        if (stockData) {
            return parseInt(stockData.dataset.totalStock) || 0;
        }
        
        // Fallback: buscar en elementos de stock
        const stockElements = document.querySelectorAll('.stock-status');
        if (stockElements.length > 0) {
            const firstElement = stockElements[0];
            const progressBar = firstElement.querySelector('.stock-progress');
            if (progressBar) {
                const currentWidth = progressBar.style.width;
                const currentStock = this.currentStock || 0;
                if (currentWidth && currentStock > 0) {
                    const percentage = parseFloat(currentWidth.replace('%', ''));
                    return Math.round((currentStock / percentage) * 100);
                }
            }
        }
        
        return 0;
    }

    restoreOriginalStock() {
        console.log('Restaurando stock original');
        this.isVariantSelected = false;
        
        // Buscar el stock original en los datos del producto
        const originalStockElement = document.querySelector('[data-original-stock]');
        if (originalStockElement) {
            const originalStock = parseInt(originalStockElement.dataset.originalStock) || 0;
            this.updateAllStockIndicators(originalStock);
        }
    }

    // Método público para actualizar stock desde código externo
    updateStock(stock) {
        this.currentStock = stock;
        this.isVariantSelected = true;
        this.updateAllStockIndicators(stock);
    }

    // Método público para restaurar stock original
    resetStock() {
        this.restoreOriginalStock();
    }

    // Método para actualización temporal (hover)
    updateStockTemporary(stock) {
        this.currentStock = stock;
        this.updateAllStockIndicators(stock);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.stockSync = new StockSync();
});

// Exportar para uso global
window.StockSync = StockSync;
