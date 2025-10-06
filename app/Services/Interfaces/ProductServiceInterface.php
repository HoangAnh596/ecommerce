<?php

namespace App\Services\Interfaces;

/**
 * Interface ProductServiceInterface
 * @package App\Services\Interfaces
 */
interface ProductServiceInterface
{
    public function paginate($request, $languageId);

    public function combineProductAndPromotion($productId = [], $products, $flag = false);

    public function getAttribute($product, $languageId);
}
