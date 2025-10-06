<?php

namespace App\Services;

use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class WidgetService
 * @package App\Services
 */
class WidgetService extends BaseService implements WidgetServiceInterface
{
    protected $widgetRepository;
    protected $promotionRepository;
    protected $productService;
    protected $productCatalogueRepository;

    public function __construct(
        WidgetRepository $widgetRepository,
        PromotionRepository $promotionRepository,
        ProductService $productService,
        ProductCatalogueRepository $productCatalogueRepository,
    ) {
        $this->widgetRepository = $widgetRepository;
        $this->promotionRepository = $promotionRepository;
        $this->productService = $productService;
        $this->productCatalogueRepository = $productCatalogueRepository;
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
            $payload['album'] = $request->input('album') ?? null;
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
    public function getWidget(array $params = [], int $language)
    {
        $whereIn = [];
        if (count($params)) {
            foreach ($params as $key => $val) {
                $whereIn[] = $val['keyword'];
            }
        }

        $widgets = $this->widgetRepository->getWidgetWhereIn($whereIn);
        if (!is_null($widgets)) {
            $temp = [];
            foreach ($widgets as $key => $widget) {
                $class = loadClassInterface($widget->model);
                $agrument = $this->widgetAgrument($widget, $language, $params[$key]);
                $object = $class->findByCondition(...$agrument);
                $model = lcfirst(str_replace('Catalogue', '', $widget->model));
                $replace = $model . 's';
                $service  = $model . 'Service';

                if (count($object) && strpos($widget->model, 'Catalogue')) {
                    $classRepo = loadClassInterface(ucfirst($model));
                    foreach ($object as $objValue) {
                        if (isset($params[$key]['children']) && $params[$key]['children']) {
                            $childrenAgrument = $this->childrenAgrument([$objValue->id], $language);
                            $objValue->childrens = $class->findByCondition(...$childrenAgrument);
                        }

                        /**************** LẤY SẢN PHẨM ****************/
                        $childId = $class->recursiveCategory($objValue->id, $model);
                        $ids = [];
                        foreach ($childId as $child_id) {
                            $ids[] = $child_id->id;
                        }

                        if ($objValue->rgt - $objValue->lft >= 1) {
                            $objValue->{$replace} = $classRepo->findObjectByCategoryIds($ids, $model, $language);
                        }

                        if (isset($params[$key]['promotion']) && $params[$key]['promotion'] == true) {
                            $productId = $objValue->{$replace}->pluck('id')->toArray();
                            $objValue->{$replace} = $this->{$service}->combineProductAndPromotion($productId, $objValue->{$replace});
                        }
                        $widgets[$key]->object = $object;
                    }
                } else {
                    $productId = $object->pluck('id')->toArray();
                    $object = $this->{$service}->combineProductAndPromotion($productId, $object);
                    $widget->object = $object;
                }

                $temp[$widget->keyword] = $widgets[$key];
            }
        }

        return $temp;
    }

    private function widgetAgrument($widget, $language, $param)
    {
        $relation = [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ];

        $withCount = [];
        if (strpos($widget->model, 'Catalogue')) {
            $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
            if (isset($param['object'])) {
                $relation[$model] = function ($query) use ($param, $language) {
                    $query->whereHas('languages', function ($query) use ($language) {
                        $query->where('language_id', $language);
                    });
                    $query->take(($param['limit']) ?? 8);
                    $query->orderBy('order', 'desc');
                };
            }
            if (isset($param['countObject'])) {
                $withCount[] = $model;
            }
        } else {
            $model = lcfirst($widget->model) . '_catalogues';
            $relation[$model] = function ($query) use ($language) {
                $query->with('languages', function ($query) use ($language) {
                    $query->where('language_id', $language);
                });
            };
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

    private function childrenAgrument($objectId, $language)
    {
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ],
            'params' => [
                'whereIn' => $objectId,
                'whereInField' => 'parent_id',
            ],
        ];
    }
}
