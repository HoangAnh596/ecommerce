<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class postCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    protected $postCatalogueRepository;
    protected $routerRepository;
    protected $nestedset;
    protected $language;
    protected $controllerName;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        RouterRepository $routerRepository,
    ){
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->routerRepository = $routerRepository;
        $this->language = $this->currentLanguage();
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreign_key' => 'post_catalogue_id',
            'language_id' => $this->language,
        ]);
        $this->controllerName = 'PostCatalogueController';
    }

    public function paginate($request){
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $this->language]
            ]
        ];
        $postCatalogues = $this->postCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'post.catalogue.index'],
            ['post_catalogues.lft','ASC'],
            [
                ['post_catalogue_language as tb2', 'tb2.post_catalogue_id', '=', 'post_catalogues.id'],
            ]
        );

        return $postCatalogues;
    }

    public function create($request) {
        DB::beginTransaction();
        try {
            $postCatalogue = $this->createPostCatalogue($request);
            if($postCatalogue->id > 0) {
                $this->updateLanguageForCatalogue($postCatalogue, $request);
                $this->createRouter($postCatalogue, $request, $this->controllerName); 
                $this->nestedset();
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
            $postCatalogue = $this->postCatalogueRepository->findById($id);
            $flag = $this->updatePostCatalogue($postCatalogue, $request);
            if($flag == true){
                $this->updateLanguageForCatalogue($postCatalogue, $request);
                $this->updateRouter($postCatalogue, $request, $this->controllerName);
                $this->nestedset();
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
            $this->postCatalogueRepository->delete($id);
            $this->nestedset();
            
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
            $this->postCatalogueRepository->update($post['modelId'], $payload);

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
            $this->postCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);

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

    private function createPostCatalogue($request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->postCatalogueRepository->create($payload);;
    }

    private function updatePostCatalogue($postCatalogue, $request) {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();

        return $this->postCatalogueRepository->update($postCatalogue->id, $payload);;
    }

    private function updateLanguageForCatalogue($postCatalogue, $request)
    {
        $payload = $this->formatLanguagePayload($request);
        $postCatalogue->languages()->detach([$this->language, $postCatalogue->id]);
        $response = $this->postCatalogueRepository->createPivot($postCatalogue, $payload, 'languages');

        return $response;
    }

    private function formatLanguagePayload($request)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $this->language;
        
        return $payload;
    }

    private function paginateSelect() {
        return [
            'post_catalogues.id',
            'post_catalogues.image',
            'post_catalogues.publish',
            'post_catalogues.level',
            'post_catalogues.order',
            'tb2.name',
            'tb2.canonical'
        ];
    }

    private function payload() {
        return ['parent_id', 'image', 'album', 'publish', 'follow'];
    }

    private function payloadLanguage() {
        return ['name', 'canonical', 'description', 'content', 'meta_title', 'meta_description', 'meta_keyword'];
    }
}
