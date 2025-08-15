<?php

namespace App\Repositories\Interfaces;

/**
 * Interface BaseRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all(array $relation);

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 20,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = []
    );

    public function findById(int $id);

    public function create(array $payload);

    public function update(int $id = 0, array $payload = []);

    public function updateOrInsert(array $payload= [], array $condition = []);

    public function updateByWhere(array $condition = [], array $payload= []);

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []);

    public function delete(int $id = 0);
    
    public function createPivot($model, array $payload = [], string $relation = '');

    public function forceDeleteByCondition(array $condition = []);

    public function findByCondition($condition = [], $flag = false, $relation = [], array $orderBy = ['id', 'DESC']);

    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '');
}
