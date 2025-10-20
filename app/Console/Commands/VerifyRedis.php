<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Queue;

class VerifyRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:verify {--detailed : Mostrar información detallada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar si Redis está configurado y funcionando correctamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando configuración de Redis...');
        $this->newLine();

        // Verificar configuración
        $this->checkConfiguration();
        
        // Verificar conectividad
        $this->checkConnectivity();
        
        // Verificar servicios
        $this->checkServices();
        
        // Verificar operaciones
        $this->checkOperations();
        
        $this->newLine();
        $this->info('✅ Verificación completada');
    }

    protected function checkConfiguration()
    {
        $this->info('📋 CONFIGURACIÓN:');
        
        $cacheDriver = config('cache.default');
        $sessionDriver = config('session.driver');
        $queueConnection = config('queue.default');
        
        $this->line("Caché: <fg=cyan>{$cacheDriver}</>");
        $this->line("Sesiones: <fg=cyan>{$sessionDriver}</>");
        $this->line("Colas: <fg=cyan>{$queueConnection}</>");
        
        $usingRedis = in_array('redis', [$cacheDriver, $sessionDriver, $queueConnection]);
        
        if ($usingRedis) {
            $this->info('✅ Redis está configurado en al menos un servicio');
        } else {
            $this->warn('⚠️  Redis no está configurado en ningún servicio');
        }
        
        $this->newLine();
    }

    protected function checkConnectivity()
    {
        $this->info('🔌 CONECTIVIDAD:');
        
        try {
            $pong = Redis::ping();
            $this->info("✅ Redis responde: {$pong}");
            
            // Información del servidor
            if ($this->option('detailed')) {
                $info = Redis::info();
                $this->line("Versión: " . ($info['redis_version'] ?? 'Desconocida'));
                $this->line("Modo: " . ($info['redis_mode'] ?? 'Desconocido'));
                $this->line("Memoria usada: " . ($info['used_memory_human'] ?? 'Desconocida'));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error de conexión: " . $e->getMessage());
            $this->warn('   Verifica que Redis esté habilitado en Laravel Cloud');
        }
        
        $this->newLine();
    }

    protected function checkServices()
    {
        $this->info('⚙️  SERVICIOS:');
        
        // Verificar caché
        $this->checkCache();
        
        // Verificar sesiones
        $this->checkSessions();
        
        // Verificar colas
        $this->checkQueues();
        
        $this->newLine();
    }

    protected function checkCache()
    {
        try {
            $key = 'redis_test_' . time();
            $value = 'test_value_' . rand(1000, 9999);
            
            Cache::put($key, $value, 60);
            $retrieved = Cache::get($key);
            
            if ($retrieved === $value) {
                $this->info('✅ Caché funcionando correctamente');
            } else {
                $this->error('❌ Error en operaciones de caché');
            }
            
            Cache::forget($key);
            
        } catch (\Exception $e) {
            $this->error('❌ Error en caché: ' . $e->getMessage());
        }
    }

    protected function checkSessions()
    {
        try {
            $sessionDriver = config('session.driver');
            
            if ($sessionDriver === 'redis') {
                $this->info('✅ Sesiones configuradas para Redis');
                
                // Probar operación de sesión
                session(['redis_test' => 'test_value']);
                $value = session('redis_test');
                
                if ($value === 'test_value') {
                    $this->info('✅ Operaciones de sesión funcionando');
                } else {
                    $this->error('❌ Error en operaciones de sesión');
                }
                
                session()->forget('redis_test');
            } else {
                $this->line("Sesiones usando: <fg=yellow>{$sessionDriver}</>");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error en sesiones: ' . $e->getMessage());
        }
    }

    protected function checkQueues()
    {
        try {
            $queueConnection = config('queue.default');
            
            if ($queueConnection === 'redis') {
                $this->info('✅ Colas configuradas para Redis');
                
                // Verificar cola de Redis
                $queue = Queue::connection('redis');
                $this->info('✅ Conexión de colas Redis establecida');
                
            } else {
                $this->line("Colas usando: <fg=yellow>{$queueConnection}</>");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error en colas: ' . $e->getMessage());
        }
    }

    protected function checkOperations()
    {
        $this->info('🧪 OPERACIONES:');
        
        try {
            // Probar diferentes tipos de datos
            $testData = [
                'string' => 'test_string',
                'number' => 12345,
                'array' => ['key1' => 'value1', 'key2' => 'value2'],
                'boolean' => true,
            ];
            
            foreach ($testData as $type => $value) {
                $key = "test_{$type}_" . time();
                Redis::setex($key, 60, serialize($value));
                $retrieved = unserialize(Redis::get($key));
                
                if ($retrieved === $value) {
                    $this->info("✅ {$type}: OK");
                } else {
                    $this->error("❌ {$type}: Error");
                }
                
                Redis::del($key);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error en operaciones: ' . $e->getMessage());
        }
    }
}
