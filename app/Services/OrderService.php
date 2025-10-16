<?php

namespace App\Services;

use App\Services\Interfaces\OrderServiceInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface as ProductVariantRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderService
 * @package App\Services
 */
class OrderService extends BaseService implements OrderServiceInterface
{
    protected $orderRepository;
    protected $productVariantRepository;

    public function __construct(
        OrderRepository $orderRepository,
        ProductVariantRepository $productVariantRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    public function paginate($request)
    {
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];

        foreach (__('cart') as $key => $val) {
            $condition['dropdown'][$key] = $request->string($key);
        }

        $condition['created_at'] = $request->input('created_at');

        $orders = $this->orderRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'order/index'],
            ['id', 'DESC'],
        );

        return $orders;
    }

    public function getOrderItemImage($order)
    {
        foreach ($order->products as $key => $val) {
            $uuid = $val->pivot->uuid;
            if (!is_null($uuid)) {
                $variant = $this->productVariantRepository->findByCondition([
                    ['uuid', '=', $uuid]
                ]);

                $variantImage = explode(',', $variant->album)[0] ?? null;
                $val->image = $variantImage;
            }
        }

        return $order;
    }

    public function update($request)
    {
        DB::beginTransaction();
        try {
            $orderId = $request->input('id');
            $payload = $request->input('payload');
            $this->orderRepository->update($orderId, $payload);

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

    public function updatePaymentOnline($payload, $order)
    {
        DB::beginTransaction();
        try {
            $this->orderRepository->update($order->id, $payload);

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
        return [
            'id',
            'code',
            'fullname',
            'phone',
            'email',
            'province_id',
            'district_id',
            'ward_id',
            'address',
            'description',
            'promotion',
            'cart',
            'customer_id',
            'guest_cookie',
            'method',
            'confirm',
            'payment',
            'delivery',
            'shipping',
            'created_at'
        ];
    }
}
