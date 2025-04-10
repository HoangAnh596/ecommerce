<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCatalogueRequest;
use App\Http\Requests\UpdateProductCatalogueRequest;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Http\Requests\DeleteProductCatalogueRequest;
use App\Models\Language;

class ProductCatalogueController extends Controller
{
    protected $productCatalogueService;
    protected $productCatalogueRepository;
    protected $nestedset;
    protected $language;

    public function __construct(
        ProductCatalogueService $productCatalogueService,
        ProductCatalogueRepository $productCatalogueRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->productCatalogueService = $productCatalogueService;
        $this->productCatalogueRepository = $productCatalogueRepository;
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'product.catalogue.index');
        $productCatalogues = $this->productCatalogueService->paginate($request, $this->language);
        
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'ProductCatalogue'
        ];
        $template = 'backend.product.catalogue.index';
        $config['seo']  = __('messages.productCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'productCatalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'product.catalogue.create');
        $config = $this->configData();
        $template = 'backend.product.catalogue.store';
        $config['seo']  = __('messages.productCatalogue');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreProductCatalogueRequest $request){
        if($this->productCatalogueService->create($request, $this->language)){

            return redirect()->route('product.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'product.catalogue.update');
        $config = $this->configData();
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        $template = 'backend.product.catalogue.store';
        $config['seo']  = __('messages.productCatalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode($productCatalogue->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'productCatalogue',
            'dropdown',
            'album'
        ));
    }

    public function update(UpdateProductCatalogueRequest $request, $id) {
        if($this->productCatalogueService->update($request, $id, $this->language)){

            return redirect()->route('product.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'product.catalogue.destroy');
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        $template = 'backend.product.catalogue.delete';
        $config['seo']  = __('messages.productCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'productCatalogue'
        ));
    }

    public function destroy(DeleteProductCatalogueRequest $request, $id) {
        if($this->productCatalogueService->destroy($id, $this->language)){

            return redirect()->route('product.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('product.catalogue.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
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
