<?php

namespace App\Services;

use App\Services\Interfaces\PostServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class postService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{
    protected $postRepository;
    protected $routerRepository;
    protected $controllerName;

    public function __construct(
        PostRepository $postRepository,
        RouterRepository $routerRepository,
    ){
        $this->postRepository = $postRepository;
        $this->routerRepository = $routerRepository;
        $this->controllerName = 'PostController';
    }

    public function paginate($request, $languageId){
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        $posts = $this->postRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'post.index', 'groupBy' => $this->paginateSelect()],
            ['posts.id','ASC'],
            [
                ['post_language as tb2', 'tb2.post_id', '=', 'posts.id'],
                ['post_catalogue_post as tb3', 'posts.id', '=', 'tb3.post_id']
            ],
            ['post_catalogues'],
            $this->whereRaw($request, $languageId)
        );

        return $posts;
    }

    public function create($request, $languageId) {
        DB::beginTransaction();
        try {
            $post = $this->createPost($request);
            if($post->id > 0) {
                $this->updateLanguageForPost($post, $request, $languageId);
                $this->createRouter($post, $request, $this->controllerName, $languageId); 
                $post->post_catalogues()->sync($this->catalogue($request));
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($request, $id, $languageId){
        DB::beginTransaction();
        try {
            $post = $this->postRepository->findById($id);
            if($this->uploadPost($post, $request)){
                $this->updateLanguageForPost($post, $request, $languageId);
                $this->updateRouter($post, $request, $this->controllerName, $languageId);
                $post->post_catalogues()->sync($this->catalogue($request));
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id) {
        DB::beginTransaction();
        try {
            $this->postRepository->delete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    private function paginateSelect() {
        return [
            'posts.id',
            'posts.image',
            'posts.publish',
            'posts.order',
            'tb2.name',
            'tb2.canonical'
        ];
    }

    private function whereRaw($request, $languageId){
        $rawCondition = [];
        if($request->integer('post_catalogue_id') > 0){
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.post_catalogue_id IN (
                        SELECT id
                        FROM post_catalogues
                        JOIN post_catalogue_language ON post_catalogues.id = post_catalogue_language.post_catalogue_id
                        WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
                        AND post_catalogue_language.language_id = '.$languageId.'
                    )',
                    [$request->integer('post_catalogue_id'), $request->integer('post_catalogue_id')]
                ]
            ];
            
        }
        return $rawCondition;
    }

    private function createPost($request) {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);

        return $this->postRepository->create($payload);
    }

    private function uploadPost($post, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);

        return $this->postRepository->update($post->id, $payload);
    }

    private function updateLanguageForPost($post, $request, $languageId) {
        $payload = $request->only($this->payloadPost());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['post_id'] = $post->id;
        $post->languages()->detach([$languageId, $post->id]);

        return $this->postRepository->createPivot($post, $payload, 'languages');
    }

    private function catalogue($request) {
        if($request->input('catalogue') != null) {
            return array_unique(array_merge($request->input('catalogue'), [$request->input('post_catalogue_id')]));
        }

        return $request->post_catalogue_id;
    }

    private function payload() {
        return ['post_catalogue_id', 'image', 'album', 'publish', 'follow'];
    }

    private function payloadPost() {
        return ['name', 'canonical', 'description', 'content', 'meta_title', 'meta_description', 'meta_keyword'];
    }
}
