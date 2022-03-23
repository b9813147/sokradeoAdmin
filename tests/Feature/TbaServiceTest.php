<?php

namespace Tests\Feature;

use App\Models\GroupChannel;
use App\Models\User;
use App\Services\GroupChannelService;
use App\Services\TbaService;
use App\Services\UserService;
use Tests\TestCase;

class TbaServiceTest extends TestCase
{
    public function testSendNotifyForCustom()
    {
        $tbaService          = app(TbaService::class);
        $sendNotifyForCustom = $tbaService->sendNotifyForCustom(9889, 132);
        dump($sendNotifyForCustom);
        $this->assertTrue($sendNotifyForCustom);
    }

    public function testGroupChannel()
    {
        $groupChannelService = app(GroupChannelService::class);
        $userService         = app(UserService::class);
        $channelForUser      = $groupChannelService->getChannelForUser(['131']);
//        $haBooks             = User::query()->whereIn('id', $channelForUser)->pluck('habook');

        $users = $userService->findWhereIn('id', $channelForUser->toArray(), ['habook'])->map(function ($item) {
            return $item['habook'];
        })->toArray();
        dd($users);
        $this->assertEquals(GroupChannel::class, $channelForUser);
    }
}
