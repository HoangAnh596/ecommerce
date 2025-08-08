<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $language;
    public function __construct(

    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function changeStatus(Request $request) {
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']). 'Service';
        if(class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        $flag = $serviceInstance->updateStatus($post);

        return response()->json(['flag' => $flag]);
    }

    public function changeStatusAll(Request $request) {
        $post = $request->input();
        $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($post['model']). 'Service';
        if(class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        $flag = $serviceInstance->updateStatusAll($post);

        return response()->json(['flag' => $flag]);
    }

    public function getMenu(Request $request) {
        $model = $request->input('model');
        $serviceInterfaceNamespace = '\App\Repositories\\' . ucfirst($model). 'Repository';
        if(class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        $arguments = $this->paginationArgument($model);
        $data = $serviceInstance->pagination(...array_values($arguments));

        return response()->json(['data' => $data]);
    }

    private function paginationArgument(string $model = '') : array {
        $model = Str::snake($model);
        $join = [
            [$model.'_language as tb2', 'tb2.'.$model.'_id', '=', $model.'s.id'],
        ];
        if(strpos($model, '_catalogue') === false) {
            $join[] = [''.$model.'_catalogue_'.$model.' as tb3', ''.$model.'s.id', '=', 'tb3.'.$model.'_id'];
        }
        return [
            'select' => ['id', 'name'],
            'condition' => [
                'where' => [
                    ['tb2.language_id', '=', $this->language]
                ]
            ],
            'perpage' => 10,
            'paginationConfig' => [
                'path' => $model.'.index',
                'groupBy' => ['id', 'name']
            ],
            'orderBy' => [$model.'s.id', 'DESC'],
            'join' => $join,
            'relations' => [],
        ];
    }
}
