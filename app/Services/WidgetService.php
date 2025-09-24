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

        if (!is_null($widget)) {
            $class = loadClassInterface($widget->model);
            $agrument = $this->widgetAgrument($widget, $language, $param);
            $object = $class->findByCondition(...$agrument);

            $model = lcfirst(str_replace('Catalogue', '', $widget->model));
            if (count($object)) {
                foreach ($object as $val) {
                    if ($model === 'product' && isset($param['object']) && $param['object'] == true) {
                        // if ($val->id != 8) continue; // để test id = 8
                        $productId = $val->products->pluck('id')->toArray();
                        //dd($productId); // array:3 [▼ app\Services\WidgetService.php:164
                        //   0 => 10
                        //   1 => 11
                        //   2 => 90
                        // ]
                        $val->products = $this->productService->combineProductAndPromotion($productId, $val->products);
                    }

                    if (isset($param['children']) && $param['children'] == true) {
                        $val->childrens = $this->productCatalogueRepository->findByCondition(
                            [
                                ['lft', '>', $val->lft],
                                ['rgt', '<', $val->rgt],
                                config('apps.general.defaultPublish'),
                            ],
                            true
                        );
                    }
                }
            }

            return $object;
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
        if (strpos($widget->model, 'Catalogue') && isset($param['object'])) {
            $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
            $relation[$model] = function ($query) use ($param, $language) {
                $query->whereHas('languages', function ($query) use ($language) {
                    $query->where('language_id', $language);
                });
                $query->take(($param['limit']) ?? 8);
                $query->orderBy('order', 'desc');
            };

            if (isset($param['countObject'])) {
                $withCount[] = $model;
            }
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

        // if (strpos($widget->model, 'Catalogue') && isset($param['children'])) {
        //     $model = lcfirst(str_replace('Catalogue', '', $widget->model)) . 's';
        //     $relation[$model] = function ($query) use ($param, $language) {
        //         $limit = $param['limit'] ?? 8;
        //         $query->limit($limit);
        //         $query->where('publish', config('apps.general.public')); // config('apps.general.public') = 2
        //         $query->with(['languages' => function ($query) use ($language) {
        //             $query->where('language_id', $language);
        //         }]);
        //         $query->with('promotions', function ($query) use($limit) {
        //             $query->select(
        //                 'promotions.id',
        //                 'promotions.discountValue',
        //                 'promotions.discountType',
        //                 'promotions.maxDiscountValue',
        //                 DB::raw("
        //                     IF(promotions.maxDiscountValue != 0, 
        //                         LEAST(
        //                             CASE
        //                             WHEN discountType = 'cash' THEN discountValue
        //                             WHEN discountType = 'percent' THEN ((SELECT price FROM products
        //                             WHERE products.id = product_id) * discountValue / 100)
        //                             ELSE 0
        //                             END, 
        //                             promotions.maxDiscountValue
        //                         ),
        //                         CASE
        //                             WHEN discountType = 'cash' THEN discountValue
        //                             WHEN discountType = 'percent' THEN ((SELECT price FROM products
        //                             WHERE products.id = product_id) * discountValue / 100)
        //                             ELSE 0
        //                             END
        //                     ) as discount
        //                 ")
        //             );
        //             $query->where('publish', 2);
        //             $query->whereDate('endDate', '>', now());
        //             $query->orderBy('discount', 'DESC');
        //             $query->take($limit);
        //         });
        //     };

        //     $withCount[] = $model;
        // }
    }
}
