<?php

namespace App\Http\Controllers\Api\V1\Globals;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\GlobalRecommendedVideoRequest;
use App\Http\Resources\GlobalRecommendedVideoCollection;
use App\Services\GroupChannelService;
use App\Services\GroupChannelContentService;
use App\Services\RecommendedVideoService;
use Illuminate\Http\Request;
use Mockery\Exception;

class RecommendedVideoController extends Controller
{
    /**
     * @var RecommendedVideoService
     */
    protected $recommendedVideoService;
    protected $groupChannelService;
    protected $groupChannelContentService;


    /**
     * RecommendedVideoController constructor.
     * @param RecommendedVideoService $recommendedVideoService
     * @param GroupChannelService $groupChannelService
     * @param GroupChannelContentService $groupChannelContentService
     */
    public function __construct(RecommendedVideoService $recommendedVideoService, GroupChannelService $groupChannelService, GroupChannelContentService $groupChannelContentService)
    {
        $this->recommendedVideoService    = $recommendedVideoService;
        $this->groupChannelService        = $groupChannelService;
        $this->groupChannelContentService = $groupChannelContentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'limit' => 'integer'
            ]);

            $limit             = $request->input('limit');
            $recommendedVideos = $this->recommendedVideoService->getWithLimit(isset($limit) ? $limit : null);

            return response()->json(new GlobalRecommendedVideoCollection($recommendedVideos), 200);

        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }
    }

    /**
     * Display a listing of channel.
     *
     * @return \Illuminate\Http\Response
     */
    public function getChannels()
    {
        try {
            $channels = $this->groupChannelService->all()->map(function ($q) {
                return ['id' => $q->id, 'name' => $q->name];
            });

            return response()->json($channels, 200);

        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }
    }

    /**
     * Display a listing of channel content
     *
     * @return \Illuminate\Http\Response
     */
    public function getChannelContents($channelId)
    {
        try {
            $channelContents = $this->groupChannelContentService->getGroupChannelContentAndTbaByGroupChannelId($channelId)->toArray();

            return response()->json($channelContents, 200);

        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }
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
     * @param GlobalRecommendedVideoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(GlobalRecommendedVideoRequest $request)
    {
        $new_order = $request->input('order');

        try {
            $this->recommendedVideoService->adjustSort('create', [], 'order', 0, $new_order);
            $this->recommendedVideoService->create([
                'group_channel_id' => $request->input('channelId'),
                'tba_id'           => $request->input('contentId'),
                'order'            => $new_order,
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->fail($exception->getCode());
        }

        return $this->success('200');
    }

    /**
     * Display the specified resource.
     *
     * @param
     * @return
     */
    public function show(int $groupId)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $groupId
     * @return \Illuminate\Http\Response
     */
    public function edit($groupId)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'order' => 'required|integer'
        ]);

        $new_order = $request->input('order');

        try {
            if ($this->recommendedVideoService->exists(['id' => $id])) {
                $info = $this->recommendedVideoService->find($id);
                $this->recommendedVideoService->adjustSort('update', [], 'order', $info->order, $new_order);
                $this->recommendedVideoService->updateBy('id', $id, [
                    'order' => $new_order,
                ]);
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->fail($exception->getCode());
        }

        return $this->success('200');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $ratingId
     * @return void
     */
    public function destroy($id)
    {
        if ($this->recommendedVideoService->exists(['id' => $id])) {
            $info = $this->recommendedVideoService->find($id);
            $this->recommendedVideoService->adjustSort('destroy', [], 'order', $info->order, 0);
            $this->recommendedVideoService->destroy($id);

            return $this->success('200');
        }

        return response()->json('Do not delete during use', 422);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function updateSort(Request $request, int $id)
    {
        $request->validate([
            'order' => 'required|integer'
        ]);

        $new_order = $request->input('order');

        try {
            if ($this->recommendedVideoService->exists(['id' => $id])) {
                $info = $this->recommendedVideoService->find($id);
                $this->recommendedVideoService->adjustSort('update', [], 'order', $info->order, $new_order);
                $this->recommendedVideoService->updateBy('id', $id, [
                    'order' => $new_order,
                ]);
            }
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return $this->fail($exception->getCode());
        }

        return $this->success('200');
    }
}
