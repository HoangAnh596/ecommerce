<?php

namespace App\Repositories;

use App\Models\Ward;
use App\Repositories\Interfaces\WardRepositoryInterface;

/**
 * Class WardRepository
 * @package App\Repositories
 */
class WardRepository implements WardRepositoryInterface
{
    public function getAllPaginate(){
        return Ward::paginate(15);
    }
}
