// Funciones para manejo de contraseñas
// Indicador de fortaleza de contraseña
function checkPasswordStrength(password) {
    console.log('checkPasswordStrength called with:', password);
    
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        symbol: /[\W_]/.test(password)
    };
    
    let strength = 0;
    if (requirements.length) strength++;
    if (requirements.uppercase) strength++;
    if (requirements.lowercase) strength++;
    if (requirements.number) strength++;
    if (requirements.symbol) strength++;
    
    console.log('Password strength:', strength, 'Requirements:', requirements);
    
    // Actualizar indicadores visuales
    updateRequirement('req-length', requirements.length);
    updateRequirement('req-uppercase', requirements.uppercase);
    updateRequirement('req-lowercase', requirements.lowercase);
    updateRequirement('req-number', requirements.number);
    updateRequirement('req-symbol', requirements.symbol);
    
    // Actualizar medidor de fortaleza
    const strengthMeter = document.getElementById('password-strength-meter');
    const strengthText = document.getElementById('strength-text');
    
    console.log('Strength meter element:', strengthMeter);
    console.log('Strength text element:', strengthText);
    
    if (!strengthMeter || !strengthText) return;
    
    // Limpiar clases anteriores
    strengthMeter.className = 'strength-meter-fill';
    
    if (password.length === 0) {
        strengthText.textContent = 'Débil';
        strengthText.className = 'text-xs font-medium text-red-400';
        // Limpiar todas las clases y establecer ancho 0
        strengthMeter.className = 'strength-meter-fill';
        strengthMeter.style.width = '0%';
        strengthMeter.style.backgroundColor = '#374151';
        console.log('Empty password - setting width to 0%');
        console.log('Meter element after setting width:', strengthMeter);
    } else {
        // Limpiar clases anteriores y estilos inline
        strengthMeter.className = 'strength-meter-fill';
        strengthMeter.style.width = '';
        strengthMeter.style.backgroundColor = '';
        
        console.log('Processing password with strength:', strength);
        
        let widthPercentage = '0%';
        let backgroundColor = '#374151';
        
        switch(strength) {
            case 1:
                strengthText.textContent = 'Muy débil';
                strengthText.className = 'text-xs font-medium text-red-400';
                widthPercentage = '20%';
                backgroundColor = '#ef4444';
                console.log('Strength 1 - setting width to 20%');
                break;
            case 2:
                strengthText.textContent = 'Débil';
                strengthText.className = 'text-xs font-medium text-red-400';
                widthPercentage = '40%';
                backgroundColor = '#ef4444';
                console.log('Strength 2 - setting width to 40%');
                break;
            case 3:
                strengthText.textContent = 'Moderada';
                strengthText.className = 'text-xs font-medium text-yellow-400';
                widthPercentage = '60%';
                backgroundColor = '#f59e0b';
                console.log('Strength 3 - setting width to 60%');
                break;
            case 4:
                strengthText.textContent = 'Fuerte';
                strengthText.className = 'text-xs font-medium text-blue-400';
                widthPercentage = '80%';
                backgroundColor = '#3b82f6';
                console.log('Strength 4 - setting width to 80%');
                break;
            case 5:
                strengthText.textContent = 'Muy fuerte';
                strengthText.className = 'text-xs font-medium text-green-400';
                widthPercentage = '100%';
                backgroundColor = '#10b981';
                console.log('Strength 5 - setting width to 100%');
                break;
            default:
                strengthText.textContent = 'Muy débil';
                strengthText.className = 'text-xs font-medium text-red-400';
                widthPercentage = '0%';
                backgroundColor = '#374151';
                console.log('Default case - setting width to 0%');
        }
        
        // Aplicar estilos directamente
        strengthMeter.style.width = widthPercentage;
        strengthMeter.style.backgroundColor = backgroundColor;
        
        console.log('Final strength meter width:', strengthMeter.style.width);
        console.log('Final strength meter background:', strengthMeter.style.backgroundColor);
        console.log('Meter element after applying styles:', strengthMeter);
    }
}

// Función para actualizar requisitos
function updateRequirement(elementId, isValid) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const icon = element.querySelector('i');
    if (!icon) return;
    
    if (isValid) {
        element.classList.add('valid');
        icon.className = 'fas fa-check-circle text-green-400';
        icon.classList.remove('text-gray-600');
    } else {
        element.classList.remove('valid');
        icon.className = 'fas fa-circle text-gray-600';
        icon.classList.remove('text-green-400');
    }
}

// Función para mostrar/ocultar contraseña - ACTUALIZADA
function togglePasswordVisibility(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(toggleId);
    
    if (!input || !toggle) return;
    
    // Cambiar el tipo de input
    if (input.type === 'password') {
        input.type = 'text';
        // Cambiar icono a ojo tachado (manteniendo posición estática)
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.className = 'fas fa-eye-slash';
        }
    } else {
        input.type = 'password';
        // Cambiar icono a ojo normal (manteniendo posición estática)
        const icon = toggle.querySelector('i');
        if (icon) {
            icon.className = 'fas fa-eye';
        }
    }
}

// Configurar los toggles de contraseña - ACTUALIZADO
function setupPasswordToggles() {
    // Toggle para contraseña de login
    const togglePasswordLogin = document.getElementById('toggle-password-login');
    if (togglePasswordLogin) {
        togglePasswordLogin.addEventListener('click', () => {
            togglePasswordVisibility('contrasena_login', 'toggle-password-login');
        });
    }
    
    // Toggle para contraseña de registro
    const togglePasswordRegister = document.getElementById('toggle-password-register');
    if (togglePasswordRegister) {
        togglePasswordRegister.addEventListener('click', () => {
            togglePasswordVisibility('contrasena_register', 'toggle-password-register');
        });
    }
    
    // Toggle para confirmar contraseña
    const togglePasswordConfirm = document.getElementById('toggle-password-confirm');
    if (togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', () => {
            togglePasswordVisibility('contrasena_confirmation', 'toggle-password-confirm');
        });
    }
}

// Configurar el indicador de fortaleza de contraseña
function setupPasswordStrengthIndicator() {
    const passwordInput = document.getElementById('contrasena_register');
    const passwordStrength = document.getElementById('password-strength');
    
    if (passwordInput && passwordStrength) {
        passwordInput.addEventListener('input', (e) => {
            const password = e.target.value;
            if (password.length > 0) {
                passwordStrength.style.display = 'block';
                checkPasswordStrength(password);
            } else {
                passwordStrength.style.display = 'none';
            }
        });
        
        passwordInput.addEventListener('focus', () => {
            if (passwordInput.value.length > 0) {
                passwordStrength.style.display = 'block';
            }
        });
        
        passwordInput.addEventListener('blur', () => {
            // Mantener visible si hay contenido
            if (passwordInput.value.length === 0) {
                passwordStrength.style.display = 'none';
            }
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    setupPasswordToggles();
    setupPasswordStrengthIndicator();
});
