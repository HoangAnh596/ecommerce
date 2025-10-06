<?php

namespace App\Services;

use App\Enums\PromotionEnum;
use App\Services\Interfaces\PromotionServiceInterface;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class PromotionService
 * @package App\Services
 */
class PromotionService extends BaseService implements PromotionServiceInterface
{
    protected $promotionRepository;

    public function __construct(PromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function paginate($request)
    {
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];
        $promotions = $this->promotionRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'promotion/index'],
            ['id', 'DESC'],
            [],
            []
        );

        return $promotions;
    }

    private function request($request)
    {
        $payload = $request->only(
            'name',
            'code',
            'description',
            'method',
            'discountValue',
            'discountType',
            'maxDiscountValue',
            'startDate',
            'endDate',
            'neverEndDate',
        );

        $payload['discountValue'] = convert_price($request->input(PromotionEnum::PRODUCT_AND_QUANTITY . '.discountValue'));
        $payload['discountType'] = $request->input(PromotionEnum::PRODUCT_AND_QUANTITY . '.discountType');
        $payload['maxDiscountValue'] = convert_price($request->input(PromotionEnum::PRODUCT_AND_QUANTITY . '.maxDiscountValue'));
        $payload['startDate'] = Carbon::createFromFormat('d/m/Y H:i', $request->input('startDate'));
        if (isset($payload['endDate'])) {
            $payload['endDate'] = Carbon::createFromFormat('d/m/Y H:i', $request->input('endDate'));
        }
        $payload['user_id'] = auth()->id();
        $payload['code'] = (empty($payload['code'])) ? time() : $payload['code'];

        switch ($payload['method']) {
            case PromotionEnum::ORDER_AMOUNT_RANGE:
                $payload[PromotionEnum::DISCOUNT] = $this->orderByRanger($request);
                break;
            case PromotionEnum::PRODUCT_AND_QUANTITY:
                $payload[PromotionEnum::DISCOUNT] = $this->productAndQuantity($request);
                break;
        }

        return $payload;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $this->request($request);

            $promotion = $this->promotionRepository->create($payload);
            if ($promotion->id > 0) {
                $this->handleRelation($promotion, $request);
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

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $payload = $this->request($request);

            $promotion = $this->promotionRepository->update($id, $payload);
            $this->handleRelation($promotion, $request, 'update');

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
            $this->promotionRepository->delete($id);

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

    private function handleRelation($promotion, $request, $method = 'create')
    {
        if ($request->input('method') === PromotionEnum::PRODUCT_AND_QUANTITY) {
            $object = $request->input('object');
            $payload = [];
            if (!is_null($object)) {
                foreach ($object['id'] as $key => $val) {
                    $payload[] = [
                        'promotion_id' => $promotion->id,
                        'product_id' => $val,
                        // 'product_variant_id' => ($object['product_variant_id'][$key] ?? 0) === 'null' ? 0 : ($object['product_variant_id'][$key] ?? 0),
                        'variant_uuid' => $object['variant_uuid'][$key],
                        'model' => $request->input(PromotionEnum::MODULE_TYPE)
                    ];
                }
            }

            if ($method == 'update') {
                $promotion->products()->detach();
            }

            $promotion->products()->sync($payload);
        }
    }

    private function handleSourceAndCondition($request)
    {
        $data = [
            'source' => [
                'status' => $request->input('source'),
                'data' => $request->input('sourceValue'),
            ],
            'apply' => [
                'status' => $request->input('applyStatus'),
                'data' => $request->input('applyValue'),
            ],
        ];

        if (!is_null($data['apply']['data'])) {
            foreach ($data['apply']['data'] as $key => $val) {
                $data['apply']['condition'][$val] = $request->input($val);
            }
        }

        return $data;
    }

    private function orderByRanger($request)
    {
        $data['info'] = $request->input('promotion_order_amount_range');

        return $data + $this->handleSourceAndCondition($request);
    }

    private function productAndQuantity($request)
    {
        $data['info'] = $request->input('product_and_quantity');
        $data['info']['model'] = $request->input(PromotionEnum::MODULE_TYPE);
        $data['info']['object'] = $request->input('object');

        return $data + $this->handleSourceAndCondition($request);
    }

    private function paginateSelect()
    {
        return [
            'id',
            'name',
            'code',
            'description',
            'method',
            'discountInformation',
            'startDate',
            'endDate',
            'neverEndDate',
            'order',
            'publish'
        ];
    }
}
