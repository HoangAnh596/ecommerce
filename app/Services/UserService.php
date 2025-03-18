<?php

namespace App\Services;

use App\Services\Interfaces\UserServiceInterface;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class UserService
 * @package App\Services
 */
class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function paginate($request){
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perpage = $request->integer('perpage');
        $users = $this->userRepository->pagination($this->paginateSelect(), $condition, [], ['path' => 'user/index'], $perpage);

        return $users;
    }

    public function create($request) {
        DB::beginTransaction();
        try {
            $payload = $request->except('_token', 'send', 're_password');
            if($payload['birthday'] != null) {
                $payload['birthday'] = $this->convertBirthdayDate($payload['birthday']);
            }
            $payload['password'] = Hash::make($payload['password']);
            
            $this->userRepository->create($payload);

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
            $this->userRepository->update($id, $payload);

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
            $this->userRepository->delete($id);

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

    public function updateStatus($post = []) {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = ($post['value'] == 1) ? 2 : 1;
            $this->userRepository->update($post['modelId'], $payload);

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

    public function updateStatusAll($post) {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];
            $this->userRepository->updateByWhereIn('id', $post['id'], $payload);

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
        return ['id', 'name', 'email', 'phone', 'address', 'image', 'publish', 'user_catalogue_id'];
    }
}
