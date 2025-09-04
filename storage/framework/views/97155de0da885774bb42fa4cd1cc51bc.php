<!-- Modal para cambiar contraseña -->
<div id="changePasswordModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-2xl rounded-2xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600">
        <div class="mt-3">
            <!-- Encabezado -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e($title ?? 'Cambiar Contraseña'); ?></h3>
                <button type="button" onclick="closeChangePasswordModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-full transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Formulario -->
            <form id="changePasswordForm" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <!-- Contraseña Actual -->
                <div>
                    <label for="contrasena_actual" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left mb-2">
                        <?php echo e($currentPasswordLabel ?? 'Contraseña Actual'); ?>

                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="contrasena_actual" 
                               name="contrasena_actual" 
                               required 
                               class="mt-1 block w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500">
                        <button type="button" 
                                onclick="togglePasswordVisibility('contrasena_actual', 'toggle_contrasena_actual')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-200"
                                id="toggle_contrasena_actual">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="error_contrasena_actual" class="mt-2 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Nueva Contraseña -->
                <div>
                    <label for="nueva_contrasena" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left mb-2">
                        <?php echo e($newPasswordLabel ?? 'Nueva Contraseña'); ?>

                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="nueva_contrasena" 
                               name="nueva_contrasena" 
                               required 
                               class="mt-1 block w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500"
                               placeholder="<?php echo e($newPasswordPlaceholder ?? 'Mínimo 8 caracteres'); ?>">
                        <button type="button" 
                                onclick="togglePasswordVisibility('nueva_contrasena', 'toggle_nueva_contrasena')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-200"
                                id="toggle_nueva_contrasena">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <?php if(isset($passwordRequirements)): ?>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-left">
                            <?php echo e($passwordRequirements); ?>

                        </p>
                    <?php endif; ?>
                    <div id="error_nueva_contrasena" class="mt-2 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Confirmar Nueva Contraseña -->
                <div>
                    <label for="nueva_contrasena_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left mb-2">
                        <?php echo e($confirmPasswordLabel ?? 'Confirmar Nueva Contraseña'); ?>

                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="nueva_contrasena_confirmation" 
                               name="nueva_contrasena_confirmation" 
                               required 
                               class="mt-1 block w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500"
                               placeholder="<?php echo e($confirmPasswordPlaceholder ?? 'Repite tu nueva contraseña'); ?>">
                        <button type="button" 
                                onclick="togglePasswordVisibility('nueva_contrasena_confirmation', 'toggle_nueva_contrasena_confirmation')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-200"
                                id="toggle_nueva_contrasena_confirmation">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="error_nueva_contrasena_confirmation" class="mt-2 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" 
                            onclick="closeChangePasswordModal()"
                            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 hover:shadow-md">
                        <?php echo e($cancelButtonText ?? 'Cancelar'); ?>

                    </button>
                    <button type="submit" 
                            id="submitChangePassword"
                            class="px-6 py-3 bg-blue-600 dark:bg-blue-500 text-white text-sm font-medium rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 hover:shadow-lg hover:shadow-blue-500/25">
                        <?php echo e($submitButtonText ?? 'Cambiar Contraseña'); ?>

                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para mostrar/ocultar contraseña
function togglePasswordVisibility(inputId, buttonId) {
    const input = document.getElementById(inputId);
    const button = document.getElementById(buttonId);
    
    if (input.type === 'password') {
        input.type = 'text';
        // Cambiar a icono de ojo cerrado
        button.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
            </svg>
        `;
    } else {
        input.type = 'password';
        // Cambiar a icono de ojo abierto
        button.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        `;
    }
}

// Función para abrir el modal
function showChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
    // Limpiar formulario y errores
    document.getElementById('changePasswordForm').reset();
    clearAllErrors();
}

// Función para cerrar el modal
function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
    clearAllErrors();
}

// Función para limpiar todos los errores
function clearAllErrors() {
    const errorDivs = document.querySelectorAll('[id^="error_"]');
    errorDivs.forEach(div => {
        div.classList.add('hidden');
        div.textContent = '';
    });
}

// Función para mostrar errores
function showError(fieldId, message) {
    const errorDiv = document.getElementById(`error_${fieldId}`);
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }
}

// Función para limpiar un error específico
function clearError(fieldId) {
    const errorDiv = document.getElementById(`error_${fieldId}`);
    if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
    }
}

// Validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('changePasswordForm');
    const submitButton = document.getElementById('submitChangePassword');
    
    if (!form || !submitButton) return;
    
    // Validar nueva contraseña
    const nuevaContrasenaInput = document.getElementById('nueva_contrasena');
    if (nuevaContrasenaInput) {
        nuevaContrasenaInput.addEventListener('input', function() {
            const value = this.value;
            clearError('nueva_contrasena');
            
            if (value.length === 0) {
                showError('nueva_contrasena', 'La nueva contraseña es requerida');
                return;
            }
            
            if (value.length < 8) {
                showError('nueva_contrasena', 'La contraseña debe tener al menos 8 caracteres');
                return;
            }
            
            if (!/[A-Z]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos una mayúscula');
                return;
            }
            
            if (!/[a-z]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos una minúscula');
                return;
            }
            
            if (!/[0-9]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos un número');
                return;
            }
            
            if (!/[!@#$%^&*]/.test(value)) {
                showError('nueva_contrasena', 'La contraseña debe contener al menos un carácter especial (!@#$%^&*)');
                return;
            }
        });
    }
    
    // Validar confirmación
    const confirmacionInput = document.getElementById('nueva_contrasena_confirmation');
    if (confirmacionInput) {
        confirmacionInput.addEventListener('input', function() {
            const value = this.value;
            const newPassword = document.getElementById('nueva_contrasena')?.value || '';
            clearError('nueva_contrasena_confirmation');
            
            if (value.length === 0) {
                showError('nueva_contrasena_confirmation', 'La confirmación de contraseña es requerida');
                return;
            }
            
            if (value !== newPassword) {
                showError('nueva_contrasena_confirmation', 'Las contraseñas no coinciden');
                return;
            }
        });
    }
    
    // Validar contraseña actual
    const contrasenaActualInput = document.getElementById('contrasena_actual');
    if (contrasenaActualInput) {
        contrasenaActualInput.addEventListener('input', function() {
            clearError('contrasena_actual');
        });
    }
    
    // Manejar envío del formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validar todos los campos
        const contrasenaActual = document.getElementById('contrasena_actual')?.value || '';
        const nuevaContrasena = document.getElementById('nueva_contrasena')?.value || '';
        const confirmacion = document.getElementById('nueva_contrasena_confirmation')?.value || '';
        
        // Limpiar errores previos
        clearAllErrors();
        
        // Validaciones básicas
        let hasErrors = false;
        
        if (!contrasenaActual) {
            showError('contrasena_actual', 'La contraseña actual es requerida');
            hasErrors = true;
        }
        
        if (!nuevaContrasena) {
            showError('nueva_contrasena', 'La nueva contraseña es requerida');
            hasErrors = true;
        } else if (nuevaContrasena.length < 8) {
            showError('nueva_contrasena', 'La contraseña debe tener al menos 8 caracteres');
            hasErrors = true;
        } else if (!/[A-Z]/.test(nuevaContrasena)) {
            showError('nueva_contrasena', 'La contraseña debe contener al menos una mayúscula');
            hasErrors = true;
        } else if (!/[a-z]/.test(nuevaContrasena)) {
            showError('nueva_contrasena', 'La contraseña debe contener al menos una minúscula');
            hasErrors = true;
        } else if (!/[0-9]/.test(nuevaContrasena)) {
            showError('nueva_contrasena', 'La contraseña debe contener al menos un número');
            hasErrors = true;
        } else if (!/[!@#$%^&*]/.test(nuevaContrasena)) {
            showError('nueva_contrasena', 'La contraseña debe contener al menos un carácter especial (!@#$%^&*)');
            hasErrors = true;
        }
        
        if (!confirmacion) {
            showError('nueva_contrasena_confirmation', 'La confirmación de contraseña es requerida');
            hasErrors = true;
        } else if (nuevaContrasena !== confirmacion) {
            showError('nueva_contrasena_confirmation', 'Las contraseñas no coinciden');
            hasErrors = true;
        }
        
        if (hasErrors) {
            return;
        }
        
        // Deshabilitar botón y mostrar loading
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Procesando...';
        
        try {
            const formData = new FormData(form);
            
            // Usar la ruta personalizada si se proporciona, o una por defecto
            const route = '<?php echo e($submitRoute ?? route("cambiar.contrasena.post")); ?>';
            
            const response = await fetch(route, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Éxito
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message || 'Contraseña cambiada exitosamente',
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        closeChangePasswordModal();
                        // Recargar la página para mostrar el mensaje de éxito
                        window.location.reload();
                    });
                } else {
                    // Fallback si no hay SweetAlert
                    alert('Contraseña cambiada exitosamente');
                    closeChangePasswordModal();
                    window.location.reload();
                }
            } else {
                // Error del servidor
                if (data.errors) {
                    // Errores de validación
                    Object.keys(data.errors).forEach(field => {
                        const fieldId = field.replace(/\./g, '_');
                        showError(fieldId, data.errors[field][0]);
                    });
                } else {
                    // Error general
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al cambiar la contraseña',
                            confirmButtonText: 'Entendido'
                        });
                    } else {
                        alert('Error al cambiar la contraseña: ' + (data.message || 'Error desconocido'));
                    }
                }
            }
        } catch (error) {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión. Inténtalo de nuevo.',
                    confirmButtonText: 'Entendido'
                });
            } else {
                alert('Error de conexión. Inténtalo de nuevo.');
            }
        } finally {
            // Restaurar botón
            submitButton.disabled = false;
            submitButton.textContent = '<?php echo e($submitButtonText ?? "Cambiar Contraseña"); ?>';
        }
    });
});
</script>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/components/change-password-modal.blade.php ENDPATH**/ ?>