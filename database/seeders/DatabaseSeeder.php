<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario administrador por defecto
        Usuario::firstOrCreate(
            ['correo_electronico' => 'admin@4gmovil.com'],
            [
                'nombre_usuario' => 'Administrador',
                'correo_electronico' => 'admin@4gmovil.com',
                'contrasena' => Hash::make('Admin123!'),
                'telefono' => '3000000000',
                'estado' => true,
                'rol' => 'admin',
                'fecha_registro' => now(),
                'email_verified_at' => now(),
            ]
        );

        // Ejecutar seeders específicos
        $this->call([
            MetodosPagoSeeder::class,
            EstadosPedidoSeeder::class,
        ]);

        $this->command->info('✅ Base de datos poblada exitosamente!');
        $this->command->info('👤 Usuario admin creado: admin@4gmovil.com / Admin123!');
    }
}
