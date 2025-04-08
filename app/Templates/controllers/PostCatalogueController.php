<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{$class}CatalogueRequest;
use App\Http\Requests\Update{$class}CatalogueRequest;
use App\Repositories\Interfaces\{$class}CatalogueRepositoryInterface as {$class}CatalogueRepository;
use App\Services\Interfaces\{$class}CatalogueServiceInterface as {$class}CatalogueService;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Http\Requests\Delete{$class}CatalogueRequest;
use App\Models\Language;

class {$class}CatalogueController extends Controller
{
    protected ${module}CatalogueService;
    protected ${module}CatalogueRepository;
    protected $nestedset;
    protected $language;

    public function __construct(
        {$class}CatalogueService ${module}CatalogueService,
        {$class}CatalogueRepository ${module}CatalogueRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->{module}CatalogueService = ${module}CatalogueService;
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => '{module}_catalogues',
            'foreignkey' => '{module}_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 
    
    public function index(Request $request)
    {
        $this->authorize('modules', '{module}.catalogue.index');
        ${module}Catalogues = $this->{module}CatalogueService->paginate($request, $this->language);
        
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => '{$class}Catalogue'
        ];
        $template = 'backend.{module}.catalogue.index';
        $config['seo']  = __('messages.{module}Catalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}Catalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', '{module}.catalogue.create');
        $config = $this->configData();
        $template = 'backend.{module}.catalogue.store';
        $config['seo']  = __('messages.{module}Catalogue');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(Store{$class}CatalogueRequest $request){
        if($this->{module}CatalogueService->create($request, $this->language)){

            return redirect()->route('{module}.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('{module}.catalogue.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', '{module}.catalogue.update');
        $config = $this->configData();
        ${module}Catalogue = $this->{module}CatalogueRepository->get{$class}CatalogueById($id, $this->language);
        $template = 'backend.{module}.catalogue.store';
        $config['seo']  = __('messages.{module}Catalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode(${module}Catalogue->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}Catalogue',
            'dropdown',
            'album'
        ));
    }

    public function update(Update{$class}CatalogueRequest $request, $id) {
        if($this->{module}CatalogueService->update($request, $id, $this->language)){

            return redirect()->route('{module}.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('{module}.catalogue.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', '{module}.catalogue.destroy');
        ${module}Catalogue = $this->{module}CatalogueRepository->get{$class}CatalogueById($id, $this->language);
        $template = 'backend.{module}.catalogue.delete';
        $config['seo']  = __('messages.{module}Catalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}Catalogue'
        ));
    }

    public function destroy(Delete{$class}CatalogueRequest $request, $id) {
        if($this->{module}CatalogueService->destroy($id, $this->language)){

            return redirect()->route('{module}.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('{module}.catalogue.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
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
