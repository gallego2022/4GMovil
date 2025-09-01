<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Refactoring
    |--------------------------------------------------------------------------
    |
    | Este archivo contiene configuraciones específicas para el proceso
    | de refactoring del proyecto.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Configuración de Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('REFACTORING_LOGGING_ENABLED', true),
        'level' => env('REFACTORING_LOG_LEVEL', 'info'),
        'channels' => [
            'refactoring' => env('REFACTORING_LOG_CHANNEL', 'daily'),
        ],
        'max_files' => env('REFACTORING_LOG_MAX_FILES', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Caché
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('REFACTORING_CACHE_ENABLED', true),
        'default_ttl' => env('REFACTORING_CACHE_DEFAULT_TTL', 3600),
        'prefix' => env('REFACTORING_CACHE_PREFIX', 'refactoring'),
        'stores' => [
            'products' => [
                'ttl' => env('REFACTORING_CACHE_PRODUCTS_TTL', 1800),
                'enabled' => env('REFACTORING_CACHE_PRODUCTS_ENABLED', true),
            ],
            'inventory' => [
                'ttl' => env('REFACTORING_CACHE_INVENTORY_TTL', 300),
                'enabled' => env('REFACTORING_CACHE_INVENTORY_ENABLED', true),
            ],
            'users' => [
                'ttl' => env('REFACTORING_CACHE_USERS_TTL', 3600),
                'enabled' => env('REFACTORING_CACHE_USERS_ENABLED', true),
            ],
            'search' => [
                'ttl' => env('REFACTORING_CACHE_SEARCH_TTL', 7200),
                'enabled' => env('REFACTORING_CACHE_SEARCH_ENABLED', true),
            ],
            'reports' => [
                'ttl' => env('REFACTORING_CACHE_REPORTS_TTL', 3600),
                'enabled' => env('REFACTORING_CACHE_REPORTS_ENABLED', true),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Validación
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'enabled' => env('REFACTORING_VALIDATION_ENABLED', true),
        'strict_mode' => env('REFACTORING_VALIDATION_STRICT', false),
        'custom_messages' => env('REFACTORING_VALIDATION_CUSTOM_MESSAGES', true),
        'languages' => [
            'default' => env('REFACTORING_VALIDATION_LANG', 'es'),
            'fallback' => 'en',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de API
    |--------------------------------------------------------------------------
    */
    'api' => [
        'version' => env('REFACTORING_API_VERSION', 'v1'),
        'rate_limiting' => [
            'enabled' => env('REFACTORING_API_RATE_LIMITING', true),
            'default_limit' => env('REFACTORING_API_RATE_LIMIT', 60),
            'default_decay' => env('REFACTORING_API_RATE_DECAY', 60),
        ],
        'response_format' => [
            'include_timestamp' => env('REFACTORING_API_INCLUDE_TIMESTAMP', true),
            'include_request_id' => env('REFACTORING_API_INCLUDE_REQUEST_ID', true),
            'include_metadata' => env('REFACTORING_API_INCLUDE_METADATA', true),
        ],
        'pagination' => [
            'default_per_page' => env('REFACTORING_API_DEFAULT_PER_PAGE', 15),
            'max_per_page' => env('REFACTORING_API_MAX_PER_PAGE', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Testing
    |--------------------------------------------------------------------------
    */
    'testing' => [
        'enabled' => env('REFACTORING_TESTING_ENABLED', true),
        'coverage_threshold' => env('REFACTORING_TESTING_COVERAGE', 80),
        'parallel' => env('REFACTORING_TESTING_PARALLEL', false),
        'databases' => [
            'testing' => env('REFACTORING_TESTING_DB', 'sqlite'),
            'seeders' => env('REFACTORING_TESTING_SEEDERS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Performance
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'monitoring' => [
            'enabled' => env('REFACTORING_PERFORMANCE_MONITORING', true),
            'threshold' => env('REFACTORING_PERFORMANCE_THRESHOLD', 1.0),
        ],
        'optimization' => [
            'eager_loading' => env('REFACTORING_OPTIMIZATION_EAGER_LOADING', true),
            'query_optimization' => env('REFACTORING_OPTIMIZATION_QUERIES', true),
            'cache_optimization' => env('REFACTORING_OPTIMIZATION_CACHE', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Seguridad
    |--------------------------------------------------------------------------
    */
    'security' => [
        'logging' => [
            'enabled' => env('REFACTORING_SECURITY_LOGGING', true),
            'sensitive_fields' => [
                'password', 'password_confirmation', 'token', 'api_key',
                'secret', 'credit_card', 'cvv', 'ssn', 'dni'
            ],
        ],
        'validation' => [
            'strict_input' => env('REFACTORING_SECURITY_STRICT_INPUT', true),
            'sanitize_output' => env('REFACTORING_SECURITY_SANITIZE_OUTPUT', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Monitoreo
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => env('REFACTORING_MONITORING_ENABLED', true),
        'metrics' => [
            'response_time' => env('REFACTORING_MONITORING_RESPONSE_TIME', true),
            'error_rate' => env('REFACTORING_MONITORING_ERROR_RATE', true),
            'throughput' => env('REFACTORING_MONITORING_THROUGHPUT', true),
        ],
        'alerts' => [
            'enabled' => env('REFACTORING_MONITORING_ALERTS', true),
            'channels' => [
                'email' => env('REFACTORING_MONITORING_EMAIL_ALERTS', false),
                'slack' => env('REFACTORING_MONITORING_SLACK_ALERTS', false),
                'webhook' => env('REFACTORING_MONITORING_WEBHOOK_ALERTS', false),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Migración
    |--------------------------------------------------------------------------
    */
    'migration' => [
        'enabled' => env('REFACTORING_MIGRATION_ENABLED', true),
        'batch_size' => env('REFACTORING_MIGRATION_BATCH_SIZE', 1000),
        'timeout' => env('REFACTORING_MIGRATION_TIMEOUT', 300),
        'rollback' => [
            'enabled' => env('REFACTORING_MIGRATION_ROLLBACK', true),
            'max_steps' => env('REFACTORING_MIGRATION_MAX_ROLLBACK_STEPS', 10),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Documentación
    |--------------------------------------------------------------------------
    */
    'documentation' => [
        'enabled' => env('REFACTORING_DOCUMENTATION_ENABLED', true),
        'auto_generate' => env('REFACTORING_DOCUMENTATION_AUTO_GENERATE', true),
        'formats' => [
            'markdown' => env('REFACTORING_DOCUMENTATION_MARKDOWN', true),
            'api_docs' => env('REFACTORING_DOCUMENTATION_API_DOCS', true),
            'code_comments' => env('REFACTORING_DOCUMENTATION_CODE_COMMENTS', true),
        ],
        'paths' => [
            'base' => env('REFACTORING_DOCUMENTATION_BASE_PATH', 'docs'),
            'api' => env('REFACTORING_DOCUMENTATION_API_PATH', 'docs/api'),
            'code' => env('REFACTORING_DOCUMENTATION_CODE_PATH', 'docs/code'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Deployment
    |--------------------------------------------------------------------------
    */
    'deployment' => [
        'staging' => [
            'enabled' => env('REFACTORING_DEPLOYMENT_STAGING', true),
            'url' => env('REFACTORING_DEPLOYMENT_STAGING_URL'),
            'database' => env('REFACTORING_DEPLOYMENT_STAGING_DB'),
        ],
        'production' => [
            'enabled' => env('REFACTORING_DEPLOYMENT_PRODUCTION', false),
            'url' => env('REFACTORING_DEPLOYMENT_PRODUCTION_URL'),
            'database' => env('REFACTORING_DEPLOYMENT_PRODUCTION_DB'),
        ],
        'rollback' => [
            'enabled' => env('REFACTORING_DEPLOYMENT_ROLLBACK', true),
            'max_versions' => env('REFACTORING_DEPLOYMENT_MAX_VERSIONS', 5),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Fases
    |--------------------------------------------------------------------------
    */
    'phases' => [
        'current' => env('REFACTORING_CURRENT_PHASE', 'fundamentos'),
        'fundamentos' => [
            'enabled' => true,
            'completed' => true,
            'estimated_duration' => '2 semanas',
            'dependencies' => [],
        ],
        'core_services' => [
            'enabled' => true,
            'completed' => true,
            'estimated_duration' => '2 semanas',
            'dependencies' => ['fundamentos'],
        ],
        'checkout_module' => [
            'enabled' => false,
            'estimated_duration' => '2 semanas',
            'dependencies' => ['core_services'],
        ],
        'inventory_module' => [
            'enabled' => false,
            'estimated_duration' => '2 semanas',
            'dependencies' => ['core_services'],
        ],
        'product_module' => [
            'enabled' => false,
            'estimated_duration' => '2 semanas',
            'dependencies' => ['core_services'],
        ],
        'user_module' => [
            'enabled' => false,
            'estimated_duration' => '2 semanas',
            'dependencies' => ['core_services'],
        ],
        'api_refactoring' => [
            'enabled' => false,
            'estimated_duration' => '2 semanas',
            'dependencies' => ['checkout_module', 'inventory_module', 'product_module'],
        ],
        'optimization_testing' => [
            'enabled' => false,
            'estimated_duration' => '2 semanas',
            'dependencies' => ['api_refactoring'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Métricas
    |--------------------------------------------------------------------------
    */
    'metrics' => [
        'targets' => [
            'controller_lines' => env('REFACTORING_METRICS_CONTROLLER_LINES', 150),
            'test_coverage' => env('REFACTORING_METRICS_TEST_COVERAGE', 80),
            'response_time' => env('REFACTORING_METRICS_RESPONSE_TIME', 200),
            'code_duplication' => env('REFACTORING_METRICS_CODE_DUPLICATION', 5),
        ],
        'tracking' => [
            'enabled' => env('REFACTORING_METRICS_TRACKING', true),
            'interval' => env('REFACTORING_METRICS_INTERVAL', 'daily'),
            'storage' => env('REFACTORING_METRICS_STORAGE', 'database'),
        ],
    ],
];
