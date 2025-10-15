<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Interfaces\OrderRepositoryInterface;

/**
 * Class OrderRepository
 * @package App\Repositories
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    protected $model;

    public function __construct(Order $model)
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
        $query = $this->model->select($column);
        return $query->keyword(
            $condition['keyword'] ?? null,
            ['fullname', 'phone', 'email', 'address', 'code'],
            [
                'field' => 'name',
                'relation' => 'products'
            ]
        )->publish($condition['publish'] ?? null)
            ->customDropdownFilter($condition['dropdown'] ?? null)
            ->relationCount($relations ?? null)
            ->customWhere($condition['where'] ?? null)
            ->customWhereRaw($rawQuery['whereRaw'] ?? null)
            ->customJoin($join ?? null)
            ->customGroupBy($extend['groupBy'] ?? null)
            ->customOrderBy($orderBy ?? null)
            ->customerCreatedAt($condition['created_at'] ?? null)
            ->paginate($perPage)
            ->withQueryString()->withPath(env('APP_URL') . $extend['path']);
    }

    public function getOrderById($id)
    {

        return $this->model->select([
            'orders.*',
            'provinces.name as province_name',
            'districts.name as district_name',
            'wards.name as ward_name'
        ])->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->with('products')
            ->find($id);
    }
}
