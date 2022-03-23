<?php

namespace App\Http\Controllers\Api\V1\District;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\DistrictChannelContentCollection;
use App\Libraries\Lang\Lang;
use App\Services\DistrictChannelContentService;
use App\Services\RecordService;
use App\Types\Record\RecordType;
use Illuminate\Http\Request;

class DistrictChannelContentController extends Controller
{
    /**
     * @var DistrictChannelContentService
     */
    protected $districtChannelContentService;
    protected $recordService;

    /**
     * DistrictChannelContentController constructor.
     * @param DistrictChannelContentService $districtChannelContentService
     * @param RecordService $recordService
     */
    public function __construct(DistrictChannelContentService $districtChannelContentService, RecordService $recordService)
    {
        $this->districtChannelContentService = $districtChannelContentService;
        $this->recordService                 = $recordService;
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
     * @param int $districtId
     * @return void
     */
    public function show(int $districtId)
    {
        $localesId                  = Lang::getConvertByLangStringForId();
        $districtChannelContentInfo = $this->districtChannelContentService->getDistrictChannelContent($districtId, $localesId);
        return response()->json(new DistrictChannelContentCollection($districtChannelContentInfo), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, int $id)
    {

        $districtChannelContentInfo = $this->districtChannelContentService->updateBy('id', $id, [
            'ratings_id' => $request->params['rating']
        ]);
        // 操作紀錄
        $this->recordService->create([
            'type'                        => RecordType::UPDATE,
            'user_id'                     => auth()->id(),
            'rating_id'                   => $request->params['rating'],
            'district_channel_content_id' => $id,
        ]);
        return response()->json($districtChannelContentInfo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $destroy = $this->districtChannelContentService->destroy($id);
        // 操作紀錄
        $this->recordService->create([
            'type'                        => RecordType::DELETE,
            'user_id'                     => auth()->id(),
            'district_channel_content_id' => $id,
        ]);
        return response($destroy, 204);
    }
}
