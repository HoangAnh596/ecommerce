<?php

namespace App\Repositories\Interfaces;

/**
 * Interface BaseRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all();

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perpage = 20,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relattions = []
    );

    public function findById(int $id);

    public function create(array $payload);

    public function update(int $id = 0, array $payload = []);

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []);

    public function delete(int $id = 0);
    
    public function createLanguagePivot($model, array $payload = []);
}
