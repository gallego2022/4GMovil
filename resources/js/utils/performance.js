/**
 * Utilidades de Optimización de Rendimiento - 4GMovil
 * Sistema de monitoreo y optimización en tiempo real
 */

class PerformanceMonitor {
    constructor() {
        this.metrics = {
            navigation: {},
            resources: {},
            memory: {},
            errors: [],
            cls: {
                total: 0,
                entries: []
            }
        };
        
        this.init();
    }
    
    async init() {
        await this.monitorNavigation();
        this.monitorResources();
        this.monitorMemory();
        this.monitorErrors();
        this.setupPerformanceObserver();
    }
    
    /**
     * Monitorear métricas de navegación
     */
    async monitorNavigation() {
        if ('performance' in window) {
            const navigation = performance.getEntriesByType('navigation')[0];
            if (navigation) {
                // Obtener métricas básicas
                const basicMetrics = {
                    domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart,
                    loadComplete: navigation.loadEventEnd - navigation.loadEventStart,
                    domInteractive: navigation.domInteractive,
                    firstPaint: this.getFirstPaint(),
                    firstContentfulPaint: this.getFirstContentfulPaint()
                };
                
                // Obtener LCP de forma asíncrona
                try {
                    const lcp = await this.getLargestContentfulPaint();
                    this.metrics.navigation = {
                        ...basicMetrics,
                        largestContentfulPaint: lcp
                    };
                } catch (e) {
                    console.warn('Error al obtener LCP:', e);
                    this.metrics.navigation = {
                        ...basicMetrics,
                        largestContentfulPaint: 0
                    };
                }
            }
        }
    }
    
    /**
     * Monitorear recursos cargados
     */
    monitorResources() {
        if ('performance' in window) {
            const resources = performance.getEntriesByType('resource');
            this.metrics.resources = {
                total: resources.length,
                css: resources.filter(r => r.name.includes('.css')).length,
                js: resources.filter(r => r.name.includes('.js')).length,
                images: resources.filter(r => r.name.match(/\.(png|jpg|jpeg|gif|svg|webp)$/)).length,
                fonts: resources.filter(r => r.name.match(/\.(woff|woff2|ttf|eot)$/)).length,
                totalSize: resources.reduce((sum, r) => sum + (r.transferSize || 0), 0)
            };
        }
    }
    
    /**
     * Monitorear uso de memoria
     */
    monitorMemory() {
        if ('memory' in performance) {
            this.metrics.memory = {
                used: performance.memory.usedJSHeapSize,
                total: performance.memory.totalJSHeapSize,
                limit: performance.memory.jsHeapSizeLimit
            };
        }
    }
    
    /**
     * Monitorear errores de rendimiento
     */
    monitorErrors() {
        window.addEventListener('error', (e) => {
            this.metrics.errors.push({
                message: e.message,
                filename: e.filename,
                lineno: e.lineno,
                timestamp: Date.now()
            });
        });
    }
    
