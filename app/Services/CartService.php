<?php

namespace App\Services;

use App\Services\Interfaces\CartServiceInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

/**
 * Class CartService
 * @package App\Services
 */
class CartService implements CartServiceInterface
{
    protected $productRepository;
    protected $productService;
    protected $productVariantRepository;
    protected $promotionRepository;

    public function __construct(
        ProductRepository $productRepository,
        ProductService $productService,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
    }

    public function create($request, $language = 1)
    {
        try {
            $data = [];
            $payload = $request->input();
            $product = $this->productRepository->findById(
                $payload['id'],
                [
                    'id'
                ],
                [
                    'languages' => function ($query) use ($language) {
                        $query->where('language_id', $language);
                    }
                ]
            );

            $data = [
                'id' => $product->id,
                'name' => $product->languages->first()->pivot->name,
                'qty' => $payload['quantity'],
            ];

            if (isset($payload['attribute_id']) && count($payload['attribute_id'])) {
                $attributeId = sortAttribute($payload['attribute_id']);
                $variant = $this->productVariantRepository->findVariant($attributeId, $product->id, $language);
                $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
                $variantPrice = getVariantPrice($variant, $variantPromotion);

                $data['id'] = $product->id . '_' . $variant->uuid;
                $data['name'] = $product->languages->first()->pivot->name . ' ' . $variant->languages->first()->pivot->name;
                $data['price'] = ($variantPrice['priceSale'] > 0) ? $variantPrice['priceSale'] : $variantPrice['price'];
                $data['options'] = [
                    'attribute' => $payload['attribute_id']
                ];
            } else {
                $product = $this->productService->combineProductAndPromotion([$product->id], $product, true);
                $price = getPrice($product);
                $data['price'] = ($price['priceSale'] > 0) ? $price['priceSale'] : $price['price'];
            }

            Cart::instance('shopping')->add($data);     // Lưu trữ list sản phẩm vào Session

            return true;
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
            die;
            return false;
        }
    }

    public function update($request)
    {
        try {
            $payload = $request->all();
            Cart::instance('shopping')->update($payload['rowId'], $payload['qty']);

            $cartCaculate = $this->cartAndPromotion();

            $cartItem = Cart::instance('shopping')->get($payload['rowId']);
            $cartCaculate['cartItemSubTotal'] = $cartItem->qty * $cartItem->price;

            return $cartCaculate;
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
            die;
            return false;
        }
    }

    public function delete($request)
    {
        try {
            $payload = $request->all();

            Cart::instance('shopping')->remove($payload['rowId']);
            $cartCaculate = $this->cartAndPromotion();

            return $cartCaculate;
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
            die;
            return false;
        }
    }

    private function cartAndPromotion()
    {
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);
        $cartCaculate['cartTotal'] = $cartCaculate['cartTotal'] - $cartPromotion['discount'];
        $cartCaculate['cartDiscount'] = $cartPromotion['discount'];

        return $cartCaculate;
    }

    public function reCaculateCart()
    {
        $carts = Cart::instance('shopping')->content();
        $total = 0;
        $totalItems = 0;
        foreach ($carts as $cart) {
            $total = $total + $cart->price * $cart->qty;
            $totalItems = $totalItems + $cart->qty;
        }

        return [
            'cartTotal' => $total,
            'cartTotalItems' => $totalItems
        ];
    }

    public function remakeCart($carts)
    {
        $cartId = $carts->pluck('id')->all();
        $temp = [];
        $objects = [];
        if (count($cartId)) {
            foreach ($cartId as $key => $val) {
                $extract = explode('_', $val);
                if (count($extract) > 1) {
                    $temp['variant'][] = $extract[1];
                }

                $temp['product'][] = $extract[0];
            }
        }

        if (isset($temp['variant'])) {
            $objects['variants'] = $this->productVariantRepository->findByCondition(
                [],
                true,
                [],
                ['id', 'desc'],
                [
                    'whereIn' => $temp['variant'],
                    'whereInField' => 'uuid'
                ]
            )->keyBy('uuid');
        }

        if (isset($temp['product'])) {
            $objects['products'] = $this->productRepository->findByCondition(
                [
                    config('apps.general.defaultPublish')
                ],
                true,
                [],
                ['id', 'desc'],
                [
                    'whereIn' => $temp['product'],
                    'whereInField' => 'id'
                ]
            )->keyBy('id');
        }

        foreach ($carts as $cart) {
            $explode = explode('_', $cart->id);
            $objectId = $explode[1] ?? $explode[0];
            if (isset($objects['variants'][$objectId])) {
                $variantItem = $objects['variants'][$objectId];
                $variantImage = explode(',', $variantItem->album)[0] ?? null;
                $cart->image = $variantImage;
                $cart->priceOriginal = $variantItem->price;
            } elseif (isset($objects['products'][$objectId])) {
                $productItem = $objects['products'][$objectId];
                $cart->image = $productItem->image;
                $cart->priceOriginal = $productItem->price;
            }
        }

        return $carts;
    }

    public function cartPromotion($cartTotal = 0)
    {
        $maxDiscount = 0;
        $selectPromotion = null;
        $promotions = $this->promotionRepository->getPromotionByCartTotal();

        if (!is_null($promotions)) {
            foreach ($promotions as $promotion) {
                $discount = $promotion->discountInformation['info'];
                $amountFrom = $discount['amountFrom'] ?? [];
                $amountTo = $discount['amountTo'] ?? [];
                $amountValue = $discount['amountValue'] ?? [];
                $amountType = $discount['amountType'] ?? [];

                if (
                    !empty($amountFrom)
                    && count($amountFrom) == count($amountTo)
                    && count($amountTo) == count($amountValue)
                ) {
                    for ($i = 0; $i < count($amountFrom); $i++) {
                        $currentAmountFrom = convert_price($amountFrom[$i]);
                        $currentAmountTo = convert_price($amountTo[$i]);
                        $currentAmountValue = convert_price($amountValue[$i]);
                        $currentAmountType = $amountType[$i];

                        if (
                            ($cartTotal > $currentAmountFrom && $cartTotal < $currentAmountTo)
                            || $cartTotal > $currentAmountTo
                        ) {
                            if ($currentAmountType == 'cash') {
                                $maxDiscount = max($maxDiscount, $currentAmountValue);
                            } else if ($currentAmountType == 'percent') {
                                $discountValue = ($currentAmountValue / 100) * $cartTotal;
                                $maxDiscount = max($maxDiscount, $discountValue);
                            }
                            $selectPromotion = $promotion;
                        }
                    }
                }
            }
        }

        return [
            'discount' => $maxDiscount,
            'selectPromotion' => $selectPromotion
        ];
    }
}
