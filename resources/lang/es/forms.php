<?php

return [
    // Mensajes generales de formulario
    'required_field' => 'Este campo es obligatorio',
    'invalid_format' => 'El formato no es válido',
    'fix_errors' => 'Por favor, corrige los errores antes de continuar',
    'validation_error' => 'Error de validación',
    'success' => 'Operación exitosa',
    'error' => 'Ha ocurrido un error',
    'loading' => 'Cargando...',
    'processing' => 'Procesando solicitud...',
    'confirm_action' => '¿Estás seguro de realizar esta acción?',
    'yes' => 'Sí',
    'no' => 'No',
    'cancel' => 'Cancelar',
    'save' => 'Guardar',
    'close' => 'Cerrar',
    'back' => 'Volver',
    'continue' => 'Continuar',

    // Mensajes de estado
    'status' => [
        'success' => 'Operación completada con éxito',
        'error' => 'Ha ocurrido un error al procesar la solicitud',
        'warning' => 'Hay algunos problemas que requieren tu atención',
        'info' => 'Información importante',
        'loading' => 'Por favor espera...',
        'saving' => 'Guardando cambios...',
        'updating' => 'Actualizando información...',
        'deleting' => 'Eliminando...',
        'session_expired' => 'Tu sesión ha expirado, por favor vuelve a iniciar sesión',
    ],

    // Mensajes específicos de campos
    'name' => [
        'required' => 'El nombre es obligatorio',
        'format' => 'El nombre debe tener entre 2 y 25 caracteres y solo puede contener letras',
        'invalid_chars' => 'El nombre contiene caracteres no permitidos',
        'too_short' => 'El nombre es demasiado corto (mínimo 2 caracteres)',
        'too_long' => 'El nombre es demasiado largo (máximo 25 caracteres)',
    ],
    'email' => [
        'required' => 'El correo electrónico es obligatorio',
        'format' => 'El formato del correo electrónico no es válido',
        'unique' => 'Este correo electrónico ya está registrado',
        'not_found' => 'No se encontró ninguna cuenta con este correo electrónico',
        'verified' => 'Correo electrónico verificado correctamente',
        'verification_sent' => 'Se ha enviado un correo de verificación',
    ],
    'phone' => [
        'required' => 'El teléfono es obligatorio',
        'format' => 'El teléfono debe tener exactamente 10 dígitos',
        'invalid' => 'El número de teléfono no es válido',
        'unique' => 'Este número de teléfono ya está registrado',
    ],
    'password' => [
        'required' => 'La contraseña es obligatoria',
        'min_length' => 'La contraseña debe tener al menos 8 caracteres',
        'uppercase' => 'La contraseña debe contener al menos una mayúscula',
        'lowercase' => 'La contraseña debe contener al menos una minúscula',
        'number' => 'La contraseña debe contener al menos un número',
        'symbol' => 'La contraseña debe contener al menos un símbolo',
        'mismatch' => 'Las contraseñas no coinciden',
        'current_wrong' => 'La contraseña actual no es correcta',
        'recently_used' => 'No puedes usar una contraseña que hayas usado recientemente',
        'requirements' => 'La contraseña debe cumplir con los siguientes requisitos:',
        'strength' => [
            'weak' => 'Débil',
            'medium' => 'Media',
            'strong' => 'Fuerte',
            'very_strong' => 'Muy fuerte',
        ],
    ],
    'terms' => [
        'required' => 'Debes aceptar los términos y condiciones',
        'updated' => 'Los términos y condiciones han sido actualizados',
        'read_more' => 'Leer más',
    ],

    // Mensajes de autenticación
    'auth' => [
        'welcome' => '¡Bienvenido de nuevo!',
        'goodbye' => '¡Hasta pronto!',
        'login_success' => 'Has iniciado sesión correctamente',
        'login_error' => 'Error al iniciar sesión',
        'logout_success' => 'Has cerrado sesión correctamente',
        'invalid_credentials' => 'Las credenciales proporcionadas no son correctas',
        'account_locked' => 'Tu cuenta ha sido bloqueada temporalmente',
        'too_many_attempts' => 'Demasiados intentos fallidos. Por favor, intenta de nuevo en :minutes minutos',
        'remember_me' => 'Recordar mi sesión',
        'forgot_password' => '¿Olvidaste tu contraseña?',
        'reset_password' => 'Restablecer contraseña',
        'reset_password_success' => 'Tu contraseña ha sido restablecida correctamente',
    ],

    // Mensajes de registro
    'register' => [
        'success' => 'Registro completado exitosamente',
        'error' => 'Error al completar el registro',
        'verification_required' => 'Por favor, verifica tu correo electrónico',
        'already_registered' => 'Ya tienes una cuenta registrada',
        'complete_profile' => 'Completa tu perfil',
    ],

    // Mensajes de perfil
    'profile' => [
        'updated' => 'Perfil actualizado correctamente',
        'update_error' => 'Error al actualizar el perfil',
        'photo_updated' => 'Foto de perfil actualizada',
        'photo_error' => 'Error al actualizar la foto de perfil',
        'delete_account' => 'Eliminar cuenta',
        'delete_confirm' => '¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer',
    ],

    // Mensajes de errores comunes
    'errors' => [
        'default' => 'Ha ocurrido un error inesperado',
        'connection' => 'Error de conexión',
        'timeout' => 'La solicitud ha excedido el tiempo de espera',
        'validation' => 'Por favor, verifica los datos ingresados',
        'server' => 'Error en el servidor',
        'not_found' => 'No encontrado',
        'forbidden' => 'Acceso denegado',
        'unauthorized' => 'No autorizado',
    ],
]; 