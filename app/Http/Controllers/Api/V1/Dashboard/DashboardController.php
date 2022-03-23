<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\GroupChannelResource;
use App\Services\GroupChannelService;
use App\Services\SemestersService;
use Illuminate\Http\Request;

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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $channel_id       = GlobalPlatform::convertGroupIdToChannelId($id);
        $groupChannelInfo = $this->groupChannelService->getGroupChannelByTba((int)$channel_id);

        $data = [
            'result' => new GroupChannelResource($groupChannelInfo),
        ];
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
