<?php

namespace App\Http\Controllers\Backend\Promotion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Source\StoreSourceRequest;
use App\Http\Requests\Source\UpdateSourceRequest;
use App\Services\Interfaces\SourceServiceInterface as SourceService;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use Illuminate\Http\Request;
use App\Models\Language;

class SourceController extends Controller
{
    protected $sourceService;
    protected $sourceRepository;
    protected $languageRepository;
    protected $language;

    public function __construct(
        SourceService $sourceService,
        SourceRepository $sourceRepository,
        LanguageRepository $languageRepository,
    ) {
        $this->sourceService = $sourceService;
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
        $this->authorize('modules', 'source.index');
        $sources = $this->sourceService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Source'
        ];
        $template = 'backend.source.index';
        $config['seo']  = __('messages.source');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'sources'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'source.create');
        $config = $this->configData();
        $template = 'backend.source.store';
        $config['seo']  = __('messages.source');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSourceRequest $request)
    {
        if ($this->sourceService->create($request, $this->language)) {

            return redirect()->route('source.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('source.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'source.update');
        $source = $this->sourceRepository->findById($id);

        $config = $this->configData();
        $template = 'backend.source.store';
        $config['seo']  = __('messages.source');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'source'
        ));
    }

    public function update(UpdateSourceRequest $request, $id)
    {
        if ($this->sourceService->update($request, $id, $this->language)) {

            return redirect()->route('source.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('source.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'source.destroy');
        $source = $this->sourceRepository->findById($id);
        $template = 'backend.source.delete';
        $config['seo']  = __('messages.source');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'source'
        ));
    }

    public function destroy($id)
    {
        if ($this->sourceService->destroy($id)) {

            return redirect()->route('source.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('source.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/library/source.js',
            ],
        ];
    }
}
