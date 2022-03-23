<?php

namespace App\Console;

use App\Console\Commands\BackupDatabase;
use App\Console\Commands\CreateTbaVideoCommand;
use App\Console\Commands\EventChannel;
use App\Console\Commands\RemoveNotificationCommand;
use App\Console\Commands\SendAnnounceNotifyCommand;
use App\Console\Commands\SendNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        BackupDatabase::class,
        SendNotification::class,
        EventChannel::class,
        SendAnnounceNotifyCommand::class,
        RemoveNotificationCommand::class,
        CreateTbaVideoCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('db:backup')->cron('* 23 * * * *');
        // 每分鐘 發送通知訊息
        $schedule->command('notification:send')->everyMinute()->withoutOverlapping();
        // 每分鐘 發送通知訊息
        $schedule->command('notifications:announce')->everyMinute()->withoutOverlapping();
        // 每分鐘 定期刪除過期通知
        $schedule->command('notifications:remove')->dailyAt('1:00')->withoutOverlapping();
        // 每分鐘 檢查是否有頻道狀態需要修改
        $schedule->command('event-channel:run')->everyMinute()->withoutOverlapping();
        // 每分鐘 檢查是否有需要建立的影片
        $schedule->command('video:create')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
