<?php

namespace App\Services\Interfaces;

/**
 * Interface WidgetServiceInterface
 * @package App\Services\Interfaces
 */
interface WidgetServiceInterface
{
    public function paginate($request);

    public function findWidgetByKeyword(string $keyword = '', int $language = 1, $param = []);
}
