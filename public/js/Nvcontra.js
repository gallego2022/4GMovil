// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando validación de contraseña...');
    
    // Mostrar/ocultar contraseña
    const togglePassword = document.getElementById('toggle-password');
    const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }

    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function() {
            const passwordInput = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }

    // Validación de contraseña en tiempo real
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            console.log('Validando contraseña en tiempo real...');
            validatePassword();
            checkPasswordsMatch();
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            console.log('Verificando coincidencia de contraseñas...');
            checkPasswordsMatch();
        });
    }

    function validatePassword() {
        const password = document.getElementById('password').value;
        const submitBtn = document.getElementById('submit-btn');
        
        console.log('Validando contraseña:', password);
        
        // Validar requisitos
        const hasMinLength = password.length >= 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        console.log('Requisitos:', {
            minLength: hasMinLength,
            upperCase: hasUpperCase,
            number: hasNumber,
            specialChar: hasSpecialChar
        });
        
        // Actualizar iconos de requisitos
        updateRequirement('req-length', hasMinLength);
        updateRequirement('req-uppercase', hasUpperCase);
        updateRequirement('req-number', hasNumber);
        updateRequirement('req-special', hasSpecialChar);
        
        // Calcular fortaleza de la contraseña
        let strength = 0;
        if (hasMinLength) strength += 1;
        if (hasUpperCase) strength += 1;
        if (hasNumber) strength += 1;
        if (hasSpecialChar) strength += 1;
        
        console.log('Fortaleza calculada:', strength);
        
        // Actualizar medidor de fortaleza
        const strengthMeter = document.getElementById('password-strength-meter');
        const strengthText = document.getElementById('password-strength-text');
        
        if (strengthMeter && strengthText) {
            strengthMeter.style.width = `${strength * 25}%`;
            
            if (password.length === 0) {
                strengthText.textContent = '';
                strengthMeter.style.background = '#374151';
            } else {
                switch(strength) {
                    case 1:
                        strengthText.textContent = 'Débil';
                        strengthText.className = 'text-xs font-medium text-red-500';
                        strengthMeter.style.background = '#ef4444';
                        break;
                    case 2:
                        strengthText.textContent = 'Moderada';
                        strengthText.className = 'text-xs font-medium text-yellow-500';
                        strengthMeter.style.background = '#f59e0b';
                        break;
                    case 3:
                        strengthText.textContent = 'Fuerte';
                        strengthText.className = 'text-xs font-medium text-blue-500';
                        strengthMeter.style.background = '#3b82f6';
                        break;
                    case 4:
                        strengthText.textContent = 'Muy fuerte';
                        strengthText.className = 'text-xs font-medium text-green-500';
                        strengthMeter.style.background = '#10b981';
                        break;
                    default:
                        strengthText.textContent = 'Muy débil';
                        strengthText.className = 'text-xs font-medium text-red-500';
                        strengthMeter.style.background = '#ef4444';
                }
            }
        }
        
        // Habilitar/deshabilitar botón de envío
        const allValid = hasMinLength && hasUpperCase && hasNumber && hasSpecialChar;
        if (submitBtn) {
            submitBtn.disabled = !allValid;
            console.log('Botón habilitado:', allValid);
        }
    }

    function updateRequirement(elementId, isValid) {
        const element = document.getElementById(elementId);
        if (!element) {
            console.log('Elemento no encontrado:', elementId);
            return;
        }
        
        const icon = element.querySelector('i');
        if (!icon) {
            console.log('Icono no encontrado en:', elementId);
            return;
        }
        
        if (isValid) {
            element.classList.add('valid');
            icon.classList.add('text-green-500');
            icon.classList.remove('text-gray-600');
            console.log('Requisito cumplido:', elementId);
        } else {
            element.classList.remove('valid');
            icon.classList.remove('text-green-500');
            icon.classList.add('text-gray-600');
            console.log('Requisito no cumplido:', elementId);
        }
    }

    function checkPasswordsMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        const matchElement = document.getElementById('password-match');
        const mismatchElement = document.getElementById('password-mismatch');
        const submitBtn = document.getElementById('submit-btn');
        
        console.log('Verificando coincidencia:', { password: password.length, confirmPassword: confirmPassword.length });
        
        if (!matchElement || !mismatchElement) {
            console.log('Elementos de coincidencia no encontrados');
            return;
        }
        
        if (confirmPassword.length === 0) {
            matchElement.classList.add('hidden');
            mismatchElement.classList.add('hidden');
            return;
        }
        
        if (password === confirmPassword) {
            matchElement.classList.remove('hidden');
            mismatchElement.classList.add('hidden');
            console.log('Contraseñas coinciden');
            
            // Solo habilitar si también se cumplen los requisitos
            if (submitBtn) {
                const passwordValue = document.getElementById('password').value;
                const hasMinLength = passwordValue.length >= 8;
                const hasUpperCase = /[A-Z]/.test(passwordValue);
                const hasNumber = /[0-9]/.test(passwordValue);
                const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(passwordValue);
                const allValid = hasMinLength && hasUpperCase && hasNumber && hasSpecialChar;
                
                submitBtn.disabled = !allValid;
                console.log('Botón habilitado por coincidencia:', allValid);
            }
        } else {
            matchElement.classList.add('hidden');
            mismatchElement.classList.remove('hidden');
            console.log('Contraseñas no coinciden');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
        }
    }

    // Envío del formulario
    const form = document.getElementById('password-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Formulario enviado');
            // La validación del lado del cliente ya está hecha
            // El formulario se enviará normalmente a Laravel
            // Si hay errores, Laravel los mostrará en la vista
        });
    }
    
    // Inicializar validación al cargar
    console.log('Inicializando validación inicial...');
    if (passwordInput) {
        validatePassword();
    }
    
    console.log('Validación de contraseña inicializada correctamente');
});

// Funciones para navegación entre formularios (simuladas)
function showVerificationForm() {
    console.log("Volviendo al formulario de verificación");
    // En una implementación real, esto volvería al paso anterior
}