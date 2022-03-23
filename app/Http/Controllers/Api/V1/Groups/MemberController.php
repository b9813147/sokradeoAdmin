<?php

namespace App\Http\Controllers\Api\V1\Groups;

use App\Enums\NotificationMessageType;
use App\Helpers\CoreService\CoreServiceApi;
use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Notifications\EventChannel;
use App\Services\GroupService;
use App\Services\Library\BbService;
use App\Services\NotificationMessageService;
use App\Services\RecordService;
use App\Services\UserService;
use App\Types\Group\DutyType;
use App\Types\Record\RecordType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class MemberController extends Controller
{
    use CoreServiceApi;

    protected $userService;
    protected $groupService;
    protected $recordService;
    /**
     * @var BbService
     */
    protected $bbService;
    /**
     * @var NotificationMessageService
     */
    protected $notificationMessageService;

    public function __construct(UserService $userService, GroupService $groupService, RecordService $recordService, BbService $bbService, NotificationMessageService $notificationMessageService)
    {
        $this->userService                = $userService;
        $this->groupService               = $groupService;
        $this->recordService              = $recordService;
        $this->bbService                  = $bbService;
        $this->notificationMessageService = $notificationMessageService;
    }

    /**
     * @param $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($user_id): JsonResponse
    {
        $userInfo = $this->userService->getUser($user_id);
        return $this->setStatus(200)->success($userInfo);
    }

    /**
     * 更新頻道使用者
     *
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function update(Request $request, $userId): JsonResponse
    {
        try {
            // 操作紀錄
            $this->recordService->create([
                'type'     => RecordType::UPDATE,
                'user_id'  => auth()->id(),
                'group_id' => $request->groupId,
                'user'     => $userId
            ]);
            $this->userService->getUser($userId)->groups()->updateExistingPivot($request->groupId, [
                'member_duty'   => $request->member_duty,
                'member_status' => $request->member_status,
            ]);
        } catch (\Exception $exception) {
            return $this->fail(['message' => __('response.fail')]);
        }

        return $this->setStatus(204)->success(['message' => __('response.success')]);
    }

    /**
     *  新加頻道使用者
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Qrcode 掃描加入
            if ($request->has('signature')) {
                if (!$request->hasValidSignature()) {
                    return $this->fail(['message' => __('response.expired')]);
                }
                // 預設資料
                $request->merge([
                    'habook'        => auth()->user()->habook,
                    'member_status' => 1,
                    'member_duty'   => DutyType::General,
                ]);
            }
            foreach (array_filter(explode(PHP_EOL, $request->habook)) as $item) {
                // 排除特殊字元
                $user = trim(preg_replace("/[‘.,:;*?~`!@$%^& =)(<>{}]|\]|\[|\/|\\\|\"|\|/", '', $item));

                $userInfo = $this->userService->firstWhere(['habook' => $user]);

                // CoreService API to check if the user exists
                if (!$userInfo) {
                    if (!$this->isExist($user)) {
                        return $this->setStatus(422)->respond([
                            'message' => $user . __('response.not_exist')
                        ]);
                    }
                    $userInfo = $this->userService->loginAsHaBook($user, ['habook' => $user, 'name' => $user]);
                    $this->userService->setUser($userInfo->id, [], [6]);
                }
                $groups = $userInfo->groups();
                // 使用者已存在 就不做異動
                if ($groups->where('id', $request->groupId)->exists()) {
                    return $this->setStatus(422)->respond([
                        'message' => $user . __('response.exist')
                    ]);
                } else {
                    // 新增身份 及 狀態
                    $groups->attach($request->groupId, [
                        'member_status' => $request->member_status,
                        'member_duty'   => $request->member_duty,
                    ]);

                    $groupInfo = $this->groupService->find($request->groupId);

                    switch ($groupInfo->country_code) {
                        case 886:
                            \App::setLocale('tw');
                            break;
                        case 1:
                            \App::setLocale('en');
                            break;
                        default:
                            \App::setLocale('cn');
                    }
                    // 頻道人數
                    $userTotal = $groupInfo->users()->where('member_duty', DutyType::General)->count();

                    // 僅限活動頻道
                    if ($groupInfo->public != 0 && $request->member_duty === DutyType::General) {
                        // 取得授權時間
                        $event_data = json_decode($groupInfo->event_data);
                        //專屬頻道
                        $group_id = $userInfo->group_channel_id ? GlobalPlatform::convertChannelIdToGroupId($userInfo->group_channel_id) : null;
                        $group    = $userInfo->groups->firstWhere('id', $group_id);

                        // 僅限活動頻道
                        if (!empty((array)$event_data) && (bool)($event_data->enableTrial) && $userTotal < $event_data->maxParticipant) {
                            $schoolCode  = ($groupInfo->school_code . $groupInfo->name) ?? null;
                            $LicenseData = [
                                "id"          => $user,                          //醍摩豆帳號
                                "name"        => $userInfo->name ?? $user,       //姓名
                                "productCode" => $event_data->productCode,       //產品八碼
                                "trialDay"    => $event_data->trialDeadline,     //申請試用天數
                                "cqty"        => $event_data->cqty,              //授權數
                                "schoolCode"  => $event_data->school_code ?? null,//學校簡碼 ※可不填
                                "schoolName"  => $group->name ?? null,           //學校名稱 ※可不填
                                "ap"          => collect($event_data->ap)->where('val', true), //附加功能
                            ];
                            // App data
                            $hiTeachMessage = json_encode([
                                'content' => $LicenseData,
                            ]);
                            $licence        = $this->bbService->getLicence($schoolCode, $LicenseData);
                            Log::info('License', [$licence]);
                            $licence->serial = $licence->serial ?? null;
                            // 紀錄序號
                            $groups->updateExistingPivot($groupInfo->id, ['user_data' => json_encode(['license' => $licence->serial])]);
                            $message = [
                                'channel_id'  => GlobalPlatform::convertGroupIdToChannelId($groupInfo->id),
                                'title'       => __('license.title'),
                                'content'     => __('license.content') . __('license.license') . $licence->serial . "\n\n" . __('license.expire') . $event_data->trialDeadline,
                                'isOperating' => false,
                                'top'         => false,
                            ];
                            $userInfo->notify(new EventChannel($message));

                            \Log::info('App Notify', ['status' => $this->sendNotify([$user], $hiTeachMessage, $message['title'], $groupInfo->notify_status)]);
                        }
                    }

                    // 發送給管理員通知
                    if ($request->has('signature')) {
                        $userAdminIds = $groupInfo->users()->where('member_duty', DutyType::Admin)->get();
                        $message      = [
                            'channel_id'  => GlobalPlatform::convertGroupIdToChannelId($groupInfo->id),
                            'title'       => __('notification.Member', ['name' => $userInfo->name]),
                            'content'     => $userInfo->name . " ($userInfo->habook) " . __('notification.Join'),
                            'url'         => getenv('SOKRADEO_URL') . '/exhibition/tbavideo#/myChannel/' . GlobalPlatform::convertGroupIdToChannelId($groupInfo->id),
                            'isOperating' => false,
                            'top'         => false,
                        ];
                        $userAdminIds->each(function ($userAdminId) use ($message) {
                            $userAdminId->notify(new EventChannel($message));
                        });
                    }

                    // 發送給加入頻道的使用者
                    Log::info('sendNotify', [
                        'status'   => GlobalPlatform::sendNotify($groupInfo->id, [$userInfo->id], NotificationMessageType::JOIN),
                        'group_id' => $request->input('group_id'), 'user_ids' => $userInfo->id
                    ]);
                }
                // 操作紀錄
                $this->recordService->create([
                    'type'     => RecordType::CREATE,
                    'user_id'  => auth()->id(),
                    'group_id' => $request->groupId,
                    'user'     => $userInfo->id
                ]);
            }
        } catch (Exception $exception) {
            return $this->fail(['message' => __('response.fail')]);
        }
        return $this->setStatus(201)->success(['message' => __('response.success')]);
    }

    /**
     * 移除頻道使用者
     *
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function destroy(Request $request, $userId): JsonResponse
    {
        try {
            $channel_id = GlobalPlatform::convertGroupIdToChannelId($request->groupId);
            $userInfo   = $this->userService->getUser($userId);

            if ($userInfo->where('group_channel_id', $channel_id)->exists()) {
                $userInfo->group_channel_id = null;
                $userInfo->save();
            }
            $this->userService->getUser($userId)->groups()->detach($request->groupId);
        } catch (\Exception $exception) {
            $this->fail(['message' => __('response.fail')]);
        }

        return $this->setStatus(204)->success(['message' => __('response.success')]);
    }
}
