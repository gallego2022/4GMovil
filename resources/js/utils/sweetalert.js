/**
 * Utilidades de SweetAlert2 para 4GMovil
 * Configuración centralizada y helpers para alertas
 */

import Swal from 'sweetalert2';

// Configuración global de SweetAlert2
const defaultConfig = {
    confirmButtonColor: '#0064c8',
    cancelButtonColor: '#6b7280',
    customClass: {
        popup: 'bg-white dark:bg-gray-800 rounded-lg shadow-xl',
        title: 'text-lg font-medium text-gray-900 dark:text-white',
        content: 'text-sm text-gray-500 dark:text-gray-300',
        confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md',
        cancelButton: 'px-4 py-2 text-sm font-medium text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 rounded-md'
    }
};

// Toast configuration
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// Helper functions
export const SweetAlertUtils = {
    // Alerta de confirmación
    confirm: (title, text, confirmText = 'Sí, continuar', cancelText = 'Cancelar') => {
        return Swal.fire({
            title,
            text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            ...defaultConfig
        });
    },

    // Alerta de eliminación
    delete: (title = '¿Estás seguro?', text = '¡Esta acción no se puede deshacer!') => {
        return Swal.fire({
            title,
            text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#4f46e5',
            customClass: {
                ...defaultConfig.customClass,
                confirmButton: 'px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 rounded-md',
                cancelButton: 'px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 rounded-md'
            }
        });
    },

    // Alerta de éxito
    success: (title, text, timer = 2500) => {
        return Swal.fire({
            title,
            text,
            icon: 'success',
            showConfirmButton: false,
            timer,
            timerProgressBar: true,
            ...defaultConfig
        });
    },

    // Alerta de error
    error: (title, text, timer = 3000) => {
        return Swal.fire({
            title,
            text,
            icon: 'error',
            showConfirmButton: false,
            timer,
            timerProgressBar: true,
            customClass: {
                ...defaultConfig.customClass,
                popup: 'bg-white dark:bg-gray-800 rounded-lg shadow-xl border-l-4 border-red-500'
            }
        });
    },

    // Alerta de información
    info: (title, text, timer = 3000) => {
        return Swal.fire({
            title,
            text,
            icon: 'info',
            showConfirmButton: false,
            timer,
            timerProgressBar: true,
            ...defaultConfig
        });
    },

    // Toast de éxito
    toastSuccess: (message) => {
        return Toast.fire({
            icon: 'success',
            title: message
        });
    },

    // Toast de error
    toastError: (message) => {
        return Toast.fire({
            icon: 'error',
            title: message
        });
    },

    // Toast de información
    toastInfo: (message) => {
        return Toast.fire({
            icon: 'info',
            title: message
        });
    },

    // Toast de advertencia
    toastWarning: (message) => {
        return Toast.fire({
            icon: 'warning',
            title: message
        });
    },

    // Cargando
    loading: (title = 'Cargando...', text = 'Por favor espera') => {
        return Swal.fire({
            title,
            text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    // Cerrar loading
    close: () => {
        Swal.close();
    }
};

// Exportar también Swal para uso directo
export { Swal, Toast };
export default SweetAlertUtils;
