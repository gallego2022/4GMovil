<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Direccion;
use App\Models\Usuario;

class DireccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el usuario admin
        $admin = Usuario::where('correo_electronico', '4gmoviltest@gmail.com')->first();
        
        if ($admin) {
            // Crear direcciones de ejemplo para el admin
            Direccion::create([
                'usuario_id' => $admin->usuario_id,
                'nombre_destinatario' => 'Admin 4GMovil',
                'telefono' => '3001234567',
                'calle' => 'Av. Corrientes',
                'numero' => '1234',
                'piso' => '5',
                'departamento' => 'A',
                'codigo_postal' => '1043',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'Buenos Aires',
                'pais' => 'Argentina',
                'referencias' => 'Edificio azul, timbre 5A',
                'predeterminada' => true,
                'tipo_direccion' => 'trabajo',
                'activo' => true
            ]);

            Direccion::create([
                'usuario_id' => $admin->usuario_id,
                'nombre_destinatario' => 'Admin 4GMovil',
                'telefono' => '3001234567',
                'calle' => 'Calle Florida',
                'numero' => '567',
                'piso' => null,
                'departamento' => null,
                'codigo_postal' => '1005',
                'ciudad' => 'Buenos Aires',
                'provincia' => 'Buenos Aires',
                'pais' => 'Argentina',
                'referencias' => 'Casa familiar',
                'predeterminada' => false,
                'tipo_direccion' => 'casa',
                'activo' => true
            ]);

            $this->command->info('🏠 Direcciones de ejemplo creadas para el usuario admin');
        } else {
            $this->command->warn('⚠️ Usuario admin no encontrado. Ejecuta primero el DatabaseSeeder.');
        }
    }
}
