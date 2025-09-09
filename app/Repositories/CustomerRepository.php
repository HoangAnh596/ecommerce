<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class CustomerRepository
 * @package App\Repositories
 */
class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    protected $model;

    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perPage = 20,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = []
    ) {
        $query = $this->model->select($column)->where(function ($query) use ($condition) {
            if (isset($condition['keyword']) && !empty($condition['keyword'])) {
                $query->where('name', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('email', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('phone', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('address', 'LIKE', '%' . $condition['keyword'] . '%');
            }

            if (isset($condition['publish']) && $condition['publish'] != 0) {
                $query->where('publish', '=', $condition['publish']);
            }

            return $query;
        })->with(['customer_catalogues', 'sources']);

        if (!empty($join)) {
            $query->join(...$join);
        }

        return $query->paginate($perPage)->withQueryString()->withPath(url($extend['path']));
    }
}
