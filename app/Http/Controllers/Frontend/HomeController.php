<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\SlideEnum;
use App\Http\Controllers\FrontendController;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Services\Interfaces\SlideServiceInterface as SlideService;

class HomeController extends FrontendController
{
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

        // SEO and System
        $system = $this->system;
        $seo = [
            'meta_title' => $system['seo_meta_title'],
            'meta_keyword' => $system['seo_meta_keyword'],
            'meta_description' => $system['seo_meta_description'],
            'meta_images' => $system['seo_meta_images'],
            'canonical' => config('app.url')
        ];

        return view('frontend.homepage.home.index', compact(
            'language',
            'slides',
            'widgets',
            'system',
            'seo'
        ));
    }
}
