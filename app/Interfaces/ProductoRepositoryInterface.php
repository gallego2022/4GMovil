<?php

namespace App\Interfaces;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Collection;

interface ProductoRepositoryInterface
{
    public function getAllWithRelations(): Collection;
    public function findById(int $id): ?Producto;
    public function create(array $data): Producto;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function attachImages(Producto $producto, array $images): void;
    public function deleteImages(Producto $producto): void;
} 