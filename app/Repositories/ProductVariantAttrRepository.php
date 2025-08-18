<?php

namespace App\Repositories;

use App\Models\ProductVariantAttribute;
use App\Repositories\Interfaces\ProductVariantAttrRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class ProductVariantAttrRepository
 * @package App\Repositories
 */
class ProductVariantAttrRepository extends BaseRepository implements ProductVariantAttrRepositoryInterface
{
    protected $model;
    
    public function __construct(ProductVariantAttribute $model)
    {
        $this->model = $model;
    }
}
