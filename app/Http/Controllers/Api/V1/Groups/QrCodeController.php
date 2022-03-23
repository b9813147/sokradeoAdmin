<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Http\Controllers\Api\V1\Controller;

use App\Services\GroupService;
use App\Services\QrCodeService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QrCodeController extends Controller
{
    protected $qrCodeService;
    protected $userService;
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * QrCodeController constructor.
     * @param QrCodeService $qrCodeService
     * @param UserService $userService
     * @param GroupService $groupService
     */
    public function __construct(QrCodeService $qrCodeService, UserService $userService, GroupService $groupService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->userService   = $userService;
        $this->groupService  = $groupService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($group_id): \Illuminate\Http\JsonResponse
    {
        $groupInfo = $this->groupService->find($group_id);
        $url       = app('Dingo\Api\Routing\UrlGenerator')
            ->version('v1')
            ->temporarySignedRoute('member.store', now()->addMinutes(5), ['groupId' => $group_id, 'sname' => $groupInfo->name]);

        $result = [
            'url' => $url
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
//        $group_id = $request->group_id;
//        $duty     = $request->duty;
//        $end_time = $request->end_time;
//        $url      = url("api/qrcode/join?group_id=$group_id&duty=$duty&end_time=$end_time");
//
//        $data = [
//            'url'      => '$url',
//            'group_id' => (int)$group_id,
//            'status'   => (int)1,
//            'duty'     => $duty,
//            'end_time' => $end_time,
//        ];
//        $this->qrCodeService->create($data);
    }

    /**
     * user join to group
     *
     * Display the specified resource.
     *
     * @param Request $request
     * @return false|string
     */
    public function joinGroup(Request $request)
    {
        $lang = [];

        $headerAccount = $request->header('x-auth-hash');
        $headerLang    = $request->header('msglang');

        $auth2_account = base64_decode($headerAccount);
        $user          = $this->userService->findBy('habook', $auth2_account);


        switch ($headerLang) {
            case 'en-US':
                $lang = (object)['success' => 'channel join success', 'fail' => 'invalid QrCode'];
                break;
            case 'zh-TW':
                $lang = (object)['success' => '頻道加入成功', 'fail' => '無效 二維碼'];
                break;
            case '"zh-CN':
                $lang = (object)['success' => '频道加入成功', 'fail' => '无效 二维码'];
                break;
        }

        // 需帶入 groupId member_duty
        $group_id    = $request->group_id;
        $member_duty = $request->duty;
        $end_time    = $request->end_time;
        $nowDate     = Carbon::now()->toDateString();

        if ($end_time < $nowDate) {
            return $this->fail([
                'status'  => 0,
                'error'   => 404,
                'message' => $lang->fail,
            ]);
        }

        \Log::info($headerLang);
        \Log::info($headerAccount);
        \Log::info($auth2_account);
        \Log::info($user);

        if (!$user) {
            return $this->fail([
                'status'  => 1,
                'error'   => 404,
                'message' => $lang->fail,
            ]);
        }

        //  加入頻道

        $data = [
            'status'  => 0,
            'message' => $lang->success,
        ];

        return $this->success($data);

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
