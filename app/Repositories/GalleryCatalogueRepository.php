<?php

namespace App\Repositories;

use App\Models\GalleryCatalogue;
use App\Repositories\Interfaces\GalleryCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class GalleryCatalogueRepository
 * @package App\Repositories
 */
class GalleryCatalogueRepository extends BaseRepository implements GalleryCatalogueRepositoryInterface
{
    protected $model;
    
    public function __construct(GalleryCatalogue $model)
    {
        $this->model = $model;
    }

    public function getGalleryCatalogueById(int $id = 0, $language_id = 0)
    {
        return $this->model->select([
                    'gallery_catalogues.id',
                    'gallery_catalogues.parent_id',
                    'gallery_catalogues.image',
                    'gallery_catalogues.icon',
                    'gallery_catalogues.album',
                    'gallery_catalogues.publish',
                    'gallery_catalogues.follow',
                    'tb2.name',
                    'tb2.description',
                    'tb2.content',
                    'tb2.meta_title',
                    'tb2.meta_keyword',
                    'tb2.meta_description',
                    'tb2.canonical',
                ])
            ->join('gallery_catalogue_language as tb2', 'tb2.gallery_catalogue_id', '=', 'gallery_catalogues.id')
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }
}
