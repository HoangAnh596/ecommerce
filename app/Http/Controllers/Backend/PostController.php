<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Services\Interfaces\PostServiceInterface as PostService;
use Illuminate\Http\Request;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class PostController extends Controller
{
    protected $postService;
    protected $postRepository;
    protected $nestedset;
    protected $language;

    public function __construct(
        PostService $postService,
        PostRepository $postRepository
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale();
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->initialize();
    }

    private function initialize() {
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => 1,
        ]);
    }
    
    public function index(Request $request)
    {
        $this->authorize('modules', 'post.index');
        $posts = $this->postService->paginate($request, $this->language);       
        $config = [
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'model' => 'Post'
        ];
        $template = 'backend.post.post.index';
        $config['seo']  = __('messages.post');
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'posts',
            'dropdown'
        ));
    }

    public function create()
    {
        $this->authorize('modules', 'post.create');
        $config = $this->configData();
        $template = 'backend.post.post.store';
        $config['seo']  = __('messages.post');
        $config['method'] = 'create';
        $dropdown = $this->nestedset->Dropdown();

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown'
        ));
    }

    public function store(StorePostRequest $request){
        if($this->postService->create($request, $this->language)){

            return redirect()->route('post.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('post.index')->with('errors', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id) {
        $this->authorize('modules', 'post.update');
        $config = $this->configData();
        $post = $this->postRepository->getPostById($id, $this->language);
        $template = 'backend.post.post.store';
        $config['seo']  = __('messages.post');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode($post->album);

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'post',
            'dropdown',
            'album'
        ));
    }

    public function update(UpdatePostRequest $request, $id) {
        if($this->postService->update($request, $id, $this->language)){

            return redirect()->route('post.index')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('post.index')->with('errors', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id) {
        $this->authorize('modules', 'post.destroy');
        $post = $this->postRepository->getPostById($id, $this->language);
        $template = 'backend.post.post.delete';
        $config['seo']  = __('messages.post');

        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'post'
        ));
    }

    public function destroy($id) {
        if($this->postService->destroy($id)){

            return redirect()->route('post.index')->with('success', 'Xóa bản ghi thành công');
        }
        return redirect()->route('post.index')->with('errors', 'Xóa bản ghi không thành công. Hãy thử lại');
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
