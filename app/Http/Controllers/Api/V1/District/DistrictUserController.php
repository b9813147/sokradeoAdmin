<?php

namespace App\Http\Controllers\Api\V1\District;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\DistrictUserCollection;
use App\Libraries\Lang\Lang;
use App\Models\User;
use App\Services\DistrictUserService;
use App\Services\RecordService;
use App\Services\UserService;
use App\Types\Record\RecordType;
use Illuminate\Http\Request;
use Mockery\Exception;

class DistrictUserController extends Controller
{
    /**
     * @var DistrictUserService
     */
    protected $districtUserService;
    /**
     * @var UserService
     */
    protected $userService;
    protected $recordService;

    /**
     * DistrictUserController constructor.
     * @param DistrictUserService $districtUserService
     * @param UserService $userService
     * @param RecordService $recordService
     */
    public function __construct(DistrictUserService $districtUserService, UserService $userService, RecordService $recordService)
    {
        $this->districtUserService = $districtUserService;
        $this->userService         = $userService;
        $this->recordService       = $recordService;
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
        $userInfo = $this->userService->findBy('habook', $request->input('habook'));

        $result = $this->districtUserService->updateOrCreate([
            'user_id'      => $userInfo->id,
            'districts_id' => $request->input('districts_id')
        ], [
                'member_duty'   => $request->input('member_duty'),
                'member_status' => 1
            ]
        );
        // 操作記錄
        $this->recordService->create([
            'type'             => RecordType::CREATE,
            'user_id'          => auth()->id(),
            'district_user_id' => $result->id
        ]);

        return response()->json($result, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $districtId
     * @return void
     */
    public function show(int $districtId)
    {
        $convertByLangStringForId = Lang::getConvertByLangStringForId();

        try {
            $userInfo = $this->districtUserService->getDistrictUserInfo($districtId, $convertByLangStringForId);

            return response()->json(new DistrictUserCollection($userInfo), 200);

        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $districtId
     * @return void
     */
    public function update(Request $request, int $districtId)
    {
        try {
            $districtUserService = $this->districtUserService->updateWhere(['districts_id' => $districtId, 'user_id' => $request->input('user_id')], [
                'member_duty'   => $request->input('member_duty'),
                'member_status' => 1
            ]);

            // 操作記錄
            $this->recordService->create([
                'type'             => RecordType::UPDATE,
                'user_id'          => auth()->id(),
                'district_user_id' => $this->districtUserService->firstWhere(['districts_id' => $districtId, 'user_id' => $request->input('user_id')])->id
            ]);
            return response()->json($districtUserService, 204);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $districtId
     * @return void
     */
    public function destroy(Request $request, int $districtId)
    {
        try {
            $id                  = $this->districtUserService->firstWhere(['districts_id' => $districtId, 'user_id' => $request->input('user_id')])->id;
            $districtUserService = $this->districtUserService->deleteWhere(['user_id' => $request->input('user_id'), 'districts_id' => $districtId]);

            // 操作記錄
            $this->recordService->create([
                'type'             => RecordType::DELETE,
                'user_id'          => auth()->id(),
                'district_user_id' => $id
            ]);
            return response()->json($districtUserService, 204);
        } catch (Exception $exception) {
            response()->json(['message' => $exception->getMessage()], 401);
        }
    }

    /**
     * 判斷使用者是否存在
     * @param Request $request
     * @return bool
     */
    public function userExist(Request $request)
    {
        $teamModelId = urldecode($request->input('habook'));

        if ($this->userService->findBy('habook', $teamModelId) instanceof User) {

            return response()->json($teamModelId, 204);
        }

        return response()->json(['message' => 'does not exist'], 422);

    }
}