    /**
     * Configurar observador de rendimiento
     */
    setupPerformanceObserver() {
        if ('PerformanceObserver' in window) {
            // Observar cambios de diseño
            const layoutObserver = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.value > 16) { // Más de 16ms (60fps)
                        console.warn('Layout shift detectado:', entry);
                    }
                }
            });
            
            try {
                layoutObserver.observe({ entryTypes: ['layout-shift'] });
            } catch (e) {
                console.warn('Layout shift observer no disponible');
            }
            
            // Observar entradas de usuario (usando tipos soportados)
            try {
                // Usar 'event' en lugar de 'interaction' que no está soportado
                const eventObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (entry.entryType === 'event' && entry.processingStart && entry.startTime) {
                            const processingTime = entry.processingStart - entry.startTime;
                            // Solo reportar eventos muy lentos (>100ms) para evitar spam
                            if (processingTime > 100) {
                                console.warn('Evento lento detectado (', processingTime.toFixed(1), 'ms):', {
                                    name: entry.name,
                                    duration: processingTime,
                                    timestamp: entry.startTime
                                });
                            }
                        }
                    }
                });
                
                eventObserver.observe({ entryTypes: ['event'] });
            } catch (e) {
                console.warn('Event observer no disponible:', e);
            }
            
            // Observar cambios de diseño (layout-shift)
            try {
                const layoutShiftObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        // Acumular CLS para métricas
                        this.metrics.cls.total += entry.value;
                        this.metrics.cls.entries.push({
                            value: entry.value,
                            hadRecentInput: entry.hadRecentInput,
                            timestamp: entry.startTime,
                            sources: entry.sources?.length || 0
                        });
                        
                        // Solo reportar si no hay input reciente del usuario (evitar falsos positivos)
                        if (!entry.hadRecentInput && entry.value > 0.25) { // CLS > 0.25 es considerado pobre
                            console.warn('Layout shift significativo detectado (CLS:', entry.value.toFixed(3), ', Total acumulado:', this.metrics.cls.total.toFixed(3), '):', {
                                value: entry.value,
                                totalCLS: this.metrics.cls.total,
                                sources: entry.sources?.length || 0,
                                timestamp: entry.startTime
                            });
                        } else if (entry.value > 0.5) { // CLS muy alto incluso con input reciente
                            console.warn('Layout shift crítico detectado (CLS:', entry.value.toFixed(3), ', Total acumulado:', this.metrics.cls.total.toFixed(3), '):', {
                                value: entry.value,
                                hadRecentInput: entry.hadRecentInput,
                                sources: entry.sources?.length || 0
                            });
                        }
                    }
                });
                
                layoutShiftObserver.observe({ entryTypes: ['layout-shift'] });
            } catch (e) {
                console.warn('Layout shift observer no disponible:', e);
            }
        }
    }
    
    /**
     * Obtener primera pintura
     */
    getFirstPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const firstPaint = paintEntries.find(entry => entry.name === 'first-paint');
        return firstPaint ? firstPaint.startTime : 0;
    }
    
    /**
     * Obtener primera pintura de contenido
     */
    getFirstContentfulPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const firstContentfulPaint = paintEntries.find(entry => entry.name === 'first-contentful-paint');
        return firstContentfulPaint ? firstContentfulPaint.startTime : 0;
    }
    
    /**
     * Obtener pintura de contenido más grande
     */
    getLargestContentfulPaint() {
        // Usar la API moderna de PerformanceObserver para LCP
        if ('PerformanceObserver' in window) {
            return new Promise((resolve) => {
                try {
                    const lcpObserver = new PerformanceObserver((list) => {
                        const entries = list.getEntries();
                        const lastEntry = entries[entries.length - 1];
                        resolve(lastEntry ? lastEntry.startTime : 0);
                    });
                    
                    lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
                    
                    // Timeout de seguridad
                    setTimeout(() => {
                        lcpObserver.disconnect();
                        resolve(0);
                    }, 10000);
                } catch (e) {
                    console.warn('LCP observer no disponible:', e);
                    resolve(0);
                }
            });
        }
        
        // Fallback para navegadores antiguos
        try {
            const lcpEntries = performance.getEntriesByType('largest-contentful-paint');
            if (lcpEntries.length > 0) {
                return lcpEntries[lcpEntries.length - 1].startTime;
            }
        } catch (e) {
            console.warn('LCP entries no disponibles:', e);
        }
        return 0;
    }
    
    /**
     * Obtener métricas de rendimiento
     */
    getMetrics() {
        return this.metrics;
    }
    
    /**
     * Generar reporte de rendimiento
     */
    generateReport() {
        const report = {
            timestamp: Date.now(),
            url: window.location.href,
            userAgent: navigator.userAgent,
            metrics: this.metrics,
            recommendations: this.generateRecommendations()
        };
        
        return report;
    }
    
    /**
     * Generar recomendaciones de optimización
     */
    generateRecommendations() {
        const recommendations = [];
        
        if (this.metrics.navigation.domContentLoaded > 1000) {
            recommendations.push('El tiempo de carga del DOM es alto. Considera optimizar JavaScript crítico.');
        }
        
        if (this.metrics.resources.total > 20) {
            recommendations.push('Demasiados recursos cargados. Considera combinar archivos CSS/JS.');
        }
        
        if (this.metrics.resources.totalSize > 5000000) { // 5MB
            recommendations.push('El tamaño total de recursos es alto. Considera comprimir imágenes y minificar archivos.');
        }
        
        // Recomendaciones específicas para CLS
        if (this.metrics.cls.total > 0.25) {
            recommendations.push(`CLS alto detectado (${this.metrics.cls.total.toFixed(3)}). Considera: 1) Definir dimensiones fijas para imágenes, 2) Evitar insertar contenido sobre contenido existente, 3) Usar transform en lugar de cambiar propiedades de layout.`);
        }
        
        return recommendations;
    }
    
    /**
     * Obtener métricas de CLS
     */
    getCLSMetrics() {
        return {
            total: this.metrics.cls.total,
            entries: this.metrics.cls.entries.length,
            average: this.metrics.cls.entries.length > 0 ? this.metrics.cls.total / this.metrics.cls.entries.length : 0,
            lastEntry: this.metrics.cls.entries[this.metrics.cls.entries.length - 1] || null
        };
    }
    
    /**
     * Limpiar entradas antiguas de CLS (mantener solo las últimas 50)
     */
    cleanupCLSEntries() {
        if (this.metrics.cls.entries.length > 50) {
            const removedEntries = this.metrics.cls.entries.splice(0, this.metrics.cls.entries.length - 50);
            const removedValue = removedEntries.reduce((sum, entry) => sum + entry.value, 0);
            this.metrics.cls.total = Math.max(0, this.metrics.cls.total - removedValue);
        }
    }
    
    /**
     * Obtener resumen rápido del rendimiento
     */
    getPerformanceSummary() {
        const clsMetrics = this.getCLSMetrics();
        return {
            cls: {
                total: clsMetrics.total.toFixed(3),
                status: clsMetrics.total <= 0.1 ? 'Excelente' : 
                        clsMetrics.total <= 0.25 ? 'Bueno' : 
                        clsMetrics.total <= 0.5 ? 'Necesita mejora' : 'Pobre',
                entries: clsMetrics.entries
            },
            navigation: this.metrics.navigation,
            resources: this.metrics.resources,
            errors: this.metrics.errors.length
        };
    }
}

