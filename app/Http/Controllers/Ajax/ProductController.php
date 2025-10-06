<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface as ProductVariantRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Models\Language;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $attributeRepository;
    protected $language;

    public function __construct(
        ProductVariantRepository $productVariantRepository,
        PromotionRepository $promotionRepository,
        AttributeRepository $attributeRepository,
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->promotionRepository = $promotionRepository;
        $this->attributeRepository = $attributeRepository;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function loadProductPromotion(Request $request)
    {
        $get = $request->input();
        $loadClass = loadClassInterface($get['model']);

        if ($get['model'] == 'Product') {
            $condition = [
                ['tb2.language_id', '=', $this->language]
            ];

            if (isset($get['keyword']) && $get['keyword'] != '') {
                $keywordCondition = ['tb2.name', 'LIKE', '%' . $get['keyword'] . '%'];
                array_push($condition, $keywordCondition);
            }

            $objects = $loadClass->findProductForPromotion($condition);
        } else if ($get['model'] == 'ProductCatalogue') {
            $conditionArray['keyword'] = ($get['keyword']) ?? null;
            $conditionArray['where'] = [
                ['tb2.language_id', '=', $this->language]
            ];

            $objects = $loadClass->pagination(
                [
                    'product_catalogues.id',
                    'tb2.name'
                ],
                $conditionArray,
                20,
                ['path' => 'product.catalogue.index'],
                ['product_catalogues.id', 'DESC'],
                [
                    ['product_catalogue_language as tb2', 'tb2.product_catalogue_id', '=', 'product_catalogues.id'],
                ],
                []
            );
        }

        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects,
        ]);
    }

    public function loadVariant(Request $request)
    {
        $productId = $request->input('product_id');
        $languageId = $request->input('language_id');
        $attributeId = $request->input('attribute_id');
     
        sort($attributeId, SORT_NUMERIC);
        $attributeId = implode(', ', $attributeId);

        $variant = $this->productVariantRepository->findVariant($attributeId, $productId, $languageId);
        $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
        $variantPrice = getVariantPrice($variant, $variantPromotion);

        return response()->json([
            'variant' => $variant,
            'variantPrice' => $variantPrice
        ]);
    }
}
