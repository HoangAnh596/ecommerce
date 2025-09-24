<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface as ProductVariantLanguageRepository;
use App\Repositories\Interfaces\ProductVariantAttrRepositoryInterface as ProductVariantAttrRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    protected $prVariantLanguageRepository;
    protected $productVariantAttrRepository;
    protected $controllerName;
    protected $promotionRepository;

    public function __construct(
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
        ProductVariantLanguageRepository $prVariantLanguageRepository,
        ProductVariantAttrRepository $productVariantAttrRepository,
        PromotionRepository $promotionRepository,
    ) {
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->prVariantLanguageRepository = $prVariantLanguageRepository;
        $this->productVariantAttrRepository = $productVariantAttrRepository;
        $this->promotionRepository = $promotionRepository;
        $this->controllerName = 'ProductController';
    }

    public function paginate($request, $languageId)
    {
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $products = $this->productRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'product.index', 'groupBy' => $this->paginateSelect()],
            ['products.id', 'DESC'],
            [
                ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
                ['product_catalogue_product as tb3', 'products.id', '=', 'tb3.product_id']
            ],
            ['product_catalogues'],
            $this->whereRaw($request, $languageId)
        );

        return $products;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $product = $this->createProduct($request);

            if ($product->id > 0) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->createRouter($product, $request, $this->controllerName, $languageId);
                $product->product_catalogues()->sync($this->catalogue($request));

                if ($request->input('attribute')) {
                    $this->createVariant($product, $request, $languageId);
                }
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

    public function update($request, $id, $languageId)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->findById($id);
            if ($this->uploadProduct($product, $request)) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateRouter($product, $request, $this->controllerName, $languageId);
                $product->product_catalogues()->sync($this->catalogue($request));

                $product->product_variants()->each(function ($variant) {
                    $variant->languages()->detach();
                    $variant->attributes()->detach();
                    $variant->delete();
                });

                if ($request->input('attribute')) {
                    $this->createVariant($product, $request, $languageId);
                }
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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->productRepository->delete($id);

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
            'products.id',
            'products.image',
            'products.publish',
            'products.order',
            'tb2.name',
            'tb2.canonical'
        ];
    }

    private function createVariant($product, $request, $languageId)
    {
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload, $product);

        $variants = $product->product_variants()->createMany($variant);
        $variantId = $variants->pluck('id');
        $productVariantLanguage = [];
        $variantAttribute = [];
        $attributeCombies = $this->combineAttribute(array_values($payload['attribute']));

        if (count($variantId)) {
            foreach ($variantId as $key => $val) {
                $productVariantLanguage[] = [
                    'product_variant_id' => $val,
                    'language_id' =>  $languageId,
                    'name' => $payload['productVariant']['name'][$key]
                ];

                if (count($attributeCombies)) {
                    foreach ($attributeCombies[$key] as $attributeId) {
                        $variantAttribute[] = [
                            'product_variant_id' => $val,
                            'attribute_id' => $attributeId
                        ];
                    }
                }
            }
        }
        $this->prVariantLanguageRepository->createBatch($productVariantLanguage);
        /* create variant attribute */
        $this->productVariantAttrRepository->createBatch($variantAttribute);
    }

    private function combineAttribute($attributes = [], $index = 0)
    {
        if ($index === count($attributes)) return [[]];
        $subCombie = $this->combineAttribute($attributes, $index + 1);
        $combies = [];
        foreach ($attributes[$index] as $key => $val) {
            foreach ($subCombie as $keySub => $valSub) {
                $combies[] = array_merge([$val], $valSub);
            }
        }
        return $combies;
    }

    private function createVariantArray(array $payload = [], $product): array
    {
        $variant = [];
        if (isset($payload['variant']['sku']) && count($payload['variant']['sku'])) {
            foreach (($payload['variant']['sku']) as $key => $val) {
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $payload['productVariant']['id'][$key]);
                $variant[] = [
                    'uuid' => $uuid,
                    'code' => ($payload['productVariant']['id'][$key]) ?? '',
                    'quantity' => ($payload['variant']['quantity'][$key]) ?? 0,
                    'sku' => $val,
                    'barcode' => ($payload['variant']['barcode'][$key]) ?? '',
                    'price' => ($payload['variant']['price'][$key]) ? convert_price($payload['variant']['price'][$key]) : '',
                    'file_name' => ($payload['variant']['file_name'][$key]) ?? '',
                    'file_url' => ($payload['variant']['file_url'][$key]) ?? '',
                    'album' => ($payload['variant']['album'][$key]) ?? '',
                    'user_id' => Auth::id()
                ];
            }
        }
        return $variant;
    }

    private function whereRaw($request, $languageId)
    {
        $rawCondition = [];
        if ($request->integer('product_catalogue_id') > 0) {
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        JOIN product_catalogue_language ON product_catalogues.id = product_catalogue_language.product_catalogue_id
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                        AND product_catalogue_language.language_id = ' . $languageId . '
                    )',
                    [$request->integer('product_catalogue_id'), $request->integer('product_catalogue_id')]
                ]
            ];
        }
        return $rawCondition;
    }

    private function createProduct($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price(($payload['price']) ?? 0);
        $payload['attributeCatalogue'] = $this->formatJson($request, 'attributeCatalogue');
        $payload['attribute'] = $this->formatJson($request, 'attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');

        return $this->productRepository->create($payload);
    }

    private function uploadProduct($product, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price($payload['price']);

        return $this->productRepository->update($product->id, $payload);
    }

    private function updateLanguageForProduct($product, $request, $languageId)
    {
        $payload = $request->only($this->payloadProduct());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_id'] = $product->id;
        $product->languages()->detach([$languageId, $product->id]);

        return $this->productRepository->createPivot($product, $payload, 'languages');
    }

    private function catalogue($request)
    {
        if ($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->input('product_catalogue_id')]));
        }

        return $request->product_catalogue_id;
    }

    private function payload()
    {
        return [
            'product_catalogue_id',
            'image',
            'album',
            'publish',
            'follow',
            'made_in',
            'code',
            'price',
            'attributeCatalogue',
            'attribute',
            'variant'
        ];
    }

    private function payloadProduct()
    {
        return ['name', 'canonical', 'description', 'content', 'meta_title', 'meta_description', 'meta_keyword'];
    }

    public function combineProductAndPromotion($productId = [], $products)
    {
        $promotions = $this->promotionRepository->findByProduct($productId);
        if ($promotions) {
            foreach ($products as $index => $product) {
                foreach ($promotions as $key => $promotion) {
                    if ($promotion->product_id == $product->id) {
                        $products[$index]->promotions = $promotion;
                    }
                }
            }
        }

        return $products;
    }
}
