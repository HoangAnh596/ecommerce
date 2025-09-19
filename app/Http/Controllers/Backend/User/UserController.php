<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\Interfaces\UserServiceInterface as UserService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $userRepository;
    protected $provinceRepository;

    public function __construct(
        UserService $userService,
        ProvinceRepository $provinceRepository,
        UserRepository $userRepository,
    ) {
        $this->userService = $userService;
        $this->provinceRepository = $provinceRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'user.index');
        $users = $this->userService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'User'
        ];
        $template = 'backend.user.user.index';
        $config['seo']  = config('apps.user');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'users'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'user.create');
        $provinces = $this->provinceRepository->all();
        $config = $this->configData();
        $template = 'backend.user.user.store';
        $config['seo']  = config('apps.user');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces'
        ));
    }

    public function store(StoreUserRequest $request)
    {
        if ($this->userService->create($request)) {

            return redirect()->route('user.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('user.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'user.update');
        $user = $this->userRepository->findById($id);
        $provinces = $this->provinceRepository->all();

        $config = $this->configData();
        $template = 'backend.user.user.store';
        $config['seo']  = config('apps.user');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces',
            'user'
        ));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        if ($this->userService->update($request, $id)) {

            return redirect()->route('user.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('user.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'user.destroy');
        $user = $this->userRepository->findById($id);
        $template = 'backend.user.user.delete';
        $config['seo']  = config('apps.user');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'user'
        ));
    }

    public function destroy($id)
    {
        if ($this->userService->destroy($id)) {

            return redirect()->route('user.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('user.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js'
            ],
        ];
    }
}
