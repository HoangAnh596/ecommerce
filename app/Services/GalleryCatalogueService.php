<?php

namespace App\Services;

use App\Services\Interfaces\GalleryCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\GalleryCatalogueRepositoryInterface as GalleryCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class GalleryCatalogueService
 * @package App\Services
 */
class GalleryCatalogueService extends BaseService implements GalleryCatalogueServiceInterface
{
    protected $galleryCatalogueRepository;
    protected $routerRepository;
    protected $nestedset;
    protected $controllerName = 'GalleryCatalogueController';

    public function __construct(
        GalleryCatalogueRepository $galleryCatalogueRepository,
        RouterRepository $routerRepository,
    ){
        $this->galleryCatalogueRepository = $galleryCatalogueRepository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request, $languageId){
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $galleryCatalogues = $this->galleryCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'gallery.catalogue.index'],
            ['gallery_catalogues.lft','ASC'],
            [
                ['gallery_catalogue_language as tb2', 'tb2.gallery_catalogue_id', '=', 'gallery_catalogues.id'],
            ]
        );

        return $galleryCatalogues;
    }

    public function create($request, $languageId) {
        DB::beginTransaction();
        try {
            $galleryCatalogue = $this->createGalleryCatalogue($request);
            if($galleryCatalogue->id > 0) {
                $this->updateLanguageForCatalogue($galleryCatalogue, $request, $languageId);
                $this->createRouter($galleryCatalogue, $request, $this->controllerName);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'gallery_catalogues',
                    'foreignkey' => 'gallery_catalogue_id',
                    'language_id' => $languageId,
                ]);
                $this->nestedset();
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($request, $id, $languageId){
        DB::beginTransaction();
        try {
            $galleryCatalogue = $this->galleryCatalogueRepository->findById($id);
            $flag = $this->updateGalleryCatalogue($galleryCatalogue, $request);
            if($flag == true){
                $this->updateLanguageForCatalogue($galleryCatalogue, $request, $languageId);
                $this->updateRouter($galleryCatalogue, $request, $this->controllerName);
                $this->nestedset = new Nestedsetbie([
                    'table' => 'gallery_catalogues',
                    'foreignkey' => 'gallery_catalogue_id',
                    'language_id' => $languageId,
                ]);
                $this->nestedset();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $this->galleryCatalogueRepository->delete($id);
            $this->nestedset = new Nestedsetbie([
                'table' => 'gallery_catalogues',
                'foreignkey' => 'gallery_catalogue_id',
                'language_id' => $languageId,
            ]);
            $this->nestedset();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatus($gallery = []) {
        DB::beginTransaction();
        try {
            $payload[$gallery['field']] = ($gallery['value'] == 1) ? 2 : 1;
            $this->galleryCatalogueRepository->update($gallery['modelId'], $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatusAll($gallery) {
        DB::beginTransaction();
        try {
            $payload[$gallery['field']] = $gallery['value'];
            $this->galleryCatalogueRepository->updateByWhereIn('id', $gallery['id'], $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function createGalleryCatalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->galleryCatalogueRepository->create($payload);;
    }

    private function updateGalleryCatalogue($galleryCatalogue, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->galleryCatalogueRepository->update($galleryCatalogue->id, $payload);;
    }

    private function updateLanguageForCatalogue($galleryCatalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($request, $languageId);
        $galleryCatalogue->languages()->detach([$languageId, $galleryCatalogue->id]);
        $response = $this->galleryCatalogueRepository->createPivot($galleryCatalogue, $payload, 'languages');

        return $response;
    }

    private function formatLanguagePayload($request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        
        return $payload;
    }

    private function paginateSelect() {
        return [
            'gallery_catalogues.id',
            'gallery_catalogues.image',
            'gallery_catalogues.publish',
            'gallery_catalogues.level',
            'gallery_catalogues.order',
            'tb2.name',
            'tb2.canonical'
        ];
    }

    private function payload() {
        return ['parent_id', 'image', 'album', 'publish', 'follow'];
    }

    private function payloadLanguage() {
        return ['name', 'canonical', 'description', 'content', 'meta_title', 'meta_description', 'meta_keyword'];
    }
}
