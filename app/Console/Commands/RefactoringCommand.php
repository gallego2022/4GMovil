<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use App\Services\LoggingService;
use App\Services\CacheService;

class RefactoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refactoring:manage {action} {--phase=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestionar y monitorear el proceso de refactoring del proyecto';

    /**
     * @var LoggingService
     */
    protected $loggingService;

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * Execute the console command.
     */
    public function handle(LoggingService $loggingService, CacheService $cacheService)
    {
        $this->loggingService = $loggingService;
        $this->cacheService = $cacheService;

        $action = $this->argument('action');
        $phase = $this->option('phase');
        $verbose = false;

        $this->info('🚀 GESTIÓN DE REFACTORING - PROYECTO LARAVEL');
        $this->info('================================================');

        switch ($action) {
            case 'status':
                $this->showStatus($phase, $verbose);
                break;
            case 'analyze':
                $this->analyzeCodebase($phase, $verbose);
                break;
            case 'progress':
                $this->showProgress($phase, $verbose);
                break;
            case 'metrics':
                $this->showMetrics($phase, $verbose);
                break;
            case 'services':
                $this->showServices($phase, $verbose);
                break;
            case 'cleanup':
                $this->cleanup($phase, $verbose);
                break;
            default:
                $this->error("Acción '{$action}' no reconocida.");
                $this->showHelp();
                return 1;
        }

        $this->loggingService->userAction("refactoring_command_{$action}", [
            'phase' => $phase,
            'verbose' => $verbose
        ]);

        return 0;
    }

    /**
     * Mostrar estado actual del refactoring
     */
    protected function showStatus(?string $phase = null, bool $verbose = false): void
    {
        $this->info('📊 ESTADO ACTUAL DEL REFACTORING');
        $this->info('==================================');

        $phases = Config::get('refactoring.phases');
        $currentPhase = Config::get('refactoring.phases.current');

        $this->info("Fase actual: <comment>{$currentPhase}</comment>");

        foreach ($phases as $phaseName => $phaseConfig) {
            if ($phase && $phase !== $phaseName) {
                continue;
            }

            if (is_array($phaseConfig)) {
                $status = $phaseConfig['enabled'] ? '✅ Habilitada' : '❌ Deshabilitada';
                $duration = $phaseConfig['estimated_duration'] ?? 'No especificada';
                $dependencies = implode(', ', $phaseConfig['dependencies'] ?? ['Ninguna']);

                $this->line("• <info>{$phaseName}</info>: {$status}");
                $this->line("  Duración estimada: {$duration}");
                $this->line("  Dependencias: {$dependencies}");

                if ($verbose) {
                    $this->showPhaseDetails($phaseName);
                }
            } else {
                $this->line("• <info>{$phaseName}</info>: {$phaseConfig}");
            }

            $this->line('');
        }

        $this->showCurrentMetrics();
    }

    /**
     * Analizar el código base
     */
    protected function analyzeCodebase(?string $phase = null, bool $verbose = false): void
    {
        $this->info('🔍 ANÁLISIS DEL CÓDIGO BASE');
        $this->info('==============================');

        $this->analyzeControllers($verbose);
        $this->analyzeServices($verbose);
        $this->analyzeRepositories($verbose);
        $this->analyzeModels($verbose);

        if ($verbose) {
            $this->analyzeDependencies($verbose);
        }
    }

