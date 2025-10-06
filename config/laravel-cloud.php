<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Cloud Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración específica para el despliegue en Laravel Cloud.
    | Este archivo contiene configuraciones optimizadas para el entorno de producción.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de caché optimizada para Laravel Cloud.
    |
    */

    'cache' => [
        'driver' => env('CACHE_DRIVER', 'file'),
        'path' => env('CACHE_PATH', storage_path('framework/cache/data')),
        'ttl' => env('CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | View Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de vistas optimizada para Laravel Cloud.
    |
    */

    'views' => [
        'compiled_path' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),
        'cache_enabled' => env('VIEW_CACHE_ENABLED', true),
        'debug' => env('VIEW_DEBUG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de almacenamiento para Laravel Cloud.
    |
    */

    'storage' => [
        'public_path' => public_path('storage'),
        'framework_path' => storage_path('framework'),
        'cache_path' => storage_path('framework/cache'),
        'sessions_path' => storage_path('framework/sessions'),
        'views_path' => storage_path('framework/views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones de rendimiento para Laravel Cloud.
    |
    */

    'performance' => [
        'optimize_autoloader' => true,
        'cache_config' => true,
        'cache_routes' => true,
        'cache_views' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Deployment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones específicas para el despliegue.
    |
    */

    'deployment' => [
        'create_directories' => true,
        'set_permissions' => true,
        'clear_cache' => true,
        'optimize' => true,
    ],

];
