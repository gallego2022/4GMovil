<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    /**
     * Obtener todos los registros
     */
    public function all(array $columns = ['*']);

    /**
     * Obtener registros paginados
     */
    public function paginate(int $perPage = 15, array $columns = ['*']);

    /**
     * Buscar por ID
     */
    public function find($id, array $columns = ['*']);

    /**
     * Buscar por ID o fallar
     */
    public function findOrFail($id, array $columns = ['*']);

    /**
     * Buscar por campo específico
     */
    public function findBy(string $field, $value, array $columns = ['*']);

    /**
     * Buscar el primer registro que coincida
     */
    public function firstWhere(string $field, $value, array $columns = ['*']);

    /**
     * Crear nuevo registro
     */
    public function create(array $data);

    /**
     * Actualizar registro existente
     */
    public function update($id, array $data);

    /**
     * Eliminar registro
     */
    public function delete($id);

    /**
     * Contar registros
     */
    public function count(array $criteria = []);

    /**
     * Verificar si existe
     */
    public function exists($id): bool;

    /**
     * Obtener con relaciones
     */
    public function with(array $relations);

    /**
     * Aplicar filtros
     */
    public function where(array $criteria);

    /**
     * Ordenar resultados
     */
    public function orderBy(string $column, string $direction = 'asc');
}
