<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\SlideEnum;
use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use Illuminate\Http\Request;

class HomeController extends FrontendController
{
    protected $language;
    protected $slideRepository;
    protected $slideService;
    protected $widgetService;

    public function __construct(
        SlideRepository $slideRepository,
        SlideService $slideService,
        WidgetService $widgetService,
    ) {
        parent::__construct();
        $this->slideRepository = $slideRepository;
        $this->slideService = $slideService;
        $this->widgetService = $widgetService;
    }

    public function index()
    {
        $language = $this->language;
        $slides = $this->slideService->getSlide([SlideEnum::BANNER, SlideEnum::MAIN_SLIDE], $language);

        $widgets = $this->widgetService->getWidget([
            [ 'keyword' => 'category-home', 'children' => true, 'promotion' => true, 'object' => true],
            [ 'keyword' => 'category', 'children' => true, 'countObject' => true ],
            // [ 'keyword' => 'news' ],
            [ 'keyword' => 'category-highlight'],
            [ 'keyword' => 'product-bestseller',],
        ], $language);

        $system = $this->system;

        return view('frontend.homepage.home.index', compact(
            'language',
            'slides',
            'widgets',
            'system',
        ));
    }
}
