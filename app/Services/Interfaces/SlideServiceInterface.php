<?php

namespace App\Services\Interfaces;

/**
 * Interface SlideServiceInterface
 * @package App\Services\Interfaces
 */
interface SlideServiceInterface
{
    public function paginate($request);

    public function getSlide($array = [], $languageId = 1);
}
