<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Interfaces\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function getAll(): Collection
    {
        return Usuario::all();
    }

    public function findById(int $id): ?Usuario
    {
        return Usuario::find($id);
    }

    public function create(array $data): Usuario
    {
        return Usuario::create([
            'nombre_usuario' => $data['nombre_usuario'],
            'correo_electronico' => $data['correo_electronico'],
            'contrasena' => $data['contrasena'],
            'telefono' => $data['telefono'],
            'rol' => $data['rol'] ?? 'cliente',
            'estado' => $data['estado'] ?? true,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $usuario = $this->findById($id);
        if (!$usuario) {
            return false;
        }

        $updateData = [
            'nombre_usuario' => $data['nombre_usuario'],
            'correo_electronico' => $data['correo_electronico'],
            'telefono' => $data['telefono'] ?? null,
            'rol' => $data['rol'],
            'estado' => $data['estado']
        ];

        if (!empty($data['contrasena'])) {
            $updateData['contrasena'] = Hash::make($data['contrasena']);
        }

        return $usuario->update($updateData);
    }

    public function delete(int $id): bool
    {
        $usuario = $this->findById($id);
        if (!$usuario) {
            return false;
        }

        if ($usuario->foto_perfil) {
            $this->deleteProfileImage($usuario);
        }

        return $usuario->delete();
    }

    public function updateProfileImage(Usuario $usuario, $image): bool
    {
        if ($usuario->foto_perfil) {
            $this->deleteProfileImage($usuario);
        }

        $path = $image->store('perfiles', 'public');
        return $usuario->update(['foto_perfil' => $path]);
    }

    public function deleteProfileImage(Usuario $usuario): bool
    {
        if ($usuario->foto_perfil) {
            Storage::disk('public')->delete($usuario->foto_perfil);
            return $usuario->update(['foto_perfil' => null]);
        }
        return true;
    }

    public function updateRole(int $id, string $role): bool
    {
        $usuario = $this->findById($id);
        if (!$usuario) {
            return false;
        }

        return $usuario->update(['rol' => $role]);
    }

    public function toggleStatus(int $id): bool
    {
        $usuario = $this->findById($id);
        if (!$usuario) {
            return false;
        }

        return $usuario->update(['estado' => !$usuario->estado]);
    }
} 