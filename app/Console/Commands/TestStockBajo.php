<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Producto;
use App\Mail\StockBajo;
use Illuminate\Support\Facades\Mail;

class TestStockBajo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:stock-bajo {email} {producto_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar el Mailable de stock bajo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $productoId = $this->argument('producto_id');
        
        $this->info("⚠️ Probando Mailable de stock bajo...");
        $this->info("📧 Email: {$email}");
        
        // Buscar el producto
        if ($productoId) {
            $producto = Producto::find($productoId);
            if (!$producto) {
                $this->error("❌ Producto no encontrado con ID: {$productoId}");
                return 1;
            }
        } else {
            // Buscar el primer producto disponible
            $producto = Producto::first();
            if (!$producto) {
                $this->error("❌ No hay productos en la base de datos");
                return 1;
            }
        }
        
        $this->info("✅ Producto encontrado: {$producto->nombre}");
        
        // Simular stock bajo
        $stockActual = 5;
        $stockMinimo = 10;
        
        $this->info("📊 Stock simulado:");
        $this->info("   • Stock Actual: {$stockActual}");
        $this->info("   • Stock Mínimo: {$stockMinimo}");
        $this->info("   • Estado: CRÍTICO");
        
        $this->info("\n📧 Enviando email de stock bajo...");
        
        try {
            $this->testStockBajo($producto, $stockActual, $stockMinimo);
            
            $this->info("\n✅ Email de stock bajo enviado exitosamente!");
            $this->info("📬 Revisa tu bandeja de entrada (y carpeta de spam)");
            
        } catch (\Exception $e) {
            $this->error("❌ Error al enviar email: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function testStockBajo($producto, $stockActual, $stockMinimo)
    {
        $this->info("   ⚠️ Enviando email de stock bajo...");
        
        Mail::to($this->argument('email'))
            ->send(new StockBajo($producto, $stockActual, $stockMinimo));
        
        $this->info("      ✅ Email de stock bajo enviado");
    }
}
