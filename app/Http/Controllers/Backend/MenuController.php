<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Services\Interfaces\MenuServiceInterface as MenuService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface  as MenuCatalogueRepository;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuService;
    protected $menuRepository;
    protected $menuCatalogueRepository;

    public function __construct(
        MenuService $menuService,
        MenuRepository $menuRepository,
        MenuCatalogueRepository $menuCatalogueRepository
    ){
        $this->menuService = $menuService;
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'menu.index');
        $menus = $this->menuService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Menu'
        ];
        $template = 'backend.menu.menu.index';
        $config['seo']  = __('messages.menu');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menus'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'menu.create');
        $menuCatalogues = $this->menuCatalogueRepository->all();
        $config = $this->configData();
        $template = 'backend.menu.menu.store';
        $config['seo']  = __('messages.menu');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogues'
        ));
    }

    public function store(StoreMenuRequest $request){
        if($this->menuService->create($request)){

            return redirect()->route('menu.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'menu.update');
        $menu = $this->menuRepository->findById($id);
        
        $config = $this->configData();
        $template = 'backend.menu.menu.store';
        $config['seo']  = __('messages.menu');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menu'
        ));
    }

    public function update(UpdateMenuRequest $request, $id) {
        if($this->menuService->update($request, $id)){

            return redirect()->route('menu.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'menu.destroy');
        $menu = $this->menuRepository->findById($id);
        $template = 'backend.menu.menu.delete';
        $config['seo']  = __('messages.menu');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menu'
        ));
    }

    public function destroy($id) {
        if($this->menuService->destroy($id)){

            return redirect()->route('menu.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/menu.js',
            ],
        ];
    }
}
