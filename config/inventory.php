<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración del Sistema de Inventario
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar las opciones del sistema de inventario
    | incluyendo alertas, umbrales y notificaciones.
    |
    */

    'stock_alerts' => [
        /*
        |--------------------------------------------------------------------------
        | Emails para Alertas de Stock
        |--------------------------------------------------------------------------
        |
        | Lista de emails que recibirán las alertas de stock bajo/agotado.
        | Puedes usar un array o un string separado por comas.
        |
        */
        'emails' => env('INVENTORY_ALERT_EMAILS', 'osmandavidgallego@gmail.com'),

        /*
        |--------------------------------------------------------------------------
        | Umbrales de Stock para Variantes
        |--------------------------------------------------------------------------
        |
        | Porcentajes del stock mínimo para determinar el nivel de alerta.
        |
        */
        'umbrales' => [
            'critico' => env('INVENTORY_CRITICAL_THRESHOLD', 20), // ≤20% del mínimo
            'bajo' => env('INVENTORY_LOW_THRESHOLD', 60), // ≤60% del mínimo
        ],

        /*
        |--------------------------------------------------------------------------
        | Configuración de Notificaciones
        |--------------------------------------------------------------------------
        |
        */
        'notificaciones' => [
            'habilitadas' => env('INVENTORY_NOTIFICATIONS_ENABLED', true),
            'frecuencia_maxima' => env('INVENTORY_MAX_FREQUENCY', 24), // horas
            'incluir_recomendaciones' => env('INVENTORY_INCLUDE_RECOMMENDATIONS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Reservas de Stock
    |--------------------------------------------------------------------------
    |
    */
    'reservas' => [
        'tiempo_expiracion' => env('STOCK_RESERVATION_EXPIRY', 30), // minutos
        'limpiar_automaticamente' => env('AUTO_CLEANUP_RESERVATIONS', true),
        'max_reservas_usuario' => env('MAX_USER_RESERVATIONS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Reportes
    |--------------------------------------------------------------------------
    |
    */
    'reportes' => [
        'incluir_variantes' => env('INCLUDE_VARIANTS_IN_REPORTS', true),
        'mostrar_movimientos' => env('SHOW_MOVEMENTS_IN_REPORTS', true),
        'exportar_excel' => env('EXPORT_TO_EXCEL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Dashboard
    |--------------------------------------------------------------------------
    |
    */
    'dashboard' => [
        'mostrar_alertas' => env('SHOW_ALERTS_IN_DASHBOARD', true),
        'limite_alertas' => env('DASHBOARD_ALERTS_LIMIT', 10),
        'actualizar_automaticamente' => env('AUTO_REFRESH_DASHBOARD', true),
        'intervalo_actualizacion' => env('DASHBOARD_REFRESH_INTERVAL', 300), // segundos
    ],
];
