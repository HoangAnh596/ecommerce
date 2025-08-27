<?php

namespace App\Services;

use App\Services\Interfaces\SlideServiceInterface;
use App\Repositories\Interfaces\SlideRepositoryInterface as SlideRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Class SlideService
 * @package App\Services
 */
class SlideService extends BaseService implements SlideServiceInterface
{
    protected $slideRepository;

    public function __construct(SlideRepository $slideRepository)
    {
        $this->slideRepository = $slideRepository;
    }

    public function paginate($request)
    {
        $perpage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];
        $slides = $this->slideRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'slide/index'],
            ['id', 'DESC'],
            [],
            []
        );

        return $slides;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword', 'setting', 'short_code');
            $payload['item'] = $this->handlSlideItem($request, $languageId);
            $payload['user_id'] = auth()->id();

            $this->slideRepository->create($payload);

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

    public function update($request, $id, $languageId)
    {
        DB::beginTransaction();
        try {
            $slide = $this->slideRepository->findById($id);
            $slideItem = $slide->item;
            unset($slideItem[$languageId]);
            $payload = $request->only('name', 'keyword', 'setting', 'short_code');
            $payload['item'] = $this->handlSlideItem($request, $languageId) + $slideItem;
            $payload['user_id'] = auth()->id();

            $this->slideRepository->update($id, $payload);

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

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->slideRepository->delete($id);

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

    private function handlSlideItem($request, $languageId)
    {
        $slide = $request->input('slide');
        $temp = [];
        foreach ($slide['image'] as $key => $val) {
            $temp[$languageId][] = [
                'image' => $val,
                'name' => $slide['name'][$key],
                'description' => $slide['description'][$key],
                'canonical' => $slide['canonical'][$key],
                'alt' => $slide['alt'][$key],
                'window' => (isset($slide['window'][$key])) ? $slide['window'][$key] : '',
            ];
        }

        return $temp;
    }

    private function paginateSelect()
    {
        return ['id', 'name', 'keyword', 'item', 'publish'];
    }
}
