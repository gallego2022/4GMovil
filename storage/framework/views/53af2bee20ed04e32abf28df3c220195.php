<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('titulo_pagina'); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/style-login.css')); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen flex items-center justify-center p-4">
    <div
        class="container mx-auto flex flex-col lg:flex-row items-center justify-center gap-16 min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 p-4">
        <!-- Phone Illustration -->
        <div class="phone-container">
            <div class="phone">
                <div class="notch"></div>
                <div class="phone-screen">
                    <div class="text-center mb-8">
                        <a href="<?php echo e(route('landing')); ?>">
                            <h2 class="text-2xl font-bold text-white">4GMovil</h2>
                        </a>
                        <p class="text-gray-400 mt-2">Tu tienda de dispositivos tecnológicos</p>
                    </div>

                    <div class="w-full max-w-xs">
                        <div class="grid grid-cols-4 gap-2 mb-6">
                            <div class="app-icon bg-gradient-to-br from-blue-500 to-blue-600">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-purple-500 to-purple-600">
                                <i class="fas fa-mobile-screen"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-red-500 to-red-600">
                                <i class="fas fa-headphones"></i>
                            </div>
                            <div class="app-icon bg-gradient-to-br from-green-500 to-green-600">
                                <i class="fas fa-tablet-screen-button"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(session('error_login')): ?>
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '<?php echo e(session('error_login')); ?>',
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

        <?php if(session('status')): ?>
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: '<?php echo e(session('status_type') === 'success' ? 'success' : 'info'); ?>',
                    title: '<?php echo e(session('status')); ?>',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    background: '#1f2937',
                    color: '<?php echo e(session('status_type') === 'success' ? '#10b981' : '#3b82f6'); ?>',
                    iconColor: '<?php echo e(session('status_type') === 'success' ? '#10b981' : '#3b82f6'); ?>',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            </script>
        <?php endif; ?>

        <!-- Forms Container -->
        <div class="max-w-xl w-full toggle-forms">
            <!-- Login Form -->
            <div class="form-container max-w-xl h-auto bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700"
                id="login-form">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white">Iniciar Sesión</h2>
                    <p class="text-gray-400 mt-2">¡Bienvenido de vuelta a 4GMovil!</p>
                </div>

                <form action="<?php echo e(route('logear')); ?>" method="post" class="space-y-6" id="login-form" novalidate>
                    <?php echo csrf_field(); ?>
                    
                    
                    <?php if($errors->any()): ?>
                        <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                                <span class="text-red-400 font-medium">Por favor corrige los siguientes errores:</span>
                            </div>
                            <ul class="mt-2 space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="text-red-300 text-sm flex items-center space-x-2">
                                        <i class="fas fa-circle text-red-400 text-xs"></i>
                                        <span><?php echo e($error); ?></span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    
                    <div class="relative">
                        <input type="email" name="correo_electronico" id="correo_electronico_login"
                            value="<?php echo e(old('correo_electronico')); ?>" placeholder="Ingresa tu correo electrónico"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['correo_electronico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        
                        <?php $__errorArgs = ['correo_electronico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="error-message-container">
                                <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div class="input-highlight"></div>
                    </div>

                    
                    <div class="relative">
                        <input type="password" placeholder="Ingresa tu contraseña" name="contrasena" id="contrasena_login"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['contrasena'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        
                        <div class="password-toggle" id="toggle-password-login">
                            <i class="fas fa-eye"></i>
                        </div>
                        
                        <?php $__errorArgs = ['contrasena'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="error-message-container">
                                <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div class="input-highlight"></div>
                    </div>
                    
                    
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox"
                                class="form-checkbox h-4 w-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                            <span class="ml-2 text-gray-300 text-sm">Mantener sesión activa</span>
                        </label>

                        <a href="<?php echo e(route('password.request')); ?>"
                            class="text-sm text-blue-400 hover:text-blue-300">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-blue-600 transition duration-300 shadow-lg hover:shadow-blue-500/30">
                        <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-400">¿No tienes una cuenta?
                        <button onclick="toggleForms()"
                            class="text-blue-400 hover:text-blue-300 font-medium">Crear cuenta</button>
                    </p>
                </div>

                <div class="mt-4">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-800 text-gray-400">O continúa con</span>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-1 justify-center items-center">
                        <a href="<?php echo e(route('google.redirect')); ?>"
                            class="flex items-center justify-center py-2 px-4 bg-gray-700 rounded-lg text-gray-300 hover:bg-gray-600 transition">
                            <i class="fab fa-google text-red-400 mr-2"></i> Continuar con Google
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulario de Registro -->
            <div class="form-container max-w-xl h-auto bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-700 absolute inset-0 flipped opacity-0"
                id="register-form" style="max-height: 80vh; overflow-y: auto;">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-white">Crear Cuenta</h2>
                    <p class="text-gray-400 mt-2">Únete a la familia 4GMovil</p>
                </div>

                <form class="space-y-4" action="<?php echo e(route('registrar')); ?>" method="post" id="register-form"
                    novalidate>
                    <?php echo csrf_field(); ?>
                    
                    
                    <?php if($errors->any()): ?>
                        <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-exclamation-triangle text-red-400"></i>
                                <span class="text-red-400 font-medium">Por favor corrige los siguientes errores:</span>
                            </div>
                            <ul class="mt-2 space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="text-red-300 text-sm flex items-center space-x-2">
                                        <i class="fas fa-circle text-red-400 text-xs"></i>
                                        <span><?php echo e($error); ?></span>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    
                    <div class="relative">
                        <input type="text" placeholder="Escribe tu nombre completo" name="nombre_usuario" id="nombre_usuario"
                        value="<?php echo e(old('nombre_usuario')); ?>"
                            class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['nombre_usuario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        
                        <?php $__errorArgs = ['nombre_usuario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="error-message-container">
                                <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="relative">
                        <input type="email" placeholder="Escribe tu correo electrónico" name="correo_electronico"
                            id="correo_electronico_register" value="<?php echo e(old('correo_electronico')); ?>"
                            class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['correo_electronico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        
                        <?php $__errorArgs = ['correo_electronico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="error-message-container">
                                <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    
                    <div class="relative">
                        <input type="tel" placeholder="Escribe tu número de teléfono" name="telefono" id="telefono"
                        value="<?php echo e(old('telefono')); ?>"
                            class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400  focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        
                        <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="error-message-container">
                                <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        
                        <div class="relative">
                            <input type="password" placeholder="Crea una contraseña segura" name="contrasena"
                        id="contrasena_register"
                                class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['contrasena'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    
                    <!-- Icono para mostrar/ocultar contraseña -->
                            <div class="password-toggle" id="toggle-password-register">
                        <i class="fas fa-eye"></i>
                            </div>
                            
                            <?php $__errorArgs = ['contrasena'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="error-message-container">
                                    <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="relative">
                            <input type="password" placeholder="Confirma tu contraseña" name="contrasena_confirmation"
                                id="contrasena_confirmation"
                                class="w-full px-4 py-3 bg-gray-700 border rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition pr-10 <?php $__errorArgs = ['contrasena_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">

                            <!-- Icono para mostrar/ocultar contraseña -->
                            <div class="password-toggle" id="toggle-password-confirm">
                                <i class="fas fa-eye"></i>
                            </div>
                            
                            <?php $__errorArgs = ['contrasena_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="error-message-container">
                                    <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                                </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <!-- Indicador de fortaleza de contraseña -->
                    <div class="password-strength mt-2" id="password-strength">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 text-xs">Nivel de seguridad:</span>
                            <span class="text-xs font-medium" id="strength-text">Débil</span>
                        </div>
                        
                        <!-- Medidor de fortaleza -->
                        <div class="strength-meter mb-3">
                            <div class="strength-meter-fill" id="password-strength-meter"></div>
                        </div>
                        
                        <!-- Requisitos de contraseña -->
                        <div class="text-xs text-gray-400 space-y-1">
                            <div class="flex items-center space-x-2 requirement" id="req-length">
                                <i class="fas fa-circle text-gray-600"></i>
                                <span>Mínimo 8 caracteres</span>
                            </div>
                            <div class="flex items-center space-x-2 requirement" id="req-uppercase">
                                <i class="fas fa-circle text-gray-600"></i>
                                <span>Al menos una mayúscula</span>
                            </div>
                            <div class="flex items-center space-x-2 requirement" id="req-lowercase">
                                <i class="fas fa-circle text-gray-600"></i>
                                <span>Al menos una minúscula</span>
                            </div>
                            <div class="flex items-center space-x-2 requirement" id="req-number">
                                <i class="fas fa-circle text-gray-600"></i>
                                <span>Al menos un número</span>
                            </div>
                            <div class="flex items-center space-x-2 requirement" id="req-symbol">
                                <i class="fas fa-circle text-gray-600"></i>
                                <span>Al menos un símbolo</span>
                            </div>
                </div>
                     </div>  
                
                <div class="space-y-2">
                    <label class="flex items-start space-x-2">
                        <input type="checkbox" name="acepta_terminos"
                            class="form-checkbox h-4 w-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 mt-1 <?php $__errorArgs = ['acepta_terminos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            <?php echo e(old('acepta_terminos') ? 'checked' : ''); ?> required>
                        <span class="text-gray-300 text-sm">
                            Acepto los
                            <a href="#" class="text-blue-400 hover:text-blue-300">Términos y Condiciones</a> y la
                            <a href="#" class="text-blue-400 hover:text-blue-300">Política de Privacidad</a>
                        </span>
                    </label>
                    
                    <?php $__errorArgs = ['acepta_terminos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="error-message-container">
                            <span class="text-red-400 text-xs"><?php echo e($message); ?></span>
                        </div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                    <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-500 text-white py-3 px-4 rounded-lg font-medium
            hover:from-blue-700 hover:to-blue-600 transition duration-300 shadow-lg hover:shadow-blue-500/30 flex justify-center items-center">
                    <i class="fas fa-user-plus mr-2"></i> Crear Mi Cuenta
                    </button>
            </form>

            <div class="mt-2 text-center flex justify-center items-center">
                <p class="text-gray-400">¿Ya tienes una cuenta?
                    <button onclick="toggleForms()" class="text-blue-400 hover:text-blue-300 font-medium">Iniciar
                        sesión</button>
                </p>
                </div>
            </div>



        </div>
    </div>
    </div>
    <!-- Alerta confrimacion registro -->
    <?php if(session('registro_exitoso')): ?>
        <script>
            Swal.fire({
                title: '<?php echo e(__('messages.registration_success')); ?>',
                icon: 'success',
                timer: 2300,
                showConfirmButton: false
            });
        </script>
    <?php endif; ?>
    <!-- Validacion de contraseña -->
    <script src="<?php echo e(asset('js/password-utils.js')); ?>"></script>

    <!-- Debug: Verificar que las funciones estén disponibles -->
    <script>
        console.log('Verificando funciones de contraseña...');
        console.log('setupPasswordToggles disponible:', typeof setupPasswordToggles);
        console.log('togglePasswordVisibility disponible:', typeof togglePasswordVisibility);

        // Verificar que los elementos existan
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM cargado, verificando elementos...');
            console.log('toggle-password-login:', document.getElementById('toggle-password-login'));
            console.log('toggle-password-register:', document.getElementById('toggle-password-register'));
            console.log('toggle-password-confirm:', document.getElementById('toggle-password-confirm'));

            // Llamar manualmente a la función si está disponible
            if (typeof setupPasswordToggles === 'function') {
                console.log('Llamando a setupPasswordToggles...');
                setupPasswordToggles();
            } else {
                console.error('setupPasswordToggles no está disponible');
            }
        });
    </script>

    <!-- Validacion de campos login-form -->
    <script>
        // Agregar los mensajes de validación al inicio
        const validationMessages = {
            required_field: '<?php echo e(__('forms.required_field')); ?>',
            invalid_format: '<?php echo e(__('forms.invalid_format')); ?>',
            fix_errors: '<?php echo e(__('forms.fix_errors')); ?>',
            validation_error: '<?php echo e(__('forms.validation_error')); ?>',
            name: {
                required: '<?php echo e(__('forms.name.required')); ?>',
                format: '<?php echo e(__('forms.name.format')); ?>'
            },
            email: {
                required: '<?php echo e(__('forms.email.required')); ?>',
                format: '<?php echo e(__('forms.email.format')); ?>'
            },
            phone: {
                required: '<?php echo e(__('forms.phone.required')); ?>',
                format: '<?php echo e(__('forms.phone.format')); ?>'
            },
            password: {
                required: '<?php echo e(__('forms.password.required')); ?>',
                min_length: '<?php echo e(__('forms.password.min_length')); ?>',
                uppercase: '<?php echo e(__('forms.password.uppercase')); ?>',
                lowercase: '<?php echo e(__('forms.password.lowercase')); ?>',
                number: '<?php echo e(__('forms.password.number')); ?>',
                symbol: '<?php echo e(__('forms.password.symbol')); ?>',
                mismatch: '<?php echo e(__('forms.password.mismatch')); ?>'
            },
            terms: {
                required: '<?php echo e(__('forms.terms.required')); ?>'
            },
            auth: {
                login_success: '<?php echo e(__('messages.login_success')); ?>',
                login_error: '<?php echo e(__('messages.error')); ?>'
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('login-form');
            const correoInput = document.getElementById('correo_electronico_login');
            const passInput = document.getElementById('contrasena_login');

            function showToast(message, icon = 'error') {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: icon,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }

            function createErrorElement() {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
                return errorDiv;
            }

            function showInputError(input, message) {
                // Remover mensaje de error anterior si existe
                const existingError = input.parentNode.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }

                // Crear y mostrar nuevo mensaje de error
                if (message) {
                    const errorDiv = createErrorElement();
                    errorDiv.textContent = message;
                    input.parentNode.appendChild(errorDiv);
                    input.classList.add('border-red-500');
                    input.classList.remove('border-green-500');
                } else {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                }
            }

            function validateInput(input, validations) {
                const value = input.value.trim();

                for (const validation of validations) {
                    if (!validation.isValid(value)) {
                        showInputError(input, validation.message);
                        return false;
                    }
                }

                showInputError(input, ''); // Limpiar error si es válido
                return true;
            }

            const emailValidations = [{
                    isValid: value => value !== '',
                    message: validationMessages.email.required
                },
                {
                    isValid: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                    message: validationMessages.email.format
                }
            ];

            const passwordValidations = [{
                    isValid: value => value !== '',
                    message: validationMessages.password.required
                },
                {
                    isValid: value => value.length >= 8,
                    message: validationMessages.password.min_length
                }
            ];

            correoInput.addEventListener('input', () => {
                validateInput(correoInput, emailValidations);
            });

            passInput.addEventListener('input', () => {
                validateInput(passInput, passwordValidations);
            });

            loginForm.addEventListener('submit', (e) => {
                const isEmailValid = validateInput(correoInput, emailValidations);
                const isPasswordValid = validateInput(passInput, passwordValidations);

                if (!isEmailValid || !isPasswordValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: validationMessages.validation_error,
                        text: validationMessages.fix_errors,
                        confirmButtonText: '<?php echo e(__('messages.confirm')); ?>'
                    });
                }
            });
        });
    </script>

    <!-- Validacion de campos form-register-->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('register-form');
            const inputs = {
                nombre: document.getElementById('nombre_usuario'),
                correo: document.getElementById('correo_electronico_register'),
                telefono: document.getElementById('telefono'),
                contrasena: document.getElementById('contrasena_register'),
                confirmacion: document.getElementById('contrasena_confirmation'),
                terminos: document.querySelector('input[name="acepta_terminos"]')
            };

            const validations = {
                nombre: [{
                        isValid: value => value.trim() !== '',
                        message: validationMessages.name.required
                    },
                    {
                        isValid: value => /^[a-zA-ZÀ-ÿ\s]{2,25}$/.test(value),
                        message: validationMessages.name.format
                    }
                ],
                correo: [{
                        isValid: value => value.trim() !== '',
                        message: validationMessages.email.required
                    },
                    {
                        isValid: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                        message: validationMessages.email.format
                    }
                ],
                telefono: [{
                        isValid: value => value.trim() !== '',
                        message: validationMessages.phone.required
                    },
                    {
                        isValid: value => /^\d{10}$/.test(value),
                        message: validationMessages.phone.format
                    }
                ],
                contrasena: [{
                        isValid: value => value.trim() !== '',
                        message: validationMessages.password.required
                    },
                    {
                        isValid: value => value.length >= 8,
                        message: validationMessages.password.min_length
                    },
                    {
                        isValid: value => /[A-Z]/.test(value),
                        message: validationMessages.password.uppercase
                    },
                    {
                        isValid: value => /[a-z]/.test(value),
                        message: validationMessages.password.lowercase
                    },
                    {
                        isValid: value => /\d/.test(value),
                        message: validationMessages.password.number
                    },
                    {
                        isValid: value => /[\W_]/.test(value),
                        message: validationMessages.password.symbol
                    }
                ]
            };

            function showToast(message, icon = 'error') {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: icon,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }

            function createErrorElement() {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
                return errorDiv;
            }

            function showInputError(input, message) {
                // Remover mensaje de error anterior si existe
                const existingError = input.parentNode.querySelector('.error-message');
                if (existingError) {
                    existingError.remove();
                }

                // Crear y mostrar nuevo mensaje de error
                if (message) {
                    const errorDiv = createErrorElement();
                    errorDiv.textContent = message;
                    input.parentNode.appendChild(errorDiv);
                    input.classList.add('border-red-500');
                    input.classList.remove('border-green-500');
                } else {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                }
            }

            function validateInput(input, validationRules) {
                const value = input.value.trim();

                for (const validation of validationRules) {
                    if (!validation.isValid(value)) {
                        showInputError(input, validation.message);
                        return false;
                    }
                }

                showInputError(input, ''); // Limpiar error si es válido
                return true;
            }

            // Validación en tiempo real para cada input
            Object.entries(inputs).forEach(([key, input]) => {
                if (key !== 'confirmacion' && key !== 'terminos') {
                    input.addEventListener('input', () => {
                        validateInput(input, validations[key]);
                    });

                    // También validar cuando el campo pierde el foco
                    input.addEventListener('blur', () => {
                        validateInput(input, validations[key]);
                    });
                }
            });

            // Validación especial para confirmación de contraseña
            inputs.confirmacion.addEventListener('input', () => {
                const isValid = inputs.confirmacion.value === inputs.contrasena.value;
                showInputError(
                    inputs.confirmacion,
                    isValid ? '' : validationMessages.password.mismatch
                );
            });

            // También validar confirmación cuando pierde el foco
            inputs.confirmacion.addEventListener('blur', () => {
                const isValid = inputs.confirmacion.value === inputs.contrasena.value;
                showInputError(
                    inputs.confirmacion,
                    isValid ? '' : validationMessages.password.mismatch
                );
            });

            // Validación de términos y condiciones
            inputs.terminos.addEventListener('change', () => {
                showInputError(
                    inputs.terminos,
                    inputs.terminos.checked ? '' : validationMessages.terms.required
                );
            });

            // Validación al enviar el formulario
            form.addEventListener('submit', (e) => {
                let isValid = true;

                // Validar todos los campos
                Object.entries(inputs).forEach(([key, input]) => {
                    if (key !== 'confirmacion' && key !== 'terminos') {
                        if (!validateInput(input, validations[key])) {
                            isValid = false;
                        }
                    }
                });

                // Validar confirmación de contraseña
                if (inputs.confirmacion.value !== inputs.contrasena.value) {
                    isValid = false;
                    showInputError(inputs.confirmacion, validationMessages.password.mismatch);
                }

                // Validar términos y condiciones
                if (!inputs.terminos.checked) {
                    isValid = false;
                    showInputError(inputs.terminos, validationMessages.terms.required);
                }

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: validationMessages.validation_error,
                        text: validationMessages.fix_errors,
                        confirmButtonText: '<?php echo e(__('messages.confirm')); ?>'
                    });
                }
            });

            // Formateo automático del teléfono
            inputs.telefono.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) value = value.slice(0, 10);
                e.target.value = value;
            });

            // Configurar los toggles de contraseña
            if (typeof setupPasswordToggles === 'function') {
                setupPasswordToggles();
            }

            // Indicador de fortaleza de contraseña
            const passwordInput = document.getElementById('contrasena_register');
            const passwordStrength = document.getElementById('password-strength');

            if (passwordInput && passwordStrength) {
                console.log('Setting up password strength indicator');

                // Mostrar siempre el indicador de fortaleza
                passwordStrength.style.display = 'block';

                // Mostrar estado inicial
                console.log('Calling checkPasswordStrength with empty string');
                checkPasswordStrength('');

                passwordInput.addEventListener('input', (e) => {
                    const password = e.target.value;
                    console.log('Password input changed:', password);
                    checkPasswordStrength(password);
                });

                passwordInput.addEventListener('focus', () => {
                    // Asegurar que esté visible al enfocar
                    passwordStrength.style.display = 'block';
                });
            } else {
                console.log('Password input or strength indicator not found:', {
                    passwordInput,
                    passwordStrength
                });
            }
        });

        // Las funciones de contraseña ahora están en password-utils.js

        // Auto-hide para la alerta de éxito
        document.addEventListener('DOMContentLoaded', function() {
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                // Auto-hide después de 5 segundos
                setTimeout(() => {
                    successAlert.style.animation = 'fade-out 0.5s ease-in forwards';
                    setTimeout(() => {
                        successAlert.remove();
                    }, 500);
                }, 5000);
            }
        });
    </script>


    <script>
        function toggleForms() {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            loginForm.classList.toggle('flipped');
            loginForm.classList.toggle('opacity-0');
            registerForm.classList.toggle('flipped');
            registerForm.classList.toggle('opacity-0');

            setTimeout(() => {
                if (loginForm.classList.contains('flipped')) {
                    loginForm.style.zIndex = '0';
                    registerForm.style.zIndex = '10';
                } else {
                    loginForm.style.zIndex = '10';
                    registerForm.style.zIndex = '0';
                }
            }, 250);
        }
    </script>

</body>

</html>
<?php /**PATH C:\Users\osman\OneDrive\Documentos\GitHub\4GMovil\resources\views/modules/auth/login.blade.php ENDPATH**/ ?>