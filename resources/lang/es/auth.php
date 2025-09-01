<?php

return [
    // Mensajes de autenticación
    'login' => 'Iniciar Sesión',
    'register' => 'Crear Cuenta',
    'logout' => 'Cerrar Sesión',
    'remember_me' => 'Mantener sesión activa',
    'forgot_password' => '¿Olvidaste tu contraseña?',
    'reset_password' => 'Restablecer Contraseña',
    'send_reset_link' => 'Enviar Enlace de Restablecimiento',
    'click_reset_link' => 'Haz clic aquí para restablecer tu contraseña',
    'verify_email' => 'Verificar Correo Electrónico',
    'verify_email_sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.',
    'verify_email_notice' => 'Antes de continuar, por favor verifica tu correo electrónico con el enlace que te enviamos.',
    'verify_email_success' => 'Has verificado tu correo electrónico correctamente.',

    // Mensajes de contraseña
    'change_password' => 'Cambiar Contraseña',
    'change_password_description' => 'Por favor, ingresa tu nueva contraseña para continuar.',
    'current_password' => 'Contraseña Actual',
    'new_password' => 'Nueva Contraseña',
    'confirm_password' => 'Confirmar Contraseña',
    'password_requirements' => 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.',
    'password_current_wrong' => 'La contraseña actual no es correcta.',
    'password_change_success' => 'Tu contraseña ha sido cambiada exitosamente.',
    'password_change_error' => 'Ha ocurrido un error al cambiar la contraseña.',

    // Mensajes de éxito/error
    'login_success' => '¡Bienvenido de vuelta! Has iniciado sesión correctamente.',
    'login_error' => 'El correo electrónico o la contraseña no son correctos. Por favor, verifica tus datos.',
    'login_error_google_account' => 'Esta cuenta fue creada con Google. Por favor, inicia sesión usando el botón "Continuar con Google" o establece una contraseña desde tu perfil.',
    'account_inactive' => 'Tu cuenta está inactiva. Por favor, contacta al administrador para activarla.',
    'register_success' => '¡Cuenta creada exitosamente! Por favor verifica tu correo electrónico para activar tu cuenta.',
    'register_success_title' => '¡Registro Exitoso!',
    'logout_success' => 'Has cerrado sesión correctamente. ¡Hasta pronto!',
    'welcome' => '¡Bienvenido a 4GMovil!',
    'goodbye' => '¡Gracias por usar 4GMovil!',

    // Mensajes de validación
    'validation' => [
        'nombre_usuario' => [
            'required' => 'El nombre de usuario es obligatorio.',
            'max' => 'El nombre de usuario no puede tener más de :max caracteres.',
            'regex' => 'El nombre de usuario solo puede contener letras y espacios.',
        ],
        'correo_electronico' => [
            'required' => 'El correo electrónico es obligatorio.',
            'email' => 'Por favor, ingresa un correo electrónico válido.',
            'unique' => 'Este correo electrónico ya está registrado en nuestro sistema.',
        ],
        'telefono' => [
            'required' => 'El número de teléfono es obligatorio.',
            'regex' => 'El teléfono debe tener 10 dígitos y comenzar con 3.',
        ],
        'contrasena' => [
            'required' => 'La contraseña es obligatoria.',
            'min' => 'La contraseña debe tener al menos :min caracteres.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'regex' => 'La contraseña debe cumplir con los requisitos de seguridad.',
        ],
        'acepta_terminos' => [
            'accepted' => 'Debes aceptar los términos y condiciones para continuar.',
        ],
    ],

    // Mensajes de verificación
    'verification' => [
        'required' => 'Por favor, verifica tu correo electrónico antes de continuar.',
        'sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo electrónico.',
        'verified' => 'Tu correo electrónico ha sido verificado correctamente.',
        'already_verified' => 'Tu correo electrónico ya ha sido verificado.',
    ],

    // Mensajes de seguridad
    'security' => [
        'password_requirements' => 'Tu contraseña debe cumplir con los requisitos de seguridad para proteger tu cuenta.',
        'account_locked' => 'Tu cuenta ha sido bloqueada temporalmente por múltiples intentos fallidos.',
        'too_many_attempts' => 'Demasiados intentos fallidos. Por favor, intenta nuevamente en :minutes minutos.',
        'session_expired' => 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
    ],
]; 