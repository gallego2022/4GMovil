<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4GMovil - Nueva Contraseña</title>
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
                        <p class="text-gray-400 mt-2">Nueva Contraseña</p>
                    </div>
                    
                    <div class="w-full max-w-xs">
                        <div class="grid grid-cols-4 gap-2 mb-6">
                            <div class="app-icon bg-gradient-to-br from-blue-500 to-blue-600">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-purple-500 to-purple-600">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-red-500 to-red-600">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-green-500 to-green-600">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        
                        <div class="bg-green-900 bg-opacity-30 rounded-lg p-4 text-center border border-green-800">
                            <p class="text-green-200 text-sm">Contraseña segura</p>
                            <p class="text-white font-medium">Crea tu nueva contraseña</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Forms Container -->
        <div class="max-w-xl w-full toggle-forms">
            <!-- Reset Password Form -->
            <div class="form-container max-w-xl h-auto bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700" id="reset-password-form">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white">Nueva Contraseña</h2>
                    <p class="text-gray-400 mt-2">Ingresa el código OTP y tu nueva contraseña</p>
                </div>
                
                <?php if($errors->any()): ?>
                    <script>
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: '<?php echo e($errors->first()); ?>',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            background: '#1f2937',
                            color: '#ef4444',
                            iconColor: '#ef4444',
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    </script>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('password.update')); ?>" id="password-form" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Campo de email -->
                    <div class="relative">
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Correo electrónico</label>
                        <div class="relative">
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo e($email ?? old('email')); ?>"
                                   required
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Tu correo electrónico">
                            <div class="input-highlight"></div>
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <!-- Código OTP -->
                    <div class="relative">
                        <label for="otp_code" class="block text-sm font-medium text-gray-300 mb-1">Código OTP</label>
                        <div class="relative">
                            <input type="text" 
                                   id="otp_code" 
                                   name="otp_code" 
                                   maxlength="6"
                                   required 
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition text-center text-lg font-mono tracking-widest <?php $__errorArgs = ['otp_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="000000">
                            <div class="input-highlight"></div>
                        </div>
                        <?php $__errorArgs = ['otp_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-gray-400 text-xs mt-1">
                            Ingresa el código de 6 dígitos enviado a tu correo
                        </p>
                    </div>

                    <!-- Nueva contraseña -->
                    <div class="relative">
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-300">Nueva contraseña</label>
                            <span id="password-strength-text" class="text-xs font-medium text-gray-400"></span>
                        </div>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password"
                                   required
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Ingresa tu nueva contraseña">
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300"
                                    onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                            <div class="input-highlight"></div>
                        </div>
                        <div class="strength-meter mt-2">
                            <div class="strength-meter-fill" id="password-strength-meter"></div>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-400 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Confirmar contraseña -->
                    <div class="relative">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">Confirmar contraseña</label>
                        <div class="relative">
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation"
                                   required
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10"
                                   placeholder="Confirma tu nueva contraseña">
                            <button type="button" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-300"
                                    onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation-icon"></i>
                            </button>
                            <div class="input-highlight"></div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-500 text-white py-3 px-4 rounded-lg font-medium hover:from-green-700 hover:to-green-600 transition duration-300 shadow-lg hover:shadow-green-500/30">
                        <i class="fas fa-key mr-2"></i> Restablecer Contraseña
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="<?php echo e(route('password.request')); ?>" class="text-blue-400 hover:text-blue-300 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a solicitar código
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
    .strength-meter {
        height: 4px;
        background-color: #374151;
        border-radius: 2px;
        overflow: hidden;
    }

    .strength-meter-fill {
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .strength-weak { background-color: #ef4444; width: 25%; }
    .strength-fair { background-color: #f59e0b; width: 50%; }
    .strength-good { background-color: #10b981; width: 75%; }
    .strength-strong { background-color: #059669; width: 100%; }
    </style>

    <script>
    // Función para alternar visibilidad de contraseña
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    // Validación de contraseña en tiempo real
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthMeter = document.getElementById('password-strength-meter');
        const strengthText = document.getElementById('password-strength-text');
        
        let strength = 0;
        let feedback = '';
        
        // Verificar longitud
        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 25;
        
        // Verificar complejidad
        if (/[a-z]/.test(password)) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 25;
        if (/[^A-Za-z0-9]/.test(password)) strength += 25;
        
        // Aplicar límite máximo
        strength = Math.min(strength, 100);
        
        // Actualizar medidor
        strengthMeter.className = 'strength-meter-fill';
        if (strength <= 25) {
            strengthMeter.classList.add('strength-weak');
            feedback = 'Débil';
        } else if (strength <= 50) {
            strengthMeter.classList.add('strength-fair');
            feedback = 'Regular';
        } else if (strength <= 75) {
            strengthMeter.classList.add('strength-good');
            feedback = 'Buena';
        } else {
            strengthMeter.classList.add('strength-strong');
            feedback = 'Fuerte';
        }
        
        strengthText.textContent = feedback;
    });

    // Validación de confirmación de contraseña
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;
        
        if (confirmation && password !== confirmation) {
            this.setCustomValidity('Las contraseñas no coinciden');
        } else {
            this.setCustomValidity('');
        }
    });

    // Formatear código OTP
    document.getElementById('otp_code').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
    });
    </script>
</body>
</html>
<?php /**PATH C:\Users\usuario\Documents\GitHub\4GMovil\resources\views/auth/passwords/reset-otp.blade.php ENDPATH**/ ?>