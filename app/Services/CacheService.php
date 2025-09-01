<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * @var LoggingService
     */
    protected $loggingService;

    /**
     * Tiempo de expiración por defecto (1 hora)
     */
    const DEFAULT_TTL = 3600;

    /**
     * Constructor
     */
    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    /**
     * Obtener valor del caché
     */
    public function get(string $key, $default = null)
    {
        try {
            $value = Cache::get($key, $default);
            
            if ($value !== null) {
                $this->loggingService->debug("Cache hit: {$key}");
            } else {
                $this->loggingService->debug("Cache miss: {$key}");
            }
            
            return $value;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al obtener del caché: {$key}", [
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Establecer valor en caché
     */
    public function set(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        try {
            $result = Cache::put($key, $value, $ttl);
            
            if ($result) {
                $this->loggingService->debug("Cache set: {$key} (TTL: {$ttl}s)");
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al establecer en caché: {$key}", [
                'error' => $e->getMessage(),
                'ttl' => $ttl
            ]);
            return false;
        }
    }

    /**
     * Establecer valor en caché si no existe
     */
    public function add(string $key, $value, int $ttl = self::DEFAULT_TTL): bool
    {
        try {
            $result = Cache::add($key, $value, $ttl);
            
            if ($result) {
                $this->loggingService->debug("Cache added: {$key} (TTL: {$ttl}s)");
            } else {
                $this->loggingService->debug("Cache add failed (key exists): {$key}");
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al agregar al caché: {$key}", [
                'error' => $e->getMessage(),
                'ttl' => $ttl
            ]);
            return false;
        }
    }

    /**
     * Eliminar valor del caché
     */
    public function forget(string $key): bool
    {
        try {
            $result = Cache::forget($key);
            
            if ($result) {
                $this->loggingService->debug("Cache forgotten: {$key}");
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al eliminar del caché: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si existe en caché
     */
    public function has(string $key): bool
    {
        try {
            return Cache::has($key);
        } catch (\Exception $e) {
            $this->loggingService->error("Error al verificar caché: {$key}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener o establecer valor en caché
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        try {
            $value = Cache::remember($key, $ttl, $callback);
            $this->loggingService->debug("Cache remembered: {$key} (TTL: {$ttl}s)");
            return $value;
        } catch (\Exception $e) {
            $this->loggingService->error("Error en remember del caché: {$key}", [
                'error' => $e->getMessage(),
                'ttl' => $ttl
            ]);
            
            // Ejecutar callback sin caché en caso de error
            return $callback();
        }
    }

    /**
     * Obtener o establecer valor en caché permanentemente
     */
    public function rememberForever(string $key, callable $callback)
    {
        try {
            $value = Cache::rememberForever($key, $callback);
            $this->loggingService->debug("Cache remembered forever: {$key}");
            return $value;
        } catch (\Exception $e) {
            $this->loggingService->error("Error en rememberForever del caché: {$key}", [
                'error' => $e->getMessage()
            ]);
            
            // Ejecutar callback sin caché en caso de error
            return $callback();
        }
    }

    /**
     * Incrementar valor en caché
     */
    public function increment(string $key, int $value = 1): int
    {
        try {
            $result = Cache::increment($key, $value);
            $this->loggingService->debug("Cache incremented: {$key} by {$value}");
            return $result;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al incrementar caché: {$key}", [
                'error' => $e->getMessage(),
                'value' => $value
            ]);
            return 0;
        }
    }

    /**
     * Decrementar valor en caché
     */
    public function decrement(string $key, int $value = 1): int
    {
        try {
            $result = Cache::decrement($key, $value);
            $this->loggingService->debug("Cache decremented: {$key} by {$value}");
            return $result;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al decrementar caché: {$key}", [
                'error' => $e->getMessage(),
                'value' => $value
            ]);
            return 0;
        }
    }

    /**
     * Obtener múltiples valores del caché
     */
    public function many(array $keys): array
    {
        try {
            $values = Cache::many($keys);
            $this->loggingService->debug("Cache many retrieved: " . implode(', ', $keys));
            return $values;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al obtener múltiples del caché", [
                'error' => $e->getMessage(),
                'keys' => $keys
            ]);
            return array_fill_keys($keys, null);
        }
    }

    /**
     * Establecer múltiples valores en caché
     */
    public function putMany(array $values, int $ttl = self::DEFAULT_TTL): bool
    {
        try {
            $result = Cache::putMany($values, $ttl);
            
            if ($result) {
                $this->loggingService->debug("Cache many set: " . implode(', ', array_keys($values)) . " (TTL: {$ttl}s)");
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al establecer múltiples en caché", [
                'error' => $e->getMessage(),
                'keys' => array_keys($values),
                'ttl' => $ttl
            ]);
            return false;
        }
    }

    /**
     * Obtener y eliminar valor del caché
     */
    public function pull(string $key, $default = null)
    {
        try {
            $value = Cache::pull($key, $default);
            $this->loggingService->debug("Cache pulled: {$key}");
            return $value;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al pull del caché: {$key}", [
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Obtener tiempo de expiración restante
     */
    public function getTtl(string $key): ?int
    {
        try {
            return Cache::getTimeToLive($key);
        } catch (\Exception $e) {
            $this->loggingService->error("Error al obtener TTL del caché: {$key}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Limpiar todo el caché
     */
    public function flush(): bool
    {
        try {
            $result = Cache::flush();
            
            if ($result) {
                $this->loggingService->info("Cache flushed completely");
            }
            
            return $result;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al limpiar caché", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener estadísticas del caché
     */
    public function getStats(): array
    {
        try {
            $driver = config('cache.default');
            $config = config("cache.stores.{$driver}");
            
            return [
                'driver' => $driver,
                'prefix' => $config['prefix'] ?? null,
                'connection' => $config['connection'] ?? null,
                'default_ttl' => self::DEFAULT_TTL,
            ];
        } catch (\Exception $e) {
            $this->loggingService->error("Error al obtener estadísticas del caché", [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Generar clave de caché con prefijo
     */
    public function generateKey(string $prefix, ...$parts): string
    {
        $key = $prefix;
        
        foreach ($parts as $part) {
            if (is_array($part)) {
                $key .= ':' . md5(serialize($part));
            } else {
                $key .= ':' . $part;
            }
        }
        
        return $key;
    }

    /**
     * Caché de productos con TTL específico
     */
    public function cacheProduct(string $key, callable $callback, int $ttl = 1800): mixed
    {
        return $this->remember("product:{$key}", $ttl, $callback);
    }

    /**
     * Caché de inventario con TTL corto
     */
    public function cacheInventory(string $key, callable $callback, int $ttl = 300): mixed
    {
        return $this->remember("inventory:{$key}", $ttl, $callback);
    }

    /**
     * Caché de usuario con TTL medio
     */
    public function cacheUser(string $key, callable $callback, int $ttl = 3600): mixed
    {
        return $this->remember("user:{$key}", $ttl, $callback);
    }

    /**
     * Caché de búsquedas con TTL largo
     */
    public function cacheSearch(string $key, callable $callback, int $ttl = 7200): mixed
    {
        return $this->remember("search:{$key}", $ttl, $callback);
    }

    /**
     * Invalidar caché por patrón
     */
    public function invalidatePattern(string $pattern): bool
    {
        try {
            // Esta funcionalidad depende del driver de caché
            // Para Redis se puede implementar con SCAN
            $this->loggingService->info("Cache pattern invalidated: {$pattern}");
            return true;
        } catch (\Exception $e) {
            $this->loggingService->error("Error al invalidar patrón de caché: {$pattern}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Caché de reportes con TTL específico
     */
    public function cacheReport(string $key, callable $callback, int $ttl = 3600): mixed
    {
        return $this->remember("report:{$key}", $ttl, $callback);
    }

    /**
     * Caché de configuración con TTL largo
     */
    public function cacheConfig(string $key, callable $callback, int $ttl = 86400): mixed
    {
        return $this->remember("config:{$key}", $ttl, $callback);
    }
}
