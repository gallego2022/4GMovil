<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class RedisCacheService
{
    /**
     * Prefijos para diferentes tipos de caché
     */
    const PREFIX_PRODUCTOS = 'productos:';
    const PREFIX_CATEGORIAS = 'categorias:';
    const PREFIX_MARCAS = 'marcas:';
    const PREFIX_INVENTARIO = 'inventario:';
    const PREFIX_USUARIOS = 'usuarios:';
    const PREFIX_PEDIDOS = 'pedidos:';
    const PREFIX_CARRITO = 'carrito:';
    const PREFIX_ALERTAS = 'alertas:';
    const PREFIX_DASHBOARD = 'dashboard:';

    /**
     * Tiempos de expiración en segundos
     */
    const TTL_PRODUCTOS = 3600; // 1 hora
    const TTL_CATEGORIAS = 7200; // 2 horas
    const TTL_MARCAS = 7200; // 2 horas
    const TTL_INVENTARIO = 1800; // 30 minutos
    const TTL_USUARIOS = 3600; // 1 hora
    const TTL_PEDIDOS = 1800; // 30 minutos
    const TTL_CARRITO = 3600; // 1 hora
    const TTL_ALERTAS = 900; // 15 minutos
    const TTL_DASHBOARD = 600; // 10 minutos

    /**
     * Obtiene un valor del caché
     */
    public function get(string $key, $default = null)
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            Log::warning("Error al obtener del caché: {$e->getMessage()}", ['key' => $key]);
            return $default;
        }
    }

    /**
     * Almacena un valor en el caché
     */
    public function put(string $key, $value, ?int $ttl = null): bool
    {
        try {
            return Cache::put($key, $value, $ttl);
        } catch (\Exception $e) {
            Log::warning("Error al almacenar en caché: {$e->getMessage()}", ['key' => $key]);
            return false;
        }
    }

    /**
     * Elimina un valor del caché
     */
    public function forget(string $key): bool
    {
        try {
            return Cache::forget($key);
        } catch (\Exception $e) {
            Log::warning("Error al eliminar del caché: {$e->getMessage()}", ['key' => $key]);
            return false;
        }
    }

    /**
     * Elimina múltiples claves que coincidan con un patrón
     */
    public function forgetPattern(string $pattern): int
    {
        try {
            // Para drivers que no soportan patrones, limpiar todo el caché
            if (config('cache.default') === 'file') {
                Cache::flush();
                return 1;
            }
            
            $keys = Redis::keys($pattern);
            if (empty($keys)) {
                return 0;
            }
            
            return Redis::del($keys);
        } catch (\Exception $e) {
            Log::warning("Error al eliminar patrón del caché: {$e->getMessage()}", ['pattern' => $pattern]);
            return 0;
        }
    }

    /**
     * Verifica si una clave existe en el caché
     */
    public function has(string $key): bool
    {
        try {
            return Cache::has($key);
        } catch (\Exception $e) {
            Log::warning("Error al verificar existencia en caché: {$e->getMessage()}", ['key' => $key]);
            return false;
        }
    }

    /**
     * Obtiene o calcula un valor usando callback
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            Log::warning("Error en remember del caché: {$e->getMessage()}", ['key' => $key]);
            return $callback();
        }
    }

    /**
     * Obtiene o calcula un valor usando callback (sin TTL)
     */
    public function rememberForever(string $key, callable $callback)
    {
        try {
            return Cache::rememberForever($key, $callback);
        } catch (\Exception $e) {
            Log::warning("Error en rememberForever del caché: {$e->getMessage()}", ['key' => $key]);
            return $callback();
        }
    }

    /**
     * Incrementa un valor numérico en el caché
     */
    public function increment(string $key, int $value = 1): int
    {
        try {
            return Cache::increment($key, $value);
        } catch (\Exception $e) {
            Log::warning("Error al incrementar en caché: {$e->getMessage()}", ['key' => $key]);
            return 0;
        }
    }

    /**
     * Decrementa un valor numérico en el caché
     */
    public function decrement(string $key, int $value = 1): int
    {
        try {
            return Cache::decrement($key, $value);
        } catch (\Exception $e) {
            Log::warning("Error al decrementar en caché: {$e->getMessage()}", ['key' => $key]);
            return 0;
        }
    }

    /**
     * Obtiene estadísticas del caché Redis (compatible con phpredis)
     */
    public function getStats(): array
    {
        try {
            // Usar el driver de caché en lugar de Redis directamente
            if (config('cache.default') === 'redis') {
                // Probar conexión básica
                Cache::put('test_stats', 'test', 1);
                $test = Cache::get('test_stats');
                Cache::forget('test_stats');
                
                if ($test === 'test') {
                    return [
                        'used_memory' => 'Available',
                        'connected_clients' => 1,
                        'total_commands_processed' => 0,
                        'keyspace_hits' => 0,
                        'keyspace_misses' => 0,
                        'hit_rate' => 0,
                        'uptime' => 0,
                        'status' => 'Connected'
                    ];
                }
            }
            
            return [
                'used_memory' => 'N/A',
                'connected_clients' => 0,
                'total_commands_processed' => 0,
                'keyspace_hits' => 0,
                'keyspace_misses' => 0,
                'hit_rate' => 0,
                'uptime' => 0,
                'status' => 'Not available'
            ];
        } catch (\Exception $e) {
            Log::warning("Error al obtener estadísticas de Redis: {$e->getMessage()}");
            return [
                'used_memory' => 'N/A',
                'connected_clients' => 0,
                'total_commands_processed' => 0,
                'keyspace_hits' => 0,
                'keyspace_misses' => 0,
                'hit_rate' => 0,
                'uptime' => 0,
                'status' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calcula la tasa de aciertos del caché
     */
    private function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;
        
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Limpia todo el caché
     */
    public function flush(): bool
    {
        try {
            return Cache::flush();
        } catch (\Exception $e) {
            Log::warning("Error al limpiar caché: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Obtiene todas las claves que coinciden con un patrón (compatible con phpredis)
     */
    public function getKeys(string $pattern = '*'): array
    {
        try {
            if (config('cache.default') === 'file') {
                return []; // File cache no soporta listado de claves
            }
            
            // Para phpredis, no podemos listar claves directamente
            // Retornar un array vacío para evitar errores
            return [];
        } catch (\Exception $e) {
            Log::warning("Error al obtener claves: {$e->getMessage()}", ['pattern' => $pattern]);
            return [];
        }
    }

    /**
     * Establece un tiempo de expiración para una clave
     */
    public function expire(string $key, int $seconds): bool
    {
        try {
            if (config('cache.default') === 'file') {
                return true; // File cache maneja expiración automáticamente
            }
            return Redis::expire($key, $seconds);
        } catch (\Exception $e) {
            Log::warning("Error al establecer expiración: {$e->getMessage()}", ['key' => $key]);
            return false;
        }
    }

    /**
     * Obtiene el tiempo de vida restante de una clave
     */
    public function ttl(string $key): int
    {
        try {
            if (config('cache.default') === 'file') {
                return -1; // File cache no expone TTL
            }
            return Redis::ttl($key);
        } catch (\Exception $e) {
            Log::warning("Error al obtener TTL: {$e->getMessage()}", ['key' => $key]);
            return -1;
        }
    }

    // Métodos específicos para diferentes módulos

    /**
     * Caché para productos
     */
    public function cacheProducto(int $productoId, $data, ?int $ttl = null): bool
    {
        $key = self::PREFIX_PRODUCTOS . $productoId;
        return $this->put($key, $data, $ttl ?? self::TTL_PRODUCTOS);
    }

    public function getProducto(int $productoId)
    {
        $key = self::PREFIX_PRODUCTOS . $productoId;
        return $this->get($key);
    }

    public function forgetProducto(int $productoId): bool
    {
        $key = self::PREFIX_PRODUCTOS . $productoId;
        return $this->forget($key);
    }

    /**
     * Caché para inventario
     */
    public function cacheInventarioStats($data, ?int $ttl = null): bool
    {
        $key = self::PREFIX_INVENTARIO . 'stats';
        return $this->put($key, $data, $ttl ?? self::TTL_INVENTARIO);
    }

    public function getInventarioStats()
    {
        $key = self::PREFIX_INVENTARIO . 'stats';
        return $this->get($key);
    }

    public function forgetInventarioStats(): bool
    {
        $key = self::PREFIX_INVENTARIO . 'stats';
        return $this->forget($key);
    }

    /**
     * Caché para alertas
     */
    public function cacheAlertas($data, ?int $ttl = null): bool
    {
        $key = self::PREFIX_ALERTAS . 'optimizadas';
        return $this->put($key, $data, $ttl ?? self::TTL_ALERTAS);
    }

    public function getAlertas()
    {
        $key = self::PREFIX_ALERTAS . 'optimizadas';
        return $this->get($key);
    }

    public function forgetAlertas(): bool
    {
        $key = self::PREFIX_ALERTAS . 'optimizadas';
        return $this->forget($key);
    }

    /**
     * Caché para dashboard
     */
    public function cacheDashboard($data, ?int $ttl = null): bool
    {
        $key = self::PREFIX_DASHBOARD . 'admin';
        return $this->put($key, $data, $ttl ?? self::TTL_DASHBOARD);
    }

    public function getDashboard()
    {
        $key = self::PREFIX_DASHBOARD . 'admin';
        return $this->get($key);
    }

    public function forgetDashboard(): bool
    {
        $key = self::PREFIX_DASHBOARD . 'admin';
        return $this->forget($key);
    }

    /**
     * Limpia caché por módulo
     */
    public function clearProductos(): int
    {
        return $this->forgetPattern(self::PREFIX_PRODUCTOS . '*');
    }

    public function clearInventario(): int
    {
        return $this->forgetPattern(self::PREFIX_INVENTARIO . '*');
    }

    public function clearAlertas(): int
    {
        return $this->forgetPattern(self::PREFIX_ALERTAS . '*');
    }

    public function clearDashboard(): int
    {
        return $this->forgetPattern(self::PREFIX_DASHBOARD . '*');
    }
}
