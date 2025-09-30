<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;

class ProductCatalogueController extends FrontendController
{
    protected $productCatalogueRepository;
    protected $productService;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductService $productService,
    ) {
        parent::__construct();
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productService = $productService;
    }

    public function index($id, $request, $page = 1)
    {
        $language = $this->language;
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $language);
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $language);
        $products = $this->productService->paginate(
            $request, 
            $language, 
            $productCatalogue, 
            $page,
            ['path' => $productCatalogue->canonical],
        );
        $productId = $products->pluck('id')->toArray();
        if(count($productId) && !is_null($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }

        // SEO and System
        $system = $this->system;
        $seo = seo($productCatalogue, $page);

        return view('frontend.product.catalogue.index', compact(
            'productCatalogue',
            'products',
            'breadcrumb',
            'system',
            'seo',
        ));
    }
}
