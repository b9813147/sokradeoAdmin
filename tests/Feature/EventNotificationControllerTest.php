<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EventNotificationControllerTest extends TestCase
{
//    use DatabaseTransactions;

    public function testBasic()
    {
        Passport::actingAs(User::query()->find(948), ['*']);
        $response = $this->post('api/group/notification', [
            'content'  => json_encode([
                "top"           => false,
                "url"           => "rr",
                "link"          => null,
                "title"         => "test",
                "content"       => "testestestes",
                "isReview"      => false,
                "channel_id"    => null,
                "channel_ids"   => null,
                "isOperating"   => false,
                "district_ids"  => null,
                "teamModel_ids" => null
            ]),
            'status'   => 1,
            'validity' => Carbon::now(),
            'group_id' => 573,
            'type'     => 5
        ]);

        $response->dump();
        $response->assertStatus(200);
    }
}