    /**
     * Analizar controllers
     */
    protected function analyzeControllers(bool $verbose = false): void
    {
        $this->info('📋 ANÁLISIS DE CONTROLLERS');
        $this->line('----------------------------');

        $controllersPath = app_path('Http/Controllers');
        $controllers = File::glob($controllersPath . '/*.php');

        $totalLines = 0;
        $largeControllers = [];
        $mediumControllers = [];
        $smallControllers = [];

        foreach ($controllers as $controller) {
            $filename = basename($controller, '.php');
            $content = file_get_contents($controller);
            $lines = substr_count($content, "\n") + 1;
            $totalLines += $lines;

            if ($lines > 400) {
                $largeControllers[] = ['name' => $filename, 'lines' => $lines];
            } elseif ($lines > 200) {
                $mediumControllers[] = ['name' => $filename, 'lines' => $lines];
            } else {
                $smallControllers[] = ['name' => $filename, 'lines' => $lines];
            }
        }

        $this->line("Total de controllers: " . count($controllers));
        $this->line("Total de líneas: {$totalLines}");
        $this->line("Promedio por controller: " . round($totalLines / count($controllers), 2));

        $this->line('');
        $this->line("Controllers grandes (>400 líneas): " . count($largeControllers));
        $this->line("Controllers medianos (200-400 líneas): " . count($mediumControllers));
        $this->line("Controllers pequeños (<200 líneas): " . count($smallControllers));

        if ($verbose && !empty($largeControllers)) {
            $this->line('');
            $this->line("Controllers que requieren refactoring urgente:");
            foreach ($largeControllers as $controller) {
                $this->line("  • {$controller['name']}: {$controller['lines']} líneas");
            }
        }
    }

    /**
     * Analizar servicios
     */
    protected function analyzeServices(bool $verbose = false): void
    {
        $this->info('⚙️ ANÁLISIS DE SERVICIOS');
        $this->line('-------------------------');

        $servicesPath = app_path('Services');
        $services = File::glob($servicesPath . '/*.php');

        $this->line("Total de servicios: " . count($services));

        if ($verbose) {
            foreach ($services as $service) {
                $filename = basename($service, '.php');
                $content = file_get_contents($service);
                $lines = substr_count($content, "\n") + 1;
                $this->line("  • {$filename}: {$lines} líneas");
            }
        }
    }

    /**
     * Analizar repositorios
     */
    protected function analyzeRepositories(bool $verbose = false): void
    {
        $this->info('🗄️ ANÁLISIS DE REPOSITORIOS');
        $this->line('----------------------------');

        $reposPath = app_path('Repositories');
        $repositories = File::glob($reposPath . '/**/*.php');

        $this->line("Total de repositorios: " . count($repositories));

        if ($verbose) {
            foreach ($repositories as $repo) {
                $filename = basename($repo, '.php');
                $content = file_get_contents($repo);
                $lines = substr_count($content, "\n") + 1;
                $this->line("  • {$filename}: {$lines} líneas");
            }
        }
    }

    /**
     * Analizar modelos
     */
    protected function analyzeModels(bool $verbose = false): void
    {
        $this->info('📦 ANÁLISIS DE MODELOS');
        $this->line('----------------------');

        $modelsPath = app_path('Models');
        $models = File::glob($modelsPath . '/*.php');

        $this->line("Total de modelos: " . count($models));

        if ($verbose) {
            foreach ($models as $model) {
                $filename = basename($model, '.php');
                $content = file_get_contents($model);
                $lines = substr_count($content, "\n") + 1;
                $this->line("  • {$filename}: {$lines} líneas");
            }
        }
    }

    /**
     * Mostrar progreso del refactoring
     */
    protected function showProgress(?string $phase = null, bool $verbose = false): void
    {
        $this->info('📈 PROGRESO DEL REFACTORING');
        $this->info('============================');

        $phases = Config::get('refactoring.phases');
        $currentPhase = Config::get('refactoring.phases.current');

        $completedPhases = [];
        $inProgressPhases = [];
        $pendingPhases = [];

        foreach ($phases as $phaseName => $phaseConfig) {
            if ($phase && $phase !== $phaseName) {
                continue;
            }

            if (is_array($phaseConfig)) {
                if ($phaseConfig['completed'] ?? false) {
                    $completedPhases[] = $phaseName;
                } elseif ($phaseConfig['enabled'] ?? false) {
                    $inProgressPhases[] = $phaseName;
                } else {
                    $pendingPhases[] = $phaseName;
                }
            }
        }

        $this->line("✅ Fases completadas: " . implode(', ', $completedPhases ?: ['Ninguna']));
        $this->line("🔄 Fase en progreso: " . implode(', ', $inProgressPhases ?: ['Ninguna']));
        $this->line("⏳ Fases pendientes: " . implode(', ', $pendingPhases ?: ['Ninguna']));

        $totalPhases = count($phases);
        $completedCount = count($completedPhases);
        $progress = round(($completedCount / $totalPhases) * 100, 2);

        $this->line('');
        $this->line("Progreso general: {$progress}%");
        $this->progressBar($progress);
    }

