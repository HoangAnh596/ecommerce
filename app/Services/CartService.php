<?php

namespace App\Services;

use App\Enums\PromotionEnum;
use App\Services\Interfaces\CartServiceInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB;

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
    protected $orderRepository;

    protected $priceOriginal;
    protected $image;

    public function __construct(
        ProductRepository $productRepository,
        ProductService $productService,
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->productService = $productService;
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->orderRepository = $orderRepository;
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

    public function order($request, $system)
    {
        DB::beginTransaction();
        try {
            $payload = $this->request($request);
            $order = $this->orderRepository->create($payload, ['products']);

            if ($order->id > 0) {
                $this->createOrderProduct($payload, $order);
                // $this->paymentOnline($payload['method']);
                // $this->mail($order, $system);
                // Cart::instance('shopping')->destroy();
            }

            DB::commit();
            return [
                'order' => $order,
                'flag' => true
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return [
                'order' => null,
                'flag' => false
            ];
        }
    }

    private function paymentOnline($method = '')
    {
        switch ($method) {
            case 'zalo':
                $this->zaloPay();
                break;
            case 'momo':
                $this->momoPay();
                break;
            case 'shopee':
                $this->shopeePay();
                break;
            case 'vnpay':
                $this->momoPay();
                break;
            case 'paypal':
                $this->paypal();
                break;  
        }
    }

    private function createOrderProduct($payload, $order)
    {
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);

        $temp = [];
        if (!is_null($carts)) {
            foreach ($carts as $val) {
                $extract = explode('_', $val->id);
                $temp[] = [
                    'product_id' => $extract[0],
                    'uuid' => $extract[1] ?? null,
                    'name' => $val->name,
                    'qty' => $val->qty,
                    'price' => $val->price,
                    'priceOriginal' => $val->priceOriginal,
                    'option' => json_encode($val->options)
                ];
            }
        }

        $order->products()->sync($temp);
    }

    private function request($request)
    {
        $cartCaculate = $this->reCaculateCart();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);

        $payload = $request->except('_token', 'create', 'voucher');
        $payload['code'] = 'dh-'.time();
        $payload['cart'] = $cartCaculate;
        $payload['promotion']['discount'] = $cartPromotion['discount'];
        $payload['promotion']['name'] = $cartPromotion['selectPromotion']->name ?? null;
        $payload['promotion']['code'] = $cartPromotion['selectPromotion']->code ?? null;
        $payload['promotion']['startDate'] = $cartPromotion['selectPromotion']->startDate ?? null;
        $payload['promotion']['endDate'] = $cartPromotion['selectPromotion']->endDate ?? null;
        $payload['confirm'] = 'pending';
        $payload['delivery'] = 'pending';
        $payload['payment'] = 'unpaid';

        return $payload;
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
                $cart->setImage($variantImage)->setPriceOriginal($variantItem->price);
                // $cart->image = $variantImage;
                // $cart->priceOriginal = $variantItem->price;
            } elseif (isset($objects['products'][$objectId])) {
                $productItem = $objects['products'][$objectId];
                $cart->setImage($productItem->image)->setPriceOriginal($productItem->price);
                // $cart->image = $productItem->image;
                // $cart->priceOriginal = $productItem->price;
            }
        }

        return $carts;
    }

    private function mail($order, $system)
    {
        $carts = Cart::instance('shopping')->content();
        $carts = $this->remakeCart($carts);
        $cartCaculate = $this->cartAndPromotion();
        $cartPromotion = $this->cartPromotion($cartCaculate['cartTotal']);

        $to = $order->email;
        $cc = $system['contact_email'];
        $data = [
            'order' => $order,
            'carts' => $carts,
            'cartCaculate' => $cartCaculate,
            'cartPromotion' => $cartPromotion
        ];

        Mail::to($to)->cc($cc)->send(new OrderMail($data));
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
