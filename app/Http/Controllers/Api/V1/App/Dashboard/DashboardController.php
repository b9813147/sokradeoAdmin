<?php

namespace App\Http\Controllers\Api\V1\App\Dashboard;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\GroupChannelResource;
use App\Services\GroupChannelService;
use App\Services\SemestersService;
use Illuminate\Http\Request;
use Mockery\Exception;

class DashboardController extends Controller
{
    protected $groupChannelService;
    /**
     * @var SemestersService
     */
    protected $semestersService;

    /**
     * DashboardController constructor.
     * @param GroupChannelService $groupChannelService
     * @param SemestersService $semestersService
     */
    public function __construct(GroupChannelService $groupChannelService, SemestersService $semestersService)
    {
        $this->groupChannelService = $groupChannelService;
        $this->semestersService    = $semestersService;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param $abbr
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($abbr): \Illuminate\Http\JsonResponse
    {
        try {
            $channel_id = GlobalPlatform::convertAbbrToChannelId($abbr);

            $groupChannelInfo = $this->groupChannelService->getGroupChannelByTba($channel_id);

            $data = [
                'result' => new GroupChannelResource($groupChannelInfo),
            ];
        } catch (\Exception $exception) {
            return $this->setStatus(404)->respond(['message' => 'No such school']);
        }

        return $this->success($data);
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
