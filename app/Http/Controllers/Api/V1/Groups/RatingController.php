<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Helpers\Api\Response;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\RatingRequest;
use App\Http\Resources\RatingCollection;
use App\Services\GroupChannelContentService;
use App\Services\RatingService;
use App\Services\RecordService;
use App\Types\Record\RecordType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RatingController extends Controller
{
    /**
     * @var RatingService
     */
    protected $ratingService;
    /**
     * @var GroupChannelContentService
     */
    protected $groupChannelContentService;
    protected $recordService;

    /**
     * RatingController constructor.
     * @param RatingService $ratingService
     * @param GroupChannelContentService $groupChannelContentService
     * @param RecordService $recordService
     */
    public function __construct(RatingService $ratingService, GroupChannelContentService $groupChannelContentService, RecordService $recordService)
    {
        $this->ratingService              = $ratingService;
        $this->groupChannelContentService = $groupChannelContentService;
        $this->recordService              = $recordService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RatingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RatingRequest $request): \Illuminate\Http\JsonResponse
    {
        $only   = collect($request->only('name', 'type', 'groups_id', 'districts_id'));
        $name   = trim($request->input('name'));
        $result = $only->merge(['name' => $name]);

        try {
            $model = $this->ratingService->create($result->toArray());
            // 操作紀錄
            $this->recordService->create([
                'type'      => RecordType::CREATE,
                'user_id'   => auth()->id(),
                'rating_id' => $model->id,
                'group_id'  => $request->input('groups_id'),
            ]);
        } catch (\Exception $exception) {

            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }

        return $this->setStatus(204)->success(null);
    }

    /**
     * Display the specified resource.
     *
     * @param int $group_id
     * @return RatingCollection
     */
    public function show(int $group_id)
    {
        $ratingServices = $this->ratingService->getByUseStatistics(['groups_id' => $group_id], 'ASC');
        RatingCollection::wrap('ratings');

        return $this->setStatus(200)->success(new  RatingCollection($ratingServices));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $group_id
     * @return \Illuminate\Http\Response
     */
    public function edit($group_id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $ratingId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $ratingId)
    {
        $request->validate([
            'name'      => [
                'required',
                Rule::unique('ratings')->where(function ($q) use ($request, $ratingId) {
                    return $q->where('groups_id', $request->groups_id)->where('id', '!=', $ratingId);
                })
            ],
            'groups_id' => 'required|integer',
            'type'      => 'required|integer',
        ]);
        $new_order = $request->input('type');

        try {
            if ($this->ratingService->exists(['id' => $ratingId])) {
                $info = $this->ratingService->find($ratingId);

                $this->ratingService->adjustSort('update', ['groups_id' => $info->groups_id], 'type', $info->type, $new_order);
                $this->ratingService->updateBy('id', $ratingId, [
                    'name'      => trim($request->input('name')),
                    'groups_id' => $request->input('groups_id'),
                    'type'      => $new_order,
                ]);
            }

            // 操作紀錄
            $this->recordService->create([
                'type'      => RecordType::UPDATE,
                'user_id'   => auth()->id(),
                'rating_id' => $ratingId,
                'group_id'  => $request->input('groups_id'),
            ]);
        } catch (\Exception $exception) {

            \Log::error($exception->getMessage());
            return $this->fail($exception->getCode());
        }

        return $this->setStatus(204)->success(null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $ratingId
     * @return void
     */
    public function destroy($ratingId)
    {
        if ($this->ratingService->exists(['id' => $ratingId])) {
            $info = $this->ratingService->find($ratingId);
            $this->ratingService->adjustSort('destroy', ['groups_id' => $info->groups_id], 'type', $info->type, 0);
            $this->ratingService->destroy($ratingId);
            // 操作紀錄
            $this->recordService->create([
                'type'      => RecordType::DELETE,
                'user_id'   => auth()->id(),
                'rating_id' => $ratingId,
            ]);
            return $this->setStatus(204)->success(null);
        }

        return response()->json('Do not delete during use', 422);
    }

    /**
     * @param Request $request
     * @param int $ratingId
     * @return mixed
     */
    public function updateSort(Request $request, int $ratingId)
    {
        $request->validate([
            'type' => 'required|integer'
        ]);

        $new_order = $request->input('type');
        try {
            if ($this->ratingService->exists(['id' => $ratingId])) {
                $info = $this->ratingService->find($ratingId);
                $this->ratingService->adjustSort('update', ['groups_id' => $info->groups_id], 'type', $info->type, $new_order);
                $this->ratingService->updateBy('id', $ratingId, [
                    'type' => $new_order,
                ]);
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->setStatus(422)->fail($exception->getCode());
        }

        return $this->setStatus(204)->success('200');
    }
}
