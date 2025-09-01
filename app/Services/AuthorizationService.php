<?php

namespace App\Services;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Models\Usuario;

class AuthorizationService
{
    protected $loggingService;
    protected $cacheService;
    protected $cachePrefix = 'auth_permissions';

    public function __construct(LoggingService $loggingService, CacheService $cacheService)
    {
        $this->loggingService = $loggingService;
        $this->cacheService = $cacheService;
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission(Usuario $user, string $permission): bool
    {
        try {
            $cacheKey = "{$this->cachePrefix}_{$user->id}_{$permission}";
            
            return $this->cacheService->remember($cacheKey, 3600, function () use ($user, $permission) {
                $hasPermission = $this->checkUserPermission($user, $permission);
                
                $this->loggingService->info('Verificación de permiso', [
                    'user_id' => $user->id,
                    'permission' => $permission,
                    'result' => $hasPermission
                ]);
                
                return $hasPermission;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al verificar permiso', [
                'user_id' => $user->id,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(Usuario $user, string $role): bool
    {
        try {
            $cacheKey = "{$this->cachePrefix}_{$user->id}_role_{$role}";
            
            return $this->cacheService->remember($cacheKey, 3600, function () use ($user, $role) {
                $hasRole = $this->checkUserRole($user, $role);
                
                $this->loggingService->info('Verificación de rol', [
                    'user_id' => $user->id,
                    'role' => $role,
                    'result' => $hasRole
                ]);
                
                return $hasRole;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al verificar rol', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar si el usuario tiene al menos uno de los roles especificados
     */
    public function hasAnyRole(Usuario $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($user, $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el usuario tiene todos los roles especificados
     */
    public function hasAllRoles(Usuario $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($user, $role)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verificar si el usuario puede realizar una acción específica
     */
    public function can(Usuario $user, string $action, $model = null): bool
    {
        try {
            $cacheKey = "{$this->cachePrefix}_{$user->id}_can_{$action}";
            if ($model) {
                $cacheKey .= "_" . get_class($model) . "_{$model->id}";
            }
            
            return $this->cacheService->remember($cacheKey, 1800, function () use ($user, $action, $model) {
                $canPerform = Gate::forUser($user)->allows($action, $model);
                
                $this->loggingService->info('Verificación de capacidad', [
                    'user_id' => $user->id,
                    'action' => $action,
                    'model' => $model ? get_class($model) : null,
                    'result' => $canPerform
                ]);
                
                return $canPerform;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al verificar capacidad', [
                'user_id' => $user->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener todos los permisos del usuario
     */
    public function getUserPermissions(Usuario $user): array
    {
        try {
            $cacheKey = "{$this->cachePrefix}_{$user->id}_all_permissions";
            
            return $this->cacheService->remember($cacheKey, 3600, function () use ($user) {
                $permissions = $this->extractUserPermissions($user);
                
                $this->loggingService->info('Permisos del usuario obtenidos', [
                    'user_id' => $user->id,
                    'permissions_count' => count($permissions)
                ]);
                
                return $permissions;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener permisos del usuario', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Obtener todos los roles del usuario
     */
    public function getUserRoles(Usuario $user): array
    {
        try {
            $cacheKey = "{$this->cachePrefix}_{$user->id}_all_roles";
            
            return $this->cacheService->remember($cacheKey, 3600, function () use ($user) {
                $roles = $this->extractUserRoles($user);
                
                $this->loggingService->info('Roles del usuario obtenidos', [
                    'user_id' => $user->id,
                    'roles_count' => count($roles)
                ]);
                
                return $roles;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener roles del usuario', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Asignar un rol a un usuario
     */
    public function assignRole(Usuario $user, string $role): bool
    {
        try {
            $this->loggingService->info('Asignando rol a usuario', [
                'user_id' => $user->id,
                'role' => $role
            ]);

            // Aquí implementarías la lógica para asignar el rol
            // Por ejemplo, usando Spatie Permission o similar
            
            // Limpiar cache relacionado
            $this->clearUserCache($user);
            
            $this->loggingService->info('Rol asignado exitosamente', [
                'user_id' => $user->id,
                'role' => $role
            ]);
            
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al asignar rol', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remover un rol de un usuario
     */
    public function removeRole(Usuario $user, string $role): bool
    {
        try {
            $this->loggingService->info('Removiendo rol de usuario', [
                'user_id' => $user->id,
                'role' => $role
            ]);

            // Aquí implementarías la lógica para remover el rol
            
            // Limpiar cache relacionado
            $this->clearUserCache($user);
            
            $this->loggingService->info('Rol removido exitosamente', [
                'user_id' => $user->id,
                'role' => $role
            ]);
            
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al remover rol', [
                'user_id' => $user->id,
                'role' => $role,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Asignar un permiso a un usuario
     */
    public function assignPermission(Usuario $user, string $permission): bool
    {
        try {
            $this->loggingService->info('Asignando permiso a usuario', [
                'user_id' => $user->id,
                'permission' => $permission
            ]);

            // Aquí implementarías la lógica para asignar el permiso
            
            // Limpiar cache relacionado
            $this->clearUserCache($user);
            
            $this->loggingService->info('Permiso asignado exitosamente', [
                'user_id' => $user->id,
                'permission' => $permission
            ]);
            
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al asignar permiso', [
                'user_id' => $user->id,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remover un permiso de un usuario
     */
    public function removePermission(Usuario $user, string $permission): bool
    {
        try {
            $this->loggingService->info('Removiendo permiso de usuario', [
                'user_id' => $user->id,
                'permission' => $permission
            ]);

            // Aquí implementarías la lógica para remover el permiso
            
            // Limpiar cache relacionado
            $this->clearUserCache($user);
            
            $this->loggingService->info('Permiso removido exitosamente', [
                'user_id' => $user->id,
                'permission' => $permission
            ]);
            
            return true;

        } catch (\Exception $e) {
            $this->loggingService->error('Error al remover permiso', [
                'user_id' => $user->id,
                'permission' => $permission,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verificar acceso a un recurso específico
     */
    public function checkResourceAccess(Usuario $user, string $resource, string $action, $model = null): bool
    {
        try {
            $cacheKey = "{$this->cachePrefix}_{$user->id}_resource_{$resource}_{$action}";
            if ($model) {
                $cacheKey .= "_{$model->id}";
            }
            
            return $this->cacheService->remember($cacheKey, 1800, function () use ($user, $resource, $action, $model) {
                $hasAccess = $this->evaluateResourceAccess($user, $resource, $action, $model);
                
                $this->loggingService->info('Verificación de acceso a recurso', [
                    'user_id' => $user->id,
                    'resource' => $resource,
                    'action' => $action,
                    'model_id' => $model ? $model->id : null,
                    'result' => $hasAccess
                ]);
                
                return $hasAccess;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al verificar acceso a recurso', [
                'user_id' => $user->id,
                'resource' => $resource,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener políticas de acceso para un recurso
     */
    public function getResourcePolicies(string $resource): array
    {
        try {
            $cacheKey = "{$this->cachePrefix}_policies_{$resource}";
            
            return $this->cacheService->remember($cacheKey, 7200, function () use ($resource) {
                $policies = $this->extractResourcePolicies($resource);
                
                $this->loggingService->info('Políticas de recurso obtenidas', [
                    'resource' => $resource,
                    'policies_count' => count($policies)
                ]);
                
                return $policies;
            });

        } catch (\Exception $e) {
            $this->loggingService->error('Error al obtener políticas de recurso', [
                'resource' => $resource,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Limpiar cache de autorización de un usuario
     */
    public function clearUserCache(Usuario $user): void
    {
        try {
            $patterns = [
                "{$this->cachePrefix}_{$user->id}_*",
                "{$this->cachePrefix}_{$user->id}_role_*",
                "{$this->cachePrefix}_{$user->id}_can_*",
                "{$this->cachePrefix}_{$user->id}_resource_*"
            ];

            // Limpiar cache específico del usuario
            $this->cacheService->forget("{$this->cachePrefix}_{$user->id}_all_permissions");
            $this->cacheService->forget("{$this->cachePrefix}_{$user->id}_all_roles");

            $this->loggingService->info('Cache de autorización limpiado', [
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            $this->loggingService->error('Error al limpiar cache de autorización', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Verificar permiso del usuario (implementación específica)
     */
    protected function checkUserPermission(Usuario $user, string $permission): bool
    {
        // Aquí implementarías la lógica específica para verificar permisos
        // Por ejemplo, usando Spatie Permission, roles en base de datos, etc.
        
        // Por ahora, implementación básica
        if ($user->role === 'admin') {
            return true;
        }
        
        // Verificar permisos específicos del usuario
        $userPermissions = $user->permissions ?? [];
        return in_array($permission, $userPermissions);
    }

    /**
     * Verificar rol del usuario (implementación específica)
     */
    protected function checkUserRole(Usuario $user, string $role): bool
    {
        // Aquí implementarías la lógica específica para verificar roles
        
        // Por ahora, implementación básica
        return $user->role === $role;
    }

    /**
     * Extraer permisos del usuario
     */
    protected function extractUserPermissions(Usuario $user): array
    {
        // Aquí implementarías la lógica para extraer todos los permisos
        // Por ejemplo, desde roles, permisos directos, etc.
        
        $permissions = [];
        
        // Permisos del rol
        if ($user->role === 'admin') {
            $permissions = ['*']; // Todos los permisos
        } elseif ($user->role === 'manager') {
            $permissions = ['read', 'write', 'delete'];
        } elseif ($user->role === 'user') {
            $permissions = ['read'];
        }
        
        // Agregar permisos específicos del usuario
        if (isset($user->permissions) && is_array($user->permissions)) {
            $permissions = array_merge($permissions, $user->permissions);
        }
        
        return array_unique($permissions);
    }

    /**
     * Extraer roles del usuario
     */
    protected function extractUserRoles(Usuario $user): array
    {
        // Aquí implementarías la lógica para extraer todos los roles
        
        $roles = [];
        
        if (isset($user->role)) {
            $roles[] = $user->role;
        }
        
        // Agregar roles adicionales si existen
        if (isset($user->additional_roles) && is_array($user->additional_roles)) {
            $roles = array_merge($roles, $user->additional_roles);
        }
        
        return array_unique($roles);
    }

    /**
     * Evaluar acceso a recurso
     */
    protected function evaluateResourceAccess(Usuario $user, string $resource, string $action, $model = null): bool
    {
        // Aquí implementarías la lógica específica para evaluar acceso a recursos
        
        // Verificar permisos básicos
        if (!$this->hasPermission($user, "{$resource}.{$action}")) {
            return false;
        }
        
        // Verificar políticas específicas del modelo si existe
        if ($model && method_exists($model, 'canUserAccess')) {
            return $model->canUserAccess($user, $action);
        }
        
        return true;
    }

    /**
     * Extraer políticas de recurso
     */
    protected function extractResourcePolicies(string $resource): array
    {
        // Aquí implementarías la lógica para extraer políticas de recursos
        
        // Por ahora, políticas básicas
        $policies = [
            'product' => ['create', 'read', 'update', 'delete'],
            'order' => ['create', 'read', 'update', 'cancel'],
            'user' => ['read', 'update', 'delete'],
            'inventory' => ['read', 'update', 'adjust']
        ];
        
        return $policies[$resource] ?? [];
    }
}
