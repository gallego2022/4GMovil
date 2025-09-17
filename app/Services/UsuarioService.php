<?php

namespace App\Services;

use App\Interfaces\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UsuarioService
{
    protected $usuarioRepository;

    public function __construct(UsuarioRepositoryInterface $usuarioRepository)
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function getAllUsers(): Collection
    {
        return $this->usuarioRepository->getAll();
    }

    public function getUserById(int $id)
    {
        return $this->usuarioRepository->findById($id);
    }

    public function updateUser(int $id, array $data, $profileImage = null): array
    {
        try {
            $success = $this->usuarioRepository->update($id, $data);

            if ($success && $profileImage) {
                $usuario = $this->usuarioRepository->findById($id);
                $this->usuarioRepository->updateProfileImage($usuario, $profileImage);
            }

            return [
                'success' => $success,
                'message' => $success ? 'Usuario actualizado correctamente.' : 'No se encontró el usuario.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ];
        }
    }

    public function deleteUser(int $id): array
    {
        try {
            $success = $this->usuarioRepository->delete($id);

            return [
                'success' => $success,
                'message' => $success ? 'Usuario eliminado correctamente.' : 'No se encontró el usuario.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
            ];
        }
    }

    public function updateRole(int $id, string $role): array
    {
        try {
            $success = $this->usuarioRepository->updateRole($id, $role);

            return [
                'success' => $success,
                'message' => $success ? 'Rol actualizado correctamente.' : 'No se encontró el usuario.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar el rol: ' . $e->getMessage()
            ];
        }
    }

    public function toggleUserStatus(int $id): array
    {
        try {
            $success = $this->usuarioRepository->toggleStatus($id);

            return [
                'success' => $success,
                'message' => $success ? 'Estado del usuario actualizado.' : 'No se encontró el usuario.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al cambiar el estado del usuario: ' . $e->getMessage()
            ];
        }
    }

    public function createUser(array $data): array
    {
        try {
            $usuario = $this->usuarioRepository->create($data);

            return [
                'success' => true,
                'message' => 'Usuario creado correctamente.',
                'usuario' => $usuario
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al crear el usuario: ' . $e->getMessage()
            ];
        }
    }
} 