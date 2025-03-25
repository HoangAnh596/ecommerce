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

    public function nestedset()
    {
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }

    public function createRouter($model, $request, $controllerName)
    {
        $payload = $this->formatRouterPayload($model, $request, $controllerName);
        // dd($payload);
        return $this->routerRepository->create($payload);
    }

    public function updateRouter($model, $request, $controllerName)
    {
        $payload = $this->formatRouterPayload($model, $request, $controllerName);
        $condition = [
            ['module_id', '=', $model->id],
            ['controller', '=', 'App\Http\Controllers\Frontend\\'.$controllerName],
        ];
        $router = $this->routerRepository->findByCondition($condition);
        
        return $this->routerRepository->update($router->id, $payload);
    }

    public function formatRouterPayload($model, $request, $controllerName)
    {
        $router = [
            'canonical' => Str::slug($request->string('canonical')),
            'module_id' => $model->id,
            'controller' => 'App\Http\Controllers\Frontend\\'.$controllerName.''
        ];

        return $router;
    }
}