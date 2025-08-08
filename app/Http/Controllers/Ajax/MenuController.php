<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Repositories\Interfaces\MenuRepositoryInterface  as MenuRepository;
use App\Services\Interfaces\MenuCatalogueServiceInterface  as MenuCatalogueService;
use App\Models\Language;

class MenuController extends Controller
{
    protected $menuRepository;
    protected $menuCatalogueService;
    protected $language;

    public function __construct(
        MenuRepository $menuRepository,
        MenuCatalogueService $menuCatalogueService
    ){
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueService = $menuCatalogueService;
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
}
