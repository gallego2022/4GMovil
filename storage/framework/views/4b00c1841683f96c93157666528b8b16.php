<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4GMovil - Verificación de correo</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/style-login.css')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-center gap-16 min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 p-4">
        <!-- Phone Illustration -->
        <div class="phone-container">
            <div class="phone">
                <div class="notch"></div>
                <div class="phone-screen">
                    <div class="text-center mb-8">
                        <a href="<?php echo e(route('landing')); ?>">
                            <h2 class="text-2xl font-bold text-white">4GMovil</h2>
                        </a>
                        <p class="text-gray-400 mt-2">Verificación de correo</p>
                    </div>
                    
                    <div class="w-full max-w-xs">
                        <div class="grid grid-cols-4 gap-2 mb-6">
                            <div class="app-icon bg-gradient-to-br from-blue-500 to-blue-600">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-purple-500 to-purple-600">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-red-500 to-red-600">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-green-500 to-green-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        
                        <div class="bg-blue-900 bg-opacity-30 rounded-lg p-4 text-center border border-blue-800">
                            <p class="text-blue-200 text-sm">Verificación segura</p>
                            <p class="text-white font-medium">Ingresa el código OTP</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Forms Container -->
        <div class="max-w-xl w-full toggle-forms">
            <!-- OTP Verification Form for Registration -->
            <div class="form-container max-w-xl h-auto bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700" id="otp-verification-form">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white">Verificar tu correo</h2>
                    <p class="text-gray-400 mt-2">Ingresa el código de 6 dígitos enviado a tu correo</p>
                </div>

                <?php if(session('error_login') || session('error')): ?>
                    <div class="mb-6 p-4 bg-red-900 bg-opacity-30 rounded-lg border border-red-800">
                        <p class="text-red-200 text-sm">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <?php echo e(session('error_login') ?? session('error')); ?>

                        </p>
                    </div>
                <?php endif; ?>

                <!-- Información del email -->
                <div class="bg-blue-900 bg-opacity-30 rounded-lg p-4 mb-6 border border-blue-800">
                    <p class="text-blue-200 text-sm">
                        <strong>Código enviado a:</strong> <span class="text-white"><?php echo e($email); ?></span>
                    </p>
                    <p class="text-blue-300 text-xs mt-1">
                        Revisa tu bandeja de entrada y carpeta de spam.
                    </p>
                </div>

                <!-- Formulario de verificación de OTP -->
                <form id="otpForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Código OTP
                        </label>
                        <div class="flex space-x-2 justify-center">
                            <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-600 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-600 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-600 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-600 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-600 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center border border-gray-600 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="button" onclick="resendOtp()" id="resendOtpBtn"
                            class="text-sm text-blue-400 hover:text-blue-300">
                            <span id="resendOtpText">Reenviar código</span>
                            <span id="resendOtpTimer" class="hidden">Reenviar en 60s</span>
                        </button>
                    </div>

                    <button type="button" onclick="verifyOtp()" id="verifyOtpBtn"
                        class="w-full bg-gradient-to-r from-green-600 to-green-500 text-white py-3 px-4 rounded-lg font-medium hover:from-green-700 hover:to-green-600 transition duration-300 shadow-lg hover:shadow-green-500/30">
                        <span id="verifyOtpText">Verificar Código</span>
                        <div id="verifyOtpSpinner" class="hidden ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="<?php echo e(route('login')); ?>" class="text-blue-400 hover:text-blue-300 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Ir al inicio de sesión
                    </a>
                </div>
            </div>
        </div>
    </div>

<script>
let currentEmail = '<?php echo e($email); ?>';
let resendTimer = null;
let resendCountdown = 60;

// Función para verificar OTP
async function verifyOtp() {
    const otpInputs = document.querySelectorAll('.otp-input');
    const codigo = Array.from(otpInputs).map(input => input.value).join('');

    if (codigo.length !== 6) {
        showAlert('Por favor ingresa el código completo de 6 dígitos', 'error');
        return;
    }

    const btn = document.getElementById('verifyOtpBtn');
    const text = document.getElementById('verifyOtpText');
    const spinner = document.getElementById('verifyOtpSpinner');

    // Mostrar loading
    btn.disabled = true;
    text.textContent = 'Verificando...';
    spinner.classList.remove('hidden');

    try {
        const response = await fetch('<?php echo e(route("otp.verify")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ 
                email: currentEmail, 
                codigo 
            })
        });

        const data = await response.json();

        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                window.location.href = '<?php echo e(route("landing")); ?>';
            }, 2000);
        } else {
            showAlert(data.message, 'error');
            // Limpiar inputs en caso de error
            otpInputs.forEach(input => input.value = '');
            otpInputs[0].focus();
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al verificar el código OTP', 'error');
    } finally {
        // Restaurar botón
        btn.disabled = false;
        text.textContent = 'Verificar Código';
        spinner.classList.add('hidden');
    }
}

// Función para reenviar OTP
async function resendOtp() {
    if (resendTimer) return;

    const btn = document.getElementById('resendOtpBtn');
    const text = document.getElementById('resendOtpText');
    const timer = document.getElementById('resendOtpTimer');

    btn.disabled = true;
    text.classList.add('hidden');
    timer.classList.remove('hidden');

    try {
        const response = await fetch('<?php echo e(route("otp.send")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ email: currentEmail })
        });

        const data = await response.json();

        if (data.success) {
            showAlert('Código OTP reenviado correctamente', 'success');
            startResendTimer();
        } else {
            showAlert(data.message, 'error');
            resetResendButton();
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al reenviar el código OTP', 'error');
        resetResendButton();
    }
}

// Función para iniciar timer de reenvío
function startResendTimer() {
    resendCountdown = 60;
    const btn = document.getElementById('resendOtpBtn');
    const text = document.getElementById('resendOtpText');
    const timer = document.getElementById('resendOtpTimer');

    btn.disabled = true;
    text.classList.add('hidden');
    timer.classList.remove('hidden');

    resendTimer = setInterval(() => {
        resendCountdown--;
        timer.textContent = `Reenviar en ${resendCountdown}s`;

        if (resendCountdown <= 0) {
            clearInterval(resendTimer);
            resendTimer = null;
            resetResendButton();
        }
    }, 1000);
}

// Función para resetear botón de reenvío
function resetResendButton() {
    const btn = document.getElementById('resendOtpBtn');
    const text = document.getElementById('resendOtpText');
    const timer = document.getElementById('resendOtpTimer');

    btn.disabled = false;
    text.classList.remove('hidden');
    timer.classList.add('hidden');
}

// Función para mostrar alertas
function showAlert(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'success' ? '¡Éxito!' : 'Error',
            text: message,
            icon: type,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: type === 'success' ? '#10B981' : '#EF4444'
        });
    } else {
        alert(message);
    }
}

// Configurar inputs OTP
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.otp-input');

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Solo permitir números
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }

            // Mover al siguiente input
            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            // Mover al input anterior con backspace
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '');
            
            if (pastedData.length === 6) {
                otpInputs.forEach((input, i) => {
                    input.value = pastedData[i] || '';
                });
                otpInputs[5].focus();
            }
        });
    });

    // Enfocar el primer input al cargar la página
    setTimeout(() => {
        otpInputs[0].focus();
    }, 100);
});
</script>
</body>
</html>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/auth/otp-verification-register.blade.php ENDPATH**/ ?>