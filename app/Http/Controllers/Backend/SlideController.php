<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideRequest;
use App\Http\Requests\UpdateSlideRequest;
use App\Services\Interfaces\SlideServiceInterface as SlideService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use Illuminate\Http\Request;
use App\Models\Language;

class SlideController extends Controller
{
    protected $slideService;
    protected $slideRepository;
    protected $provinceRepository;
    protected $language;

    public function __construct(
        SlideService $slideService,
        ProvinceRepository $provinceRepository,
        SlideRepository $slideRepository,
    ) {
        $this->slideService = $slideService;
        $this->provinceRepository = $provinceRepository;
        $this->slideRepository = $slideRepository;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'slide.index');
        $slides = $this->slideService->paginate($request);
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Slide'
        ];
        $template = 'backend.slide.index';
        $config['seo']  = __('messages.slide');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slides'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'slide.create');
        $config = $this->configData();
        $template = 'backend.slide.store';
        $config['seo']  = __('messages.slide');
        $config['method'] = 'create';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSlideRequest $request)
    {
        if ($this->slideService->create($request, $this->language)) {

            return redirect()->route('slide.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'slide.update');
        $slide = $this->slideRepository->findById($id);
        $slideItem = convertArrayByKey(
            $slide->item[$this->language],
            ['image', 'description', 'window', 'canonical', 'name', 'alt']
        );

        $config = $this->configData();
        $template = 'backend.slide.store';
        $config['seo']  = __('messages.slide');
        $config['method'] = 'edit';

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slide',
            'slideItem'
        ));
    }

    public function update(UpdateSlideRequest $request, $id)
    {
        if ($this->slideService->update($request, $id, $this->language)) {

            return redirect()->route('slide.edit', ['id' => $id])->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'slide.destroy');
        $slide = $this->slideRepository->findById($id);
        $template = 'backend.slide.delete';
        $config['seo']  = __('messages.slide');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slide'
        ));
    }

    public function destroy($id)
    {
        if ($this->slideService->destroy($id)) {

            return redirect()->route('slide.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/slide.js',
            ],
        ];
    }
}
