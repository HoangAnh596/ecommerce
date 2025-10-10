<?php

namespace App\Services\Interfaces;

/**
 * Interface CartServiceInterface
 * @package App\Services\Interfaces
 */
interface CartServiceInterface
{
    public function remakeCart($carts);

    public function reCaculateCart();

    public function cartPromotion($cartTotal = 0);

    public function order($request, $system);
}
