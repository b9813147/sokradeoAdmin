<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\GroupUserTbaStatsCollection;
use App\Http\Resources\SemesterResource;
use App\Services\GroupChannelService;
use App\Services\SemestersService;
use App\Services\TbaService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Mockery\Exception;

class GroupChannelController extends Controller
{
    /**
     * @var TbaService
     */
    protected $tbaService;
    /**
     * @var GroupChannelService
     */
    protected $groupChannelService;

    /**
     * GroupChannelController constructor.
     * @param TbaService $tbaService
     * @param GroupChannelService $groupChannelService
     */
    public function __construct(TbaService $tbaService, GroupChannelService $groupChannelService)
    {
        $this->tbaService          = $tbaService;
        $this->groupChannelService = $groupChannelService;
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $groupChannelInfo = $this->groupChannelService->find($id);
            return $this->success($groupChannelInfo);
        } catch (\Exception $exception) {

            return $this->fail(['message' => $exception->getMessage()]);
        }

    }

    /**
     * @param Request $request
     * @param $id
     * @return GroupUserTbaStatsCollection
     */
    public function filter(Request $request, $id)
    {
//        try {
        $json_decodeData = json_decode($request->input('field'));

        $channel_id = GlobalPlatform::convertGroupIdToChannelId($id);
        $date       = $request->input('date');
        $search     = $json_decodeData->id ?? null;
        $column     = $json_decodeData->type ?? null;

        return new GroupUserTbaStatsCollection($this->tbaService->getTbaStatsInfoAndFilter($channel_id, $date, $search, $column));
//        } catch (Exception $exception) {
//            return \response()->json($exception->getMessage(), 404);
//        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
