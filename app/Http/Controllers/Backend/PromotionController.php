<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Requests\Promotion\UpdatePromotionRequest;
use App\Services\Interfaces\PromotionServiceInterface as PromotionService;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use Illuminate\Http\Request;
use App\Models\Language;

class PromotionController extends Controller
{
    protected $promotionService;
    protected $promotionRepository;
    protected $sourceRepository;
    protected $languageRepository;
    protected $language;

    public function __construct(
        PromotionService $promotionService,
        PromotionRepository $promotionRepository,
        SourceRepository $sourceRepository,
        LanguageRepository $languageRepository,
    ) {
        $this->promotionService = $promotionService;
        $this->promotionRepository = $promotionRepository;
        $this->sourceRepository = $sourceRepository;
        $this->languageRepository = $languageRepository;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'promotion.index');
        $promotions = $this->promotionService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Promotion'
        ];
        $template = 'backend.promotion.promotion.index';
        $config['seo']  = __('messages.promotion');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotions'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'promotion.create');
        $sources = $this->sourceRepository->all();

        $config = $this->configData();
        $template = 'backend.promotion.promotion.store';
        $config['seo']  = __('messages.promotion');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'sources'
        ));
    }

    public function store(StorePromotionRequest $request)
    {
        if ($this->promotionService->create($request, $this->language)) {

            return redirect()->route('promotion.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'promotion.update');
        $promotion = $this->promotionRepository->findById($id);
        $promotion->description = $promotion->description[$this->language];
        $modelClass = loadClassInterface($promotion->model);
        $promotionItem = convertArrayByKey($modelClass->findByCondition(
            ...array_values($this->modelItemAgrument($promotion->model_id))
        ), ['id', 'name.languages', 'image']);

        $config = $this->configData();
        $template = 'backend.promotion.promotion.store';
        $config['seo']  = __('messages.promotion');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion',
            'promotionItem'
        ));
    }

    public function update(UpdatePromotionRequest $request, $id)
    {
        if ($this->promotionService->update($request, $id, $this->language)) {

            return redirect()->route('promotion.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'promotion.destroy');
        $promotion = $this->promotionRepository->findById($id);
        $template = 'backend.promotion.promotion.delete';
        $config['seo']  = __('messages.promotion');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion'
        ));
    }

    public function destroy($id)
    {
        if ($this->promotionService->destroy($id)) {

            return redirect()->route('promotion.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function translate($languageId, $promotionId)
    {
        $this->authorize('modules', 'promotion.translate');
        $promotion = $this->promotionRepository->findById($promotionId);
        $promotion->jsonDescription = $promotion->description;
        $promotion->description = $promotion->description[$this->language];

        $promotionTranslate = new \stdClass();
        $promotionTranslate->description = ($promotion->jsonDescription[$languageId]) ?? '';

        $translate = $this->languageRepository->findById($languageId);
        $config = $this->configData();
        $template = 'backend.promotion.promotion.translate';
        $config['seo']  = __('messages.promotion');
        $config['method'] = 'translate';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion',
            'translate',
            'promotionTranslate'
        ));
    }

    public function saveTranslate(Request $request)
    {
        if ($this->promotionService->saveTranslate($request, $this->language)) {

            return redirect()->route('promotion.index')->with('success', 'Tạo bản dịch thành công');
        }
        return redirect()->route('promotion.index')->with('errors', 'Tạo bản dịch không thành công. Hãy thử lại');
    }

    private function modelItemAgrument(array $whereIn = [])
    {
        $language = $this->language;
        return [
            'condition' => [],
            'flag' => true,
            'relation' => [
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ],
            'orderBy' => ['id', 'desc'],
            'params' => [
                'whereIn' => $whereIn,
                'whereInField' => 'id'
            ]
        ];
    }

    private function configData()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.full.min.js',
                'backend/library/promotion.js',
            ],
        ];
    }
}
