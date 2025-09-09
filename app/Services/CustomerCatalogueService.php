<?php

namespace App\Services;

use App\Services\Interfaces\CustomerCatalogueServiceInterface;
use App\Repositories\Interfaces\CustomerCatalogueRepositoryInterface as CustomerCatalogueRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class CustomerCatalogueService
 * @package App\Services
 */
class CustomerCatalogueService extends BaseService implements CustomerCatalogueServiceInterface
{
    protected $customerCatalogueRepository;

    public function __construct(CustomerCatalogueRepository $customerCatalogueRepository)
    {
        $this->customerCatalogueRepository = $customerCatalogueRepository;
    }

    public function paginate($request){
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];
        $customerCatalogues = $this->customerCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'customer/catalogue/index'],
            ['id', 'DESC'],
            [],
            ['customers']
        );

        return $customerCatalogues;
    }

    public function create($request) {
        DB::beginTransaction();
        try {
            $payload = $request->except('_token', 'send');
            $this->customerCatalogueRepository->create($payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($request, $id){
        DB::beginTransaction();
        try {
            $payload = $request->except('_token', 'send');
            $this->customerCatalogueRepository->update($id, $payload);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $this->customerCatalogueRepository->delete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function paginateSelect() {
        return ['id', 'name', 'description', 'publish'];
    }
}
