<?php

namespace App\Repositories;

use App\Models\Categoria;
use App\Interfaces\CategoriaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoriaRepository implements CategoriaRepositoryInterface
{
    public function getAll(): Collection
    {
        return Categoria::all();
    }

    public function findById(int $id): ?Categoria
    {
        return Categoria::find($id);
    }

    public function create(array $data): Categoria
    {
        return Categoria::create([
            'nombre_categoria' => $data['nombre_categoria']
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $categoria = $this->findById($id);
        if (!$categoria) {
            return false;
        }

        return $categoria->update([
            'nombre_categoria' => $data['nombre_categoria']
        ]);
    }

    public function delete(int $id): bool
    {
        $categoria = $this->findById($id);
        if (!$categoria) {
            return false;
        }

        return $categoria->delete();
    }
} 