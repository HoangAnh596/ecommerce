<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StorePermissionRequest;
use App\Http\Requests\User\UpdatePermissionRequest;
use App\Repositories\Interfaces\PermissionRepositoryInterface as PermissionRepository;
use App\Services\Interfaces\PermissionServiceInterface as PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;
    protected $permissionRepository;

    public function __construct(
        PermissionService $permissionService,
        PermissionRepository $permissionRepository,
    ){
        $this->permissionService = $permissionService;
        $this->permissionRepository = $permissionRepository;
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'permission.index');
        $permissions = $this->permissionService->paginate($request);     
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Permission'
        ];
        $template = 'backend.permission.index';
        $config['seo']  = __('messages.permission');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'permissions'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'permission.create');
        $config = $this->configData();
        $template = 'backend.permission.store';
        $config['seo']  = __('messages.permission');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config'
        ));
    }

    public function store(StorePermissionRequest $request){
        if($this->permissionService->create($request)){

            return redirect()->route('permission.create')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('permission.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'permission.update');
        $config = $this->configData();
        $permission = $this->permissionRepository->findById($id);
        $template = 'backend.permission.store';
        $config['seo']  = __('messages.permission');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'permission'
        ));
    }

    public function update(UpdatePermissionRequest $request, $id) {
        if($this->permissionService->update($request, $id)){

            return redirect()->route('permission.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('permission.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'permission.destroy');
        $language = $this->permissionRepository->findById($id);
        $template = 'backend.permission.delete';
        $config['seo']  = __('messages.permission');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language'
        ));
    }

    public function destroy($id) {
        if($this->permissionService->destroy($id)){

            return redirect()->route('language.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('language.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function switchBackendLanguage($id) {
        $language = $this->permissionRepository->findById($id);
        if($this->permissionService->switch($id)){
            session(['app_locale' => $language->canonical]);
            \App::setLocale($language->canonical);
        }

        return redirect()->back();
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
