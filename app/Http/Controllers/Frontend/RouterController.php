<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Http\Request;

class RouterController extends FrontendController
{
    protected $routerRepository;
    protected $router;

    public function __construct(
        RouterRepository $routerRepository,
    ) {
        parent::__construct();
        $this->routerRepository = $routerRepository;
    }

    public function index(string $canonical = '', Request $request)
    {
        $router = $this->getRouter($canonical);

        if (!is_null($router) && !empty($router)) {
            // dd(666);
            $method = 'index';
            echo app($router->controllers)->{$method}($router->module_id, $request);
        }
    }

    public function page(string $canonical = '', $page, Request $request)
    {
        $page = (!isset($page)) ? 1 : $page;
        $router = $this->getRouter($canonical);

        if (!is_null($router) && !empty($router)) {
            $method = 'index';
            echo app($router->controllers)->{$method}($router->module_id, $request, $page);
        }
    }

    private function getRouter($canonical)
    {
        return $this->routerRepository->findByCondition(
            [
                ['canonical', '=', $canonical],
                ['language_id', '=', $this->language]
            ]
        );
    }
}
