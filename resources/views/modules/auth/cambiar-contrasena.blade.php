@extends('layouts.app-new')

@section('title', __('auth.change_password'))

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <!-- Encabezado -->
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold leading-6 text-gray-900 dark:text-white">
                            {{ __('auth.change_password') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('auth.change_password_description') }}
                        </p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-brand-100 dark:bg-brand-900 flex items-center justify-center">
                        <svg class="h-6 w-6 text-brand-600 dark:text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            @if(session('mensaje'))
            <div class="rounded-md {{ session('tipo', 'success') === 'success' ? 'bg-green-50 dark:bg-green-900' : 'bg-red-50 dark:bg-red-900' }} p-4 mx-6 mt-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if(session('tipo', 'success') === 'success')
                        <svg class="h-5 w-5 text-green-400 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        @else
                        <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ session('tipo', 'success') === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                            {{ session('mensaje') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('cambiar.contrasena') }}" class="space-y-6 p-6">
                @csrf

                <!-- Contraseña Actual -->
                <div>
                    <label for="contrasena_actual" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('auth.current_password') }}
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="password" 
                               name="contrasena_actual" 
                               id="contrasena_actual"
                               class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-brand-500 sm:text-sm @error('contrasena_actual') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               required>
                        @error('contrasena_actual')
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Nueva Contraseña -->
                <div>
                    <label for="nueva_contrasena" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('auth.new_password') }}
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="password" 
                               name="nueva_contrasena" 
                               id="nueva_contrasena"
                               class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-brand-500 sm:text-sm @error('nueva_contrasena') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               required>
                        @error('nueva_contrasena')
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.password_requirements') }}</p>
                </div>

                <!-- Confirmar Nueva Contraseña -->
                <div>
                    <label for="nueva_contrasena_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('auth.confirm_password') }}
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="password" 
                               name="nueva_contrasena_confirmation" 
                               id="nueva_contrasena_confirmation"
                               class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-brand-500 sm:text-sm @error('nueva_contrasena_confirmation') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                               required>
                        @error('nueva_contrasena_confirmation')
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('perfil') }}" 
                       class="inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        {{ __('messages.cancel') }}
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        {{ __('auth.change_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para alternar la visibilidad de la contraseña
    function togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        
        if (button) {
            button.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Cambiar el ícono
                this.querySelector('svg').innerHTML = type === 'password' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            });
        }
    }

    // Inicializar los toggles para cada campo de contraseña
    togglePasswordVisibility('contrasena_actual', 'toggle-current-password');
    togglePasswordVisibility('nueva_contrasena', 'toggle-new-password');
    togglePasswordVisibility('nueva_contrasena_confirmation', 'toggle-confirm-password');

    // Elementos del formulario
    const form = document.querySelector('form');
    const currentPassword = document.getElementById('contrasena_actual');
    const newPassword = document.getElementById('nueva_contrasena');
    const confirmPassword = document.getElementById('nueva_contrasena_confirmation');
    const submitButton = form.querySelector('button[type="submit"]');

    // Variables para controlar el estado de validación
    let currentPasswordValid = false;
    let newPasswordValid = false;
    let confirmPasswordValid = false;

    // Función para mostrar mensajes de error
    function showError(input, message) {
        clearError(input); // Limpiamos primero para evitar mensajes duplicados
        const errorDiv = document.createElement('p');
        errorDiv.className = 'mt-2 text-sm text-red-600 dark:text-red-400 error-message';
        errorDiv.textContent = message;
        input.parentElement.appendChild(errorDiv);
        input.classList.add('border-red-300');
    }

    // Función para limpiar mensajes de error
    function clearError(input) {
        const errorDiv = input.parentElement.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.remove();
        }
        input.classList.remove('border-red-300');
    }

    // Función para mostrar mensaje de éxito
    function showSuccess(input) {
        clearError(input);
        input.classList.add('border-green-500');
    }

    // Validar contraseña nueva
    function validateNewPassword() {
        const value = newPassword.value;
        clearError(newPassword);

        if (value.length === 0) {
            showError(newPassword, 'La nueva contraseña es requerida');
            newPasswordValid = false;
            return false;
        }

        if (value.length < 8) {
            showError(newPassword, 'La contraseña debe tener al menos 8 caracteres');
            newPasswordValid = false;
            return false;
        }
        if (!/[A-Z]/.test(value)) {
            showError(newPassword, 'La contraseña debe contener al menos una mayúscula');
            newPasswordValid = false;
            return false;
        }
        if (!/[a-z]/.test(value)) {
            showError(newPassword, 'La contraseña debe contener al menos una minúscula');
            newPasswordValid = false;
            return false;
        }
        if (!/[0-9]/.test(value)) {
            showError(newPassword, 'La contraseña debe contener al menos un número');
            newPasswordValid = false;
            return false;
        }
        if (!/[!@#$%^&*]/.test(value)) {
            showError(newPassword, 'La contraseña debe contener al menos un carácter especial (!@#$%^&*)');
            newPasswordValid = false;
            return false;
        }

        showSuccess(newPassword);
        newPasswordValid = true;
        return true;
    }

    // Validar confirmación de contraseña
    function validatePasswordMatch() {
        clearError(confirmPassword);
        
        if (confirmPassword.value.length === 0) {
            showError(confirmPassword, 'La confirmación de contraseña es requerida');
            confirmPasswordValid = false;
            return false;
        }

        if (newPassword.value !== confirmPassword.value) {
            showError(confirmPassword, 'Las contraseñas no coinciden');
            confirmPasswordValid = false;
            return false;
        }

        showSuccess(confirmPassword);
        confirmPasswordValid = true;
        return true;
    }

    // Validar contraseña actual
    async function validateCurrentPassword() {
        clearError(currentPassword);
        
        if (currentPassword.value.length === 0) {
            showError(currentPassword, 'La contraseña actual es requerida');
            currentPasswordValid = false;
            return false;
        }

        try {
            // Obtener el token CSRF
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            
            if (!token) {
                throw new Error('CSRF token no encontrado');
            }

            const response = await fetch('{{ route("validar.contrasena.actual") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                credentials: 'same-origin', // Importante para las cookies de sesión
                body: JSON.stringify({
                    password: currentPassword.value
                })
            });

            if (!response.ok) {
                if (response.status === 419) {
                    throw new Error('Error de sesión (CSRF). Por favor, recarga la página.');
                }
                throw new Error(`Error del servidor: ${response.status}`);
            }

            const data = await response.json();
            console.log('Respuesta del servidor:', data); // Debug

            if (data.valid) {
                showSuccess(currentPassword);
                currentPasswordValid = true;
                return true;
            } else {
                showError(currentPassword, data.message || 'La contraseña actual no es correcta');
                currentPasswordValid = false;
                return false;
            }
        } catch (error) {
            console.error('Error en la validación:', error);
            showError(currentPassword, error.message || 'Error al validar la contraseña');
            currentPasswordValid = false;
            return false;
        }
    }

    // Actualizar estado del botón submit
    function updateSubmitButton() {
        submitButton.disabled = !(currentPasswordValid && newPasswordValid && confirmPasswordValid);
    }

    // Eventos para validación en tiempo real
    let currentPasswordTimeout;
    currentPassword.addEventListener('input', () => {
        clearTimeout(currentPasswordTimeout);
        currentPasswordTimeout = setTimeout(async () => {
            await validateCurrentPassword();
            updateSubmitButton();
        }, 500);
    });

    newPassword.addEventListener('input', () => {
        validateNewPassword();
        if (confirmPassword.value) {
            validatePasswordMatch();
        }
        updateSubmitButton();
    });

    confirmPassword.addEventListener('input', () => {
        validatePasswordMatch();
        updateSubmitButton();
    });

    // Validar antes de enviar
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validar todos los campos
        const isCurrentValid = await validateCurrentPassword();
        const isNewValid = validateNewPassword();
        const isMatchValid = validatePasswordMatch();

        if (isCurrentValid && isNewValid && isMatchValid) {
            this.submit();
        }
    });

    // Deshabilitar el botón submit inicialmente
    submitButton.disabled = true;
});
</script>
@endpush