<?php

namespace App\Http\Controllers\Api\V1\Tags;


use App\Http\Controllers\Api\V1\Controller;
use App\Services\TagTypeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TagTypeController extends Controller
{

    /**
     * @var TagTypeService
     */
    protected $tagTypeService;

    public function __construct(TagTypeService $tagTypeService)
    {
        $this->tagTypeService = $tagTypeService;
    }

    public function show($id)
    {

    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $onlyData       = $request->only('group_id', 'content');
        $onlyData['id'] = Carbon::now()->format('Ymdhis');
        try {
            $this->tagTypeService->create($onlyData);
            return $this->setStatus(204)->success(null);
        } catch (\Exception $exception) {
            return $this->setStatus(412)->fail($exception->getMessage());
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $onlyData = $request->only('content');

        try {
            $this->tagTypeService->update($id, $onlyData);;
            return $this->setStatus(204)->success(null);
        } catch (\Exception $exception) {

            return $this->setStatus(412)->fail($exception->getMessage());
        }

    }

    /**
     * 狀態刪除
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->tagTypeService->update($id, ['status' => 0]);
            return $this->setStatus(204)->success(null);
        } catch (\Exception $exception) {
            return $this->setStatus(412)->fail($exception->getMessage());
        }
    }
}
