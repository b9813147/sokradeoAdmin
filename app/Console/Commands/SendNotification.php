<?php

namespace App\Console\Commands;

use App\Helpers\CoreService\CoreServiceApi;
use App\Notifications\EventChannel;
use App\Services\DistrictUserService;
use App\Services\GroupChannelService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SendNotification extends Command
{
    use CoreServiceApi;

    protected $signature = 'notifications:send';

    protected $description = '發送 通知訊息 ';
    /**
     * @var UserService
     */
    protected $userService;
    /**
     * @var GroupChannelService
     */
    protected $groupChannelService;
    /**
     * @var DistrictUserService
     */
    protected $districtUserService;

    /**
     * SendNotification constructor.
     * @param UserService $userService
     * @param GroupChannelService $groupChannelService
     * @param DistrictUserService $districtUserService
     */
    public function __construct(UserService $userService, GroupChannelService $groupChannelService, DistrictUserService $districtUserService)
    {
        parent::__construct();
        $this->userService         = $userService;
        $this->groupChannelService = $groupChannelService;
        $this->districtUserService = $districtUserService;
    }


    public function handle()
    {
        $count = Redis::llen(getenv('REDIS_NOTIFICATION'));

        $bar = $this->output->createProgressBar($count);

        if ($count > 0) {
            for ($i = 0; $i <= $count; $i++) {
                $task = Redis::rpop(getenv('REDIS_NOTIFICATION'));
                if (!$task) {
                    break;
                }
                // 取出 Core Service 回傳資料
                $result         = collect(json_decode($task, true));
                $channel_ids    = $result->get('channel_ids') ? explode(',', $result->get('channel_ids')) ?? null : null;
                $district_ids   = $result->get('district_ids') ? explode(',', $result->get('district_ids')) ?? null : null;
                $team_model_ids = $result->get('team_model_ids') ? explode(',', $result->get('team_model_ids')) ?? null : null;

                $hiTeachMessage = json_encode([
                    'content' => $result->get('content'),
                    'action'  => [
                        [
                            'type'          => 'click',
                            'label'         => __('video-upload-message.click'),
                            'url'           => getenv('SOKRADEO_URL') . '/exhibition/tbavideo/check-with-habook/?to=' . base64_encode($result->get('link')) . '&ticket=',
                            'tokenbindtype' => 1
                        ]
                    ],
                ]);

                if ($channel_ids === null && $district_ids === null && $team_model_ids === null) {
                    // 發送全部
                    $team_model_ids = $this->userService->findWhere([['habook', '!=', 'null']])->map(function ($q) use ($result) {
                        $q->notify(new EventChannel($result->toArray()));
                        return $q->habook;
                    });
                    // Send to mobile
                    $this->sendNotify($team_model_ids, $hiTeachMessage, $result->get('title'));
                } else {
                    // 發送頻道成員
                    if ($channel_ids) {
                        $user_ids       = $this->groupChannelService->getChannelForUser($channel_ids)->toArray();
                        $team_model_ids = $this->userService->findWhereIn('id', $user_ids)->map(function ($q) use ($result) {
                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
                            return $q->habook;
                        })->toArray();
                        // Send to mobile
                        $this->sendNotify($team_model_ids, $hiTeachMessage, $result->get('title'));
                    }
                    // 發送指定成員
                    if ($team_model_ids) {
                        $this->userService->findWhereIn('habook', $team_model_ids)->each(function ($q) use ($result) {
                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
                        });
                        $this->sendNotify($team_model_ids, $hiTeachMessage, $result->get('title'));
                    }
                    // 發送學區成員
                    if ($district_ids) {
                        $user_ids       = $this->districtUserService->findWhereIn('districts_id', $district_ids)->pluck('user_id')->toArray();
                        $team_model_ids = $this->userService->findWhereIn('id', $user_ids)->map(function ($q) use ($result) {
                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
                            return $q->habook;
                        })->toArray();
                        // Send to mobile
                        $this->sendNotify($team_model_ids, $hiTeachMessage, $result->get('title'));
                    }
                }
                $bar->advance();
            }
        }
        $bar->finish();
    }
}
