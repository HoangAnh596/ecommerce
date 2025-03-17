<?php

namespace App\Repositories\Interfaces;

/**
 * Interface BaseRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all();

    public function pagination(array $column = ['*'], array $condition = [], array $join = [], array $extend = [], int $perpage = 20);

    public function findById(int $id);

    public function create(array $payload);

    public function update(int $id = 0, array $payload = []);

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []);

    public function delete(int $id = 0);
}
