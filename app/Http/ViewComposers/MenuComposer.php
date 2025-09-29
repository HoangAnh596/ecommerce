<?php

namespace App\Http\ViewComposers;

use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use Illuminate\View\View;

class MenuComposer
{
    protected $menuCatalogueRepository;
    protected $language;

    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        $language
    ) {
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $agrument = $this->agrument($this->language);
        $menuCatalogue = $this->menuCatalogueRepository->findByCondition(...$agrument);

        $menus = [];
        $htmlType = ['main-menu'];
        if(count($menuCatalogue)) {
            foreach($menuCatalogue as $key => $val) {
                $type = (in_array($val->keyword, $htmlType)) ? 'html' : 'array';
                $menus[$val->keyword] = frontend_recursive_menu(recursive($val->menus), 1, $type);
            }
        }
// dd($menus['footer-menu']);
        $view->with('menus', $menus);
    }

    private function agrument($language)
    {
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,
            'relation' => [
                'menus' => function ($query) use ($language) {
                    $query->orderBy('order', 'DESC');
                    $query->with([
                        'languages' => function ($query) use ($language) {
                            $query->where('language_id', $language);
                        }
                    ]);
                }
            ]
        ];
    }
}
