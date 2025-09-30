/**
 * 4GMovil - JavaScript Principal Optimizado
 * Funcionalidades del dashboard admin y optimizaciones de rendimiento
 */

import Alpine from 'alpinejs';
import { PerformanceMonitor, LazyLoader, ClientCache, InputOptimizer } from './utils/performance.js';

// Configurar Alpine.js
window.Alpine = Alpine;

// Componentes de Alpine.js para el dashboard
Alpine.data('dashboard', () => ({
    // Estado del sidebar
    sidebarOpen: false,
    
    // Estado de notificaciones
    notifications: [],
    
    // Estado de carga
    loading: false,
    
    // Estado del tema
    darkMode: true,
    
    init() {
        // Inicializar tema desde localStorage
        this.darkMode = localStorage.getItem('darkMode') === 'true';
        this.applyTheme();
        
        // Inicializar optimizaciones de rendimiento
        this.initPerformanceOptimizations();
        
        // Configurar listeners de eventos
        this.setupEventListeners();
    },
    
    // Toggle del sidebar
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    
    // Cerrar sidebar en móviles
    closeSidebar() {
        if (window.innerWidth < 768) {
            this.sidebarOpen = false;
        }
    },
    
    // Toggle del tema oscuro
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.applyTheme();
    },
    
    // Aplicar tema
    applyTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },
    
    // Agregar notificación
    addNotification(message, type = 'info', duration = 5000) {
        const notification = {
            id: Date.now(),
            message,
            type,
            timestamp: new Date()
        };
        
        this.notifications.push(notification);
        
        // Auto-remover notificación
        setTimeout(() => {
            this.removeNotification(notification.id);
        }, duration);
    },
    
    // Remover notificación
    removeNotification(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    },
    
    // Inicializar optimizaciones de rendimiento
    initPerformanceOptimizations() {
        // Monitoreo de rendimiento
        window.performanceMonitor = new PerformanceMonitor();
        
        // Carga diferida
        window.lazyLoader = new LazyLoader();
        
        // Caché del cliente
        window.clientCache = new ClientCache();
        
        // Optimizador de entrada
        window.inputOptimizer = new InputOptimizer();
        
        // Limpiar caché periódicamente
        setInterval(() => {
            window.clientCache.cleanup();
        }, 60000);
    },
    
    // Configurar listeners de eventos
    setupEventListeners() {
        // Cerrar sidebar al hacer click fuera
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.sidebar') && !e.target.closest('.sidebar-toggle')) {
                this.closeSidebar();
            }
        });
        
        // Cerrar sidebar en resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                this.sidebarOpen = false;
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K para toggle sidebar
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.toggleSidebar();
            }
            
            // Escape para cerrar sidebar
            if (e.key === 'Escape' && this.sidebarOpen) {
                this.closeSidebar();
            }
        });
    },
    
    // Función de búsqueda optimizada
    search: Alpine.debounce(function(query) {
        if (query.length < 2) return;
        
        this.loading = true;
        
        // Simular búsqueda (reemplazar con llamada real a API)
        setTimeout(() => {
            this.loading = false;
            this.addNotification(`Búsqueda completada para: ${query}`, 'success');
        }, 500);
    }, 300),
    
    // Función de logout
    logout() {
        if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
            // Limpiar caché local
            window.clientCache.clear();
            
            // Crear y enviar formulario de logout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';
            
            // Agregar token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    }
}));

