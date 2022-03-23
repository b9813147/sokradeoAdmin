<?php

namespace App\Http\Controllers\Api\V1\Statistical;

use App\Http\Controllers\Api\V1\Controller;
use App\Services\GroupChannelContentService;
use App\Services\GroupService;
use Illuminate\Http\Request;

class StatisticalController extends Controller
{
    protected $groupChannelContentService;
    protected $groupService;

    /**
     * StatisticalController constructor.
     * @param GroupChannelContentService $groupChannelContentService
     * @param GroupService $groupService
     */
    public function __construct(GroupChannelContentService $groupChannelContentService, GroupService $groupService)
    {
        $this->groupChannelContentService = $groupChannelContentService;
        $this->groupService               = $groupService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = [
            'public_channel'   => $this->groupService->getGroupTotal(['public' => 0]),
            'unlisted_channel' => $this->groupService->getGroupTotal(['public' => 1]),
            'public_video'     => $this->groupChannelContentService->getGroupVideoTotal([1], 1),
            'unlisted_video'   => $this->groupChannelContentService->getGroupVideoTotal([1, 2], 0),
            'list'             => $this->groupChannelContentService->getVideoTotalByGroup(),
        ];

        return $this->success($result);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
