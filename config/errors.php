<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Manejo de Errores
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar cómo se manejan los errores en tu aplicación.
    | Esto incluye logging, notificaciones y respuestas de error.
    |
    */

    'logging' => [
        'enabled' => env('ERROR_LOGGING_ENABLED', true),
        'level' => env('ERROR_LOG_LEVEL', 'error'),
        'channels' => [
            'database' => env('ERROR_LOG_DATABASE_CHANNEL', 'daily'),
            'file' => env('ERROR_LOG_FILE_CHANNEL', 'daily'),
            'slack' => env('ERROR_LOG_SLACK_CHANNEL', false),
        ],
    ],

    'notifications' => [
        'enabled' => env('ERROR_NOTIFICATIONS_ENABLED', false),
        'channels' => [
            'email' => env('ERROR_NOTIFY_EMAIL', false),
            'slack' => env('ERROR_NOTIFY_SLACK', false),
        ],
        'recipients' => [
            'email' => env('ERROR_NOTIFY_EMAIL_RECIPIENTS', ''),
            'slack' => env('ERROR_NOTIFY_SLACK_WEBHOOK', ''),
        ],
    ],

    'display' => [
        'show_details' => env('APP_DEBUG', false),
        'show_trace' => env('ERROR_SHOW_TRACE', false),
        'show_suggestions' => env('ERROR_SHOW_SUGGESTIONS', true),
        'user_friendly_messages' => env('ERROR_USER_FRIENDLY_MESSAGES', true),
    ],

    'database_errors' => [
        'detailed_logging' => true,
        'user_messages' => [
            'missing_column' => 'Error de configuración: Columna no encontrada en la base de datos',
            'missing_table' => 'Error de configuración: Tabla no encontrada en la base de datos',
            'foreign_key_constraint' => 'Error de integridad: Referencia de datos no válida',
            'sql_syntax' => 'Error de sintaxis en la consulta de base de datos',
            'connection_error' => 'Error de conexión con la base de datos',
            'general' => 'Error en la base de datos',
        ],
        'suggestions' => [
            'missing_column' => 'Ejecutar: php artisan migrate',
            'missing_table' => 'Ejecutar: php artisan migrate',
            'foreign_key_constraint' => 'Verificar que los datos referenciados existan',
            'sql_syntax' => 'Revisar el código del controlador',
            'connection_error' => 'Verificar configuración de .env',
            'general' => 'Revisar logs del sistema',
        ],
    ],

    'validation_errors' => [
        'detailed_logging' => true,
        'user_messages' => [
            'required' => 'Este campo es obligatorio',
            'email' => 'El formato del correo electrónico no es válido',
            'min' => 'Este campo debe tener al menos :min caracteres',
            'max' => 'Este campo no puede exceder :max caracteres',
            'unique' => 'Este valor ya existe en el sistema',
            'numeric' => 'Este campo debe ser un número',
            'date' => 'Este campo debe ser una fecha válida',
        ],
        'suggestions' => [
            'email' => 'Usar formato: ejemplo@dominio.com',
            'password' => 'Mínimo 8 caracteres, incluir mayúsculas y números',
            'phone' => 'Usar formato: +57 300 123 4567',
        ],
    ],

    'authentication_errors' => [
        'detailed_logging' => true,
        'user_messages' => [
            'unauthenticated' => 'Debes iniciar sesión para acceder a este recurso',
            'unauthorized' => 'No tienes permisos para acceder a este recurso',
            'invalid_credentials' => 'Credenciales inválidas',
            'account_locked' => 'Tu cuenta ha sido bloqueada',
            'session_expired' => 'Tu sesión ha expirado',
        ],
        'suggestions' => [
            'unauthenticated' => 'Iniciar sesión o crear una cuenta',
            'unauthorized' => 'Contactar al administrador del sistema',
            'invalid_credentials' => 'Verificar usuario y contraseña',
            'account_locked' => 'Contactar al administrador del sistema',
            'session_expired' => 'Iniciar sesión nuevamente',
        ],
    ],

    'rate_limiting' => [
        'enabled' => env('ERROR_RATE_LIMITING_ENABLED', true),
        'max_attempts' => env('ERROR_RATE_LIMITING_MAX_ATTEMPTS', 5),
        'decay_minutes' => env('ERROR_RATE_LIMITING_DECAY_MINUTES', 1),
        'user_message' => 'Demasiados intentos. Intenta nuevamente en :minutes minutos.',
    ],

    'monitoring' => [
        'enabled' => env('ERROR_MONITORING_ENABLED', false),
        'thresholds' => [
            'critical_errors_per_hour' => env('ERROR_CRITICAL_THRESHOLD', 10),
            'database_errors_per_hour' => env('ERROR_DATABASE_THRESHOLD', 5),
            'validation_errors_per_hour' => env('ERROR_VALIDATION_THRESHOLD', 20),
        ],
        'actions' => [
            'notify_admin' => env('ERROR_NOTIFY_ADMIN_ON_THRESHOLD', true),
            'log_alert' => env('ERROR_LOG_ALERT_ON_THRESHOLD', true),
            'disable_feature' => env('ERROR_DISABLE_FEATURE_ON_THRESHOLD', false),
        ],
    ],
];
