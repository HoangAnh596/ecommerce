<?php

namespace App\Services;

use App\Services\Interfaces\CustomerServiceInterface;
use App\Repositories\Interfaces\CustomerRepositoryInterface as CustomerRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerService extends BaseService implements CustomerServiceInterface
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function paginate($request){
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];
        $customers = $this->customerRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'customer/index'],
            ['id', 'DESC'],
            [],
            []
        );

        return $customers;
    }

    public function create($request) {
        DB::beginTransaction();
        try {
            $payload = $request->except('_token', 'send', 're_password');
            if($payload['birthday'] != null) {
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }
            $payload['password'] = Hash::make($payload['password']);

            $this->customerRepository->create($payload);

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
            if($payload['birthday'] != null) {
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }
            $this->customerRepository->update($id, $payload);

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
            $this->customerRepository->delete($id);

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

    private function convertBirthdayDate($birthday = '') {
        $carbonDate  = Carbon::createFromFormat('Y-m-d', $birthday);
        $birthday = $carbonDate->format('Y-m-d H:i:s');
        
        return $birthday;
    }

    private function paginateSelect() {
        return ['id', 'name', 'email', 'phone', 'address', 'image', 'publish', 'customer_catalogue_id', 'source_id'];
    }
}
