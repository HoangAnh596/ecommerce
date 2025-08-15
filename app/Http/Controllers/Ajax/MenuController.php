<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Repositories\Interfaces\MenuRepositoryInterface  as MenuRepository;
use App\Services\Interfaces\MenuServiceInterface  as MenuService;
use App\Services\Interfaces\MenuCatalogueServiceInterface  as MenuCatalogueService;
use App\Models\Language;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuRepository;
    protected $menuService;
    protected $menuCatalogueService;
    protected $language;

    public function __construct(
        MenuRepository $menuRepository,
        MenuService $menuService,
        MenuCatalogueService $menuCatalogueService
    ){
        $this->menuRepository = $menuRepository;
        $this->menuService = $menuService;
        $this->menuCatalogueService = $menuCatalogueService;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function createCatalogue(StoreMenuCatalogueRequest $request)
    {
        $menuCatalogue = $this->menuCatalogueService->create($request);
        if($menuCatalogue !== false){

            return response()->json([
                'code' => 0,
                'message' => 'Thêm mới nhóm menu thành công',
                'data' => $menuCatalogue,
            ]);
        }

        return response()->json([
            'code' => 1,
            'message' => 'Có vấn đề xảy ra, Hãy thử lại',
        ]);
    }

    public function drag(Request $request)
    {
        $json = json_decode($request->input('json'), true);
        $menuCatalogueId = $request->integer('menu_catalogue_id');
        $flag = $this->menuService->drapUpdate($json, $menuCatalogueId, $this->language);
    }
}
