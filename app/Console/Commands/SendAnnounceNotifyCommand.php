<?php

namespace App\Console\Commands;

use App\Notifications\EventChannel;
use App\Services\GroupService;
use App\Services\NotificationMessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAnnounceNotifyCommand extends Command
{
    protected $signature = 'notifications:announce';

    protected $description = '發送公告訊息';
    /**
     * @var GroupService
     */
    protected $groupService;
    /**
     * @var NotificationMessageService
     */
    protected $notificationMessageService;


    public function __construct(GroupService $groupService, NotificationMessageService $notificationMessageService)
    {
        parent::__construct();
        $this->groupService               = $groupService;
        $this->notificationMessageService = $notificationMessageService;
    }

    public function handle()
    {
        $this->notificationMessageService->findWhere(['type' => 1, 'validity' => Carbon::now()->format('Y-m-d')])->each(function ($notificationMessage) {
            $users   = $this->groupService->getGroupInfo($notificationMessage->group_id)->users;
            $content = json_decode($notificationMessage->content, true);

            $users->map(function ($user) use ($content, $notificationMessage) {
                if ($user->notifications()->whereJsonContains('data->notification_message_id', $notificationMessage->id)->get()->isEmpty()) {
                    $user->notify(new EventChannel($content));
                }
            });
        });
    }
}
