<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id, array $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail($id, array $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(string $field, $value, array $columns = ['*']): Collection
    {
        return $this->model->where($field, $value)->get($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function firstWhere(string $field, $value, array $columns = ['*'])
    {
        return $this->model->where($field, $value)->first($columns);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id): bool
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $criteria = []): int
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }
        
        return $query->count();
    }

    /**
     * {@inheritdoc}
     */
    public function exists($id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $relations)
    {
        return $this->model->with($relations);
    }

    /**
     * {@inheritdoc}
     */
    public function where(array $criteria)
    {
        $query = $this->model->newQuery();
        
        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }
        
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy(string $column, string $direction = 'asc')
    {
        return $this->model->orderBy($column, $direction);
    }

    /**
     * Obtener el modelo
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Establecer el modelo
     */
    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Crear múltiples registros
     */
    public function createMany(array $data): Collection
    {
        $models = collect();
        
        foreach ($data as $item) {
            $models->push($this->create($item));
        }
        
        return $models;
    }

    /**
     * Actualizar o crear registro
     */
    public function updateOrCreate(array $search, array $data)
    {
        return $this->model->updateOrCreate($search, $data);
    }

    /**
     * Buscar por múltiples criterios
     */
    public function findByMultiple(array $criteria, array $columns = ['*']): Collection
    {
        return $this->where($criteria)->get($columns);
    }

    /**
     * Obtener registros con límite
     */
    public function take(int $limit, array $columns = ['*']): Collection
    {
        return $this->model->take($limit)->get($columns);
    }

    /**
     * Obtener registros recientes
     */
    public function latest(string $column = 'created_at', array $columns = ['*']): Collection
    {
        return $this->model->latest($column)->get($columns);
    }
}
