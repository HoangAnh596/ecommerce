<?php

namespace App\Services;

use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;

/**
 * Class BaseService
 * @package App\Services
 */
class BaseService implements BaseServiceInterface
{
    protected $routerRepository;

    public function __construct(RouterRepository $routerRepository){
        $this->routerRepository = $routerRepository;
    }

    public function currentLanguage() {
        return 1;
    }

    public function formatAlbum($request)
    {
        return ($request->input('album') && !empty($request->input('album'))) ? json_encode($request->input('album')) : '';
    }

    public function formatJson($request, $inputName)
    {
        return ($request->input($inputName) && !empty($request->input($inputName))) ? json_encode($request->input($inputName)) : '';
    }

    public function nestedset()
    {
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }

    public function createRouter($model, $request, $controllerName, $languageId)
    {
        $payload = $this->formatRouterPayload($model, $request, $controllerName, $languageId);

        return $this->routerRepository->create($payload);
    }

    public function updateRouter($model, $request, $controllerName, $languageId)
    {
        $payload = $this->formatRouterPayload($model, $request, $controllerName, $languageId);
        $condition = [
            ['module_id', '=', $model->id],
            ['controllers', '=', 'App\Http\Controllers\Frontend\\'.$controllerName],
        ];
        $router = $this->routerRepository->findByCondition($condition);
        
        return $this->routerRepository->update($router->id, $payload);
    }

    public function formatRouterPayload($model, $request, $controllerName, $languageId)
    {
        $router = [
            'canonical' => Str::slug($request->string('canonical')),
            'module_id' => $model->id,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controllers\Frontend\\'.$controllerName.''
        ];

        return $router;
    }

    public function updateStatus($post = []) {
        DB::beginTransaction();
        try {
            $model = lcfirst($post['model']).'Repository';
            $payload[$post['field']] = ($post['value'] == 1) ? 2 : 1;
            $this->{$model}->update($post['modelId'], $payload);

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

    public function updateStatusAll($post) {
        DB::beginTransaction();
        try {
            $model = lcfirst($post['model']).'Repository';
            $payload[$post['field']] = $post['value'];
            $this->{$model}->updateByWhereIn('id', $post['id'], $payload);

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
}