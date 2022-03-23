<?php

namespace App\Services;

use App\Helpers\CoreService\CoreServiceApi;
use App\Helpers\Custom\GlobalPlatform;
use App\Notifications\EventChannel;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use App\Services\Library\BbService;
use App\Types\Group\DutyType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GroupService extends BaseService
{
    use CoreServiceApi;

    protected $repository;
    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var BbService
     */
    protected $bbService;

    public function __construct(GroupRepository $groupRepository, UserService $userService, BbService $bbService)
    {
        $this->repository  = $groupRepository;
        $this->userService = $userService;
        $this->bbService   = $bbService;
    }

    /**
     * 頻道名稱
     * @param array $column
     * @return GroupRepository[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getGroupList(array $column = ['*'])
    {
        return $this->repository->getGroupList($column);
    }

    /**
     * 用channelId 去找 groupID
     *
     * @param $channelId
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getChannelByGroupId($channelId)
    {
        return $this->repository->getChannelByGroupId($channelId);
    }

    public function getGroupById($group_id)
    {
        return $this->repository->find($group_id);
    }

    /**
     *  同時更新 groups 與 groupChannels
     *
     * @param array $data
     * @param $files
     * @param $name
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public function updateGroupAndGroupChannel(array $data, $files, $name)
    {
        return $this->repository->updateGroupAndGroupChannel($data, $files, $name);
    }

    /**
     * 更新活動內容
     * @param int $group_id
     * @param string $event_data
     * @return bool
     */
    public function updateGroupEventData(int $group_id, string $event_data): bool
    {
        try {
            $this->repository->updateBy('id', $group_id, ['event_data' => $event_data]);
        } catch (\Exception $exception) {
            Log::debug($exception->getMessage());
            return false;
        }
        return true;

    }

    /**
     * 頻道數量
     * @param array $public
     * @return int
     */
    public function getGroupTotal(array $public)
    {
        return $this->repository->groupTotal($public);
    }

    /**
     * 取得頻道使用者資訊
     *
     * @param $group_id
     * @return \App\Models\Group|\App\Models\Group[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getUserInfo($group_id)
    {
        return $this->repository->getUserInfo($group_id);
    }

    /**
     * 取得頻道相關細部資訊
     * @param $group_id
     * @return \App\Models\Group|\App\Models\Group[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getGroupInfo($group_id)
    {
        return $this->repository->getGroupInfo($group_id);
    }

    /**
     * 更改活動狀態
     * @return bool
     */
    public function setEventChannel(): bool
    {
        return $this->repository->setEventChannel();
    }


    /**
     * 加入頻道
     *
     * @param Collection $data
     * @return array
     */
    public function join(Collection $data): array
    {
        $result = [
            'status'  => 204,
            'message' => null
        ];
        try {
            $data->each(function ($item) use (&$result) {
                $item        = (object)$item;
                $member_data = [];
                $member_data = [
                    'member_duty'   => $item->duty,
                    'member_status' => 1
                ];
                $userInfo    = $this->userService->firstBy('habook', $item->teammodel_id);
                $groupInfo   = $this->repository->firstWhere(['abbr' => $item->abbr]);

                // 檢查頻道存不存
                if (!$this->repository->exists(['abbr' => $item->abbr])) {
                    $result = [
                        'status'  => 422,
                        'message' => 'School does not exist'
                    ];
                    return;
                }
                // CoreService API to check if the user exists
                if (!$this->isExist($item->teammodel_id)) {
                    $result = [
                        'status'  => 422,
                        'message' => $item->teammodel_id . ' Does not exist'
                    ];
                    return;
                }
                // 新用戶加入
                if (!$userInfo) {
                    $userInfo = $this->userService->loginAsHaBook($item->teammodel_id,
                        [
                            'habook'           => $item->teammodel_id, 'name' => $item->name,
                            'group_channel_id' => GlobalPlatform::convertGroupIdToChannelId($groupInfo->id)
                        ]);
                    $this->userService->setUser($userInfo->id, [], [6]);
                }

                // 確認使用者為尚未加入
                if (!$groupInfo->users()->where('habook', $item->teammodel_id)->exists()) {
                    $groupInfo->users()->attach($userInfo->id, $member_data);

                    // 頻道人數
                    $userTotal = $groupInfo->users()->where('member_duty', DutyType::General)->count();
                    // 僅限活動頻道
                    $this->eventChannelByLicense($groupInfo, $item, $userTotal, $userInfo);
                }
            });
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            Log::info($data);
            $result = [
                'message' => $exception->getMessage(),
                'status'  => 401
            ];
        }

        return $result;
    }

    /**
     * @param string $abbr
     * @param array $teamModel_ids
     * @return array
     */
    public function removeJoin(string $abbr, array $teamModel_ids): array
    {
        $result = [
            'status'  => 204,
            'message' => null
        ];
        try {
            // 檢查頻道存不存
            if (!$this->repository->exists(['abbr' => $abbr])) {
                return [
                    'status'  => 422,
                    'message' => 'School does not exist'
                ];
            }
            $groupInfo = $this->repository->firstWhere(['abbr' => $abbr]);
            $user_ids  = $this->userService->findWhereIn('habook', $teamModel_ids)->pluck('id')->toArray();
            $groupInfo->users()->detach($user_ids);
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            $result = [
                'message' => $exception->getMessage(),
                'status'  => 401
            ];
        }

        return $result;
    }

    public function updateJoin(string $abbr, Collection $data): array
    {
        $result = [
            'status'  => 204,
            'message' => null
        ];
        try {
            $data->each(function ($item) use ($abbr, &$result) {
                $item        = (object)$item;
                $member_data = [
                    'member_duty'   => $item->duty,
                    'member_status' => 1
                ];
                $userInfo    = $this->userService->firstBy('habook', $item->teammodel_id);
                $groupInfo   = $this->repository->firstWhere(['abbr' => $abbr]);

                // 檢查頻道存不存
                if (!$this->repository->exists(['abbr' => $abbr])) {
                    $result = [
                        'status'  => 422,
                        'message' => 'School does not exist'
                    ];
                    return;
                }
                // CoreService API to check if the user exists
                if (!$this->isExist($item->teammodel_id)) {
                    $result = [
                        'status'  => 422,
                        'message' => $item->teammodel_id . ' Does not exist'
                    ];
                    return;
                }
                // 新用戶加入
                if ($userInfo) {
                    $userInfo->groups()->updateExistingPivot($groupInfo->id, $member_data);
                }
            });
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
            $result = [
                'message' => $exception->getMessage(),
                'status'  => 401
            ];
        }

        return $result;
    }

    /**
     * 取得活動頻道序號
     *
     * @param object $groupInfo
     * @param object $item
     * @param int $userTotal
     * @param object $userInfo
     */
    private function eventChannelByLicense(object $groupInfo, object $item, int $userTotal, object $userInfo): void
    {
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

        if ($groupInfo->public != 0 && $item->duty === DutyType::General) {
            // 取得授權時間
            $event_data = json_decode($groupInfo->event_data);
            //專屬頻道
//                        $group_id = $userInfo->group_channel_id ? GlobalPlatform::convertChannelIdToGroupId($userInfo->group_channel_id) : null;
//                        $group    = $userInfo->groups->firstWhere('id', $group_id);

            // 僅限活動頻道
            if (!empty($event_data) && (bool)($event_data->enableTrial) && $userTotal < $event_data->maxParticipant) {
                $schoolCode  = ($groupInfo->school_code . $groupInfo->name) ?? null;
                $LicenseData = [
                    "id"          => $userInfo->teammodel_id,                    //醍摩豆帳號
                    "name"        => $userInfo->name ?? $userInfo->teammodel_id, //姓名
                    "productCode" => $event_data->productCode,                   //產品八碼
                    "trialDay"    => $event_data->trialDeadline,                 //申請試用天數
                    "cqty"        => $event_data->cqty,                          //授權數
                    "schoolCode"  => $event_data->school_code ?? null,           //學校簡碼 ※可不填
                    "schoolName"  => $groupInfo->name ?? null,                   //學校名稱 ※可不填
                    "ap"          => collect($event_data->ap)->where('val', true), //附加功能
                ];
                // App data
                $hiTeachMessage = json_encode([
                    'content' => $LicenseData,
                ]);
                // 取得授權
                $licence = $this->bbService->getLicence($schoolCode, $LicenseData);
                Log::info('License', [$licence]);
                $licence->serial = $licence->serial ?? null;
                // 紀錄序號
                $groupInfo->updateExistingPivot($groupInfo->id, ['user_data' => json_encode(['license' => $licence->serial])]);
                $message = [
                    'channel_id'  => GlobalPlatform::convertGroupIdToChannelId($groupInfo->id),
                    'title'       => __('license.title'),
                    'content'     => __('license.content') . __('license.license') . $licence->serial . "\n\n" . __('license.expire') . $event_data->trialDeadline,
                    'isOperating' => false,
                    'top'         => false,
                ];
                $userInfo->notify(new EventChannel($message));
                \Log::info('App Notify', ['status' => $this->sendNotify([$userInfo->teammodel_id], $hiTeachMessage, $message['title'], $groupInfo->notify_status)]);
            }
        }
    }
}
