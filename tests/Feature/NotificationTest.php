<?php

namespace Tests\Feature;

use App\Enums\NotificationMessageType;
use App\Helpers\Custom\GlobalArr;
use App\Helpers\Custom\GlobalPlatform;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\NotificationMessage;
use App\Models\User;
use App\Notifications\EventChannel;
use App\Services\GroupChannelService;
use App\Services\GroupService;
use App\Services\NotificationMessageService;
use App\Services\TbaService;
use App\Services\UserService;
use App\Types\App\NotificationType;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCommand()
    {
//        $message = [
//            'title'      => '2018全国醍摩豆杯智慧课堂创新团队竞赛(个人组)',
//            'content'    => '2018全国醍摩豆杯智慧课堂创新团队竞赛(个人组)',
//            'group_id'   => 8,
//            'channel_id' => 8,
//            'thumbnail'  => 'thum.png',
//            'top'        => false,
//            'link'       => '/activity-channel/' . 8,
//        ];
//        Redis::rpush(getenv('REDIS_NOTIFICATION'), json_encode($message));

        $count = Redis::llen(getenv('REDIS_NOTIFICATION'));

        if ($count > 0) {

            for ($i = 0; $i <= $count; $i++) {
                $task = Redis::rpop(getenv('REDIS_NOTIFICATION'));
                if (!$task) {
                    break;
                }

                // 取出 Core Service 回傳資料
//                $result        = collect(json_decode($task, true))->toArray();
                $result = collect(json_decode($task, true));

                $channel_ids   = $result->get('channel_ids') ? explode(',', $result->get('channel_ids')) ?? null : null;
                $district_ids  = $result->get('district_ids') ? explode(',', $result->get('district_ids')) ?? null : null;
                $team_mode_ids = $result->get('team_model_ids') ? explode(',', $result->get('team_model_ids')) ?? null : null;

                if ($channel_ids === null && $district_ids === null && $team_mode_ids === null) {
                    dd(1);
                    // 發送全部
//                    $this->userService->findWhere([['habook', '!=', 'null']])->each(function ($q) use ($result) {
//                        $q->notify(new EventChannel($result->toArray()));
//                    });
                } else {
                    // 發送頻道成員
                    if ($channel_ids) {
                        dd(2);
//                        $user_ids = $this->groupChannelService->getChannelForUser($channel_ids)->toArray();
//                        $this->userService->findWhereIn('id', $user_ids)->each(function ($q) use ($result) {
//                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
//                        });
                    }
                    // 發送指定成員
                    if ($team_mode_ids) {
                        dd(3);
//                        $this->userService->findWhereIn('habook', $team_mode_ids)->each(function ($q) use ($result) {
//                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
//                        });
                    }
                    // 發送學區成員
                    if ($district_ids) {
                        dd(4);
//                        $user_ids = $this->districtUserService->findWhereIn('districts_id', $district_ids)->pluck('user_id')->toArray();
//                        $this->userService->findWhereIn('id', $user_ids)->each(function ($q) use ($result) {
//                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
//                        });
                    }
                }
//                User::query()->whereNotNull('habook')->each(function ($q) use ($result) {
//                    $q->notify(new EventChannel($result));
//                });
            }
        }
    }

    public function testSend()
    {
        $group       = Group::query()->find(94);
        $json_decode = json_decode($group->event_data);
//        dd($json_decode->ap);
        dd(collect($json_decode->ap)->where('value', true));

        $userInfo = User::query()->find(948);
        $message  = [
            'title'   => 'ares-test',
            'content' => '你已被加入頻道',
            'license' => false,
        ];
        $userInfo->notify(new EventChannel($message));
        dd();
    }

    public function testGetNotifications()
    {
        dd(app(notificationMessageService::class)->firstWhere(['group_id' => null, 'type' => 1]));

        app(notificationMessageService::class)->findWhere(['type' => 1, 'validity' => Carbon::now()->format('Y-m-d')])->each(function ($notificationMessage) {
            $users   = app(groupService::class)->getGroupInfo($notificationMessage->group_id)->users;
            $content = json_decode($notificationMessage->content, true);

            $users->map(function ($user) use ($content, $notificationMessage) {
                if ($user->notifications()->whereJsonContains('data->notification_message_id', $notificationMessage->id)->get()->isEmpty()) {
                    $user->notify(new EventChannel($content));
                }
            });
        });

//        dd($group->notificationMessages->where(['type' => 1, ['validity', '=', Carbon::now()->format('Y-m-d')]]));
//        $content = json_decode(app(NotificationMessage::class)->firstWhere(['group_id' => 7, 'type' => NotificationMessageType::Join])->content)->content;
//        $content = app(NotificationMessage::class)->firstWhere(['group_id' => 7, 'type' => NotificationMessageType::Join])->content;
//        dd($content);
    }

    public function testSendNotification()
    {
        $channel_id                                     = 7;
        $groupChannel                                   = GroupChannel::query()->select('notification_message_data->notification_message_id as notification_message_id')->findOrFail($channel_id);
        $notification_message_id                        = (int)$groupChannel->notification_message_id;
        $notification_message                           = NotificationMessage::query()->findOrFail($notification_message_id);
        $json_decode_message                            = json_decode($notification_message->content, true);
        $json_decode_message['notification_message_id'] = $notification_message->id;

        $user_ids = [7];
        app(UserService::class)->findWhereIn('id', $user_ids)->map(function ($user) use ($notification_message_id, $json_decode_message) {
            if ($user->notifications()->whereJsonContains('data->notification_message_id', $notification_message_id)->get()->isEmpty()) {
                $user->notify(new EventChannel($json_decode_message));
            }
        });

    }

    public function testNotifyType()
    {
        $app = app(TbaService::class);
        dd($app->sendNotifyForCustom(13,3));

         app(notificationMessageService::class)->findWhere([['validity', '<', Carbon::now()->format('Y-m-d')]])->each(function ($q){
             DatabaseNotification::query()->whereJsonContains('data->notification_message_id', $q->id)->delete();
             $q->delete();
        });
//        dd($where->toArray());
//        $notify = DatabaseNotification::query()->whereJsonContains('data->notification_message_id', 61)->delete();
        $notify = DatabaseNotification::query()->whereJsonContains('data->notification_message_id', 5)->count();
        dd($notify);
        dd(NotificationMessageType::list()->forget([0, 4]));
    }
}
