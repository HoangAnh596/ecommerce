<?php

namespace App\Repositories;

use App\Models\Promotion;
use App\Repositories\Interfaces\PromotionRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class PromotionRepository
 * @package App\Repositories
 */
class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    protected $model;

    public function __construct(Promotion $model)
    {
        $this->model = $model;
    }

    public function findByProduct(array $productId = [])
    {
        // 1) Subquery: tính discount cho từng hàng
        $base = DB::table('promotion_product_variant as ppv')
            ->join('promotions', 'promotions.id', '=', 'ppv.promotion_id')
            ->join('products', 'products.id', '=', 'ppv.product_id')
            ->where('products.publish', 2)
            ->where('promotions.publish', 2)
            ->whereIn('products.id', $productId)
            ->whereDate('promotions.endDate', '>', now())
            ->selectRaw("
            promotions.id            as promotion_id,
            promotions.discountValue,
            promotions.discountType,
            promotions.maxDiscountValue,
            products.id              as product_id,
            products.price           as product_price,
            IF(
                promotions.maxDiscountValue != 0,
                LEAST(
                    CASE
                        WHEN promotions.discountType = 'cash'    THEN promotions.discountValue
                        WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                        ELSE 0
                    END,
                    promotions.maxDiscountValue
                ),
                CASE
                    WHEN promotions.discountType = 'cash'    THEN promotions.discountValue
                    WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                    ELSE 0
                END
            ) as discount
        ");

        // 2) Lấy max(discount) theo product
        $maxPerProduct = DB::query()->fromSub($base, 't')
            ->select('t.product_id', DB::raw('MAX(t.discount) as max_discount'))
            ->groupBy('t.product_id');

        // 3) Join để lấy dòng có discount = max_discount
        return DB::query()->fromSub($base, 't')
            ->joinSub($maxPerProduct, 'm', function ($join) {
                $join->on('t.product_id', '=', 'm.product_id')
                    ->on('t.discount',   '=', 'm.max_discount');
            })
            ->get();
    }

    // public function findByProduct(array $productId = [])
    // {
    //     // dd($productId); // 10,11,90
    //     return $this->model->select(
    //         'promotions.id as promotion_id',
    //         'promotions.discountValue',
    //         'promotions.discountType',
    //         'promotions.maxDiscountValue',
    //         'products.id as product_id',
    //         'products.price as product_price',
    //     )
    //         ->selectRaw(
    //             "MAX(
    //             IF(promotions.maxDiscountValue != 0, 
    //                 LEAST(
    //                     CASE
    //                     WHEN discountType = 'cash' THEN discountValue
    //                     WHEN discountType = 'percent' THEN products.price * discountValue / 100
    //                     ELSE 0
    //                     END, 
    //                     promotions.maxDiscountValue
    //                     ),
    //                     CASE
    //                     WHEN discountType = 'cash' THEN discountValue
    //                     WHEN discountType = 'percent' THEN products.price * discountValue / 100
    //                     ELSE 0
    //                     END
    //                 ) 
    //             ) as discount
    //             "
    //         )
    //         ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
    //         ->join('products', 'products.id', '=', 'ppv.product_id')
    //         ->where('products.publish', 2)
    //         ->where('promotions.publish', 2)
    //         ->whereIn('products.id', $productId)
    //         ->whereDate('promotions.endDate', '>', now())
    //         ->groupBy('products.id')
    //         // ->havingRaw('MAX(discount)')
    //         ->get()->toArray();
    // }

}
