<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    protected $controllerName;

    public function __construct(
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
    ){
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'ProductController';
    }

    public function paginate($request, $languageId){
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
            ['products.id','ASC'],
            [
                ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
                ['product_catalogue_product as tb3', 'products.id', '=', 'tb3.product_id']
            ],
            ['product_catalogues'],
            $this->whereRaw($request, $languageId)
        );

        return $products;
    }

    public function create($request, $languageId) {
        DB::beginTransaction();
        try {
            $product = $this->createProduct($request);
            if($product->id > 0) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->createRouter($product, $request, $this->controllerName);
                $product->product_catalogues()->sync($this->catalogue($request));
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

    public function update($request, $id, $languageId){
        DB::beginTransaction();
        try {
            $product = $this->productRepository->findById($id);
            if($this->uploadProduct($product, $request)){
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateRouter($product, $request, $this->controllerName);
                $product->product_catalogues()->sync($this->catalogue($request));
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

    public function destroy($id) {
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

    public function updateStatus($product = []) {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = ($product['value'] == 1) ? 2 : 1;
            $this->productRepository->update($product['modelId'], $payload);

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

    public function updateStatusAll($product) {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = $product['value'];
            $this->productRepository->updateByWhereIn('id', $product['id'], $payload);

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

    private function paginateSelect() {
        return [
            'products.id',
            'products.image',
            'products.publish',
            'products.order',
            'tb2.name',
            'tb2.canonical'
        ];
    }

    private function whereRaw($request, $languageId){
        $rawCondition = [];
        if($request->integer('product_catalogue_id') > 0){
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        JOIN product_catalogue_language ON product_catalogues.id = product_catalogue_language.product_catalogue_id
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                        AND product_catalogue_language.language_id = '.$languageId.'
                    )',
                    [$request->integer('product_catalogue_id'), $request->integer('product_catalogue_id')]
                ]
            ];
            
        }
        return $rawCondition;
    }

    private function createProduct($request) {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);

        return $this->productRepository->create($payload);
    }

    private function uploadProduct($product, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);

        return $this->productRepository->update($product->id, $payload);
    }

    private function updateLanguageForProduct($product, $request, $languageId) {
        $payload = $request->only($this->payloadProduct());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_id'] = $product->id;
        $product->languages()->detach([$languageId, $product->id]);

        return $this->productRepository->createPivot($product, $payload, 'languages');
    }

    private function catalogue($request) {
        if($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->input('product_catalogue_id')]));
        }

        return $request->product_catalogue_id;
    }

    private function payload() {
        return ['product_catalogue_id', 'image', 'album', 'publish', 'follow'];
    }

    private function payloadProduct() {
        return ['name', 'canonical', 'description', 'content', 'meta_title', 'meta_description', 'meta_keyword'];
    }
}
