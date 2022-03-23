<?php


namespace App\Helpers\Custom;


use App\Enums\NotificationMessageType;
use App\Models\Districts;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\GroupUser;
use App\Models\Locale;
use App\Models\Semester;
use App\Notifications\EventChannel;
use App\Services\NotificationMessageService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

trait GlobalPlatform

{
    /**
     * 群組ID 轉頻道ID
     *
     * @param int $group_id
     * @return GroupChannel
     */
    public static function convertGroupIdToChannelId(int $group_id)
    {
        return (int)GroupChannel::where('group_id', $group_id)->pluck('id')->first();
    }

    /**
     * 頻道ID 轉 群組ID
     *
     * @param integer $channelId
     * @return mixed
     */
    public static function convertChannelIdToGroupId(int $channelId)
    {
        return GroupChannel::where('id', $channelId)->pluck('group_id')->first();
    }

    /**
     * 學校代碼 轉 群組ID
     *
     * @param $school_code
     * @return mixed
     */
    public static function convertSchoolCodeToGroupId($school_code)
    {
        return Group::where('school_code', $school_code)->pluck('id')->first();
    }

    /**
     * 學校簡碼 轉  Channel ID
     *
     * @param $abbr
     * @return mixed
     */
    public static function convertAbbrToChannelId($abbr)
    {
        return Group::with('channels')->firstWhere('abbr', $abbr)->channels->id ?? false;
    }

    /**
     * 學校簡碼 轉  Group ID
     *
     * @param $abbr
     * @return mixed
     */
    public static function convertAbbrToGroupId($abbr)
    {
        return Group::with('channels')->firstWhere('abbr', $abbr)->id ?? null;
    }

    /**
     *  轉換 年級
     *
     * @param integer $stage
     * @param integer $grade
     * @return int|null
     */
    public static function convertGrade(int $stage, int $grade): ?int
    {
        switch ($stage) {
            case 2:
                return $grade <= 6 ? $grade : null;
            case 3:
                return $grade <= 3 ? $grade + 6 : null;
            case 4:
            case 5:
                return $grade <= 3 ? $grade + 9 : null;
            default :
                return $grade;
        }
    }

    /**
     * 將語系 字串轉換與DB相同
     *
     * @return integer
     */
    public static function getConvertByLangStringForId()
    {
        switch (Redis::get('local')) {
            case 'en-US':
                return Locale::query()->where('type', 'en-US')->pluck('id')->first();
            case 'zh-TW':
                return Locale::query()->where('type', 'zh-TW')->pluck('id')->first();
            case 'zh-CN':
                return Locale::query()->where('type', 'zh-CN')->pluck('id')->first();
        }
    }

    /**
     * 取得頻道使用者身份
     *
     * @param int $group_id
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder|\LaravelIdea\Helper\App\Models\_GroupUserQueryBuilder|mixed
     */
    public static function getMemberDuty(int $group_id, int $userId)
    {
        return GroupUser::query()->where('group_id', $group_id)
            ->where('user_id', $userId)
            ->where('member_status', 1)
            ->value('member_duty');
    }

    /**
     * 無效不顯示  invalid (0, 0) 1 取消無效不顯示
     * 頻道內觀摩  valid (1, 0)  2
     * 全平台分享  share (1, 1) 3
     * 尚待審核中  pending (2, 0) 4
     * @param integer $statusId
     * @return array
     */
    public static function convertChannelStatusToSql(int $statusId): array
    {
        switch ($statusId) {
            case 1:
                $status = [
                    'content_status' => 0,
                    'content_public' => 0
                ];
                break;
            case 2:
                $status = [
                    'content_status' => 1,
                    'content_public' => 0
                ];
                break;
            case 3:
                $status = [
                    'content_status' => 1,
                    'content_public' => 1
                ];
                break;
            default:
                $status = [
                    'content_status' => 2,
                    'content_public' => 0
                ];
                break;
        }
        return $status;
    }

