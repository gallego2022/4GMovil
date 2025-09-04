<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\EspecificacionCategoria;
use App\Models\EspecificacionProducto;
use Illuminate\Support\Facades\DB;

class EspecificacionesProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen especificaciones de productos
        if (EspecificacionProducto::count() > 0) {
            $this->command->info('Las especificaciones de productos ya existen, saltando...');
            return;
        }

        $this->command->info('🌱 Generando especificaciones de ejemplo para productos existentes...');

        // Obtener todos los productos
        $productos = Producto::with('categoria')->get();
        
        if ($productos->isEmpty()) {
            $this->command->warn('No hay productos para generar especificaciones');
            return;
        }

        $totalEspecificaciones = 0;

        foreach ($productos as $producto) {
            $especificacionesGeneradas = $this->generarEspecificacionesParaProducto($producto);
            $totalEspecificaciones += $especificacionesGeneradas;
        }

        $this->command->info("✅ Se generaron {$totalEspecificaciones} especificaciones para {$productos->count()} productos");
    }

    /**
     * Genera especificaciones de ejemplo para un producto específico
     */
    private function generarEspecificacionesParaProducto(Producto $producto): int
    {
        // Obtener las especificaciones de categoría disponibles
        $especificacionesCategoria = EspecificacionCategoria::where('categoria_id', $producto->categoria_id)
            ->where('estado', true)
            ->orderBy('orden')
            ->get();

        if ($especificacionesCategoria->isEmpty()) {
            $this->command->warn("   ⚠️  No hay especificaciones de categoría para: {$producto->categoria->nombre}");
            return 0;
        }

        $especificacionesGeneradas = 0;

        foreach ($especificacionesCategoria as $especCategoria) {
            $valor = $this->generarValorEjemplo($especCategoria, $producto);
            
            if ($valor !== null) {
                try {
                    EspecificacionProducto::create([
                        'producto_id' => $producto->producto_id,
                        'especificacion_id' => $especCategoria->especificacion_id,
                        'valor' => $valor,
                    ]);
                    
                    $especificacionesGeneradas++;
                } catch (\Exception $e) {
                    $this->command->error("   ❌ Error creando especificación {$especCategoria->nombre_campo}: " . $e->getMessage());
                }
            }
        }

        $this->command->info("   ✅ {$producto->nombre_producto}: {$especificacionesGeneradas} especificaciones generadas");
        return $especificacionesGeneradas;
    }

    /**
     * Genera un valor de ejemplo basado en el tipo de campo y el producto
     */
    private function generarValorEjemplo($especCategoria, Producto $producto): ?string
    {
        $nombreCampo = $especCategoria->nombre_campo;
        $tipoCampo = $especCategoria->tipo_campo;
        $categoriaNombre = $producto->categoria->nombre;

        // Valores específicos por categoría y campo
        switch ($categoriaNombre) {
            case 'Smartphones':
                return $this->generarValorSmartphone($nombreCampo, $tipoCampo);
            
            case 'Laptops':
                return $this->generarValorLaptop($nombreCampo, $tipoCampo);
            
            case 'Tablets':
                return $this->generarValorTablet($nombreCampo, $tipoCampo);
            
            case 'Auriculares':
                return $this->generarValorAuriculares($nombreCampo, $tipoCampo);
            
            case 'Bafles':
                return $this->generarValorBafles($nombreCampo, $tipoCampo);
            
            case 'Smartwatches':
                return $this->generarValorSmartwatch($nombreCampo, $tipoCampo);
            
            case 'Accesorios':
                return $this->generarValorAccesorio($nombreCampo, $tipoCampo);
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores específicos para smartphones
     */
    private function generarValorSmartphone(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($nombreCampo) {
            case 'pantalla':
                return ['5.5"', '6.1"', '6.7"', '7.0"'][array_rand([0,1,2,3])];
            
            case 'resolucion':
                return ['HD (1280x720)', 'Full HD (1920x1080)', '2K (2560x1440)', '4K (3840x2160)'][array_rand([0,1,2,3])];
            
            case 'ram':
                return ['4GB', '6GB', '8GB', '12GB', '16GB'][array_rand([0,1,2,3,4])];
            
            case 'almacenamiento':
                return ['64GB', '128GB', '256GB', '512GB', '1TB'][array_rand([0,1,2,3,4])];
            
            case 'procesador':
                return ['Snapdragon 8 Gen 3', 'Apple A17 Pro', 'MediaTek Dimensity 9200+', 'Exynos 2400'][array_rand([0,1,2,3])];
            
            case 'bateria':
                return ['4000', '4500', '5000', '5500', '6000'][array_rand([0,1,2,3,4])];
            
            case 'camara_principal':
                return ['48MP', '50MP', '108MP', '200MP'][array_rand([0,1,2,3])];
            
            case 'sistema_operativo':
                return ['Android 14', 'iOS 17', 'HarmonyOS 4'][array_rand([0,1,2])];
            
            case 'carga_rapida':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            case 'resistente_agua':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores específicos para laptops
     */
    private function generarValorLaptop(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($nombreCampo) {
            case 'pantalla':
                return ['13.3"', '14"', '15.6"', '16"', '17.3"'][array_rand([0,1,2,3,4])];
            
            case 'resolucion':
                return ['Full HD (1920x1080)', '2K (2560x1440)', '3K (2880x1800)', '4K (3840x2160)'][array_rand([0,1,2,3])];
            
            case 'ram':
                return ['8GB', '16GB', '32GB', '64GB'][array_rand([0,1,2,3])];
            
            case 'almacenamiento':
                return ['256GB', '512GB', '1TB', '2TB', '4TB'][array_rand([0,1,2,3,4])];
            
            case 'procesador':
                return ['Intel Core i5', 'Intel Core i7', 'Intel Core i9', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9', 'Apple M2', 'Apple M3'][array_rand([0,1,2,3,4,5,6,7])];
            
            case 'tarjeta_grafica':
                return ['Intel UHD', 'NVIDIA RTX 3050', 'NVIDIA RTX 3060', 'NVIDIA RTX 4070', 'AMD Radeon 660M', 'Apple M2 Pro'][array_rand([0,1,2,3,4,5])];
            
            case 'sistema_operativo':
                return ['Windows 11', 'macOS Sonoma', 'Ubuntu 22.04'][array_rand([0,1,2])];
            
            case 'bateria':
                return ['50Wh', '60Wh', '70Wh', '80Wh', '100Wh'][array_rand([0,1,2,3,4])];
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores específicos para tablets
     */
    private function generarValorTablet(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($nombreCampo) {
            case 'pantalla':
                return ['8"', '9.7"', '10.1"', '10.9"', '11"', '12.9"', '14.6"'][array_rand([0,1,2,3,4,5,6])];
            
            case 'resolucion':
                return ['HD (1280x800)', 'Full HD (1920x1200)', '2K (2560x1600)', '3K (2880x1920)'][array_rand([0,1,2,3])];
            
            case 'ram':
                return ['4GB', '6GB', '8GB', '12GB', '16GB'][array_rand([0,1,2,3,4])];
            
            case 'almacenamiento':
                return ['64GB', '128GB', '256GB', '512GB', '1TB'][array_rand([0,1,2,3,4])];
            
            case 'procesador':
                return ['Apple M2', 'Apple M1', 'Snapdragon 8 Gen 2', 'MediaTek Dimensity 9000+', 'Intel Core i3'][array_rand([0,1,2,3,4])];
            
            case 'bateria':
                return ['6000', '7000', '8000', '10000', '12000'][array_rand([0,1,2,3,4])];
            
            case 'sistema_operativo':
                return ['iPadOS 17', 'Android 13', 'Windows 11'][array_rand([0,1,2])];
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores específicos para auriculares
     */
    private function generarValorAuriculares(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($nombreCampo) {
            case 'tipo_conexion':
                return ['Bluetooth 5.0', 'Bluetooth 5.2', 'Bluetooth 5.3', 'Cable 3.5mm', 'USB-C'][array_rand([0,1,2,3,4])];
            
            case 'cancelacion_ruido':
                return ['Sí', 'No', 'Adaptativa'][array_rand([0,1,2])];
            
            case 'bateria':
                return ['20h', '25h', '30h', '35h', '40h'][array_rand([0,1,2,3,4])];
            
            case 'resistente_agua':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            case 'resistente_sudor':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores específicos para bafles
     */
    private function generarValorBafles(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($nombreCampo) {
            case 'potencia':
                return ['20W', '30W', '40W', '60W', '80W', '100W'][array_rand([0,1,2,3,4,5])];
            
            case 'tipo_conexion':
                return ['Bluetooth 5.0', 'Bluetooth 5.2', 'WiFi', 'Auxiliar 3.5mm', 'USB'][array_rand([0,1,2,3,4])];
            
            case 'bateria':
                return ['15h', '20h', '24h', '30h'][array_rand([0,1,2,3])];
            
            case 'resistente_agua':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            case 'resistente_polvo':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores específicos para smartwatches
     */
    private function generarValorSmartwatch(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($nombreCampo) {
            case 'pantalla':
                return ['1.4"', '1.6"', '1.8"', '2.0"', '2.2"', '2.4"'][array_rand([0,1,2,3,4,5])];
            
            case 'resolucion':
                return ['360x360', '390x390', '454x454', '480x480', '572x572'][array_rand([0,1,2,3,4])];
            
            case 'bateria':
                return ['3 días', '5 días', '7 días', '10 días', '14 días', '18 días'][array_rand([0,1,2,3,4,5])];
            
            case 'resistente_agua':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            case 'gps':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            case 'monitor_cardiaco':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores específicos para accesorios
     */
    private function generarValorAccesorio(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($nombreCampo) {
            case 'tipo_conexion':
                return ['USB-C', 'Lightning', 'Micro USB', 'Wireless', 'Bluetooth'][array_rand([0,1,2,3,4])];
            
            case 'potencia':
                return ['20W', '25W', '30W', '40W', '65W', '100W'][array_rand([0,1,2,3,4,5])];
            
            case 'compatible_con':
                return ['iPhone', 'Android', 'Universal', 'iPad', 'MacBook'][array_rand([0,1,2,3,4])];
            
            case 'resistente_agua':
                return $tipoCampo === 'checkbox' ? '1' : 'Sí';
            
            default:
                return $this->generarValorGenerico($nombreCampo, $tipoCampo);
        }
    }

    /**
     * Genera valores genéricos para campos no específicos
     */
    private function generarValorGenerico(string $nombreCampo, string $tipoCampo): ?string
    {
        switch ($tipoCampo) {
            case 'texto':
                return "Valor de ejemplo para {$nombreCampo}";
            
            case 'numero':
                return (string) rand(1, 100);
            
            case 'select':
                return "Opción 1";
            
            case 'checkbox':
                return rand(0, 1) ? '1' : '0';
            
            case 'textarea':
                return "Descripción de ejemplo para el campo {$nombreCampo}";
            
            default:
                return "Valor por defecto";
        }
    }
}
