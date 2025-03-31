<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;
    protected $nestedset;
    protected $language;

    public function __construct(
        ProductService $productService,
        ProductRepository $productRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->initialize();
    }

    private function initialize() {
        $this->nestedset = new Nestedsetbie([
            'table' => 'products',
            'foreign_key' => 'product_id',
            'language_id' => 1,
        ]);
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'product.index');
        $products = $this->productService->paginate($request, $this->language);       
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Product'
        ];
        $template = 'backend.product.product.index';
        $config['seo']  = __('messages.product');
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'products',
            'dropdown'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'product.create');
        $config = $this->configData();
        $template = 'backend.product.product.store';
        $config['seo']  = __('messages.product');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreProductRequest $request){
        if($this->productService->create($request, $this->language)){

            return redirect()->route('product.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('product.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'product.update');
        $config = $this->configData();
        $product = $this->productRepository->getProductById($id, $this->language);
        $template = 'backend.product.product.store';
        $config['seo']  = __('messages.product');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode($product->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'product',
            'dropdown',
            'album'
        ));
    }

    public function update(UpdateProductRequest $request, $id) {
        if($this->productService->update($request, $id)){

            return redirect()->route('product.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('product.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'product.destroy');
        $product = $this->productRepository->getProductById($id, $this->language);
        $template = 'backend.product.product.delete';
        $config['seo']  = __('messages.product');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'product'
        ));
    }

    public function destroy($id) {
        if($this->productService->destroy($id)){

            return redirect()->route('product.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('product.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData() {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
        ];
    }
}
