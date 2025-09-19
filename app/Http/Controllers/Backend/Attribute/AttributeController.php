<?php

namespace App\Http\Controllers\Backend\Attribute;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Services\Interfaces\AttributeServiceInterface as AttributeService;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class AttributeController extends Controller
{
    protected $attributeService;
    protected $attributeRepository;
    protected $nestedset;
    protected $language;

    public function __construct(
        AttributeService $attributeService,
        AttributeRepository $attributeRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->attributeService = $attributeService;
        $this->attributeRepository = $attributeRepository;
        $this->initialize();
    }

    private function initialize() {
        $this->nestedset = new Nestedsetbie([
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
            'language_id' => 1,
        ]);
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'attribute.index');
        $attributes = $this->attributeService->paginate($request, $this->language);       
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Attribute'
        ];
        $template = 'backend.attribute.attribute.index';
        $config['seo']  = __('messages.attribute');
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'attributes',
            'dropdown'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'attribute.create');
        $config = $this->configData();
        $template = 'backend.attribute.attribute.store';
        $config['seo']  = __('messages.attribute');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreAttributeRequest $request){
        if($this->attributeService->create($request, $this->language)){

            return redirect()->route('attribute.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'attribute.update');
        $config = $this->configData();
        $attribute = $this->attributeRepository->getAttributeById($id, $this->language);
        $template = 'backend.attribute.attribute.store';
        $config['seo']  = __('messages.attribute');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode($attribute->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'attribute',
            'dropdown',
            'album'
        ));
    }

    public function update(UpdateAttributeRequest $request, $id) {
        if($this->attributeService->update($request, $id, $this->language)){

            return redirect()->route('attribute.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'attribute.destroy');
        $attribute = $this->attributeRepository->getAttributeById($id, $this->language);
        $template = 'backend.attribute.attribute.delete';
        $config['seo']  = __('messages.attribute');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'attribute'
        ));
    }

    public function destroy($id) {
        if($this->attributeService->destroy($id)){

            return redirect()->route('attribute.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
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
