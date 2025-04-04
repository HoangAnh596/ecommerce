<?php

namespace App\Services;

use App\Services\Interfaces\{Module}ServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\{Module}RepositoryInterface as {Module}Repository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class {Module}Service
 * @package App\Services
 */
class {Module}Service extends BaseService implements {Module}ServiceInterface
{
    protected ${module}Repository;
    protected $routerRepository;
    protected $nestedset;
    protected $controllerName = '{Module}Controller';

    public function __construct(
        {Module}Repository ${module}Repository,
        RouterRepository $routerRepository,
    ){
        $this->{module}Repository = ${module}Repository;
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
        ${module}s = $this->{module}Repository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => '{name}.catalogue.index'],
            ['{tableName}.lft','ASC'],
            [
                ['{name}_catalogue_language as tb2', 'tb2.{foreignKey}', '=', '{tableName}.id'],
            ]
        );

        return ${module}s;
    }

    public function create($request, $languageId) {
        DB::beginTransaction();
        try {
            ${module} = $this->create{Module}($request);
            if(${module}->id > 0) {
                $this->updateLanguageForCatalogue(${module}, $request, $languageId);
                $this->createRouter(${module}, $request, $this->controllerName);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{name}_catalogues',
                    'foreignkey' => '{name}_catalogue_id',
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
            ${module} = $this->{module}Repository->findById($id);
            $flag = $this->update{Module}(${module}, $request);
            if($flag == true){
                $this->updateLanguageForCatalogue(${module}, $request, $languageId);
                $this->updateRouter(${module}, $request, $this->controllerName);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{name}_catalogues',
                    'foreignkey' => '{name}_catalogue_id',
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
            $this->{module}Repository->delete($id);
            $this->nestedset = new Nestedsetbie([
                'table' => '{name}_catalogues',
                'foreignkey' => '{name}_catalogue_id',
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

    public function updateStatus(${name} = []) {
        DB::beginTransaction();
        try {
            $payload[${name}['field']] = (${name}['value'] == 1) ? 2 : 1;
            $this->{module}Repository->update(${name}['modelId'], $payload);

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

    public function updateStatusAll(${name}) {
        DB::beginTransaction();
        try {
            $payload[${name}['field']] = ${name}['value'];
            $this->{module}Repository->updateByWhereIn('id', ${name}['id'], $payload);

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

    private function create{Module}($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->{module}Repository->create($payload);;
    }

    private function update{Module}(${module}, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->{module}Repository->update(${module}->id, $payload);;
    }

    private function updateLanguageForCatalogue(${module}, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($request, $languageId);
        ${module}->languages()->detach([$languageId, ${module}->id]);
        $response = $this->{module}Repository->createPivot(${module}, $payload, 'languages');

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
            '{tableName}.id',
            '{tableName}.image',
            '{tableName}.publish',
            '{tableName}.level',
            '{tableName}.order',
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
