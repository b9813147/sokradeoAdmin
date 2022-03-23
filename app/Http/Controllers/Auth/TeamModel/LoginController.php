<?php

namespace App\Http\Controllers\Auth\TeamModel;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Controller;
use App\Libraries\Lang\Lang;
use App\Models\User;
use App\Services\DistrictService;
use App\Services\DistrictUserService;
use App\Services\GroupService;
use App\Services\HaBook\ApiService;
use App\Services\UserService;
use App\Types\Group\DutyType;
use App\Types\App\RoleType;
use App\Types\Module\ModuleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    protected $groupService;
    protected $userService;
    protected $apiService;
    protected $districtUserService;
    /**
     * @var DistrictService
     */
    protected $districtService;

    /**
     * LoginController constructor.
     * @param GroupService $groupService
     * @param UserService $userService
     * @param ApiService $apiService
     * @param DistrictUserService $districtUserService
     * @param DistrictService $districtService
     */
    public function __construct(GroupService $groupService, UserService $userService, ApiService $apiService, DistrictUserService $districtUserService, DistrictService $districtService)
    {
        $this->groupService        = $groupService;
        $this->userService         = $userService;
        $this->apiService          = $apiService;
        $this->districtUserService = $districtUserService;
        $this->districtService     = $districtService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|void
     */
    public function login(Request $request)
    {
        /*todo 需要增加Auth 判斷 使用者份 僅開放 Admin Expert*/
        if ($request->has('district')) {
            session()->put(['districtId' => (int)base64_decode($request->input('district'))]);
            session()->forget('channelId');
        } else if ($request->has('channel')) {
            session()->put(['channelId' => (int)base64_decode($request->input('channel'))]);
            session()->forget('districtId');
        } else if ($request->has('global')) {
            session()->put(['globalRoleType' => (string)base64_decode($request->input('global'))]);
            session()->forget('channelId');
            session()->forget('districtId');
        }

        if ($request->has('ticket')) {
            auth()->loginUsingId(Crypt::decryptString($request->input('ticket')));
        }

        //頻道管理
        $channelId = session()->get('channelId');
        //學區管理
        $districtId = session()->get('districtId');
        //全站管理
        $globalRoleType = session()->get('globalRoleType');

        //todo 增加學區後台 登錄機制
        try {
            if ($channelId) {
                return $this->channelAuth($channelId, auth()->id());
            }
            if ($districtId) {
                return $this->districtAuth($districtId, auth()->id());
            }
            if ($globalRoleType) {
                return $this->roleAuth($globalRoleType, auth()->id());
            }

        } catch (\Exception $e) {
            return redirect(getenv('SOKRADEO_URL'));
        }
        return redirect(getenv('SOKRADEO_URL'));
    }

    /**
     * 頻道登入機制
     * @param int $channelId
     * @param int $userId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    private function channelAuth(int $channelId, int $userId)
    {
        // 用channelID 去找 groupID
        $group_id                 = GlobalPlatform::convertChannelIdToGroupId($channelId);
        $groupInfo                = $this->groupService->getGroupInfo($group_id);
        $convertByLangStringForId = Lang::getConvertByLangStringForId();

        $groupName            = $groupInfo->groupLangs()->where('locales_id', $convertByLangStringForId)->first()->name ?? null;
        $userInfo             = $groupInfo->users()->where('id', $userId)->first()->pivot;
        $userInfo->name       = $groupName;
        $userInfo->thumbnail  = $groupInfo->thumbnail;
        $userInfo->public     = $groupInfo->public;
        $userInfo->channel_id = $channelId;


        $accessToken     = User::query()->find($userId)->createToken('accessToken')->accessToken;
        $userInfo->token = $accessToken;
        if (!$userInfo) {
            return redirect(env('SOKRADEO_URL'));
        }

        // 檢查身份
        if ($userInfo->member_duty === DutyType::Admin || $userInfo->member_duty === DutyType::Expert) {
            $data = [
                'userInfo' => $userInfo,
                'module'   => ModuleType::Group,
                'status'   => 200,
                'message'  => '登錄成功',
            ];

            return view('app', compact('data'));
        }
    }

    /**
     * 學區登入機制
     * @param int $districtId
     * @param int $userId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    private function districtAuth(int $districtId, int $userId)
    {
        $result       = [];
        $districtInfo = $this->districtService->getDistrictInfo($districtId, Lang::getConvertByLangStringForId());
        $userInfo     = $districtInfo->districtUser()->where(['user_id' => $userId, 'member_duty' => DutyType::Admin, 'member_status' => 1]);

        if (!$userInfo->exists()) {
            return redirect(getenv('SOKRADEO_URL'));
        }

        if (!$districtInfo->exists()) {
            return redirect(getenv('SOKRADEO_URL'));
        }
        $accessToken = User::query()->find($userId)->createToken('accessToken')->accessToken;

        $result = [
            'id'            => $userInfo->first()->id,
            'districts_id'  => $districtInfo->id,
            'user_id'       => $userInfo->first()->user_id,
            'member_status' => $userInfo->first()->member_status,
            'member_duty'   => $userInfo->first()->member_duty,
            'thumbnail'     => $districtInfo->thumbnail,
            'name'          => $districtInfo->districtLang->name,
            'token'         => $accessToken,
        ];


        $data = [
            'userInfo' => $result,
            'module'   => ModuleType::District,
            'status'   => 200,
            'message'  => '登錄成功',
        ];
        return view('app', compact('data'));
    }

    /**
     * 全平台管理登入機制
     * @param int $userId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    private function roleAuth(string $roleType, int $userId)
    {
        $result      = $this->userService->getUser($userId);
        $accessToken = $result->createToken('accessToken')->accessToken;
        if (!$result) {
            return redirect(env('SOKRADEO_URL'));
        }
        $role = $result->roles->where('type', RoleType::Root);
        // 檢查身份
        switch ($roleType) {
            case RoleType::Root:
                if ($role->isNotEmpty()) {
                    $userInfo = [
                        'user_id'          => $result->id,
                        'global_role_type' => $role->first()->type,
                        'token'            => $accessToken,
                        'name'             => __('app.global')
                    ];
                    $data     = [
                        'userInfo' => $userInfo,
                        'module'   => ModuleType::Platform,
                        'status'   => 200,
                        'message'  => '登錄成功',
                    ];

                    return view('app', compact('data'));
                } else {
                    return abort('404');
                }
                break;
            default:
                return abort('404');
                break;
        }
    }

}
