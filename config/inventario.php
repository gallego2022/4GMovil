<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Alertas de Inventario
    |--------------------------------------------------------------------------
    |
    | Aquí puedes configurar los umbrales para las diferentes alertas
    | de stock en el sistema de inventario.
    |
    */

    'alertas' => [
        /*
        |--------------------------------------------------------------------------
        | Umbrales de Stock - Alta Rotación
        |--------------------------------------------------------------------------
        |
        | Configuración optimizada para productos de alta rotación
        | (electrónicos, consumibles, productos que se venden rápido)
        |
        */
        'stock_bajo' => env('INVENTARIO_STOCK_BAJO', 0.6), // 60% del stock mínimo
        'stock_critico' => env('INVENTARIO_STOCK_CRITICO', 0.2), // 20% del stock mínimo
        
        /*
        |--------------------------------------------------------------------------
        | Configuración de Notificaciones
        |--------------------------------------------------------------------------
        |
        | Configuración para las notificaciones automáticas
        |
        */
        'notificaciones' => [
            'enabled' => env('INVENTARIO_NOTIFICACIONES', true),
            'email_admins' => env('INVENTARIO_EMAIL_ADMINS', true),
            'frecuencia_verificacion' => env('INVENTARIO_FRECUENCIA', 'daily'), // daily, weekly, monthly
        ],
        
        /*
        |--------------------------------------------------------------------------
        | Configuración de Reportes
        |--------------------------------------------------------------------------
        |
        | Configuración para los reportes de inventario
        |
        */
        'reportes' => [
            'incluir_productos_inactivos' => env('INVENTARIO_REPORTES_INACTIVOS', false),
            'limite_productos_mas_vendidos' => env('INVENTARIO_LIMITE_VENDIDOS', 10),
            'periodo_analisis_meses' => env('INVENTARIO_PERIODO_ANALISIS', 1),
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Configuración de Movimientos
    |--------------------------------------------------------------------------
    |
    | Configuración para el registro de movimientos de inventario
    |
    */
    'movimientos' => [
        'tipos' => [
            'entrada' => 'Entrada de stock',
            'salida' => 'Salida de stock',
            'ajuste_positivo' => 'Ajuste positivo',
            'ajuste_negativo' => 'Ajuste negativo',
            'devolucion' => 'Devolución',
        ],
        'requerir_motivo' => env('INVENTARIO_REQUERIR_MOTIVO', true),
        'registrar_usuario' => env('INVENTARIO_REGISTRAR_USUARIO', true),
    ],
]; 