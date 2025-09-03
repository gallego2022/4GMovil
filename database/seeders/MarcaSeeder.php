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
                'estado' => true
            ],
            [
                'nombre' => 'Apple',
                'descripcion' => 'Empresa tecnológica estadounidense líder en innovación',
                'estado' => true
            ],
            [
                'nombre' => 'Xiaomi',
                'descripcion' => 'Empresa tecnológica china especializada en smartphones',
                'estado' => true
            ],
            [
                'nombre' => 'Huawei',
                'descripcion' => 'Empresa tecnológica china líder en telecomunicaciones',
                'estado' => true
            ],
            [
                'nombre' => 'Sony',
                'descripcion' => 'Empresa japonesa líder en audio y entretenimiento',
                'estado' => true
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
