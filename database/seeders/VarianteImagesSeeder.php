<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VarianteImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Agregando imágenes de variantes...');

        // Obtener todas las variantes
        $variantes = \App\Models\VarianteProducto::all();

        foreach ($variantes as $variante) {
            // Crear 2-3 imágenes por variante
            $numImages = rand(2, 3);
            
            for ($i = 0; $i < $numImages; $i++) {
                // Crear un archivo de imagen de ejemplo
                $imageName = "variante_" . $variante->variante_id . "_img_" . ($i + 1) . ".png";
                $imagePath = "variantes/" . $imageName;
                
                // Crear una imagen de ejemplo (1x1 pixel PNG)
                $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
                
                // Guardar la imagen en el storage
                \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, $imageData);
                
                // Crear el registro en la base de datos
                \App\Models\ImagenVariante::create([
                    'variante_id' => $variante->variante_id,
                    'ruta_imagen' => $imagePath,
                    'nombre_archivo' => $imageName,
                    'tipo_mime' => 'image/png',
                    'tamaño_bytes' => strlen($imageData),
                    'orden' => $i + 1,
                    'es_principal' => $i === 0
                ]);
            }
        }

        $this->command->info('Imágenes de variantes agregadas exitosamente.');
    }
}