    /**
     * 學區簡碼 轉 群組ID
     * @param string $abbr
     * @return mixed
     */
    public static function DistrictShortCodeFindGroupIds(string $abbr)
    {
        return Districts::query()->where('abbr', $abbr)->get()->first()->groups->map(function ($q) {
            return $q->id;
        });
    }

    /**
     * 學區Id 找群組ID
     * @param int $districtId
     * @return mixed
     */
    public static function DistrictFindGroupIds(int $districtId)
    {
        return Districts::query()->find($districtId)->groups->map(function ($q) {
            return $q->id;
        });
    }

    public static function convertGroupIdToDistrictId(int $group_id)
    {
        return Group::query()->find($group_id)->districts->pluck('id')->first();
    }

    /**
     * 取當前學期
     * @param int $group_id
     * @return int
     */
    public static function getCurrentSemester(int $group_id): int
    {
        $semesters = Semester::query()->select('month')->where('group_id', $group_id)->get()->pluck('month')->toArray();
        return self::getNearest($semesters, Carbon::now()->month);
    }

    /**
     * 取最近的數值
     *
     * @param array $arr
     * @param int $search
     * @return int
     */
    public static function getNearest(array $arr, int $search): int
    {
        sort($arr);
        $closest = 0;
        foreach ($arr as $item) {
            if ($closest === 0 || abs($search - $closest) > abs($item - $search)) $closest = $item;
        }
        return $closest;
    }

    /**
     * 送通知
     * @param int $group_id
     * @param array $user_ids
     * @param int $type
     * @return bool
     */
    public static function sendNotify(int $group_id, array $user_ids, int $type): bool
    {
        $isSuccessful = true;
        try {
            $notification_message = app(notificationMessageService::class)->firstWhere(['group_id' => $group_id, 'type' => $type]);
            if ($type === NotificationMessageType::JOIN && $notification_message === null) {
                $notification_message = app(notificationMessageService::class)->firstWhere(['group_id' => null, 'type' => NotificationMessageType::JOIN]);
            }

            $json_decode_message                            = json_decode($notification_message->content, true);
            $json_decode_message['notification_message_id'] = $notification_message->id;
            app(userService::class)->findWhereIn('id', $user_ids)->map(function ($user) use ($notification_message, $json_decode_message) {
                if ($user->notifications()->whereJsonContains('data->notification_message_id', $notification_message->id)->get()->isEmpty()) {
                    $user->notify(new EventChannel($json_decode_message));
                }
            });
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }
        return $isSuccessful;
    }

    /**
     * 單位換算
     * @param int $bytes
     * @return string
     */
    public static function formatSizeUnits(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * 依據條件顯示影片 並排除重複的
     * 針對數據畫像使用
     * @param \Illuminate\Support\Collection $tbaIds
     * @param array $content_public
     * @param array $content_status
     * @return GroupChannelContent|\Illuminate\Database\Eloquent\Builder|int
     */
    public static function getGroupChannelContentForTotalAndTbaIds(\Illuminate\Support\Collection $tbaIds, array $content_public, array $content_status)
    {
        $select = "group_concat(distinct(content_id)) tba_ids,count(content_id) as total";
        return GroupChannelContent::query()->selectRaw($select)->distinct('content_id')->whereIn('content_id', $tbaIds)
            ->whereIn('content_public', $content_public)
            ->whereIn('content_status', $content_status)->get()->first();

    }

    /**
     * 取得影片長度
     * @param $full_video_path
     * @return int
     */
    public static function getDuration($full_video_path): int
    {
        if (!is_file($full_video_path)) {
            return 0;
        }

        $getID3           = new \getID3;
        $file             = $getID3->analyze($full_video_path);
        $playtime_seconds = $file['playtime_seconds'];
//        $duration = date('H:i:s.v', $playtime_seconds);
        return (int)$playtime_seconds;
    }
}
