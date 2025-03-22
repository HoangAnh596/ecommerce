<?php

namespace App\Services;

use App\Services\Interfaces\PostServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
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
    protected $language;

    public function __construct(PostRepository $postRepository){
        $this->postRepository = $postRepository;
        $this->language = $this->currentLanguage();
    }

    public function paginate($request){
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['where'] = [
            ['tb2.language_id', '=', $this->language]
        ];
        $perpage = $request->integer('perpage');
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
            $this->whereRaw($request)
        );

        return $posts;
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

    public function create($request) {
        DB::beginTransaction();
        try {
            $post = $this->createPost($request);
            if($post->id > 0) {
                $this->updateForPost($post, $request);
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

    public function update($request, $id){
        DB::beginTransaction();
        try {
            $post = $this->postRepository->findById($id);
            if($this->uploadPost($post, $request)){
                $this->updateForPost($post, $request);
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

    public function updateStatus($post = []) {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = ($post['value'] == 1) ? 2 : 1;
            $this->postRepository->update($post['modelId'], $payload);

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

    public function updateStatusAll($post) {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];
            $this->postRepository->updateByWhereIn('id', $post['id'], $payload);

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

    // private function paginateSelect() {
    //     return [
    //         'posts.id',
    //         'posts.image',
    //         'posts.publish',
    //         'posts.order',
    //         'tb2.name',
    //         'tb2.canonical'
    //     ];
    // }

    private function whereRaw($request){
        $rawCondition = [];
        if($request->integer('post_catalogue_id') > 0){
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.post_catalogue_id IN (
                        SELECT id
                        FROM post_catalogues
                        WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$request->integer('post_catalogue_id'), $request->integer('post_catalogue_id')]
                ]
            ];
            
        }
        return $rawCondition;
    }

    // private function whereRaw($request, $languageId){
    //     $rawCondition = [];
    //     if($request->integer('post_catalogue_id') > 0){
    //         $rawCondition['whereRaw'] =  [
    //             [
    //                 'tb3.post_catalogue_id IN (
    //                     SELECT id
    //                     FROM post_catalogues
    //                     JOIN post_catalogue_language ON post_catalogues.id = post_catalogue_language.post_catalogue_id
    //                     WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
    //                     AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
    //                     AND post_catalogue_language.language_id = '.$languageId.'
    //                 )',
    //                 [$request->integer('post_catalogue_id'), $request->integer('post_catalogue_id')]
    //             ]
    //         ];
            
    //     }
    //     return $rawCondition;
    // }

    private function createPost($request) {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = (isset($payload['album']) && !empty($payload['album'])) ? json_encode($payload['album']) : '';

        return $this->postRepository->create($payload);
    }

    private function uploadPost($post, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = (isset($payload['album']) && !empty($payload['album'])) ? json_encode($payload['album']) : '';

        return $this->postRepository->update($post->id, $payload);
    }

    private function updateForPost($post, $request) {
        $payload = $request->only($this->payloadPost());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $this->language;
        $payload['post_id'] = $post->id;
        $post->languages()->detach([$this->language, $post->id]);

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
