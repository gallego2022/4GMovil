<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pedido;
use App\Models\EstadoPedido;
use App\Models\Usuario;
use App\Http\Controllers\Admin\PedidoAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestAdminOrderRestrictions extends Command
{
    protected $signature = 'test:admin-order-restrictions';
    protected $description = 'Probar las restricciones de cambio de estado en pedidos';

    public function handle()
    {
        $this->info('🧪 Probando restricciones de cambio de estado en pedidos...');
        
        // 1. Verificar que hay pedidos en la base de datos
        $pedidos = Pedido::with('estado')->get();
        if ($pedidos->isEmpty()) {
            $this->error('❌ No hay pedidos en la base de datos');
            return 1;
        }
        $this->info('✅ Pedidos encontrados: ' . $pedidos->count());
        
        // 2. Verificar que hay un admin
        $admin = Usuario::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();
        
        if (!$admin) {
            $this->error('❌ No hay usuarios admin en la base de datos');
            return 1;
        }
        $this->info('✅ Admin encontrado: ' . $admin->email);
        
        // 3. Autenticar como admin
        Auth::login($admin);
        $this->info('✅ Autenticado como admin');
        
        // 4. Probar con cada pedido
        foreach ($pedidos as $pedido) {
            $this->info("\n📦 Probando pedido #{$pedido->pedido_id} - Estado: {$pedido->estado->nombre}");
            
            // Verificar si permite cambio de estado
            $estadosFinales = ['cancelado', 'confirmado', 'entregado'];
            $estadoActual = strtolower($pedido->estado->nombre ?? '');
            $permiteCambio = !in_array($estadoActual, $estadosFinales);
            
            if ($permiteCambio) {
                $this->info("   ✅ Permite cambio de estado");
                
                // Probar cambio a un estado válido
                $estadosDisponibles = EstadoPedido::where('estado_id', '!=', $pedido->estado_id)->get();
                if ($estadosDisponibles->isNotEmpty()) {
                    $nuevoEstado = $estadosDisponibles->first();
                    $this->info("   🔄 Cambiando a estado: {$nuevoEstado->nombre}");
                    
                    try {
                        $request = new Request(['estado_id' => $nuevoEstado->estado_id]);
                        $controller = new PedidoAdminController();
                        $response = $controller->updateEstado($request, $pedido->pedido_id);
                        
                        if ($response->getSession()->get('tipo') === 'success') {
                            $this->info("   ✅ Cambio exitoso");
                        } else {
                            $this->warn("   ⚠️  Cambio con advertencia: " . $response->getSession()->get('mensaje'));
                        }
                    } catch (\Exception $e) {
                        $this->error("   ❌ Error en cambio: " . $e->getMessage());
                    }
                }
            } else {
                $this->warn("   ⚠️  NO permite cambio de estado (estado final)");
                
                // Probar intento de cambio (debería fallar)
                $estadosDisponibles = EstadoPedido::where('estado_id', '!=', $pedido->estado_id)->get();
                if ($estadosDisponibles->isNotEmpty()) {
                    $nuevoEstado = $estadosDisponibles->first();
                    $this->info("   🚫 Intentando cambio a: {$nuevoEstado->nombre} (debería fallar)");
                    
                    try {
                        $request = new Request(['estado_id' => $nuevoEstado->estado_id]);
                        $controller = new PedidoAdminController();
                        $response = $controller->updateEstado($request, $pedido->pedido_id);
                        
                        if ($response->getSession()->get('tipo') === 'warning') {
                            $this->info("   ✅ Restricción funcionando: " . $response->getSession()->get('mensaje'));
                        } else {
                            $this->error("   ❌ ERROR: Debería haber sido bloqueado");
                        }
                    } catch (\Exception $e) {
                        $this->info("   ✅ Restricción funcionando: " . $e->getMessage());
                    }
                }
            }
        }
        
        // 5. Resumen de estados
        $this->info("\n📊 Resumen de estados:");
        $estadosCount = Pedido::with('estado')
            ->get()
            ->groupBy('estado.nombre')
            ->map(function($pedidos) {
                return $pedidos->count();
            });
            
        foreach ($estadosCount as $estado => $count) {
            $estadosFinales = ['cancelado', 'confirmado', 'entregado'];
            $esFinal = in_array(strtolower($estado), $estadosFinales);
            $icono = $esFinal ? '🔒' : '🔓';
            $this->info("   {$icono} {$estado}: {$count} pedidos");
        }
        
        $this->info("\n🎉 Prueba completada");
        return 0;
    }
}
