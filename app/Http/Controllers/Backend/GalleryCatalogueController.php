<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryCatalogueRequest;
use App\Http\Requests\UpdateGalleryCatalogueRequest;
use App\Repositories\Interfaces\GalleryCatalogueRepositoryInterface as GalleryCatalogueRepository;
use App\Services\Interfaces\GalleryCatalogueServiceInterface as GalleryCatalogueService;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Http\Requests\DeleteGalleryCatalogueRequest;
use App\Models\Language;

class GalleryCatalogueController extends Controller
{
    protected $galleryCatalogueService;
    protected $galleryCatalogueRepository;
    protected $nestedset;
    protected $language;

    public function __construct(
        GalleryCatalogueService $galleryCatalogueService,
        GalleryCatalogueRepository $galleryCatalogueRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->galleryCatalogueService = $galleryCatalogueService;
        $this->galleryCatalogueRepository = $galleryCatalogueRepository;
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'gallery_catalogues',
            'foreignkey' => 'gallery_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'gallery.catalogue.index');
        $galleryCatalogues = $this->galleryCatalogueService->paginate($request, $this->language);
        
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'GalleryCatalogue'
        ];
        $template = 'backend.gallery.catalogue.index';
        $config['seo']  = __('messages.galleryCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'galleryCatalogues'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'gallery.catalogue.create');
        $config = $this->configData();
        $template = 'backend.gallery.catalogue.store';
        $config['seo']  = __('messages.galleryCatalogue');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StoreGalleryCatalogueRequest $request){
        if($this->galleryCatalogueService->create($request, $this->language)){

            return redirect()->route('gallery.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('gallery.catalogue.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'gallery.catalogue.update');
        $config = $this->configData();
        $galleryCatalogue = $this->galleryCatalogueRepository->getGalleryCatalogueById($id, $this->language);
        $template = 'backend.gallery.catalogue.store';
        $config['seo']  = __('messages.galleryCatalogue');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode($galleryCatalogue->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'galleryCatalogue',
            'dropdown',
            'album'
        ));
    }

    public function update(UpdateGalleryCatalogueRequest $request, $id) {
        if($this->galleryCatalogueService->update($request, $id, $this->language)){

            return redirect()->route('gallery.catalogue.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('gallery.catalogue.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'gallery.catalogue.destroy');
        $galleryCatalogue = $this->galleryCatalogueRepository->getGalleryCatalogueById($id, $this->language);
        $template = 'backend.gallery.catalogue.delete';
        $config['seo']  = __('messages.galleryCatalogue');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'galleryCatalogue'
        ));
    }

    public function destroy(DeleteGalleryCatalogueRequest $request, $id) {
        if($this->galleryCatalogueService->destroy($id, $this->language)){

            return redirect()->route('gallery.catalogue.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('gallery.catalogue.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData() {
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
        ];
    }
}
