<?php

namespace App\Console\Commands;

use App\Services\GroupService;
use Illuminate\Console\Command;

class EventChannel extends Command
{
    protected $signature = 'event-channel:run';

    protected $description = '更新活動頻道狀態';
    /**
     * @var GroupService
     */
    protected $groupService;

    /**
     * EventChannel constructor.
     * @param GroupService $groupService
     */
    public function __construct(GroupService $groupService)
    {
        parent::__construct();
        $this->groupService = $groupService;
    }

    public function handle()
    {
        $this->groupService->setEventChannel();
    }
}
