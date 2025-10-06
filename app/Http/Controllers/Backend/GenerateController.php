<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Generate\StoreGenerateRequest;
use App\Http\Requests\Generate\UpdateGenerateRequest;
use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;
use App\Services\Interfaces\GenerateServiceInterface as GenerateService;
use Illuminate\Http\Request;

class GenerateController extends Controller
{
    protected $generateService;
    protected $generateRepository;

    public function __construct(
        GenerateService $generateService,
        GenerateRepository $generateRepository,
    ){
        $this->generateService = $generateService;
        $this->generateRepository = $generateRepository;
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'generate.index');
        $generates = $this->generateService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Generate'
        ];
        $template = 'backend.generate.index';
        $config['seo']  = __('messages.generate');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'generates'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'generate.create');
        $config = $this->configData();
        $template = 'backend.generate.store';
        $config['seo']  = __('messages.generate');
        $config['method'] = 'create';
        $config['model'] = 'Generate';

        return view('backend.dashboard.layout', compact(
            'template',
            'config'
        ));
    }

    public function store(StoreGenerateRequest $request){
        if($this->generateService->create($request)){

            return redirect()->route('generate.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('generate.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'generate.update');
        $config = $this->configData();
        $generate = $this->generateRepository->findById($id);
        $template = 'backend.generate.store';
        $config['seo']  = __('messages.generate');
        $config['method'] = 'edit';
        $config['model'] = 'Generate';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'generate'
        ));
    }

    public function update(UpdateGenerateRequest $request, $id) {
        if($this->generateService->update($request, $id)){

            return redirect()->route('generate.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('generate.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'generate.destroy');
        $generate = $this->generateRepository->findById($id);
        $template = 'backend.generate.delete';
        $config['seo']  = __('messages.generate');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'generate'
        ));
    }

    public function destroy($id) {
        if($this->generateService->destroy($id)){

            return redirect()->route('generate.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('generate.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
        ];
    }
}
