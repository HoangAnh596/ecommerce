<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;
use App\Services\Interfaces\CartServiceInterface as CartService;
use App\Http\Requests\StoreCartRequest;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends FrontendController
{
    protected $provinceRepository;
    protected $orderRepository;
    protected $cartService;

    public function __construct(
        ProvinceRepository $provinceRepository,
        OrderRepository $orderRepository,
        CartService $cartService,
    ) {
        parent::__construct();
        $this->provinceRepository = $provinceRepository;
        $this->orderRepository = $orderRepository;
        $this->cartService = $cartService;
    }

    public function checkout()
    {
        $provinces = $this->provinceRepository->all();
        $carts = Cart::instance('shopping')->content();
        $carts = $this->cartService->remakeCart($carts);
        $cartCaculate = $this->cartService->reCaculateCart();
        $cartPromotion = $this->cartService->cartPromotion($cartCaculate['cartTotal']);

        // SEO and System
        $system = $this->system;
        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_images' => '',
            'canonical' => write_url('thanh-toan')
        ];
        $config = $this->configData();

        return view('frontend.cart.index', compact(
            'seo',
            'system',
            'carts',
            'cartCaculate',
            'cartPromotion',
            'provinces',
            'config'
        ));
    }

    public function store(StoreCartRequest $request)
    {
        $order = $this->cartService->order($request);

        if ($order['flag']) {

            return redirect()
                ->route('cart.success', ['code' => $order['order']->code])
                ->with('success', 'Đặt hàng thành công');
        }
        return redirect()->route('cart.checkout')->with('errors', 'Đặt hàng không thành công. Hãy thử lại');
    }

    public function success($code)
    {
        $order = $this->orderRepository->findByCondition(
            [
                ['code', '=', $code]
            ],
        );
        if(!isset($order)) {
            abort('404');
            // return view('frontend.homepage.home.404');
        }

        // SEO and System
        $system = $this->system;
        $seo = [
            'meta_title' => 'Thanh toán đơn hàng thành công',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_images' => '',
            'canonical' => write_url('cart/success')
        ];
        $config = $this->configData();

        return view('frontend.cart.success', compact(
            'seo',
            'system',
            'config',
            'order'
        ));
    }

    private function configData()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'frontend/core/library/cart.js',
                'backend/library/location.js',
            ],
        ];
    }
}
