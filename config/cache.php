<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same driver to group types of items stored in your caches.
    |
    | Supported drivers: "apc", "array", "database", "file",
    |         "memcached", "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, or DynamoDB cache
    | stores there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env(
        'CACHE_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'
    ),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (Time To Live)
    |--------------------------------------------------------------------------
    |
    | Configure the default TTL for cached items. This value is used when
    | no specific TTL is provided when storing items in the cache.
    |
    */

    'ttl' => env('CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Asset Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching static assets like CSS, JS, and images.
    | These settings control how long assets are cached by browsers.
    |
    */

    'assets' => [
        'css' => [
            'ttl' => 31536000, // 1 año en segundos
            'headers' => [
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Expires' => 'Thu, 31 Dec 2026 23:59:59 GMT',
            ],
        ],
        'js' => [
            'ttl' => 31536000, // 1 año en segundos
            'headers' => [
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Expires' => 'Thu, 31 Dec 2026 23:59:59 GMT',
            ],
        ],
        'images' => [
            'ttl' => 31536000, // 1 año en segundos
            'headers' => [
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Expires' => 'Thu, 31 Dec 2026 23:59:59 GMT',
            ],
        ],
        'fonts' => [
            'ttl' => 31536000, // 1 año en segundos
            'headers' => [
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'Expires' => 'Thu, 31 Dec 2026 23:59:59 GMT',
            ],
        ],
    ],

];