/**
 * Optimizaciones de carga diferida
 */
class LazyLoader {
    constructor() {
        this.observer = null;
        this.init();
    }
    
    init() {
        if ('IntersectionObserver' in window) {
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadResource(entry.target);
                        this.observer.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            this.observeLazyElements();
        }
    }
    
    /**
     * Observar elementos con carga diferida
     */
    observeLazyElements() {
        // Imágenes con carga diferida
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(img => this.observer.observe(img));
        
        // CSS no crítico
        const lazyCSS = document.querySelectorAll('link[data-lazy]');
        lazyCSS.forEach(link => this.observer.observe(link));
        
        // JavaScript no crítico
        const lazyJS = document.querySelectorAll('script[data-lazy]');
        lazyJS.forEach(script => this.observer.observe(script));
    }
    
    /**
     * Cargar recurso cuando sea visible
     */
    loadResource(element) {
        if (element.tagName === 'IMG' && element.dataset.src) {
            element.src = element.dataset.src;
            element.classList.remove('lazy');
        } else if (element.tagName === 'LINK' && element.dataset.href) {
            element.href = element.dataset.href;
        } else if (element.tagName === 'SCRIPT' && element.dataset.src) {
            const script = document.createElement('script');
            script.src = element.dataset.src;
            script.async = true;
            element.parentNode.replaceChild(script, element);
        }
    }
}

/**
 * Gestión de caché del lado del cliente
 */
class ClientCache {
    constructor() {
        this.cache = new Map();
        this.maxSize = 100;
        this.ttl = 5 * 60 * 1000; // 5 minutos
    }
    
    /**
     * Almacenar elemento en caché
     */
    set(key, value, ttl = this.ttl) {
        if (this.cache.size >= this.maxSize) {
            const firstKey = this.cache.keys().next().value;
            this.cache.delete(firstKey);
        }
        
        this.cache.set(key, {
            value,
            timestamp: Date.now(),
            ttl
        });
    }
    
    /**
     * Obtener elemento del caché
     */
    get(key) {
        const item = this.cache.get(key);
        if (!item) return null;
        
        if (Date.now() - item.timestamp > item.ttl) {
            this.cache.delete(key);
            return null;
        }
        
        return item.value;
    }
    
    /**
     * Limpiar caché expirado
     */
    cleanup() {
        const now = Date.now();
        for (const [key, item] of this.cache.entries()) {
            if (now - item.timestamp > item.ttl) {
                this.cache.delete(key);
            }
        }
    }
    
    /**
     * Limpiar todo el caché
     */
    clear() {
        this.cache.clear();
    }
}

/**
 * Optimizaciones de entrada y eventos
 */
class InputOptimizer {
    constructor() {
        this.debounceTimers = new Map();
        this.throttleTimers = new Map();
    }
    
    /**
     * Debounce para entradas de búsqueda
     */
    debounce(func, delay = 300) {
        return (...args) => {
            clearTimeout(this.debounceTimers.get(func));
            const timer = setTimeout(() => func.apply(this, args), delay);
            this.debounceTimers.set(func, timer);
        };
    }
    
    /**
     * Throttle para eventos de scroll
     */
    throttle(func, delay = 100) {
        return (...args) => {
            if (!this.throttleTimers.get(func)) {
                func.apply(this, args);
                this.throttleTimers.set(func, setTimeout(() => {
                    this.throttleTimers.delete(func);
                }, delay));
            }
        };
    }
}

/**
 * Inicializar optimizaciones cuando el DOM esté listo
 */
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar monitoreo de rendimiento
    window.performanceMonitor = new PerformanceMonitor();
    
    // Inicializar carga diferida
    window.lazyLoader = new LazyLoader();
    
    // Inicializar caché del cliente
    window.clientCache = new ClientCache();
    
    // Inicializar optimizador de entrada
    window.inputOptimizer = new InputOptimizer();
    
    // Limpiar caché periódicamente
    setInterval(() => {
        window.clientCache.cleanup();
        // Limpiar entradas antiguas de CLS
        if (window.performanceMonitor) {
            window.performanceMonitor.cleanupCLSEntries();
        }
    }, 60000); // Cada minuto
    
    // Reportar métricas de rendimiento
    setTimeout(() => {
        const summary = window.performanceMonitor.getPerformanceSummary();
        console.log('📊 Resumen de Rendimiento:', summary);
        
        // Solo mostrar reporte completo si hay problemas significativos
        if (summary.cls.status !== 'Excelente' || summary.errors > 0) {
            const report = window.performanceMonitor.generateReport();
            console.log('⚠️ Reporte Detallado (problemas detectados):', report);
        }
        
        // Enviar métricas al servidor si es necesario
        if (window.performanceMetricsEndpoint) {
            fetch(window.performanceMetricsEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(summary)
            }).catch(console.error);
        }
    }, 5000); // Después de 5 segundos
});

// Exportar para uso en otros módulos
export { PerformanceMonitor, LazyLoader, ClientCache, InputOptimizer }; 