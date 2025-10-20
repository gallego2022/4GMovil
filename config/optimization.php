<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Cloud Performance Optimization
    |--------------------------------------------------------------------------
    |
    | Configuraciones específicas para optimizar el rendimiento en Laravel Cloud.
    | Estas configuraciones están diseñadas para maximizar la velocidad de carga.
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
        'default' => env('CACHE_DRIVER', 'redis'),
        'stores' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'cache',
                'prefix' => env('CACHE_PREFIX', '4gmovil_cache'),
            ],
            'file' => [
                'driver' => 'file',
                'path' => storage_path('framework/cache/data'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de sesiones optimizada para Laravel Cloud.
    |
    */

    'session' => [
        'driver' => env('SESSION_DRIVER', 'redis'),
        'lifetime' => env('SESSION_LIFETIME', 1440),
        'encrypt' => env('SESSION_ENCRYPT', true),
        'files' => storage_path('framework/sessions'),
        'connection' => 'session',
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => env('SESSION_COOKIE', '4gmovil_session'),
        'path' => '/',
        'domain' => env('SESSION_DOMAIN', null),
        'secure' => env('SESSION_SECURE_COOKIE', true),
        'http_only' => true,
        'same_site' => 'lax',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de colas optimizada para Laravel Cloud.
    |
    */

    'queue' => [
        'default' => env('QUEUE_CONNECTION', 'redis'),
        'connections' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'default',
                'queue' => env('REDIS_QUEUE', 'default'),
                'retry_after' => 90,
                'block_for' => null,
            ],
        ],
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
        'compiled_path' => env('VIEW_COMPILED_PATH', '/tmp/views'),
        'cache_enabled' => env('VIEW_CACHE_ENABLED', true),
        'debug' => env('VIEW_DEBUG', false),
        'ttl' => env('VIEW_CACHE_TTL', 7200),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de base de datos optimizada para Laravel Cloud.
    |
    */

    'database' => [
        'default' => env('DB_CONNECTION', 'mysql'),
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', '4gmovil'),
                'username' => env('DB_USERNAME', 'laravel'),
                'password' => env('DB_PASSWORD', 'password'),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]) : [],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Configuraciones de rendimiento específicas para Laravel Cloud.
    |
    */

    'performance' => [
        'enable_gzip' => true,
        'enable_brotli' => true,
        'minify_html' => true,
        'minify_css' => true,
        'minify_js' => true,
        'optimize_images' => true,
        'lazy_loading' => true,
        'preload_critical_resources' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de CDN para assets estáticos.
    |
    */

    'cdn' => [
        'enabled' => env('CDN_ENABLED', false),
        'url' => env('CDN_URL', ''),
        'assets_path' => env('CDN_ASSETS_PATH', 'assets'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de monitoreo de rendimiento.
    |
    */

    'monitoring' => [
        'enabled' => env('MONITORING_ENABLED', true),
        'log_slow_queries' => env('LOG_SLOW_QUERIES', true),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 1000), // ms
        'log_memory_usage' => env('LOG_MEMORY_USAGE', true),
        'memory_threshold' => env('MEMORY_THRESHOLD', 128), // MB
    ],
];