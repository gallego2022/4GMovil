<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Optimization Settings
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for optimizing the application
    | performance, including caching, compression, and asset optimization.
    |
    */

    'cache' => [
        'enabled' => env('CACHE_ENABLED', true),
        'ttl' => env('CACHE_TTL', 3600), // 1 hour
        'prefix' => env('CACHE_PREFIX', '4gmovil_'),
    ],

    'compression' => [
        'enabled' => env('COMPRESSION_ENABLED', true),
        'gzip' => env('GZIP_ENABLED', true),
        'brotli' => env('BROTLI_ENABLED', false),
    ],

    'assets' => [
        'minify' => env('ASSETS_MINIFY', true),
        'combine' => env('ASSETS_COMBINE', true),
        'version' => env('ASSETS_VERSION', true),
        'cdn' => [
            'enabled' => env('CDN_ENABLED', false),
            'url' => env('CDN_URL', ''),
        ],
    ],

    'database' => [
        'query_cache' => env('DB_QUERY_CACHE', true),
        'connection_pooling' => env('DB_CONNECTION_POOLING', false),
        'slow_query_log' => env('DB_SLOW_QUERY_LOG', false),
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000), // milliseconds
    ],

    'session' => [
        'optimize' => env('SESSION_OPTIMIZE', true),
        'lifetime' => env('SESSION_LIFETIME', 120), // minutes
        'secure' => env('SESSION_SECURE', false),
        'http_only' => env('SESSION_HTTP_ONLY', true),
    ],

    'queue' => [
        'optimize' => env('QUEUE_OPTIMIZE', true),
        'batch_size' => env('QUEUE_BATCH_SIZE', 100),
        'timeout' => env('QUEUE_TIMEOUT', 60), // seconds
    ],

    'logging' => [
        'optimize' => env('LOGGING_OPTIMIZE', true),
        'max_files' => env('LOGGING_MAX_FILES', 30),
        'level' => env('LOGGING_LEVEL', 'info'),
    ],

    'mail' => [
        'queue' => env('MAIL_QUEUE', true),
        'batch_size' => env('MAIL_BATCH_SIZE', 50),
    ],

    'frontend' => [
        'lazy_loading' => env('FRONTEND_LAZY_LOADING', true),
        'image_optimization' => env('FRONTEND_IMAGE_OPTIMIZATION', true),
        'critical_css' => env('FRONTEND_CRITICAL_CSS', true),
        'service_worker' => env('FRONTEND_SERVICE_WORKER', false),
    ],
]; 