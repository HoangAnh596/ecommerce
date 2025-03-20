<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
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
    protected $nestedset;
    protected $language;

    public function __construct(PostCatalogueRepository $postCatalogueRepository){
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->language = $this->currentLanguage();
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreign_key' => 'post_catalogue_id',
            'language_id' => $this->language,
        ]);
    }

    public function paginate($request){
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $condition['where'] = [
            ['tb2.language_id', '=', $this->language]
        ];
        $perpage = $request->integer('perpage');
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
            $payload = $request->only($this->payload());
            $payload['user_id'] = Auth::id();
            $payload['album'] = json_encode($payload['album']);
            $postCatalogue = $this->postCatalogueRepository->create($payload);
            if($postCatalogue->id > 0) {
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['canonical'] = Str::slug($payloadLanguage['canonical']);
                $payloadLanguage['language_id'] = $this->currentLanguage();
                $this->postCatalogueRepository->createLanguagePivot($postCatalogue, $payloadLanguage);
            }

            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();

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
            $payload = $request->only($this->payload());
            $flag = $this->postCatalogueRepository->update($id, $payload);
            if($flag == true){
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['language_id'] = $this->currentLanguage();
                $postCatalogue->languages()->detach([$payloadLanguage['language_id'], $id]);
                $this->postCatalogueRepository->createLanguagePivot($postCatalogue, $payloadLanguage);

                $this->nestedset->Get('level ASC, order ASC');
                $this->nestedset->Recursive(0, $this->nestedset->Set());
                $this->nestedset->Action();
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
            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();
            
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
