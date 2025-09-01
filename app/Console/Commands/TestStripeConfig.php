<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\Config;

class TestStripeConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stripe-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la configuración de Stripe y Cashier con la tabla usuarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔐 Probando configuración de Stripe y Cashier...\n");
        
        // Verificar configuración de Stripe
        $this->checkStripeConfig();
        
        // Verificar configuración de Cashier
        $this->checkCashierConfig();
        
        // Verificar modelo Usuario
        $this->checkUsuarioModel();
        
        // Verificar tabla usuarios
        $this->checkUsuariosTable();
        
        $this->info("\n✅ Verificación completada!");
        
        return 0;
    }
    
    private function checkStripeConfig()
    {
        $this->info("⚙️ Verificando configuración de Stripe...");
        
        $stripeVars = [
            'STRIPE_KEY' => 'Clave pública de Stripe',
            'STRIPE_SECRET' => 'Clave secreta de Stripe',
            'STRIPE_WEBHOOK_SECRET' => 'Secreto del webhook'
        ];
        
        foreach ($stripeVars as $var => $description) {
            $value = env($var);
            $status = $value ? '✅' : '❌';
            $this->line("   {$status} {$var}: " . ($value ? substr($value, 0, 20) . '...' : 'NO configurado'));
        }
    }
    
    private function checkCashierConfig()
    {
        $this->info("\n⚙️ Verificando configuración de Cashier...");
        
        $cashierVars = [
            'CASHIER_CURRENCY' => 'Moneda de Cashier',
            'CASHIER_CURRENCY_LOCALE' => 'Localización de moneda'
        ];
        
        foreach ($cashierVars as $var => $description) {
            $value = env($var);
            $status = $value ? '✅' : '⚠️';
            $this->line("   {$status} {$var}: " . ($value ?: 'Usando valor por defecto'));
        }
    }
    
    private function checkUsuarioModel()
    {
        $this->info("\n🔍 Verificando modelo Usuario...");
        
        try {
            $usuario = new Usuario();
            
            // Verificar que use Billable
            if (method_exists($usuario, 'stripe')) {
                $this->info("   ✅ Modelo implementa Billable trait");
            } else {
                $this->error("   ❌ Modelo NO implementa Billable trait");
            }
            
            // Verificar tabla
            $this->info("   ✅ Tabla configurada: " . $usuario->getTable());
            
            // Verificar clave primaria
            $this->info("   ✅ Clave primaria: " . $usuario->getKeyName());
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error al verificar modelo: " . $e->getMessage());
        }
    }
    
    private function checkUsuariosTable()
    {
        $this->info("\n🗄️ Verificando tabla usuarios...");
        
        try {
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('usuarios');
            
            $requiredStripeColumns = [
                'stripe_id',
                'pm_type', 
                'pm_last_four',
                'trial_ends_at'
            ];
            
            $this->info("   📋 Total columnas: " . count($columns));
            
            foreach ($requiredStripeColumns as $column) {
                $status = in_array($column, $columns) ? '✅' : '❌';
                $this->line("   {$status} {$column}");
            }
            
            // Verificar que no haya tabla users
            if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
                $this->warn("   ⚠️ Tabla 'users' también existe (puede causar conflictos)");
            } else {
                $this->info("   ✅ Solo existe tabla 'usuarios'");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Error al verificar tabla: " . $e->getMessage());
        }
    }
}
