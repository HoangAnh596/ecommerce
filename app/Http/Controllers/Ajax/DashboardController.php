<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $language;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function changeStatus(Request $request)
    {
        $post = $request->input();
        $class = loadClassInterface($post['model'], 'Service');
        $flag = $class->updateStatus($post);

        return response()->json(['flag' => $flag]);
    }

    public function changeStatusAll(Request $request)
    {
        $post = $request->input();
        $class = loadClassInterface($post['model'], 'Service');
        $flag = $class->updateStatusAll($post);

        return response()->json(['flag' => $flag]);
    }

    public function getMenu(Request $request)
    {
        $model = $request->input('model');
        $page = ($request->input('page')) ?? 1;
        $keyword = ($request->string('keyword')) ?? null;
        $class = loadClassInterface($model, 'Repository');
        $arguments = $this->paginationArgument($model, $keyword);
        $object = $class->pagination(...array_values($arguments));

        return response()->json($object);
    }

    private function paginationArgument(string $model = '', string $keyword): array
    {
        $model = Str::snake($model);
        $join = [
            [$model . '_language as tb2', 'tb2.' . $model . '_id', '=', $model . 's.id'],
        ];
        if (strpos($model, '_catalogue') === false) {
            $join[] = ['' . $model . '_catalogue_' . $model . ' as tb3', '' . $model . 's.id', '=', 'tb3.' . $model . '_id'];
        }
        $condition = [
            'where' => [
                ['tb2.language_id', '=', $this->language]
            ],
        ];
        if (!is_null($keyword)) {
            $condition['keyword'] = addslashes($keyword);
        }

        return [
            'column' => ['id', 'name', 'canonical'],
            'condition' => $condition,
            'perpage' => 10,
            'paginationConfig' => [
                'path' => $model . '.index',
                'groupBy' => ['id', 'name', 'canonical']
            ],
            'orderBy' => [$model . 's.id', 'DESC'],
            'join' => $join,
            'relations' => [],
        ];
    }

    public function findModelObject(Request $request)
    {
        $get = $request->input();
        $alias = Str::snake($get['model']) . '_language';
        $class = loadClassInterface($get['model'], 'Repository');
        $object = $class->findWidgetItem([
            ['name', 'LIKE', '%' . $get['keyword'] . '%'],
        ], $this->language, $alias);

        return response()->json($object);
    }

    public function findPromotionObject(Request $request)
    {
        $get = $request->input();
        $model = $get['option']['model'];
        $keyword = $get['search'];
        $alias = Str::snake($model) . '_language';
        $class = loadClassInterface($model);
        $object = $class->findWidgetItem([
            ['name', 'LIKE', '%' . $keyword . '%'],
        ], $this->language, $alias);

        $temp = [];
        if(count($object) ){
            foreach($object as $key => $val){
                $temp[] = [
                    'id' => $val->id,
                    'text' => $val->languages->first()->pivot->name,
                ];
            }
        }

        return response()->json(array('items' => $temp));
    }

    // private function loadClassInterface(string $model = '', $interface = 'Repository')
    // {
    // if ($interface === 'Repository') {
    //     $serviceInterfaceNamespace = '\App\Repositories\\' . ucfirst($model) . $interface;
    //     if (class_exists($serviceInterfaceNamespace)) {
    //         $serviceInstance = app($serviceInterfaceNamespace);
    //     }
    // } else {
    //     $serviceInterfaceNamespace = '\App\Services\\' . ucfirst($model) . $interface;
    //     if (class_exists($serviceInterfaceNamespace)) {
    //         $serviceInstance = app($serviceInterfaceNamespace);
    //     }
    // }
    // return $serviceInstance;
    // }
}
