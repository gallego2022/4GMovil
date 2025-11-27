<?php

namespace App\Services\Catalog;

use App\Interfaces\CategoriaRepositoryInterface;
use App\Services\Infrastructure\RedisCacheService;
use Illuminate\Database\Eloquent\Collection;

class CategoriaService
{
    protected CategoriaRepositoryInterface $categoriaRepository;

    protected RedisCacheService $cacheService;

    public function __construct(
        CategoriaRepositoryInterface $categoriaRepository,
        RedisCacheService $cacheService
    ) {
        $this->categoriaRepository = $categoriaRepository;
        $this->cacheService = $cacheService;
    }

    protected function invalidateProductoFormCache(): void
    {
        $this->cacheService->forget('productos:form_data');
    }

    public function getAllCategorias(): Collection
    {
        return $this->categoriaRepository->getAll();
    }

    public function getCategoriaById(int $id): ?array
    {
        $categoria = $this->categoriaRepository->findById($id);

        return $categoria ? ['categoria' => $categoria] : null;
    }

    public function createCategoria(array $data): array
    {
        try {
            $categoria = $this->categoriaRepository->create($data);
            $this->invalidateProductoFormCache();

            return [
                'success' => true,
                'message' => 'Categoría creada correctamente.',
                'categoria' => $categoria,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear la categoría: '.$e->getMessage(),
            ];
        }
    }

    public function updateCategoria(int $id, array $data): array
    {
        try {
            $success = $this->categoriaRepository->update($id, $data);
            if ($success) {
                $this->invalidateProductoFormCache();
            }

            return [
                'success' => $success,
                'message' => $success ? 'Categoría actualizada correctamente.' : 'No se encontró la categoría.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar la categoría: '.$e->getMessage(),
            ];
        }
    }

    public function deleteCategoria(int $id): array
    {
        try {
            $success = $this->categoriaRepository->delete($id);
            if ($success) {
                $this->invalidateProductoFormCache();
            }

            return [
                'success' => $success,
                'message' => $success ? 'Categoría eliminada correctamente.' : 'No se encontró la categoría.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar la categoría: '.$e->getMessage(),
            ];
        }
    }
}
