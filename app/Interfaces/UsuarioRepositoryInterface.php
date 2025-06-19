<?php

namespace App\Interfaces;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

interface UsuarioRepositoryInterface
{
    public function getAll(): Collection;
    public function findById(int $id): ?Usuario;
    public function create(array $data): Usuario;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function updateProfileImage(Usuario $usuario, $image): bool;
    public function deleteProfileImage(Usuario $usuario): bool;
    public function updateRole(int $id, string $role): bool;
    public function toggleStatus(int $id): bool;
} 