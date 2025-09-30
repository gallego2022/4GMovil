/**
 * Sistema de Validación en Tiempo Real para Dashboard 4GMovil
 * 
 * Características:
 * - Validación en tiempo real mientras el usuario escribe
 * - Validación del navegador (HTML5) como primera línea
 * - Mensajes de error personalizados y estilizados
 * - Soporte para múltiples tipos de validación
 * - Integración con Alpine.js y Tailwind CSS
 */

class ValidationSystem {
    constructor() {
        this.rules = new Map();
        this.messages = new Map();
        this.validators = new Map();
        this.init();
    }

    init() {
        this.setupDefaultValidators();
        this.setupEventListeners();
        this.setupFormValidation();
    }

    /**
     * Configurar validadores por defecto
     */
    setupDefaultValidators() {
        // Validador de email
        this.addValidator('email', (value) => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(value);
        });

        // Validador de teléfono
        this.addValidator('phone', (value) => {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            return phoneRegex.test(value.replace(/\s/g, ''));
        });

        // Validador de URL
        this.addValidator('url', (value) => {
            try {
                new URL(value);
                return true;
            } catch {
                return false;
            }
        });

        // Validador de número decimal
        this.addValidator('decimal', (value) => {
            return /^\d+(\.\d{1,2})?$/.test(value);
        });

        // Validador de número entero
        this.addValidator('integer', (value) => {
            return /^\d+$/.test(value);
        });

        // Validador de longitud mínima
        this.addValidator('minLength', (value, min) => {
            return value.length >= min;
        });

        // Validador de longitud máxima
        this.addValidator('maxLength', (value, max) => {
            return value.length <= max;
        });

        // Validador de rango numérico
        this.addValidator('range', (value, min, max) => {
            const num = parseFloat(value);
            return num >= min && num <= max;
        });

        // Validador de SKU único
        this.addValidator('uniqueSku', async (value) => {
            if (!value) return true;
            try {
                const response = await fetch(`/admin/api/check-sku?sku=${encodeURIComponent(value)}`);
                const data = await response.json();
                return !data.exists;
            } catch {
                return true; // Si hay error, permitir el valor
            }
        });

