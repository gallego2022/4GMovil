<?php

namespace App\Console\Commands;

use App\Models\VarianteProducto;
use App\Mail\StockBajoVariante;
use App\Mail\StockAgotadoVariante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProbarEmailsVariantes extends Command
{
    protected $signature = 'variantes:probar-emails {--email= : Email de destino para la prueba}';
    protected $description = 'Probar el envío de emails de alertas de variantes';

    public function handle()
    {
        $emailDestino = $this->option('email') ?: 'osmandavidgallego@gmail.com';
        
        $this->info("🧪 Probando envío de emails de variantes...");
        $this->info("📧 Email de destino: {$emailDestino}");

        // Buscar una variante para las pruebas
        $variante = VarianteProducto::with('producto')->first();
        
        if (!$variante) {
            $this->error('❌ No hay variantes disponibles para la prueba.');
            return Command::FAILURE;
        }

        $this->info("📦 Usando variante: {$variante->producto->nombre_producto} ({$variante->nombre})");

        try {
            // Probar email de stock agotado
            $this->info("📤 Enviando email de stock agotado...");
            Mail::to($emailDestino)->send(new StockAgotadoVariante($variante));
            $this->info("✅ Email de stock agotado enviado correctamente");

            // Probar email de stock crítico
            $this->info("📤 Enviando email de stock crítico...");
            Mail::to($emailDestino)->send(new StockBajoVariante($variante, 'critico'));
            $this->info("✅ Email de stock crítico enviado correctamente");

            // Probar email de stock bajo
            $this->info("📤 Enviando email de stock bajo...");
            Mail::to($emailDestino)->send(new StockBajoVariante($variante, 'bajo'));
            $this->info("✅ Email de stock bajo enviado correctamente");

            $this->info("🎉 Todos los emails fueron enviados correctamente!");
            $this->info("📧 Revisa tu bandeja de entrada en: {$emailDestino}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error enviando emails: " . $e->getMessage());
            $this->error("🔍 Verifica la configuración de email en tu archivo .env");
            
            return Command::FAILURE;
        }
    }
}
