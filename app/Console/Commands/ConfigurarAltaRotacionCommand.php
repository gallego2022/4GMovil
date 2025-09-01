<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigurarAltaRotacionCommand extends Command
{
    protected $signature = 'inventario:configurar-alta-rotacion {--revertir : Revertir a configuración moderada}';
    protected $description = 'Configurar umbrales de stock para productos de alta rotación';

    public function handle()
    {
        if ($this->option('revertir')) {
            $this->configurarModerada();
        } else {
            $this->configurarAltaRotacion();
        }

        return 0;
    }

    private function configurarAltaRotacion(): void
    {
        $this->info('🚀 Configurando umbrales para productos de ALTA ROTACIÓN...');
        
        $this->actualizarVariableEnv('INVENTARIO_STOCK_BAJO', '0.6');
        $this->actualizarVariableEnv('INVENTARIO_STOCK_CRITICO', '0.2');
        
        $this->info('✅ Configuración aplicada correctamente.');
        $this->mostrarConfiguracion();
        
        $this->info('📊 EXPLICACIÓN DE LA CONFIGURACIÓN:');
        $this->info('=====================================');
        $this->line('• Stock Bajo: 60% del stock mínimo');
        $this->line('• Stock Crítico: 20% del stock mínimo');
        $this->line('• Ideal para: Electrónicos, consumibles, productos que se venden rápido');
        $this->line('• Ventaja: Alertas tempranas para evitar stock out');
        $this->line('• Consideración: Puede generar más alertas, pero evita pérdidas de ventas');
    }

    private function configurarModerada(): void
    {
        $this->info('⚖️ Configurando umbrales para configuración MODERADA...');
        
        $this->actualizarVariableEnv('INVENTARIO_STOCK_BAJO', '0.8');
        $this->actualizarVariableEnv('INVENTARIO_STOCK_CRITICO', '0.3');
        
        $this->info('✅ Configuración aplicada correctamente.');
        $this->mostrarConfiguracion();
        
        $this->info('📊 EXPLICACIÓN DE LA CONFIGURACIÓN:');
        $this->info('=====================================');
        $this->line('• Stock Bajo: 80% del stock mínimo');
        $this->line('• Stock Crítico: 30% del stock mínimo');
        $this->line('• Ideal para: Productos de rotación media');
        $this->line('• Ventaja: Balance entre alertas y gestión de inventario');
    }

    private function actualizarVariableEnv(string $variable, string $valor): void
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            $this->error('Archivo .env no encontrado.');
            return;
        }

        $envContent = File::get($envFile);
        $nuevaLinea = "{$variable}={$valor}";

        // Buscar si ya existe la variable
        if (preg_match("/^{$variable}=.*/m", $envContent)) {
            // Reemplazar línea existente
            $envContent = preg_replace("/^{$variable}=.*/m", $nuevaLinea, $envContent);
        } else {
            // Agregar nueva línea al final
            $envContent .= "\n{$nuevaLinea}";
        }

        File::put($envFile, $envContent);
        
        $this->info("✅ Variable {$variable} configurada en: {$valor}");
    }

    private function mostrarConfiguracion(): void
    {
        $this->newLine();
        $this->info('📊 CONFIGURACIÓN ACTUAL:');
        $this->info('========================');
        
        $stockBajo = config('inventario.alertas.stock_bajo', 0.6);
        $stockCritico = config('inventario.alertas.stock_critico', 0.2);
        
        $this->table(
            ['Umbral', 'Porcentaje', 'Descripción'],
            [
                ['Stock Bajo', ($stockBajo * 100) . '%', 'Alerta cuando el stock está por debajo del ' . ($stockBajo * 100) . '% del mínimo'],
                ['Stock Crítico', ($stockCritico * 100) . '%', 'Alerta cuando el stock está por debajo del ' . ($stockCritico * 100) . '% del mínimo'],
            ]
        );

        $this->info('💡 EJEMPLO PRÁCTICO:');
        $this->info('=====================');
        $this->line('Si un producto tiene stock mínimo = 100 unidades:');
        $this->line('• Stock Bajo: ' . round($stockBajo * 100) . ' unidades');
        $this->line('• Stock Crítico: ' . round($stockCritico * 100) . ' unidades');
        
        $this->newLine();
        $this->info('🔄 Para aplicar los cambios, ejecuta:');
        $this->line('php artisan config:cache');
        $this->line('php artisan inventario:verificar-stock');
    }
} 