    /**
     * Mostrar métricas del refactoring
     */
    protected function showMetrics(?string $phase = null, bool $verbose = false): void
    {
        $this->info('📊 MÉTRICAS DEL REFACTORING');
        $this->info('============================');

        $metrics = Config::get('refactoring.metrics.targets');

        $this->line("Objetivos del refactoring:");
        $this->line("• Líneas por controller: < {$metrics['controller_lines']}");
        $this->line("• Cobertura de tests: > {$metrics['test_coverage']}%");
        $this->line("• Tiempo de respuesta: < {$metrics['response_time']}ms");
        $this->line("• Duplicación de código: < {$metrics['code_duplication']}%");

        if ($verbose) {
            $this->showDetailedMetrics();
        }
    }

    /**
     * Limpiar archivos temporales y caché
     */
    protected function cleanup(?string $phase = null, bool $verbose = false): void
    {
        $this->info('🧹 LIMPIEZA DEL REFACTORING');
        $this->info('============================');

        if ($this->confirm('¿Estás seguro de que quieres limpiar los archivos temporales?')) {
            // Limpiar caché de aplicación
            $this->call('cache:clear');
            $this->line('✅ Caché de aplicación limpiado');

            // Limpiar caché de configuración
            $this->call('config:clear');
            $this->line('✅ Caché de configuración limpiado');

            // Limpiar caché de rutas
            $this->call('route:clear');
            $this->line('✅ Caché de rutas limpiado');

            // Limpiar caché de vistas
            $this->call('view:clear');
            $this->line('✅ Caché de vistas limpiado');

            // Limpiar logs antiguos
            $this->cleanupLogs();

            $this->info('Limpieza completada exitosamente');
        } else {
            $this->info('Limpieza cancelada');
        }
    }

    /**
     * Limpiar logs antiguos
     */
    protected function cleanupLogs(): void
    {
        $logPath = storage_path('logs');
        $maxFiles = Config::get('refactoring.logging.max_files', 30);

        $logFiles = glob($logPath . '/*.log');
        $logFiles = array_filter($logFiles, function($file) {
            return filemtime($file) < strtotime('-30 days');
        });

        if (count($logFiles) > $maxFiles) {
            $filesToDelete = array_slice($logFiles, 0, count($logFiles) - $maxFiles);
            foreach ($filesToDelete as $file) {
                unlink($file);
            }
            $this->line("✅ Logs antiguos eliminados: " . count($filesToDelete));
        }
    }

    /**
     * Mostrar barra de progreso
     */
    protected function progressBar(int $percentage): void
    {
        $barLength = 50;
        $filledLength = round(($percentage / 100) * $barLength);
        $bar = str_repeat('█', $filledLength) . str_repeat('░', $barLength - $filledLength);
        
        $this->line("[{$bar}] {$percentage}%");
    }

    /**
     * Mostrar detalles de una fase específica
     */
    protected function showPhaseDetails(string $phaseName): void
    {
        $phaseConfig = Config::get("refactoring.phases.{$phaseName}");
        
        if (!$phaseConfig) {
            return;
        }

        $this->line("  Detalles de la fase {$phaseName}:");
        $this->line("    - Habilitada: " . ($phaseConfig['enabled'] ? 'Sí' : 'No'));
        $this->line("    - Duración estimada: {$phaseConfig['estimated_duration']}");
        $this->line("    - Dependencias: " . implode(', ', $phaseConfig['dependencies'] ?: ['Ninguna']));
    }

    /**
     * Mostrar métricas actuales
     */
    protected function showCurrentMetrics(): void
    {
        $this->info('📈 MÉTRICAS ACTUALES');
        $this->line('=====================');

        // Aquí se pueden agregar métricas en tiempo real
        $this->line("• Tiempo de respuesta promedio: <comment>Calculando...</comment>");
        $this->line("• Uso de memoria: <comment>Calculando...</comment>");
        $this->line("• Cobertura de tests: <comment>Calculando...</comment>");
    }

