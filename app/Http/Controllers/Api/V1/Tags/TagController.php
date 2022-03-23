<?php

namespace App\Http\Controllers\Api\V1\Tags;


use App\Http\Controllers\Api\V1\Controller;
use App\Services\TagService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TagController extends Controller
{

    /**
     * @var TagService
     */
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function show($id)
    {

    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        $onlyData = $request->only('type_id', 'content');
        try {
            $onlyData['id'] = Carbon::now()->format('Ymdhis');
            $this->tagService->create($onlyData);

            return $this->setStatus(204)->success(null);
        } catch (\Exception $exception) {

            return $this->setStatus(412)->fail($exception->getMessage());
        }

    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {

        $onlyData = $request->only('id', 'type_id', 'content');
        try {
            $this->tagService->update($id, $onlyData);

            return $this->setStatus(204)->success(null);
        } catch (\Exception $exception) {

            return $this->setStatus(412)->fail($exception->getMessage());
        }


    }

    /**
     * Status delete
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->tagService->update($id, ['status' => 0]);
            return $this->setStatus(204)->success(null);
        } catch (\Exception $exception) {
            return $this->setStatus(412)->fail($exception->getMessage());
        }
    }
}
