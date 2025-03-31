<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{ModuleTemplate}Request;
use App\Http\Requests\Update{ModuleTemplate}Request;
use App\Repositories\Interfaces\{ModuleTemplate}RepositoryInterface as {ModuleTemplate}Repository;
use App\Services\Interfaces\{ModuleTemplate}ServiceInterface as {ModuleTemplate}Service;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Http\Requests\Delete{ModuleTemplate}Request;
use App\Models\Language;

class {ModuleTemplate}Controller extends Controller
{
    protected ${moduleTemplate}Service;
    protected ${moduleTemplate}Repository;
    protected $nestedset;
    protected $language;

    public function __construct(
        {ModuleTemplate}Service ${moduleTemplate}Service,
        {ModuleTemplate}Repository ${moduleTemplate}Repository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->{moduleTemplate}Service = ${moduleTemplate}Service;
        $this->{moduleTemplate}Repository = ${moduleTemplate}Repository;
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => '{tableName}',
            'foreignkey' => '{foreignKey}',
            'language_id' =>  $this->language,
        ]);
    } 
    
    public function index(Request $request)
    {
        $this->authorize('modules', '{moduleView}.index');
        ${moduleTemplate}s = $this->{moduleTemplate}Service->paginate($request, $this->language);
        
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => '{ModuleTemplate}'
        ];
        $template = 'backend.{moduleView}.index';
        $config['seo']  = __('messages.{moduleTemplate}');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{moduleTemplate}s'
        ));
    }

    public function create()
    {
        $this->authorize('modules', '{moduleView}.create');
        $config = $this->configData();
        $template = 'backend.{moduleView}.store';
        $config['seo']  = __('messages.{moduleTemplate}');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(Store{ModuleTemplate}Request $request){
        if($this->{moduleTemplate}Service->create($request, $this->language)){

            return redirect()->route('{moduleView}.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('{moduleView}.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', '{moduleView}.update');
        $config = $this->configData();
        ${moduleTemplate} = $this->{moduleTemplate}Repository->get{moduleTemplate}ById($id, $this->language);
        $template = 'backend.{moduleView}.store';
        $config['seo']  = __('messages.{moduleTemplate}');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode(${moduleTemplate}->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{moduleTemplate}',
            'dropdown',
            'album'
        ));
    }

    public function update(Update{ModuleTemplate}Request $request, $id) {
        if($this->{moduleTemplate}Service->update($request, $id, $this->language)){

            return redirect()->route('{moduleView}.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('{moduleView}.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', '{moduleView}.destroy');
        ${moduleTemplate} = $this->{moduleTemplate}Repository->get{moduleTemplate}ById($id, $this->language);
        $template = 'backend.{moduleView}.delete';
        $config['seo']  = __('messages.{moduleTemplate}');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{moduleTemplate}'
        ));
    }

    public function destroy(Delete{moduleTemplate}Request $request, $id) {
        if($this->{moduleTemplate}Service->destroy($id)){

            return redirect()->route('{moduleView}.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('{moduleView}.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
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
