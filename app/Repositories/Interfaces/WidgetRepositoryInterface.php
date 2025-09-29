<?php

namespace App\Repositories\Interfaces;

/**
 * Interface WidgetRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface WidgetRepositoryInterface
{
    public function getWidgetWhereIn(array $whereIn = [], $whereInField = 'keyword');
}