// Componente para tablas de datos
Alpine.data('dataTable', () => ({
    // Estado de la tabla
    data: [],
    filteredData: [],
    searchQuery: '',
    sortColumn: '',
    sortDirection: 'asc',
    currentPage: 1,
    itemsPerPage: 10,
    
    // Filtros
    filters: {},
    
    init() {
        this.loadData();
        this.setupTableOptimizations();
    },
    
    // Cargar datos
    async loadData() {
        try {
            // Simular carga de datos (reemplazar con llamada real)
            this.data = [
                { id: 1, name: 'Producto 1', category: 'Electrónicos', price: 100 },
                { id: 2, name: 'Producto 2', category: 'Ropa', price: 50 },
                // ... más datos
            ];
            this.filteredData = [...this.data];
        } catch (error) {
            console.error('Error cargando datos:', error);
        }
    },
    
    // Filtrar datos
    filterData() {
        let filtered = [...this.data];
        
        // Aplicar búsqueda
        if (this.searchQuery) {
            filtered = filtered.filter(item => 
                Object.values(item).some(value => 
                    String(value).toLowerCase().includes(this.searchQuery.toLowerCase())
                )
            );
        }
        
        // Aplicar filtros
        Object.entries(this.filters).forEach(([key, value]) => {
            if (value) {
                filtered = filtered.filter(item => item[key] === value);
            }
        });
        
        this.filteredData = filtered;
        this.currentPage = 1;
    },
    
    // Ordenar datos
    sortData(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }
        
        this.filteredData.sort((a, b) => {
            let aVal = a[column];
            let bVal = b[column];
            
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            
            if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });
    },
    
    // Paginación
    get paginatedData() {
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        return this.filteredData.slice(start, end);
    },
    
    get totalPages() {
        return Math.ceil(this.filteredData.length / this.itemsPerPage);
    },
    
    // Navegación de páginas
    goToPage(page) {
        if (page >= 1 && page <= this.totalPages) {
            this.currentPage = page;
        }
    },
    
    // Optimizaciones de tabla
    setupTableOptimizations() {
        // Usar Intersection Observer para lazy loading de filas
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, { threshold: 0.1 });
            
            // Observar filas de tabla
            this.$nextTick(() => {
                const rows = this.$el.querySelectorAll('tbody tr');
                rows.forEach(row => observer.observe(row));
            });
        }
    }
}));

// Componente para formularios
Alpine.data('formHandler', () => ({
    // Estado del formulario
    formData: {},
    errors: {},
    submitting: false,
    
    // Validación
    validationRules: {},
    
    init() {
        this.setupFormOptimizations();
    },
    
    // Configurar optimizaciones del formulario
    setupFormOptimizations() {
        // Debounce para validación en tiempo real
        const validateField = Alpine.debounce((field, value) => {
            this.validateField(field, value);
        }, 300);
        
        // Agregar listeners de validación
        this.$nextTick(() => {
            const inputs = this.$el.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    validateField(input.name, input.value);
                });
            });
        });
    },
    
    // Validar campo individual
    validateField(field, value) {
        const rules = this.validationRules[field];
        if (!rules) return;
        
        let isValid = true;
        let errorMessage = '';
        
        // Aplicar reglas de validación
        if (rules.required && !value) {
            isValid = false;
            errorMessage = 'Este campo es requerido';
        } else if (rules.email && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Email inválido';
        } else if (rules.minLength && value.length < rules.minLength) {
            isValid = false;
            errorMessage = `Mínimo ${rules.minLength} caracteres`;
        }
        
        // Actualizar errores
        if (!isValid) {
            this.errors[field] = errorMessage;
        } else {
            delete this.errors[field];
        }
    },
    
    // Validar email
    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    
    // Enviar formulario
    async submitForm() {
        if (this.submitting) return;
        
        // Validar todo el formulario
        this.validateAllFields();
        
        if (Object.keys(this.errors).length > 0) {
            return;
        }
        
        this.submitting = true;
        
        try {
            // Simular envío (reemplazar con llamada real)
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Éxito
            this.addNotification('Formulario enviado exitosamente', 'success');
            this.resetForm();
            
        } catch (error) {
            this.addNotification('Error al enviar formulario', 'error');
        } finally {
            this.submitting = false;
        }
    },
    
    // Validar todos los campos
    validateAllFields() {
        Object.keys(this.formData).forEach(field => {
            this.validateField(field, this.formData[field]);
        });
    },
    
    // Resetear formulario
    resetForm() {
        this.formData = {};
        this.errors = {};
        this.$el.reset();
    },
    
    // Agregar notificación
    addNotification(message, type) {
        // Usar el sistema de notificaciones del dashboard si está disponible
        if (window.Alpine.store('dashboard')) {
            window.Alpine.store('dashboard').addNotification(message, type);
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }
}));

// Store global para el dashboard
Alpine.store('dashboard', {
    // Estado global
    user: null,
    permissions: [],
    
    // Inicializar store
    init() {
        this.loadUserData();
    },
    
    // Cargar datos del usuario
    async loadUserData() {
        try {
            // Simular carga de usuario (reemplazar con llamada real)
            this.user = {
                id: 1,
                name: 'Admin User',
                email: 'admin@4gmovil.com',
                role: 'admin'
            };
            
            this.permissions = ['read', 'write', 'delete'];
            
        } catch (error) {
            console.error('Error cargando datos del usuario:', error);
        }
    },
    
    // Verificar permisos
    hasPermission(permission) {
        return this.permissions.includes(permission);
    },
    
    // Verificar si es admin
    isAdmin() {
        return this.user?.role === 'admin';
    }
});

// Inicializar Alpine.js
Alpine.start();

// Exportar para uso en otros módulos
export { Alpine };
