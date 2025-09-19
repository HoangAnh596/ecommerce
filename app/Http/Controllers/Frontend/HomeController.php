<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use Illuminate\Http\Request;

class HomeController extends FrontendController
{
    protected $language;
    protected $slideRepository;
    protected $widgetService;

    public function __construct(
        SlideRepository $slideRepository,
        WidgetService $widgetService,
    ) {
        parent::__construct();
        $this->slideRepository = $slideRepository;
        $this->widgetService = $widgetService;
    }

    public function index()
    {
        $slides = $this->slideRepository->findByCondition(...$this->slideAgrument());
        $slides->slideItems = $slides->item[$this->language];

        $widget = [
            // 'category' => $this->widgetService->findWidgetByKeyword('category', $this->language, ['children' => true]),
            'news' => $this->widgetService->findWidgetByKeyword('news', $this->language),
        ];

        return view('frontend.homepage.home.index', compact(
            'slides'
        ));
    }

    private function slideAgrument()
    {
        return [
            'condition' => [
                config('apps.general.defaultPublish'),
                ['keyword', '=', 'main-slide'],
            ]
        ];
    }
}
