<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ShowErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'errors:show 
                            {--type=all : Tipo de error a mostrar (all, database, validation, auth, general)}
                            {--limit=50 : Número máximo de errores a mostrar}
                            {--file= : Archivo de log específico a revisar}
                            {--search= : Buscar errores que contengan este texto}
                            {--date= : Mostrar solo errores de esta fecha (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra errores del sistema de manera organizada y legible';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Analizando errores del sistema...');
        
        $logFile = $this->option('file') ?: storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            $this->error("❌ El archivo de log no existe: {$logFile}");
            return 1;
        }

        $errors = $this->parseLogFile($logFile);
        
        if (empty($errors)) {
            $this->info('✅ No se encontraron errores en el archivo de log.');
            return 0;
        }

        $this->displayErrors($errors);
        
        return 0;
    }

    /**
     * Parsea el archivo de log y extrae los errores
     */
    private function parseLogFile(string $logFile): array
    {
        $content = File::get($logFile);
        $lines = explode("\n", $content);
        $errors = [];
        $currentError = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+)$/', $line, $matches)) {
                $timestamp = $matches[1];
                $level = strtoupper($matches[2]);
                $channel = $matches[3];
                $message = $matches[4];

                // Filtrar por tipo de error si se especifica
                if ($this->option('type') !== 'all') {
                    if (!$this->shouldIncludeError($level, $message)) {
                        continue;
                    }
                }

                // Filtrar por fecha si se especifica
                if ($this->option('date')) {
                    if (!str_starts_with($timestamp, $this->option('date'))) {
                        continue;
                    }
                }

                // Filtrar por búsqueda si se especifica
                if ($this->option('search')) {
                    if (!str_contains(strtolower($message), strtolower($this->option('search')))) {
                        continue;
                    }
                }

                $currentError = [
                    'timestamp' => $timestamp,
                    'level' => $level,
                    'channel' => $channel,
                    'message' => $message,
                    'details' => []
                ];
                
                $errors[] = $currentError;
            } elseif ($currentError && trim($line) !== '') {
                // Agregar detalles adicionales del error
                $currentError['details'][] = trim($line);
            }
        }

        // Limitar el número de errores
        $limit = (int) $this->option('limit');
        return array_slice($errors, -$limit);
    }

    /**
     * Determina si un error debe incluirse basado en el tipo
     */
    private function shouldIncludeError(string $level, string $message): bool
    {
        $type = $this->option('type');
        
        switch ($type) {
            case 'database':
                return str_contains($message, 'SQLSTATE') || 
                       str_contains($message, 'Column not found') ||
                       str_contains($message, 'Table') ||
                       str_contains($message, 'foreign key');
            
            case 'validation':
                return str_contains($message, 'validation') ||
                       str_contains($message, 'ValidationException');
            
            case 'auth':
                return str_contains($message, 'authentication') ||
                       str_contains($message, 'authorization') ||
                       str_contains($message, 'login');
            
            case 'general':
                return !str_contains($message, 'SQLSTATE') &&
                       !str_contains($message, 'validation') &&
                       !str_contains($message, 'authentication');
            
            default:
                return true;
        }
    }

    /**
     * Muestra los errores de manera organizada
     */
    private function displayErrors(array $errors): void
    {
        $this->info("\n📊 Resumen de errores encontrados:");
        $this->table(
            ['Total', 'ERROR', 'WARNING', 'INFO', 'DEBUG'],
            [[count($errors), 
              count(array_filter($errors, fn($e) => $e['level'] === 'ERROR')),
              count(array_filter($errors, fn($e) => $e['level'] === 'WARNING')),
              count(array_filter($errors, fn($e) => $e['level'] === 'INFO')),
              count(array_filter($errors, fn($e) => $e['level'] === 'DEBUG'))
            ]]
        );

        $this->info("\n🚨 Errores encontrados:");
        
        foreach ($errors as $index => $error) {
            $this->displaySingleError($error, $index + 1);
        }

        $this->info("\n💡 Sugerencias:");
        $this->displaySuggestions($errors);
    }

    /**
     * Muestra un error individual
     */
    private function displaySingleError(array $error, int $index): void
    {
        $levelColor = $this->getLevelColor($error['level']);
        $levelIcon = $this->getLevelIcon($error['level']);
        
        $this->line("\n{$levelIcon} <{$levelColor}>[{$error['level']}]</> Error #{$index}");
        $this->line("⏰ <comment>{$error['timestamp']}</comment>");
        $this->line("📝 <info>{$error['message']}</info>");
        
        if (!empty($error['details'])) {
            $this->line("🔍 Detalles:");
            foreach (array_slice($error['details'], 0, 5) as $detail) {
                $this->line("   {$detail}");
            }
            
            if (count($error['details']) > 5) {
                $this->line("   ... y " . (count($error['details']) - 5) . " líneas más");
            }
        }
    }

    /**
     * Obtiene el color para el nivel de error
     */
    private function getLevelColor(string $level): string
    {
        return match($level) {
            'ERROR' => 'error',
            'WARNING' => 'warning',
            'INFO' => 'info',
            'DEBUG' => 'comment',
            default => 'comment'
        };
    }

    /**
     * Obtiene el icono para el nivel de error
     */
    private function getLevelIcon(string $level): string
    {
        return match($level) {
            'ERROR' => '💥',
            'WARNING' => '⚠️',
            'INFO' => 'ℹ️',
            'DEBUG' => '🐛',
            default => '📝'
        };
    }

    /**
     * Muestra sugerencias basadas en los errores encontrados
     */
    private function displaySuggestions(array $errors): void
    {
        $databaseErrors = array_filter($errors, fn($e) => 
            str_contains($e['message'], 'SQLSTATE') || 
            str_contains($e['message'], 'Column not found')
        );
        
        $validationErrors = array_filter($errors, fn($e) => 
            str_contains($e['message'], 'validation')
        );
        
        $authErrors = array_filter($errors, fn($e) => 
            str_contains($e['message'], 'authentication') ||
            str_contains($e['message'], 'authorization')
        );

        if (!empty($databaseErrors)) {
            $this->line("🗄️  <error>Errores de Base de Datos:</error>");
            $this->line("   • Ejecutar: php artisan migrate");
            $this->line("   • Verificar configuración de .env");
            $this->line("   • Revisar logs de MySQL");
        }

        if (!empty($validationErrors)) {
            $this->line("✅ <warning>Errores de Validación:</warning>");
            $this->line("   • Revisar reglas de validación en controladores");
            $this->line("   • Verificar formularios HTML");
            $this->line("   • Comprobar mensajes de error personalizados");
        }

        if (!empty($authErrors)) {
            $this->line("🔐 <info>Errores de Autenticación:</info>");
            $this->line("   • Verificar configuración de guards");
            $this->line("   • Revisar middleware de autenticación");
            $this->line("   • Comprobar sesiones y cookies");
        }

        if (empty($databaseErrors) && empty($validationErrors) && empty($authErrors)) {
            $this->line("🎯 <comment>Errores generales del sistema</comment>");
            $this->line("   • Revisar logs completos para más contexto");
            $this->line("   • Verificar permisos de archivos");
            $this->line("   • Comprobar configuración del servidor");
        }
    }
}
