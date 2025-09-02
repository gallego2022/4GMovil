<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Exception;

abstract class BaseRepository
{
    protected $model;
    protected $cachePrefix;
    protected $cacheTtl = 3600; // 1 hora por defecto

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->cachePrefix = strtolower(class_basename($model)) . '_';
    }

    /**
     * Obtiene todos los registros
     */
    public function all(array $columns = ['*']): Collection
    {
        $cacheKey = $this->cachePrefix . 'all_' . md5(serialize($columns));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($columns) {
            return $this->model->select($columns)->get();
        });
    }

    /**
     * Obtiene un registro por ID
     */
    public function find(int $id, array $columns = ['*'])
    {
        $cacheKey = $this->cachePrefix . 'find_' . $id . '_' . md5(serialize($columns));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $columns) {
            return $this->model->select($columns)->find($id);
        });
    }

    /**
     * Obtiene un registro por ID o lanza excepción
     */
    public function findOrFail(int $id, array $columns = ['*'])
    {
        $cacheKey = $this->cachePrefix . 'find_or_fail_' . $id . '_' . md5(serialize($columns));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($id, $columns) {
            return $this->model->select($columns)->findOrFail($id);
        });
    }

    /**
     * Busca por criterios específicos
     */
    public function findBy(array $criteria, array $columns = ['*']): Collection
    {
        $cacheKey = $this->cachePrefix . 'find_by_' . md5(serialize($criteria) . serialize($columns));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($criteria, $columns) {
            $query = $this->model->select($columns);
            
            foreach ($criteria as $field => $value) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
            
            return $query->get();
        });
    }

    /**
     * Busca un registro por criterios específicos
     */
    public function findOneBy(array $criteria, array $columns = ['*'])
    {
        $cacheKey = $this->cachePrefix . 'find_one_by_' . md5(serialize($criteria) . serialize($columns));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($criteria, $columns) {
            $query = $this->model->select($columns);
            
            foreach ($criteria as $field => $value) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
            
            return $query->first();
        });
    }

    /**
     * Crea un nuevo registro
     */
    public function create(array $data)
    {
        $result = $this->model->create($data);
        $this->clearCache();
        return $result;
    }

    /**
     * Actualiza un registro existente
     */
    public function update(int $id, array $data): bool
    {
        $result = $this->model->where('id', $id)->update($data);
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Elimina un registro
     */
    public function delete(int $id): bool
    {
        $result = $this->model->destroy($id);
        if ($result) {
            $this->clearCache();
        }
        return $result;
    }

    /**
     * Obtiene registros paginados
     */
    public function paginate(int $perPage = 15, array $columns = ['*'], string $pageName = 'page'): LengthAwarePaginator
    {
        $cacheKey = $this->cachePrefix . 'paginate_' . $perPage . '_' . request()->get($pageName, 1) . '_' . md5(serialize($columns));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($perPage, $columns, $pageName) {
            return $this->model->select($columns)->paginate($perPage, ['*'], $pageName);
        });
    }

    /**
     * Obtiene el conteo total de registros
     */
    public function count(array $criteria = []): int
    {
        $cacheKey = $this->cachePrefix . 'count_' . md5(serialize($criteria));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($criteria) {
            $query = $this->model->query();
            
            foreach ($criteria as $field => $value) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
            
            return $query->count();
        });
    }

    /**
     * Verifica si existe un registro
     */
    public function exists(array $criteria): bool
    {
        $cacheKey = $this->cachePrefix . 'exists_' . md5(serialize($criteria));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($criteria) {
            $query = $this->model->query();
            
            foreach ($criteria as $field => $value) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
            
            return $query->exists();
        });
    }

    /**
     * Obtiene registros con relaciones
     */
    public function with(array $relations, array $columns = ['*']): Collection
    {
        $cacheKey = $this->cachePrefix . 'with_' . md5(serialize($relations) . serialize($columns));
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($relations, $columns) {
            return $this->model->select($columns)->with($relations)->get();
        });
    }

    /**
     * Ejecuta una consulta personalizada
     */
    public function query()
    {
        return $this->model->newQuery();
    }

    /**
     * Limpia el caché del repositorio
     */
    protected function clearCache(): void
    {
        $keys = Cache::get($this->cachePrefix . 'keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget($this->cachePrefix . 'keys');
    }

    /**
     * Agrega una clave al caché para limpieza posterior
     */
    protected function addCacheKey(string $key): void
    {
        $keys = Cache::get($this->cachePrefix . 'keys', []);
        $keys[] = $key;
        Cache::put($this->cachePrefix . 'keys', array_unique($keys), $this->cacheTtl);
    }

    /**
     * Establece el tiempo de vida del caché
     */
    public function setCacheTtl(int $seconds): void
    {
        $this->cacheTtl = $seconds;
    }

    /**
     * Desactiva el caché para este repositorio
     */
    public function disableCache(): void
    {
        $this->cacheTtl = 0;
    }
}
