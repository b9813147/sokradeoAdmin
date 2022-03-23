<?php

namespace App\Http\Controllers\Api\V1\District;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\DistrictClassificationByChannelSubjectCollection;
use App\Http\Resources\DistrictClassificationByDistrictSubjectCollection;
use App\Services\DistrictChannelContentService;
use App\Services\DistrictGroupSubjectService;
use App\Services\DistrictSubjectService;
use App\Services\GroupSubjectFieldsService;
use App\Services\RecordService;
use App\Types\Record\RecordType;
use Illuminate\Http\Request;

class DistrictClassificationController extends Controller
{
    /**
     * @var DistrictChannelContentService
     */
    protected $districtChannelContentService;
    /**
     * @var DistrictSubjectService
     */
    protected $districtSubjectService;
    /**
     * @var DistrictGroupSubjectService
     */
    protected $districtGroupSubjectService;
    /**
     * @var GroupSubjectFieldsService
     */
    protected $groupSubjectFieldsService;
    protected $recordService;

    /**
     * DistrictClassificationController constructor.
     * @param DistrictChannelContentService $districtChannelContentService
     * @param DistrictSubjectService $districtSubjectService
     * @param DistrictGroupSubjectService $districtGroupSubjectService
     * @param GroupSubjectFieldsService $groupSubjectFieldsService
     * @param RecordService $recordService
     */
    public function __construct(
        DistrictChannelContentService $districtChannelContentService,
        DistrictSubjectService $districtSubjectService,
        DistrictGroupSubjectService $districtGroupSubjectService,
        GroupSubjectFieldsService $groupSubjectFieldsService,
        RecordService $recordService
    )
    {
        $this->districtChannelContentService = $districtChannelContentService;
        $this->districtSubjectService        = $districtSubjectService;
        $this->districtGroupSubjectService   = $districtGroupSubjectService;
        $this->groupSubjectFieldsService     = $groupSubjectFieldsService;
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
        $groupSubjectInfo    = $this->groupSubjectFieldsService->getGroupSubjects($districtId);
        $districtSubjectInfo = $this->districtSubjectService->findWhere(['districts_id' => $districtId])->sortByDesc('order');

        $result                     = [];
        $result['group_subject']    = new DistrictClassificationByChannelSubjectCollection($groupSubjectInfo);
        $result['district_subject'] = new DistrictClassificationByDistrictSubjectCollection($districtSubjectInfo);

        return response()->json($result, 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $updateInfo = $this->districtGroupSubjectService->updateWhere(['group_subject_fields_id' => $id], ['district_subjects_id' => $request->input('subject')]);

        // 操作紀錄
        $this->recordService->create([
            'type'                   => RecordType::UPDATE,
            'user_id'                => auth()->id(),
            'group_subject_field_id' => $id,
            'district_subject_id'    => $request->input('subject'),
        ]);
        return response()->json($updateInfo, 204);
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