        // Validador de nombre de campo único
        this.addValidator('uniqueFieldName', async (value, categoryId) => {
            if (!value || !categoryId) return true;
            try {
                const response = await fetch(`/admin/api/check-field-name?name=${encodeURIComponent(value)}&category=${categoryId}`);
                const data = await response.json();
                return !data.exists;
            } catch {
                return true;
            }
        });
    }

    /**
     * Agregar validador personalizado
     */
    addValidator(name, validator) {
        this.validators.set(name, validator);
    }

    /**
     * Configurar reglas de validación para un campo
     */
    addRule(fieldName, rules) {
        this.rules.set(fieldName, rules);
    }

    /**
     * Configurar mensajes personalizados
     */
    addMessage(fieldName, messages) {
        this.messages.set(fieldName, messages);
    }

    /**
     * Configurar event listeners para validación en tiempo real
     */
    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            // Validación en tiempo real para inputs
            document.querySelectorAll('input, textarea, select').forEach(field => {
                this.setupFieldValidation(field);
            });
        });
    }

    /**
     * Configurar validación para un campo específico
     */
    setupFieldValidation(field) {
        const fieldName = field.name;
        if (!fieldName) return;

        // Solo validar en tiempo real si el campo es requerido
        const isRequired = field.hasAttribute('required') || 
                          field.getAttribute('data-required') === 'true' ||
                          this.isFieldRequired(fieldName);

        if (isRequired) {
            // Eventos para validación en tiempo real (solo para campos requeridos)
            const events = ['input', 'blur', 'change'];
            
            events.forEach(event => {
                field.addEventListener(event, (e) => {
                    this.validateField(field);
                });
            });

            // Validación inicial si el campo tiene valor
            if (field.value) {
                this.validateField(field);
            }
        } else {
            // Para campos opcionales, solo validar al salir del campo (blur)
            field.addEventListener('blur', (e) => {
                this.validateField(field);
            });
        }
    }

    /**
     * Verificar si un campo es requerido basado en las reglas
     */
    isFieldRequired(fieldName) {
        const rules = this.rules.get(fieldName) || [];
        return rules.includes('required');
    }

    /**
     * Validar un campo específico
     */
    async validateField(field) {
        const fieldName = field.name;
        const value = field.value;
        const rules = this.rules.get(fieldName) || [];

        // Limpiar errores previos
        this.clearFieldErrors(field);

        // Si el campo está vacío y no es requerido, no validar
        if (!value && !rules.includes('required')) {
            return true;
        }

        // Si el campo está vacío y es requerido, mostrar error
        if (!value && rules.includes('required')) {
            this.showFieldError(field, this.getRequiredMessage(fieldName));
            return false;
        }

        let isValid = true;
        let errorMessage = '';

        // Aplicar reglas de validación (excluyendo 'required' si ya se validó)
        for (const rule of rules) {
            if (rule === 'required') continue; // Ya se validó arriba
            
            const result = await this.applyRule(field, rule, value);
            if (!result.isValid) {
                isValid = false;
                errorMessage = result.message;
                break;
            }
        }

        // Mostrar resultado
        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.showFieldSuccess(field);
        }

        return isValid;
    }

    /**
     * Obtener mensaje de campo requerido
     */
    getRequiredMessage(fieldName) {
        const customMessages = this.messages.get(fieldName) || {};
        return customMessages.required || `${this.getFieldLabel(document.querySelector(`[name="${fieldName}"]`))} es requerido`;
    }

    /**
     * Aplicar una regla de validación específica
     */
    async applyRule(field, rule, value) {
        const fieldName = field.name;
        const customMessages = this.messages.get(fieldName) || {};

        // Regla requerido
        if (rule === 'required') {
            if (!value || value.trim() === '') {
                return {
                    isValid: false,
                    message: customMessages.required || `${this.getFieldLabel(field)} es requerido`
                };
            }
        }

        // Regla email
        if (rule === 'email') {
            if (value && !this.validators.get('email')(value)) {
                return {
                    isValid: false,
                    message: customMessages.email || 'Ingresa un email válido'
                };
            }
        }

        // Regla teléfono
        if (rule === 'phone') {
            if (value && !this.validators.get('phone')(value)) {
                return {
                    isValid: false,
                    message: customMessages.phone || 'Ingresa un teléfono válido'
                };
            }
        }

        // Regla URL
        if (rule === 'url') {
            if (value && !this.validators.get('url')(value)) {
                return {
                    isValid: false,
                    message: customMessages.url || 'Ingresa una URL válida'
                };
            }
        }

        // Regla decimal
        if (rule === 'decimal') {
            if (value && !this.validators.get('decimal')(value)) {
                return {
                    isValid: false,
                    message: customMessages.decimal || 'Ingresa un número decimal válido'
                };
            }
        }

        // Regla entero
        if (rule === 'integer') {
            if (value && !this.validators.get('integer')(value)) {
                return {
                    isValid: false,
                    message: customMessages.integer || 'Ingresa un número entero válido'
                };
            }
        }

        // Regla longitud mínima
        if (rule.startsWith('minLength:')) {
            const minLength = parseInt(rule.split(':')[1]);
            if (value && !this.validators.get('minLength')(value, minLength)) {
                return {
                    isValid: false,
                    message: customMessages.minLength || `Mínimo ${minLength} caracteres`
                };
            }
        }

        // Regla longitud máxima
        if (rule.startsWith('maxLength:')) {
            const maxLength = parseInt(rule.split(':')[1]);
            if (value && !this.validators.get('maxLength')(value, maxLength)) {
                return {
                    isValid: false,
                    message: customMessages.maxLength || `Máximo ${maxLength} caracteres`
                };
            }
        }

        // Regla rango
        if (rule.startsWith('range:')) {
            const [min, max] = rule.split(':')[1].split(',').map(Number);
            if (value && !this.validators.get('range')(value, min, max)) {
                return {
                    isValid: false,
                    message: customMessages.range || `Debe estar entre ${min} y ${max}`
                };
            }
        }

        // Regla SKU único
        if (rule === 'uniqueSku') {
            if (value && !(await this.validators.get('uniqueSku')(value))) {
                return {
                    isValid: false,
                    message: customMessages.uniqueSku || 'Este SKU ya existe'
                };
            }
        }

        // Regla nombre de campo único
        if (rule === 'uniqueFieldName') {
            const categoryId = document.getElementById('categoria_id')?.value;
            if (value && categoryId && !(await this.validators.get('uniqueFieldName')(value, categoryId))) {
                return {
                    isValid: false,
                    message: customMessages.uniqueFieldName || 'Este nombre de campo ya existe en esta categoría'
                };
            }
        }

        return { isValid: true };
    }

    /**
     * Mostrar error en un campo
     */
    showFieldError(field, message) {
        // Agregar clases de error
        field.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        field.classList.remove('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');

        // Crear o actualizar mensaje de error
        let errorElement = field.parentNode.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'field-error mt-1 text-sm text-red-600 dark:text-red-400';
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    /**
     * Mostrar éxito en un campo
     */
    showFieldSuccess(field) {
        // Agregar clases de éxito
        field.classList.add('border-green-500', 'focus:border-green-500', 'focus:ring-green-500');
        field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

        // Ocultar mensaje de error
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    /**
     * Limpiar errores de un campo
     */
    clearFieldErrors(field) {
        field.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    /**
     * Obtener etiqueta del campo
     */
    getFieldLabel(field) {
        const label = field.parentNode.querySelector('label');
        return label ? label.textContent.replace('*', '').trim() : field.name;
    }

    /**
     * Configurar validación de formulario completo
     */
    setupFormValidation() {
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', async (e) => {
                    const isValid = await this.validateForm(form);
                    if (!isValid) {
                        e.preventDefault();
                        this.scrollToFirstError(form);
                    }
                });
            });
        });
    }

    /**
     * Validar formulario completo
     */
    async validateForm(form) {
        const fields = form.querySelectorAll('input, textarea, select');
        let isValid = true;

        for (const field of fields) {
            const fieldValid = await this.validateField(field);
            if (!fieldValid) {
                isValid = false;
            }
        }

        return isValid;
    }

    /**
     * Scroll al primer error
     */
    scrollToFirstError(form) {
        const firstError = form.querySelector('.border-red-500');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }
}

// Inicializar sistema de validación
window.ValidationSystem = new ValidationSystem();

// Exportar para uso en otros scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ValidationSystem;
}
