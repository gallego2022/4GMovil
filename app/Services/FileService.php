<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileService
{
    protected $loggingService;
    protected $cacheService;
    protected $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    protected $allowedDocumentTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
    protected $maxFileSize = 10240; // 10MB en KB

    public function __construct(LoggingService $loggingService, CacheService $cacheService)
    {
        $this->loggingService = $loggingService;
        $this->cacheService = $cacheService;
    }

    /**
     * Subir archivo
     */
    public function uploadFile(UploadedFile $file, string $path = 'uploads', array $options = []): array
    {
        try {
            $this->loggingService->info('Iniciando upload de archivo', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'path' => $path
            ]);

            // Validar archivo
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return $validation;
            }

            // Generar nombre único
            $filename = $this->generateUniqueFilename($file);
            $fullPath = $path . '/' . $filename;

            // Almacenar archivo
            $stored = Storage::disk('public')->putFileAs($path, $file, $filename);
            
            if (!$stored) {
                throw new \Exception('No se pudo almacenar el archivo');
            }

            // Procesar archivo si es imagen
            $processed = $this->processFile($fullPath, $file, $options);

            $result = [
                'valid' => true,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $fullPath,
                'url' => asset('storage/' . $fullPath),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'processed' => $processed
            ];

            $this->loggingService->info('Archivo subido exitosamente', $result);
            return $result;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al subir archivo', [
                'filename' => $file->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);

            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validar archivo
     */
    protected function validateFile(UploadedFile $file): array
    {
        $errors = [];

        // Validar tamaño
        if ($file->getSize() > ($this->maxFileSize * 1024)) {
            $errors[] = "El archivo excede el tamaño máximo permitido ({$this->maxFileSize}MB)";
        }

        // Validar tipo MIME
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedTypes = array_merge($this->allowedImageTypes, $this->allowedDocumentTypes);
        
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = "Tipo de archivo no permitido. Tipos válidos: " . implode(', ', $allowedTypes);
        }

        // Validar que sea un archivo válido
        if (!$file->isValid()) {
            $errors[] = "El archivo no es válido";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generar nombre único para archivo
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $basename = Str::slug($basename);
        
        return $basename . '_' . time() . '_' . Str::random(8) . '.' . $extension;
    }

    /**
     * Procesar archivo
     */
    protected function processFile(string $path, UploadedFile $file, array $options): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $processed = [];

        // Procesar imagen si es necesario
        if (in_array($extension, $this->allowedImageTypes) && isset($options['process_image'])) {
            $processed = $this->processImage($path, $options);
        }

        // Generar miniaturas si se solicita
        if (in_array($extension, $this->allowedImageTypes) && isset($options['generate_thumbnails'])) {
            $thumbnails = $this->generateThumbnails($path, $options['thumbnails'] ?? []);
            $processed['thumbnails'] = $thumbnails;
        }

        return $processed;
    }

    /**
     * Procesar imagen
     */
    protected function processImage(string $path, array $options): array
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            $image = Image::make($fullPath);

            $processed = [];

            // Redimensionar si se especifica
            if (isset($options['resize'])) {
                $width = $options['resize']['width'] ?? null;
                $height = $options['resize']['height'] ?? null;
                
                if ($width || $height) {
                    $image->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $processed['resized'] = true;
                }
            }

            // Optimizar calidad
            if (isset($options['quality'])) {
                $image->save($fullPath, $options['quality']);
                $processed['optimized'] = true;
            }

            // Convertir formato si se especifica
            if (isset($options['convert_to'])) {
                $newPath = str_replace('.' . pathinfo($path, PATHINFO_EXTENSION), '.' . $options['convert_to'], $path);
                $image->save(Storage::disk('public')->path($newPath));
                $processed['converted'] = $options['convert_to'];
            }

            return $processed;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al procesar imagen', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Generar miniaturas
     */
    protected function generateThumbnails(string $path, array $sizes): array
    {
        $thumbnails = [];
        
        try {
            $fullPath = Storage::disk('public')->path($path);
            $image = Image::make($fullPath);
            $pathInfo = pathinfo($path);

            foreach ($sizes as $size) {
                $width = $size['width'] ?? 150;
                $height = $size['height'] ?? 150;
                $suffix = $size['suffix'] ?? "{$width}x{$height}";

                $thumbnailPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . "_{$suffix}." . $pathInfo['extension'];
                
                $thumbnail = $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $thumbnail->save(Storage::disk('public')->path($thumbnailPath));
                
                $thumbnails[$suffix] = [
                    'path' => $thumbnailPath,
                    'url' => asset('storage/' . $thumbnailPath),
                    'size' => $width . 'x' . $height
                ];
            }

        } catch (\Exception $e) {
            $this->loggingService->error('Error al generar miniaturas', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }

        return $thumbnails;
    }

    /**
     * Eliminar archivo
     */
    public function deleteFile(string $path): bool
    {
        try {
            $this->loggingService->info('Eliminando archivo', ['path' => $path]);

            // Eliminar archivo principal
            $deleted = Storage::disk('public')->delete($path);

            // Buscar y eliminar miniaturas relacionadas
            $this->deleteRelatedThumbnails($path);

            if ($deleted) {
                $this->loggingService->info('Archivo eliminado exitosamente', ['path' => $path]);
            }

            return $deleted;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al eliminar archivo', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Eliminar miniaturas relacionadas
     */
    protected function deleteRelatedThumbnails(string $path): void
    {
        try {
            $pathInfo = pathinfo($path);
            $pattern = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_*.' . $pathInfo['extension'];
            
            $files = Storage::disk('public')->files($pathInfo['dirname']);
            
            foreach ($files as $file) {
                if (preg_match('/' . preg_quote($pathInfo['filename'], '/') . '_\d+x\d+\.' . $pathInfo['extension'] . '$/', $file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        } catch (\Exception $e) {
            $this->loggingService->warning('Error al eliminar miniaturas', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mover archivo
     */
    public function moveFile(string $fromPath, string $toPath): bool
    {
        try {
            $this->loggingService->info('Moviendo archivo', [
                'from' => $fromPath,
                'to' => $toPath
            ]);

            $moved = Storage::disk('public')->move($fromPath, $toPath);

            if ($moved) {
                $this->loggingService->info('Archivo movido exitosamente');
            }

            return $moved;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al mover archivo', [
                'from' => $fromPath,
                'to' => $toPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Copiar archivo
     */
    public function copyFile(string $fromPath, string $toPath): bool
    {
        try {
            $this->loggingService->info('Copiando archivo', [
                'from' => $fromPath,
                'to' => $toPath
            ]);

            $copied = Storage::disk('public')->copy($fromPath, $toPath);

            if ($copied) {
                $this->loggingService->info('Archivo copiado exitosamente');
            }

            return $copied;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al copiar archivo', [
                'from' => $fromPath,
                'to' => $toPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener información del archivo
     */
    public function getFileInfo(string $path): array
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                return ['exists' => false];
            }

            $info = [
                'exists' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'size' => Storage::disk('public')->size($path),
                'last_modified' => Storage::disk('public')->lastModified($path),
                'mime_type' => mime_content_type(Storage::disk('public')->path($path))
            ];

            // Información adicional para imágenes
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($extension, $this->allowedImageTypes)) {
                $fullPath = Storage::disk('public')->path($path);
                if (File::exists($fullPath)) {
                    $imageInfo = getimagesize($fullPath);
                    if ($imageInfo) {
                        $info['dimensions'] = [
                            'width' => $imageInfo[0],
                            'height' => $imageInfo[1]
                        ];
                    }
                }
            }

            return $info;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener información del archivo', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return ['exists' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Limpiar archivos temporales
     */
    public function cleanupTempFiles(string $path = 'temp', int $maxAge = 86400): int
    {
        try {
            $this->loggingService->info('Limpiando archivos temporales', [
                'path' => $path,
                'max_age' => $maxAge
            ]);

            $deleted = 0;
            $files = Storage::disk('public')->files($path);
            $now = time();

            foreach ($files as $file) {
                $lastModified = Storage::disk('public')->lastModified($file);
                if (($now - $lastModified) > $maxAge) {
                    if (Storage::disk('public')->delete($file)) {
                        $deleted++;
                    }
                }
            }

            $this->loggingService->info('Limpieza completada', ['deleted' => $deleted]);
            return $deleted;

        } catch (\Exception $e) {
            $this->loggingService->error('Error en limpieza de archivos temporales', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
}
