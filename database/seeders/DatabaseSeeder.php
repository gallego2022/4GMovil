<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
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
            ['correo_electronico' => '4gmoviltest@gmail.com'],
            [
                'nombre_usuario' => 'Administrador',
                'correo_electronico' => '4gmoviltest@gmail.com',
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
            MetodosPagoSeeder::class, // Métodos de pago
            EstadosPedidoSeeder::class, // Estados de pedido
            MarcaSeeder::class, // Marcas de productos
        ]);

        $this->command->info('✅ Base de datos poblada exitosamente!');
        $this->command->info('👤 Usuario admin creado: 4gmoviltest@gmail.com / Admin123!');
    }
}