    /**
     * Mostrar métricas detalladas
     */
    protected function showDetailedMetrics(): void
    {
        $this->line('');
        $this->info('Métricas detalladas:');
        $this->line('• Análisis de complejidad ciclomática');
        $this->line('• Análisis de dependencias');
        $this->line('• Análisis de duplicación de código');
        $this->line('• Análisis de performance');
    }

    /**
     * Analizar dependencias
     */
    protected function analyzeDependencies(bool $verbose = false): void
    {
        $this->info('🔗 ANÁLISIS DE DEPENDENCIAS');
        $this->line('----------------------------');

        $composerPath = base_path('composer.json');
        if (File::exists($composerPath)) {
            $composer = json_decode(File::get($composerPath), true);
            $dependencies = $composer['require'] ?? [];
            $devDependencies = $composer['require-dev'] ?? [];

            $this->line("Dependencias de producción: " . count($dependencies));
            $this->line("Dependencias de desarrollo: " . count($devDependencies));

            if ($verbose) {
                $this->line('');
                $this->line("Dependencias principales:");
                foreach (array_slice($dependencies, 0, 10) as $package => $version) {
                    $this->line("  • {$package}: {$version}");
                }
            }
        }
    }

    /**
     * Mostrar servicios disponibles
     */
    protected function showServices(?string $phase = null, bool $verbose = false): void
    {
        $this->info('🔧 SERVICIOS DISPONIBLES');
        $this->line('========================');

        $services = [
            'fundamentos' => [
                'LoggingService' => 'Servicio de logging estructurado',
                'ValidationService' => 'Servicio de validación centralizada',
                'CacheService' => 'Servicio de caché optimizado',
                'BaseController' => 'Controlador base con respuestas estandarizadas',
                'BaseRepositoryInterface' => 'Interfaz base para repositorios',
                'BaseRepository' => 'Implementación base de repositorios',
                'ApiResponse' => 'Trait para respuestas API estandarizadas'
            ],
            'core_services' => [
                'NotificationService' => 'Servicio de notificaciones (Email, SMS, Push)',
                'FileService' => 'Servicio de manejo de archivos e imágenes',
                'AuthService' => 'Servicio de autenticación y gestión de usuarios',
                'AuthorizationService' => 'Servicio de autorización y permisos',
                'ConfigurationService' => 'Servicio de configuración dinámica',
                'BaseNotification' => 'Clase base para notificaciones'
            ]
        ];

        foreach ($services as $phaseName => $phaseServices) {
            if ($phase && $phase !== $phaseName) {
                continue;
            }

            $this->line('');
            $this->info("FASE: {$phaseName}");
            $this->line(str_repeat('-', strlen($phaseName) + 6));

            foreach ($phaseServices as $serviceName => $description) {
                $this->line("• <comment>{$serviceName}</comment>: {$description}");
            }
        }

        if ($verbose) {
            $this->line('');
            $this->info('Detalles adicionales:');
            $this->line('• Todos los servicios incluyen logging estructurado');
            $this->line('• Integración con sistema de caché');
            $this->line('• Manejo de errores consistente');
            $this->line('• Preparados para testing unitario');
        }
    }

    /**
     * Mostrar ayuda del comando
     */
    protected function showHelp(): void
    {
        $this->line('');
        $this->info('Acciones disponibles:');
        $this->line('• status: Mostrar estado actual del refactoring');
        $this->line('• analyze: Analizar el código base');
        $this->line('• progress: Mostrar progreso del refactoring');
        $this->line('• metrics: Mostrar métricas del refactoring');
        $this->line('• services: Mostrar servicios disponibles');
        $this->line('• cleanup: Limpiar archivos temporales y caché');
        $this->line('');
        $this->info('Opciones:');
        $this->line('• --phase: Especificar fase específica');
        $this->line('• --verbose: Mostrar información detallada');
        $this->line('');
        $this->info('Ejemplos:');
        $this->line('• php artisan refactoring:manage status');
        $this->line('• php artisan refactoring:manage analyze --phase=fundamentos --verbose');
        $this->line('• php artisan refactoring:manage progress');
    }
}
