<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\storeMenuChildrenRequest;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Services\Interfaces\MenuServiceInterface as MenuService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Services\Interfaces\MenuCatalogueServiceInterface as MenuCatalogueService;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface  as MenuCatalogueRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Http\Request;
use App\Models\Language;

class MenuController extends Controller
{
    protected $menuService;
    protected $menuRepository;
    protected $menuCatalogueService;
    protected $menuCatalogueRepository;
    protected $languageRepository;
    protected $language;

    public function __construct(
        MenuService $menuService,
        MenuRepository $menuRepository,
        MenuCatalogueService $menuCatalogueService,
        MenuCatalogueRepository $menuCatalogueRepository,
        LanguageRepository $languageRepository,
    ){
        $this->menuService = $menuService;
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueService = $menuCatalogueService;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->languageRepository = $languageRepository;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            
            return $next($request);
        });
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'menu.index');
        $menuCatalogues = $this->menuCatalogueService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'MenuCatalogue',
        ];
        $template = 'backend.menu.menu.index';
        $config['seo']  = __('messages.menu');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogues'
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
        if($this->menuService->save($request, $this->language)){
            $menuCatalogueId = $request->input('menu_catalogue_id');

            return redirect()->route('menu.edit', ['id' => $menuCatalogueId])->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'menu.update');
        $language = $this->language;

        $menus = $this->menuRepository->findByCondition([
            ['menu_catalogue_id', '=', $id]
        ], TRUE, [
            'languages' => function($query) use ($language) {
                $query->where('language_id', $language);
            }
        ], ['order', 'DESC']);

        $menuCatalogue = $this->menuCatalogueRepository->findById($id);
        $config = $this->configData();
        $template = 'backend.menu.menu.show';
        $config['seo']  = __('messages.menu');
        $config['method'] = 'show';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menus',
            'id',
            'menuCatalogue'
        ));
    }

    public function editMenu($id)
    {
        $this->authorize('modules', 'menu.update');
        $language = $this->language;

        $menus = $this->menuRepository->findByCondition([
            ['menu_catalogue_id', '=', $id],
            ['parent_id', '=', 0]
        ], TRUE, [
            'languages' => function($query) use ($language) {
                $query->where('language_id', $language);
            }
        ], ['order', 'DESC']);
        $menuList = $this->menuService->convertMenu($menus);
        $menuCatalogues = $this->menuCatalogueRepository->all();
        $menuCatalogue = $this->menuCatalogueRepository->findById($id);

        $config = $this->configData();
        $template = 'backend.menu.menu.store';
        $config['seo']  = __('messages.menu');
        $config['method'] = 'children';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuList',
            'menuCatalogues',
            'menuCatalogue',
            'id'
        ));
    }

    public function delete($id) {
        $this->authorize('modules', 'menu.destroy');
        $menuCatalogue = $this->menuCatalogueRepository->findById($id);
        $template = 'backend.menu.menu.delete';
        $config['seo']  = __('messages.menu');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogue'
        ));
    }

    public function destroy($id) {
        if($this->menuService->destroy($id)){
            return redirect()->route('menu.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function children($id) {
        $this->authorize('modules', 'menu.create');
        $language = $this->language;
        $menu = $this->menuRepository->findById($id, ['*'], [
            'languages' => function($query) use ($language) {
                $query->where('language_id', $language);
            }
        ]);
        
        $menuList = $this->menuService->getAndConvertMenu($menu, $language);
        $config = $this->configData();
        $template = 'backend.menu.menu.children';
        $config['seo']  = __('messages.menu');
        $config['method'] = 'children';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menu',
            'menuList'
        ));
    }

    public function saveChildren(storeMenuChildrenRequest $request, $id) {
        $menu= $this->menuRepository->findById($id);
        if($this->menuService->saveChildren($request, $this->language, $menu)){

            return redirect()->route('menu.edit', ['id' => $menu->menu_catalogue_id])->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('menu.edit', ['id' => $menu->menu_catalogue_id])->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function translate(int $languageId = 1, int $id = 0) {
        $language = $this->languageRepository->findById($languageId);
        $menuCatalogue = $this->menuCatalogueRepository->findById($id);
        $currentLanguage = $this->language;
        $menus = $this->menuRepository->findByCondition([
            ['menu_catalogue_id', '=', $id],
        ], TRUE, [
            'languages' => function($query) use ($currentLanguage) {
                $query->where('language_id', $currentLanguage);
            }
        ], ['lft', 'ASC']);
        //video 73
        $menus = biuldMenu($this->menuService->findMenuItemTranslate($menus, $currentLanguage, $languageId));

        //

        $config = $this->configData();
        $template = 'backend.menu.menu.translate';
        $config['seo']  = __('messages.menu');
        $config['method'] = 'translate';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'language',
            'menuCatalogue',
            'menus'
        ));
    }

    private function configData() {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/menu.js',
                'backend/js/plugins/nestable/jquery.nestable.js'
            ],
        ];
    }
}
