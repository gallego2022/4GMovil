<?php

namespace App\Interfaces;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Collection;

interface CategoriaRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Categoria;
    public function create(array $data): Categoria;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
} 