<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;

class ProductController extends FrontendController
{
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $productRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        ProductRepository $productRepository,
    ) {
        parent::__construct();
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }

    public function index($id, $request)
    {
        $language = $this->language;
        $product = $this->productRepository->getProductById($id, $language);
        $product = $this->productService->combineProductAndPromotion([$id], $product, true);
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $language);
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $language);

        /* ------------ */
        $product = $this->productService->getAttribute($product, $language);

        $category = recursive(
            $this->productCatalogueRepository->all(
                [
                    'languages' => function ($query) use ($language) {
                        $query->where('language_id', $language);
                    }
                ],
                categorySelectRaw('product')
            )
        );

        // SEO and System
        $system = $this->system;
        $seo = seo($product);

        return view('frontend.product.product.index', compact(
            'productCatalogue',
            'product',
            'breadcrumb',
            'system',
            'seo',
            'language',
            'category'
        ));
    }
}
