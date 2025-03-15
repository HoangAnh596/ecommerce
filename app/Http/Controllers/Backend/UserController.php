<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Services\Interfaces\UserServiceInterface as UserService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;

class UserController extends Controller
{
    protected $userService;
    protected $provinceRepository;

    public function __construct(
        UserService $userService,
        ProvinceRepository $provinceRepository,
    ){
        $this->userService = $userService;
        $this->provinceRepository = $provinceRepository;
    }
    
    public function index()
    {
        $users = $this->userService->paginate();
        
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css'
            ],
        ];
        $template = 'backend.user.index';
        $config['seo']  = config('apps.user');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'users'
        ));
    }

    public function store(StoreUserRequest $request){
        dd($request->all());
    }

    public function create()
    {
        $provinces = $this->provinceRepository->all();
        
        $config = [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugin/ckfinder/ckfinder.js',
                'backend/library/location.js',
                'backend/library/ckfinder.js',
            ],
        ];
        $template = 'backend.user.create';
        $config['seo']  = config('apps.user');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'provinces'
        ));
    }
}
