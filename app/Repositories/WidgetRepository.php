<?php

namespace App\Repositories;

use App\Models\Widget;
use App\Repositories\Interfaces\WidgetRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class WidgetRepository
 * @package App\Repositories
 */
class WidgetRepository extends BaseRepository implements WidgetRepositoryInterface
{
    protected $model;

    public function __construct(Widget $model)
    {
        $this->model = $model;
    }

    public function getWidgetWhereIn(array $whereIn = [], $whereInField = 'keyword')
    {
        return $this->model->where([config('apps.general.defaultPublish')])
            ->whereIn($whereInField, $whereIn)
            ->orderByRaw("FIELD(`keyword`, '" . implode("','", $whereIn) . "')")
            ->get();
    }
}
