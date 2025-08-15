<?php

namespace App\Services;

use App\Services\Interfaces\AttributeServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class AttributeService
 * @package App\Services
 */
class AttributeService extends BaseService implements AttributeServiceInterface
{
    protected $attributeRepository;
    protected $routerRepository;
    protected $controllerName;

    public function __construct(
        AttributeRepository $attributeRepository,
        RouterRepository $routerRepository,
    ){
        $this->attributeRepository = $attributeRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'AttributeController';
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
        $attributes = $this->attributeRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'attribute.index', 'groupBy' => $this->paginateSelect()],
            ['attributes.id','ASC'],
            [
                ['attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id'],
                ['attribute_catalogue_attribute as tb3', 'attributes.id', '=', 'tb3.attribute_id']
            ],
            ['attribute_catalogues'],
            $this->whereRaw($request, $languageId)
        );

        return $attributes;
    }

    public function create($request, $languageId) {
        DB::beginTransaction();
        try {
            $attribute = $this->createAttribute($request);
            if($attribute->id > 0) {
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->createRouter($attribute, $request, $this->controllerName, $languageId); 
                $attribute->attribute_catalogues()->sync($this->catalogue($request));
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
            $attribute = $this->attributeRepository->findById($id);
            if($this->uploadAttribute($attribute, $request)){
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateRouter($attribute, $request, $this->controllerName, $languageId);
                $attribute->attribute_catalogues()->sync($this->catalogue($request));
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
            $this->attributeRepository->delete($id);
            
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

    private function paginateSelect() {
        return [
            'attributes.id',
            'attributes.image',
            'attributes.publish',
            'attributes.order',
            'tb2.name',
            'tb2.canonical'
        ];
    }

    private function whereRaw($request, $languageId){
        $rawCondition = [];
        if($request->integer('attribute_catalogue_id') > 0){
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.attribute_catalogue_id IN (
                        SELECT id
                        FROM attribute_catalogues
                        JOIN attribute_catalogue_language ON attribute_catalogues.id = attribute_catalogue_language.attribute_catalogue_id
                        WHERE lft >= (SELECT lft FROM attribute_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM attribute_catalogues as pc WHERE pc.id = ?)
                        AND attribute_catalogue_language.language_id = '.$languageId.'
                    )',
                    [$request->integer('attribute_catalogue_id'), $request->integer('attribute_catalogue_id')]
                ]
            ];
            
        }
        return $rawCondition;
    }

    private function createAttribute($request) {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);

        return $this->attributeRepository->create($payload);
    }

    private function uploadAttribute($attribute, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);

        return $this->attributeRepository->update($attribute->id, $payload);
    }

    private function updateLanguageForAttribute($attribute, $request, $languageId) {
        $payload = $request->only($this->payloadAttribute());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['attribute_id'] = $attribute->id;
        $attribute->languages()->detach([$languageId, $attribute->id]);

        return $this->attributeRepository->createPivot($attribute, $payload, 'languages');
    }

    private function catalogue($request) {
        if($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->input('attribute_catalogue_id')]));
        }

        return $request->attribute_catalogue_id;
    }

    private function payload() {
        return ['attribute_catalogue_id', 'image', 'album', 'publish', 'follow'];
    }

    private function payloadAttribute() {
        return ['name', 'canonical', 'description', 'content', 'meta_title', 'meta_description', 'meta_keyword'];
    }
}
