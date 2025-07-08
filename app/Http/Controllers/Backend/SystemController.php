<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\System;
use App\Services\Interfaces\SystemServiceInterface as SystemService;
use App\Repositories\Interfaces\SystemRepositoryInterface as SystemRepository;
use App\Models\Language;


class SystemController extends Controller
{
    protected $systemLibrary;
    protected $systemService;
    protected $systemRepository;
    protected $language;

    public function __construct(
        System $systemLibrary,
        SystemService $systemService,
        SystemRepository $systemRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        $this->systemLibrary = $systemLibrary;
        $this->systemService = $systemService;
        $this->systemRepository = $systemRepository;
    }
    /**
     * Display the system configuration page.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $systemConfig = $this->systemLibrary->config();
        // $systems = convert_array($this->systemRepository->all(), 'keyword', 'content');
        $systems = convert_array($this->systemRepository->findByCondition(
            [
                ['language_id', '=', $this->language],   
            ], TRUE
        ), 'keyword', 'content');

        $config = $this->config();
        $config['seo']  = __('messages.system');
        $template = 'backend.system.index';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'systemConfig',
            'systems'
        ));
    }

    /**
     * Store the system configuration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        if($this->systemService->save($request, $this->language)){

            return redirect()->route('system.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('system.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    /**
     * Translate system configuration.
     *
     * @return \Illuminate\Http\Response
     */
    public function translate($languageId = 0){
        $systemConfig = $this->systemLibrary->config();
        $systems = convert_array($this->systemRepository->findByCondition(
            [
                ['language_id', '=', $languageId],   
            ], TRUE
        ), 'keyword', 'content');
        $config = $this->config();
        $config['seo']  = __('messages.system');
        $config['method']  = 'translate';
        $template = 'backend.system.index';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'languageId',
            'systemConfig',
            'systems'
        ));
    }

    public function saveTranslate(Request $request, $languageId = 0){
        if($this->systemService->save($request, $languageId)){

            return redirect()->route('system.translate', ['languageId' => $languageId])->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('system.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');

    }

    private function config()
    {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js'
            ],
        ];
    }
}
