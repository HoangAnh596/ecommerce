<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    protected $languageService;
    protected $languageRepository;
    protected $provinceRepository;

    public function __construct(
        LanguageService $languageService,
        LanguageRepository $languageRepository,
    ){
        $this->languageService = $languageService;
        $this->languageRepository = $languageRepository;
    }
    
    public function index(Request $request)
    {
        $languages = $this->languageService->paginate($request);
        
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
        ];
        $template = 'backend.language.index';
        $config['seo']  = config('apps.language');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'languages'
        ));
    }

    public function create()
    {
        $config = $this->configData();
        $template = 'backend.language.store';
        $config['seo']  = config('apps.language');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config'
        ));
    }

    public function store(StoreLanguageRequest $request){
        if($this->languageService->create($request)){

            return redirect()->route('language.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('language.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $config = $this->configData();
        $language = $this->languageRepository->findById($id);
        $template = 'backend.language.store';
        $config['seo']  = config('apps.language');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language'
        ));
    }

    public function update(UpdateLanguageRequest $request, $id) {
        if($this->languageService->update($request, $id)){

            return redirect()->route('language.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('language.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $language = $this->languageRepository->findById($id);
        $template = 'backend.language.delete';
        $config['seo']  = config('apps.language');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language'
        ));
    }

    public function destroy($id) {
        if($this->languageService->destroy($id)){

            return redirect()->route('language.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('language.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData() {
        return [
            'js' => [
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js'
            ],
        ];
    }
}
