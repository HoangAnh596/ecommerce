<?php

namespace App\Services\Interfaces;

/**
 * Interface GalleryCatalogueServiceInterface
 * @package App\Services\Interfaces
 */
interface GalleryCatalogueServiceInterface
{
    public function paginate($request, $languageId);
}
