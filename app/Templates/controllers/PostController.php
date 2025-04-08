<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store{$class}Request;
use App\Http\Requests\Update{$class}Request;
use App\Repositories\Interfaces\{$class}RepositoryInterface as {$class}Repository;
use App\Services\Interfaces\{$class}ServiceInterface as {$class}Service;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class {$class}Controller extends Controller
{
    protected ${module}Service;
    protected ${module}Repository;
    protected $nestedset;
    protected $language;

    public function __construct(
        {$class}Service ${module}Service,
        {$class}Repository ${module}Repository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->{module}Service = ${module}Service;
        $this->{module}Repository = ${module}Repository;
        $this->initialize();
    }

    private function initialize() {
        $this->nestedset = new Nestedsetbie([
            'table' => '{module}_catalogues',
            'foreign_key' => '{module}_catalogue_id',
            'language_id' => 1,
        ]);
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', '{module}.index');
        ${module}s = $this->{module}Service->paginate($request, $this->language);       
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => '{$class}'
        ];
        $template = 'backend.{module}.{module}.index';
        $config['seo']  = __('messages.{module}');
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}s',
            'dropdown'
        ));
    }

    public function create()
    {
        $this->authorize('modules', '{module}.create');
        $config = $this->configData();
        $template = 'backend.{module}.{module}.store';
        $config['seo']  = __('messages.{module}');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(Store{$class}Request $request){
        if($this->{module}Service->create($request, $this->language)){

            return redirect()->route('{module}.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('{module}.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', '{module}.update');
        $config = $this->configData();
        ${module} = $this->{module}Repository->get{$class}ById($id, $this->language);
        $template = 'backend.{module}.{module}.store';
        $config['seo']  = __('messages.{module}');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode(${module}->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}',
            'dropdown',
            'album'
        ));
    }

    public function update(Update{$class}Request $request, $id) {
        if($this->{module}Service->update($request, $id)){

            return redirect()->route('{module}.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('{module}.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', '{module}.destroy');
        ${module} = $this->{module}Repository->get{$class}ById($id, $this->language);
        $template = 'backend.{module}.{module}.delete';
        $config['seo']  = __('messages.{module}');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            '{module}'
        ));
    }

    public function destroy($id) {
        if($this->{module}Service->destroy($id)){

            return redirect()->route('{module}.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('{module}.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
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
