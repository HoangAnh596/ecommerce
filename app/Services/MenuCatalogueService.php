<?php

namespace App\Services;

use App\Services\Interfaces\MenuCatalogueServiceInterface;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class MenuCatalogueService
 * @package App\Services
 */
class MenuCatalogueService extends BaseService implements MenuCatalogueServiceInterface
{
    protected $menuCatalogueRepository;

    public function __construct(MenuCatalogueRepository $menuCatalogueRepository)
    {
        $this->menuCatalogueRepository = $menuCatalogueRepository;
    }

    public function paginate($request){
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];
        $menuCatalogues = $this->menuCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'menu/index'],
            ['id', 'DESC']
        );

        return $menuCatalogues;
    }

    public function create($request) {
        DB::beginTransaction();
        try {
            $payload = $request->only(['name', 'keyword']);
            $payload['keyword'] = Str::slug($payload['keyword']);
            $payload['publish'] = config('apps.general.public');
            $menuCatalogue = $this->menuCatalogueRepository->create($payload);

            DB::commit();
            return [
                'name' => $menuCatalogue->name,
                'id' => $menuCatalogue->id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function paginateSelect() {
        return ['id', 'name', 'keyword', 'publish'];
    }
}
