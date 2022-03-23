<?php

namespace App\Console\Commands;

use App\Services\NotificationMessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class RemoveNotificationCommand extends Command
{
    protected $signature = 'notifications:remove';

    protected $description = '定期刪除過期通知';
    /**
     * @var DatabaseNotification
     */
    protected $databaseNotification;
    /**
     * @var NotificationMessageService
     */
    protected $notificationMessageService;

    /**
     * @param DatabaseNotification $databaseNotification
     * @param NotificationMessageService $notificationMessageService
     */
    public function __construct(DatabaseNotification $databaseNotification, NotificationMessageService $notificationMessageService)
    {
        parent::__construct();
        $this->databaseNotification       = $databaseNotification;
        $this->notificationMessageService = $notificationMessageService;
    }


    public function handle()
    {
        $this->notificationMessageService->findWhere([['validity', '<', Carbon::now()->format('Y-m-d')]])->each(function ($q) {
            $this->databaseNotification->query()->whereJsonContains('data->notification_message_id', $q->id)->delete();
            $q->delete();
        });
        $this->notificationMessageService->findWhere([['validity', null]])->where('created_at', '<', Carbon::now()->subDay(7))->each(function ($q) {
            $this->databaseNotification->query()->whereJsonContains('data->notification_message_id', $q->id)->delete();
            $q->delete();
        });
        $this->databaseNotification->query()->whereNull('data->notification_message_id')->where('created_at', '<', Carbon::now()->subDay(7))->delete();
    }
}
