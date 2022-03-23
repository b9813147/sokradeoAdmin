<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\Groups\RatingController;
use App\Http\Resources\RatingCollection;
use App\Models\Rating;
use App\Services\GroupChannelContentService;
use App\Services\RatingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RatingControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $application = app(RatingService::class);
        $show        = $application->getByUseStatistics(['groups_id' => 7]);

        $data = [
            "id"                    => 32,
            "groups_id"             => 7,
            "type"                  => 5,
            "name"                  => "佳作",
            "created_at"            => "2020-08-17 17:20:35",
            "updated_at"            => "2020-09-14 17:27:52",
            "group_channel_content" => [],
        ];

        $this->assertEquals($show->first()->toArray(), $data);

        $this->assertTrue(true);
    }
}
