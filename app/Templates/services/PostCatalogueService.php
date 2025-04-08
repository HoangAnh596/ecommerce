<?php

namespace App\Services;

use App\Services\Interfaces\{$class}CatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\{$class}CatalogueRepositoryInterface as {$class}CatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class {$class}CatalogueService
 * @package App\Services
 */
class {$class}CatalogueService extends BaseService implements {$class}CatalogueServiceInterface
{
    protected ${module}CatalogueRepository;
    protected $routerRepository;
    protected $nestedset;
    protected $controllerName = '{$class}CatalogueController';

    public function __construct(
        {$class}CatalogueRepository ${module}CatalogueRepository,
        RouterRepository $routerRepository,
    ){
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
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
        ${module}Catalogues = $this->{module}CatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => '{module}.catalogue.index'],
            ['{module}_catalogues.lft','ASC'],
            [
                ['{module}_catalogue_language as tb2', 'tb2.{module}_catalogue_id', '=', '{module}_catalogues.id'],
            ]
        );

        return ${module}Catalogues;
    }

    public function create($request, $languageId) {
        DB::beginTransaction();
        try {
            ${module}Catalogue = $this->create{$class}Catalogue($request);
            if(${module}Catalogue->id > 0) {
                $this->updateLanguageForCatalogue(${module}Catalogue, $request, $languageId);
                $this->createRouter(${module}Catalogue, $request, $this->controllerName);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{module}_catalogues',
                    'foreignkey' => '{module}_catalogue_id',
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
            ${module}Catalogue = $this->{module}CatalogueRepository->findById($id);
            $flag = $this->update{$class}Catalogue(${module}Catalogue, $request);
            if($flag == true){
                $this->updateLanguageForCatalogue(${module}Catalogue, $request, $languageId);
                $this->updateRouter(${module}Catalogue, $request, $this->controllerName);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{module}_catalogues',
                    'foreignkey' => '{module}_catalogue_id',
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

    public function destroy($id, $languageId) {
        DB::beginTransaction();
        try {
            $this->{module}CatalogueRepository->delete($id);
            $this->nestedset = new Nestedsetbie([
                'table' => '{module}_catalogues',
                'foreignkey' => '{module}_catalogue_id',
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

    public function updateStatus(${module} = []) {
        DB::beginTransaction();
        try {
            $payload[${module}['field']] = (${module}['value'] == 1) ? 2 : 1;
            $this->{module}CatalogueRepository->update(${module}['modelId'], $payload);

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

    public function updateStatusAll(${module}) {
        DB::beginTransaction();
        try {
            $payload[${module}['field']] = ${module}['value'];
            $this->{module}CatalogueRepository->updateByWhereIn('id', ${module}['id'], $payload);

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

    private function create{$class}Catalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->{module}CatalogueRepository->create($payload);;
    }

    private function update{$class}Catalogue(${module}Catalogue, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->{module}CatalogueRepository->update(${module}Catalogue->id, $payload);;
    }

    private function updateLanguageForCatalogue(${module}Catalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($request, $languageId);
        ${module}Catalogue->languages()->detach([$languageId, ${module}Catalogue->id]);
        $response = $this->{module}CatalogueRepository->createPivot(${module}Catalogue, $payload, 'languages');

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
            '{module}_catalogues.id',
            '{module}_catalogues.image',
            '{module}_catalogues.publish',
            '{module}_catalogues.level',
            '{module}_catalogues.order',
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
