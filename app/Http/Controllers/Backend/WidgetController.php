<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWidgetRequest;
use App\Http\Requests\UpdateWidgetRequest;
use App\Services\Interfaces\WidgetServiceInterface as WidgetService;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Http\Request;
use App\Models\Language;

class WidgetController extends Controller
{
    protected $widgetService;
    protected $widgetRepository;
    protected $languageRepository;
    protected $language;

    public function __construct(
        WidgetService $widgetService,
        WidgetRepository $widgetRepository,
        LanguageRepository $languageRepository,
    ) {
        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
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
        $this->authorize('modules', 'widget.index');
        $widgets = $this->widgetService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Widget'
        ];
        $template = 'backend.widget.index';
        $config['seo']  = __('messages.widget');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widgets'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'widget.create');
        $config = $this->configData();
        $template = 'backend.widget.store';
        $config['seo']  = __('messages.widget');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreWidgetRequest $request)
    {
        if ($this->widgetService->create($request, $this->language)) {

            return redirect()->route('widget.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'widget.update');
        $widget = $this->widgetRepository->findById($id);
        $widget->description = $widget->description[$this->language];
        $modelClass = loadClassInterface($widget->model);
        $widgetItem = convertArrayByKey($modelClass->findByCondition(
            ...array_values($this->modelItemAgrument($widget->model_id))
        ), ['id', 'name.languages', 'image']);

        $config = $this->configData();
        $template = 'backend.widget.store';
        $config['seo']  = __('messages.widget');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'widgetItem'
        ));
    }

    public function update(UpdateWidgetRequest $request, $id)
    {
        if ($this->widgetService->update($request, $id, $this->language)) {

            return redirect()->route('widget.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'widget.destroy');
        $widget = $this->widgetRepository->findById($id);
        $template = 'backend.widget.delete';
        $config['seo']  = __('messages.widget');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget'
        ));
    }

    public function destroy($id)
    {
        if ($this->widgetService->destroy($id)) {

            return redirect()->route('widget.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function translate($languageId, $widgetId)
    {
        $this->authorize('modules', 'widget.translate');
        $widget = $this->widgetRepository->findById($widgetId);
        $widget->jsonDescription = $widget->description;
        $widget->description = $widget->description[$this->language];

        $widgetTranslate = new \stdClass();
        $widgetTranslate->description = ($widget->jsonDescription[$languageId]) ?? '';

        $translate = $this->languageRepository->findById($languageId);
        $config = $this->configData();
        $template = 'backend.widget.translate';
        $config['seo']  = __('messages.widget');
        $config['method'] = 'translate';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'translate',
            'widgetTranslate'
        ));
    }

    public function saveTranslate(Request $request)
    {
        if ($this->widgetService->saveTranslate($request, $this->language)) {

            return redirect()->route('widget.index')->with('success', 'Tạo bản dịch thành công');
        }
        return redirect()->route('widget.index')->with('errors', 'Tạo bản dịch không thành công. Hãy thử lại');
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
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
            ],
        ];
    }
}
