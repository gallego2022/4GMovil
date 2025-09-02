<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marca;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen marcas
        if (Marca::count() > 0) {
            $this->command->info('Las marcas ya existen, saltando...');
            return;
        }

        $marcas = [
            [
                'nombre' => 'Samsung',
                'descripcion' => 'Empresa tecnológica surcoreana líder en smartphones',
                'activo' => true
            ],
            [
                'nombre' => 'Apple',
                'descripcion' => 'Empresa tecnológica estadounidense líder en innovación',
                'activo' => true
            ],
            [
                'nombre' => 'Xiaomi',
                'descripcion' => 'Empresa tecnológica china especializada en smartphones',
                'activo' => true
            ],
            [
                'nombre' => 'Huawei',
                'descripcion' => 'Empresa tecnológica china líder en telecomunicaciones',
                'activo' => true
            ],
            [
                'nombre' => 'Sony',
                'descripcion' => 'Empresa japonesa líder en audio y entretenimiento',
                'activo' => true
            ]
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }

        $this->command->info('✅ Marcas creadas exitosamente:');
        foreach ($marcas as $marca) {
            $this->command->info("   - {$marca['nombre']}");
        }
    }
}
