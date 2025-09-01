<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConfigurationService
{
    protected $loggingService;
    protected $cacheService;
    protected $cachePrefix = 'app_config';
    protected $cacheTtl = 3600; // 1 hora

    public function __construct(LoggingService $loggingService, CacheService $cacheService)
    {
        $this->loggingService = $loggingService;
        $this->cacheService = $cacheService;
    }

    /**
     * Obtener valor de configuración
     */
    public function get(string $key, $default = null)
    {
        try {
            $cacheKey = "{$this->cachePrefix}_{$key}";
            
            return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($key, $default) {
                // Primero intentar desde la base de datos
                $dbValue = $this->getFromDatabase($key);
                if ($dbValue !== null) {
                    return $dbValue;
                }
                
                // Luego desde config de Laravel
                $configValue = Config::get($key);
                if ($configValue !== null) {
                    return $configValue;
                }
                
                return $default;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener configuración', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Establecer valor de configuración
     */
    public function set(string $key, $value, bool $persistent = false): bool
    {
        try {
            $this->loggingService->info('Estableciendo configuración', [
                'key' => $key,
                'value' => $value,
                'persistent' => $persistent
            ]);

            // Si es persistente, guardar en base de datos
            if ($persistent) {
                $this->setInDatabase($key, $value);
            }

            // Actualizar cache
            $cacheKey = "{$this->cachePrefix}_{$key}";
            $this->cacheService->set($cacheKey, $value, $this->cacheTtl);

            $this->loggingService->info('Configuración establecida exitosamente', [
                'key' => $key,
                'persistent' => $persistent
            ]);

            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al establecer configuración', [
                'key' => $key,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener múltiples configuraciones
     */
    public function getMultiple(array $keys, array $defaults = []): array
    {
        $result = [];
        
        foreach ($keys as $key) {
            $default = $defaults[$key] ?? null;
            $result[$key] = $this->get($key, $default);
        }
        
        return $result;
    }

    /**
     * Establecer múltiples configuraciones
     */
    public function setMultiple(array $configs, bool $persistent = false): array
    {
        $results = [];
        
        foreach ($configs as $key => $value) {
            $results[$key] = $this->set($key, $value, $persistent);
        }
        
        return $results;
    }

    /**
     * Verificar si existe una configuración
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Eliminar configuración
     */
    public function forget(string $key): bool
    {
        try {
            $this->loggingService->info('Eliminando configuración', ['key' => $key]);

            // Eliminar de base de datos si existe
            $this->removeFromDatabase($key);

            // Eliminar de cache
            $cacheKey = "{$this->cachePrefix}_{$key}";
            $this->cacheService->forget($cacheKey);

            $this->loggingService->info('Configuración eliminada exitosamente', ['key' => $key]);
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al eliminar configuración', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener configuración de la aplicación
     */
    public function getAppConfig(): array
    {
        try {
            $cacheKey = "{$this->cachePrefix}_app_config";
            
            return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () {
                $configs = [
                    'app' => [
                        'name' => Config::get('app.name'),
                        'env' => Config::get('app.env'),
                        'debug' => Config::get('app.debug'),
                        'url' => Config::get('app.url'),
                        'timezone' => Config::get('app.timezone'),
                        'locale' => Config::get('app.locale')
                    ],
                    'database' => [
                        'default' => Config::get('database.default'),
                        'connections' => array_keys(Config::get('database.connections', []))
                    ],
                    'mail' => [
                        'default' => Config::get('mail.default'),
                        'from' => Config::get('mail.from')
                    ],
                    'cache' => [
                        'default' => Config::get('cache.default'),
                        'stores' => array_keys(Config::get('cache.stores', []))
                    ],
                    'session' => [
                        'driver' => Config::get('session.driver'),
                        'lifetime' => Config::get('session.lifetime')
                    ]
                ];

                // Agregar configuraciones personalizadas de la base de datos
                $customConfigs = $this->getCustomConfigs();
                if (!empty($customConfigs)) {
                    $configs['custom'] = $customConfigs;
                }

                return $configs;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener configuración de la aplicación', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener configuración de entorno
     */
    public function getEnvironmentConfig(): array
    {
        try {
            $cacheKey = "{$this->cachePrefix}_environment";
            
            return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () {
                return [
                    'environment' => app()->environment(),
                    'is_production' => app()->environment('production'),
                    'is_local' => app()->environment('local'),
                    'is_testing' => app()->environment('testing'),
                    'app_debug' => config('app.debug'),
                    'app_env' => config('app.env'),
                    'cache_driver' => config('cache.default'),
                    'session_driver' => config('session.driver'),
                    'queue_driver' => config('queue.default'),
                    'database_connection' => config('database.default')
                ];
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener configuración de entorno', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener configuración de características (features)
     */
    public function getFeatureConfig(): array
    {
        try {
            $cacheKey = "{$this->cachePrefix}_features";
            
            return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () {
                return [
                    'notifications' => [
                        'enabled' => $this->get('features.notifications.enabled', true),
                        'email' => $this->get('features.notifications.email', true),
                        'sms' => $this->get('features.notifications.sms', false),
                        'push' => $this->get('features.notifications.push', false)
                    ],
                    'payments' => [
                        'enabled' => $this->get('features.payments.enabled', true),
                        'stripe' => $this->get('features.payments.stripe', true),
                        'paypal' => $this->get('features.payments.paypal', false),
                        'cash' => $this->get('features.payments.cash', true)
                    ],
                    'inventory' => [
                        'enabled' => $this->get('features.inventory.enabled', true),
                        'auto_sync' => $this->get('features.inventory.auto_sync', false),
                        'low_stock_alerts' => $this->get('features.inventory.low_stock_alerts', true)
                    ],
                    'analytics' => [
                        'enabled' => $this->get('features.analytics.enabled', false),
                        'google_analytics' => $this->get('features.analytics.google_analytics', false),
                        'internal_tracking' => $this->get('features.analytics.internal_tracking', true)
                    ]
                ];
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener configuración de características', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Actualizar configuración de características
     */
    public function updateFeatureConfig(array $features): bool
    {
        try {
            $this->loggingService->info('Actualizando configuración de características', [
                'features' => array_keys($features)
            ]);

            foreach ($features as $category => $settings) {
                foreach ($settings as $key => $value) {
                    $configKey = "features.{$category}.{$key}";
                    $this->set($configKey, $value, true);
                }
            }

            // Limpiar cache de características
            $this->cacheService->forget("{$this->cachePrefix}_features");

            $this->loggingService->info('Configuración de características actualizada exitosamente');
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al actualizar configuración de características', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener configuración desde base de datos
     */
    protected function getFromDatabase(string $key)
    {
        try {
            // Verificar si existe la tabla de configuraciones
            if (!DB::getSchemaBuilder()->hasTable('configurations')) {
                return null;
            }

            $config = DB::table('configurations')
                ->where('key', $key)
                ->value('value');

            return $config ? json_decode($config, true) : null;

        } catch (\Exception $e) {
            $this->loggingService->warning('Error al obtener configuración de base de datos', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Establecer configuración en base de datos
     */
    protected function setInDatabase(string $key, $value): bool
    {
        try {
            // Verificar si existe la tabla de configuraciones
            if (!DB::getSchemaBuilder()->hasTable('configurations')) {
                return false;
            }

            $jsonValue = json_encode($value);
            
            DB::table('configurations')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $jsonValue,
                    'updated_at' => now()
                ]
            );

            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al establecer configuración en base de datos', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remover configuración de base de datos
     */
    protected function removeFromDatabase(string $key): bool
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('configurations')) {
                return false;
            }

            DB::table('configurations')->where('key', $key)->delete();
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al remover configuración de base de datos', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener configuraciones personalizadas
     */
    protected function getCustomConfigs(): array
    {
        try {
            if (!DB::getSchemaBuilder()->hasTable('configurations')) {
                return [];
            }

            $configs = DB::table('configurations')
                ->where('key', 'like', 'custom.%')
                ->get(['key', 'value']);

            $result = [];
            foreach ($configs as $config) {
                $key = str_replace('custom.', '', $config->key);
                $result[$key] = json_decode($config->value, true);
            }

            return $result;

        } catch (\Exception $e) {
            $this->loggingService->warning('Error al obtener configuraciones personalizadas', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Limpiar cache de configuración
     */
    public function clearCache(): bool
    {
        try {
            $this->loggingService->info('Limpiando cache de configuración');

            // Limpiar cache específico de configuración
            $this->cacheService->forget("{$this->cachePrefix}_app_config");
            $this->cacheService->forget("{$this->cachePrefix}_environment");
            $this->cacheService->forget("{$this->cachePrefix}_features");

            $this->loggingService->info('Cache de configuración limpiado exitosamente');
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al limpiar cache de configuración', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Recargar configuración
     */
    public function reload(): bool
    {
        try {
            $this->loggingService->info('Recargando configuración');

            // Limpiar cache
            $this->clearCache();

            // Recargar configuraciones críticas
            $this->getAppConfig();
            $this->getEnvironmentConfig();
            $this->getFeatureConfig();

            $this->loggingService->info('Configuración recargada exitosamente');
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al recargar configuración', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
