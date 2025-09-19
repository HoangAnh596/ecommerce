<?php

namespace App\Services;

use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class WidgetService
 * @package App\Services
 */
class WidgetService extends BaseService implements WidgetServiceInterface
{
    protected $widgetRepository;

    public function __construct(WidgetRepository $widgetRepository)
    {
        $this->widgetRepository = $widgetRepository;
    }

    public function paginate($request)
    {
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];
        $widgets = $this->widgetRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'widget/index'],
            ['id', 'DESC'],
            [],
            []
        );

        return $widgets;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'album', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            $payload['publish'] = config('apps.general.public');
            $payload['user_id'] = auth()->id();

            $this->widgetRepository->create($payload);

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

    public function update($request, $id, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword', 'short_code', 'description', 'album', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            $payload['user_id'] = auth()->id();

            $this->widgetRepository->update($id, $payload);

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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->widgetRepository->delete($id);

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

    public function saveTranslate($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $temp = [];
            $translate = $request->input('translateId');
            $widget = $this->widgetRepository->findById($request->input('widgetId'));
            $temp = $widget->description;
            $temp[$translate] = $request->input('translate_description');
            $payload['description'] = $temp;

            $this->widgetRepository->update($widget->id, $payload);

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

    private function paginateSelect()
    {
        return ['id', 'name', 'keyword', 'description', 'album', 'short_code', 'model', 'publish'];
    }

    /* FRONTEND SERVICE */

    public function findWidgetByKeyword(string $keyword = '', int $language = 1, $param = [])
    {
        $widget = $this->widgetRepository->findByCondition([
            ['keyword', '=', $keyword],
            config('apps.general.defaultPublish'), // ['publish', '=', 2]
        ]);

        if(!is_null($widget)){
            $loadClass = loadClassInterface($widget->model);
            $agrument = $this->widgetAgrument($widget, $language, $param);
            $object = $loadClass->findByCondition(...$agrument)->toArray();
            dd($object);
        }
    }

    private function widgetAgrument($widget, $language, $param)
    {
        $relation = [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ];

        $withCount = [];
        if (strpos($widget->model, 'Catalogue') && isset($param['children'])) {
            $model = lcfirst(str_replace('Catalogue', '', $widget->model)).'s';
            $relation[$model] = function ($query) use ($param, $language) {
                $query->limit($param['limit'] ?? 8);
                $query->where('publish', config('apps.general.public')); // config('apps.general.public') = 2
                $query->with(['languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }]);
            };

            $withCount[] = $model;
        }

        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => $relation,
            'params' => [
                'whereIn' => $widget->model_id,
                'whereInField' => 'id',
            ],
            'withCount' => $withCount,
        ];
    }
}
