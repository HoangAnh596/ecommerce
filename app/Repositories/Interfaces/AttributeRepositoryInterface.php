<?php

namespace App\Repositories\Interfaces;

/**
 * Interface AttributeRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface AttributeRepositoryInterface
{
    public function searchAttributes(string $keyword = '', array $option = []);
}
