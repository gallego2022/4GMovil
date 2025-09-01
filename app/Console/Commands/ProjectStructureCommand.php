<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class ProjectStructureCommand extends Command
{
    protected $signature = 'project:structure {--depth=3 : Profundidad máxima de directorios} {--exclude= : Directorios a excluir (separados por coma)} {--output= : Archivo de salida (opcional)}';
    protected $description = 'Muestra la estructura de archivos del proyecto de manera organizada';

    protected $excludedDirs = [
        'vendor',
        'node_modules',
        '.git',
        'storage/logs',
        'storage/framework/cache',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/framework/testing',
        'bootstrap/cache',
        '.vscode',
        '.idea',
        'tests/Feature',
        'tests/Unit/Services/Simple*',
    ];

    protected $fileExtensions = [
        'php' => '🔵',
        'js' => '🟡',
        'vue' => '🟢',
        'css' => '🟣',
        'scss' => '🟣',
        'html' => '🟠',
        'blade.php' => '🟠',
        'md' => '📝',
        'json' => '📋',
        'xml' => '📄',
        'yml' => '⚙️',
        'env' => '🔧',
        'sql' => '🗄️',
        'sh' => '🐚',
        'bat' => '🪟',
    ];

    public function handle()
    {
        $depth = (int) $this->option('depth');
        $exclude = $this->option('exclude');
        $outputFile = $this->option('output');

        if ($exclude) {
            $this->excludedDirs = array_merge($this->excludedDirs, explode(',', $exclude));
        }

        $this->info('🌳 ESTRUCTURA DEL PROYECTO LARAVEL');
        $this->info('=====================================');
        $this->line('');

        $structure = $this->buildStructure(base_path(), $depth);
        $output = $this->formatStructure($structure);

        if ($outputFile) {
            File::put($outputFile, $output);
            $this->info("✅ Estructura guardada en: {$outputFile}");
        } else {
            $this->line($output);
        }

        // Mostrar estadísticas
        $this->showStatistics($structure);
    }

    protected function buildStructure($path, $depth, $currentDepth = 0)
    {
        if ($currentDepth > $depth) {
            return null;
        }

        $items = [];
        $files = File::files($path);
        $directories = File::directories($path);

        // Procesar archivos
        foreach ($files as $file) {
            try {
                $relativePath = str_replace(base_path() . '/', '', $file->getPathname());
                if (!$this->shouldExclude($relativePath)) {
                    $items[] = [
                        'type' => 'file',
                        'name' => $file->getFilename(),
                        'path' => $relativePath,
                        'size' => $this->getFileSize($file),
                        'extension' => $file->getExtension()
                    ];
                }
            } catch (\Exception $e) {
                // Ignorar archivos que no se pueden leer
                continue;
            }
        }

        // Procesar directorios
        foreach ($directories as $directory) {
            try {
                $relativePath = str_replace(base_path() . '/', '', $directory);
                if (!$this->shouldExclude($relativePath)) {
                    $subStructure = $this->buildStructure($directory, $depth, $currentDepth + 1);
                    if ($subStructure !== null) {
                        $items[] = [
                            'type' => 'directory',
                            'name' => basename($directory),
                            'path' => $relativePath,
                            'children' => $subStructure
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Ignorar directorios que no se pueden leer
                continue;
            }
        }

        return $items;
    }

    protected function shouldExclude($path)
    {
        foreach ($this->excludedDirs as $excluded) {
            if (strpos($path, $excluded) === 0) {
                return true;
            }
        }
        return false;
    }

    protected function formatStructure($items, $indent = '')
    {
        $output = '';
        $lastIndex = count($items) - 1;

        foreach ($items as $index => $item) {
            $isLast = $index === $lastIndex;
            $prefix = $isLast ? '└── ' : '├── ';
            $nextIndent = $isLast ? '    ' : '│   ';

            if ($item['type'] === 'directory') {
                $output .= $indent . $prefix . '📁 ' . $item['name'] . '/';
                if (isset($item['children']) && !empty($item['children'])) {
                    $output .= "\n";
                    $output .= $this->formatStructure($item['children'], $indent . $nextIndent);
                }
            } else {
                $icon = $this->getFileIcon($item['extension']);
                $size = $this->formatFileSize($item['size']);
                $output .= $indent . $prefix . $icon . ' ' . $item['name'] . ' (' . $size . ')';
            }

            if ($index < $lastIndex) {
                $output .= "\n";
            }
        }

        return $output;
    }

    protected function getFileSize($file)
    {
        try {
            return $file->getSize();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getFileIcon($extension)
    {
        foreach ($this->fileExtensions as $ext => $icon) {
            if ($extension === $ext) {
                return $icon;
            }
        }
        return '📄';
    }

    protected function formatFileSize($bytes)
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . ' KB';
        } else {
            return round($bytes / (1024 * 1024), 1) . ' MB';
        }
    }

    protected function showStatistics($structure)
    {
        $this->line('');
        $this->info('📊 ESTADÍSTICAS DEL PROYECTO');
        $this->info('==============================');

        $stats = $this->calculateStatistics($structure);
        
        $this->line("📁 Total de directorios: {$stats['directories']}");
        $this->line("📄 Total de archivos: {$stats['files']}");
        $this->line("💾 Tamaño total: " . $this->formatFileSize($stats['totalSize']));
        
        $this->line('');
        $this->info('📋 EXTENSIONES MÁS COMUNES:');
        foreach ($stats['extensions'] as $ext => $count) {
            $icon = $this->getFileIcon($ext);
            $this->line("   {$icon} .{$ext}: {$count} archivos");
        }

        $this->line('');
        $this->info('🎯 DIRECTORIOS PRINCIPALES:');
        $this->line('   📁 app/ - Lógica de la aplicación');
        $this->line('   📁 config/ - Configuraciones');
        $this->line('   📁 database/ - Migraciones y seeders');
        $this->line('   📁 resources/ - Vistas y assets');
        $this->line('   📁 routes/ - Definición de rutas');
        $this->line('   📁 tests/ - Pruebas unitarias');
        $this->line('   📁 storage/ - Archivos temporales');
        $this->line('   📁 public/ - Archivos públicos');
    }

    protected function calculateStatistics($items)
    {
        $stats = [
            'directories' => 0,
            'files' => 0,
            'totalSize' => 0,
            'extensions' => []
        ];

        foreach ($items as $item) {
            if ($item['type'] === 'directory') {
                $stats['directories']++;
                if (isset($item['children'])) {
                    $subStats = $this->calculateStatistics($item['children']);
                    $stats['directories'] += $subStats['directories'];
                    $stats['files'] += $subStats['files'];
                    $stats['totalSize'] += $subStats['totalSize'];
                    $stats['extensions'] = array_merge($stats['extensions'], $subStats['extensions']);
                }
            } else {
                $stats['files']++;
                $stats['totalSize'] += $item['size'];
                $ext = $item['extension'];
                $stats['extensions'][$ext] = ($stats['extensions'][$ext] ?? 0) + 1;
            }
        }

        // Ordenar extensiones por cantidad
        arsort($stats['extensions']);

        return $stats;
    }
}
