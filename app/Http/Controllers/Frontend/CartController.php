<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Services\Interfaces\CartServiceInterface as CartService;
use App\Http\Requests\StoreCartRequest;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends FrontendController
{
    protected $provinceRepository;
    protected $cartService;

    public function __construct(
        ProvinceRepository $provinceRepository,
        CartService $cartService,
    ) {
        parent::__construct();
        $this->provinceRepository = $provinceRepository;
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
        $language = $this->language;
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

    public function create()
    {
        
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
