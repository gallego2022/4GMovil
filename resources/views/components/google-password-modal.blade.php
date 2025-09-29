<!-- Modal para establecer contraseña de Google -->
<div id="googlePasswordModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-2xl rounded-2xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-600">
        <div class="mt-3">
            <!-- Encabezado -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="mx-auto flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 mr-3">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $title ?? 'Establecer Contraseña' }}</h3>
                </div>
                <button type="button" onclick="closeGooglePasswordModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 p-2 rounded-full transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <!-- Descripción -->
            <div class="mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                    {{ $description ?? 'Para poder hacer login manual en el futuro, establece una contraseña para tu cuenta.' }}
                </p>
            </div>
            
            <!-- Formulario -->
            <form id="googlePasswordForm" class="space-y-4">
                @csrf
                
                <!-- Nueva Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left mb-2">
                        {{ $passwordLabel ?? 'Nueva Contraseña' }}
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               class="mt-1 block w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500"
                               placeholder="{{ $passwordPlaceholder ?? 'Mínimo 8 caracteres' }}">
                        <button type="button" 
                                onclick="togglePasswordVisibility('password', 'toggle_password')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-200"
                                id="toggle_password">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @if(isset($passwordRequirements))
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 text-left">
                            {{ $passwordRequirements }}
                        </p>
                    @endif
                    <div id="error_password" class="mt-2 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 text-left mb-2">
                        {{ $confirmPasswordLabel ?? 'Confirmar Contraseña' }}
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required 
                               class="mt-1 block w-full px-4 py-3 pr-12 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-gray-400 dark:hover:border-gray-500"
                               placeholder="{{ $confirmPasswordPlaceholder ?? 'Repite tu contraseña' }}">
                        <button type="button" 
                                onclick="togglePasswordVisibility('password_confirmation', 'toggle_password_confirmation')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-200"
                                id="toggle_password_confirmation">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="error_password_confirmation" class="mt-2 text-xs text-red-600 dark:text-red-400 hidden"></div>
                </div>
                
                <!-- Botones -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" 
                            onclick="closeGooglePasswordModal()"
                            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 hover:shadow-md">
                        {{ $cancelButtonText ?? 'Más tarde' }}
                    </button>
                    <button type="submit" 
                            id="submitGooglePassword"
                            class="px-6 py-3 bg-blue-600 dark:bg-blue-500 text-white text-sm font-medium rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 hover:shadow-lg hover:shadow-blue-500/25">
                        {{ $submitButtonText ?? 'Establecer Contraseña' }}
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
function showGooglePasswordModal() {
    document.getElementById('googlePasswordModal').classList.remove('hidden');
    // Limpiar formulario y errores
    document.getElementById('googlePasswordForm').reset();
    clearAllGoogleErrors();
}

// Función para cerrar el modal
function closeGooglePasswordModal() {
    document.getElementById('googlePasswordModal').classList.add('hidden');
    clearAllGoogleErrors();
}

// Función para limpiar todos los errores
function clearAllGoogleErrors() {
    const errorDivs = document.querySelectorAll('[id^="error_"]');
    errorDivs.forEach(div => {
        div.classList.add('hidden');
        div.textContent = '';
    });
}

// Función para mostrar errores
function showGoogleError(fieldId, message) {
    const errorDiv = document.getElementById(`error_${fieldId}`);
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }
}

// Función para limpiar un error específico
function clearGoogleError(fieldId) {
    const errorDiv = document.getElementById(`error_${fieldId}`);
    if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
    }
}

// Validación en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('googlePasswordForm');
    const submitButton = document.getElementById('submitGooglePassword');
    
    if (!form || !submitButton) return;
    
    // Validar contraseña
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const value = this.value;
            clearGoogleError('password');
            
            if (value.length === 0) {
                showGoogleError('password', 'La contraseña es requerida');
                return;
            }
            
            if (value.length < 8) {
                showGoogleError('password', 'La contraseña debe tener al menos 8 caracteres');
                return;
            }
            
            if (!/[A-Z]/.test(value)) {
                showGoogleError('password', 'La contraseña debe contener al menos una mayúscula');
                return;
            }
            
            if (!/[a-z]/.test(value)) {
                showGoogleError('password', 'La contraseña debe contener al menos una minúscula');
                return;
            }
            
            if (!/[0-9]/.test(value)) {
                showGoogleError('password', 'La contraseña debe contener al menos un número');
                return;
            }
            
            if (!/[!@#$%^&*]/.test(value)) {
                showGoogleError('password', 'La contraseña debe contener al menos un carácter especial (!@#$%^&*)');
                return;
            }
        });
    }
    
    // Validar confirmación
    const confirmationInput = document.getElementById('password_confirmation');
    if (confirmationInput) {
        confirmationInput.addEventListener('input', function() {
            const value = this.value;
            const password = document.getElementById('password')?.value || '';
            clearGoogleError('password_confirmation');
            
            if (value.length === 0) {
                showGoogleError('password_confirmation', 'La confirmación de contraseña es requerida');
                return;
            }
            
            if (value !== password) {
                showGoogleError('password_confirmation', 'Las contraseñas no coinciden');
                return;
            }
        });
    }
    
    // Manejar envío del formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validar todos los campos
        const password = document.getElementById('password')?.value || '';
        const confirmation = document.getElementById('password_confirmation')?.value || '';
        
        // Limpiar errores previos
        clearAllGoogleErrors();
        
        // Validaciones básicas
        let hasErrors = false;
        
        if (!password) {
            showGoogleError('password', 'La contraseña es requerida');
            hasErrors = true;
        } else if (password.length < 8) {
            showGoogleError('password', 'La contraseña debe tener al menos 8 caracteres');
            hasErrors = true;
        } else if (!/[A-Z]/.test(password)) {
            showGoogleError('password', 'La contraseña debe contener al menos una mayúscula');
            hasErrors = true;
        } else if (!/[a-z]/.test(password)) {
            showGoogleError('password', 'La contraseña debe contener al menos una minúscula');
            hasErrors = true;
        } else if (!/[0-9]/.test(password)) {
            showGoogleError('password', 'La contraseña debe contener al menos un número');
            hasErrors = true;
        } else if (!/[!@#$%^&*]/.test(password)) {
            showGoogleError('password', 'La contraseña debe contener al menos un carácter especial (!@#$%^&*)');
            hasErrors = true;
        }
        
        if (!confirmation) {
            showGoogleError('password_confirmation', 'La confirmación de contraseña es requerida');
            hasErrors = true;
        } else if (password !== confirmation) {
            showGoogleError('password_confirmation', 'Las contraseñas no coinciden');
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
            const route = '{{ $submitRoute ?? route("google.set-password") }}';
            
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
                        text: data.message || 'Contraseña establecida exitosamente',
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        closeGooglePasswordModal();
                        // Recargar la página para mostrar el mensaje de éxito
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    // Fallback si no hay SweetAlert
                    alert('Contraseña establecida exitosamente');
                    closeGooglePasswordModal();
                    window.location.reload();
                }
            } else {
                // Error del servidor
                if (data.errors) {
                    // Errores de validación
                    Object.keys(data.errors).forEach(field => {
                        const fieldId = field.replace(/\./g, '_');
                        showGoogleError(fieldId, data.errors[field][0]);
                    });
                } else {
                    // Error general
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al establecer la contraseña',
                            confirmButtonText: 'Entendido'
                        });
                    } else {
                        alert('Error al establecer la contraseña: ' + (data.message || 'Error desconocido'));
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
            submitButton.textContent = '{{ $submitButtonText ?? "Establecer Contraseña" }}';
        }
    });
});
</script>
