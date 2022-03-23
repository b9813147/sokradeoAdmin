<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\V1\Events\EventController;
use App\Http\Controllers\Api\V1\Groups\ChannelContentController;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupUser;
use App\Models\Tba;
use App\Models\TbaPlaylistTrack;
use App\Models\User;
use App\Notifications\EventChannel;
use App\Services\AnnexService;
use App\Services\DistrictUserService;
use App\Services\GroupChannelContentService;
use App\Services\GroupChannelService;
use App\Services\GroupService;
use App\Services\UserService;
use App\Types\Auth\AccountType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use phpDocumentor\Reflection\Types\String_;
use Tests\TestCase;

class EventsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $application = app(ChannelContentController::class);
        $model       = Tba::with([
            'tbaAnnexs' => function ($q) {
                $q->select('id');
            }
        ])->first();
        dd($model->toArray());

        $builder = Group::with([
            'users' => function ($q) {
                $q->select('name');
            }
        ])->first();
        dd($builder->toArray());
        $data = [
            'tba_id' => '', 'type' => '', 'idx' => ''
        ];


        dd($application->editScre());


        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testAddEventChannel()
    {

        $school_code = '222222';
        $name        = 'test222222';
        $group_id    = Group::query()->create([
            'school_code'   => $school_code,
            'name'          => $name,
            'description'   => '',
            'thumbnail'     => '',
            'status'        => 1,
            'public'        => 1,
            'review_status' => 1,
        ])->id;

        GroupChannel::query()->create([
            'group_id'    => $group_id,
            'cms_type'    => 'TbaVideo',
            'name'        => $name,
            'description' => '',
            'thumbnail'   => '',
            'status'      => 1,
            'public'      => 1,
        ]);
    }

    /**
     *  加入頻道成員
     * @return string
     */
    public function testJoinEventUserByChannels()
    {

//        $teamModelId = $request->teamModelId;
//        $name        = $request->name;
//        $member_duty = $request->member_duty;
//        $school_code = $request->school_code;

//        $userData[] = [
//            'teamModeId'  => '0859#0859',
//            'name'        => 'test2',
//            'member_duty' => 'Expert',
//        ];
//        $userData[] = [
//            'teamModeId'  => 'test1',
//            'name'        => 'tess1',
//            'member_duty' => 'Expert',
//        ];
        $duty = 'Admin';
//        $duty       = 'Expert';
        $userData[] = [
            'teamModeId'  => 'test2',
            'name'        => 'test2',
            'member_duty' => $duty,
            'school_code' => [
                2151012988,
//                '123dasdasdasdasd',
//                444444,
            ]
        ];
//        $school_code = [
//            2151012988,
//            123,
//            444444,
//        ];

        if (!is_array($userData)) {
            return 'userData Invalid format';
        }

        $collectionUserData = collect($userData);
        $users              = collect();

        // 檢查teamModelId 是否有綁定 user 如果沒有 則建立user
        $collectionUserData->each(function ($userData) use ($users) {
            $objUserData = (object)$userData;

            if (!empty($objUserData->teamModeId) && !empty($objUserData->member_duty) && !empty($objUserData->name)) {
                $user = User::query()->where('habook', $objUserData->teamModeId)->first();

                if ($user instanceof User) {
                    $user->member_duty = $objUserData->member_duty;
                    $user->school_code = $objUserData->school_code;
                    $users->push($user);
                }

                if (!$user instanceof User) {
                    $user              = User::query()->create([
                        'habook' => $objUserData->teamModeId,
                        'name'   => $objUserData->name,
                    ]);
                    $user->member_duty = $objUserData->member_duty;
                    $user->school_code = $objUserData->school_code;
                    $users->push($user);
                }
            }
        });
//        dd($users->toArray());

        $users->each(function ($user) {
            if ($user instanceof User) {
                if ($user->school_code) {
                    foreach ($user->school_code as $school_code) {
                        $group_id = Group::query()->select('id')->where('public', 1)->where('school_code', $school_code)->first();
                        if (!$group_id instanceof Group) {
                            echo "$school_code is not events channel";
                        }

                        if ($group_id instanceof Group) {
                            GroupUser::query()->updateOrInsert(
                                ['group_id' => $group_id->id, 'user_id' => $user->id,],
                                ['member_status' => 1, 'member_duty' => $user->member_duty, 'created_at' => Carbon::now()->format('Y-m-d h:m:s'), 'updated_at' => Carbon::now()->format('Y-m-d h:m:s')]
                            );
                        }
                    }
                }
            }
        });
//        $group_ids = Group::query()->select('id')->where('public', 1)->whereIn('school_code', $school_code)->get();


//        if ($group_ids->isEmpty()) {
//            return 'School_code is not Exist';
//        }
//
//        // 加入頻道
//        $group_ids->each(function ($group_id) use ($users) {
//            $users->each(function ($user) use ($group_id) {
//                if ($user instanceof User) {
//                    GroupUser::query()->updateOrInsert([
//                        'group_id'      => $group_id->id,
//                        'user_id'       => $user->id,
//                        'member_status' => 1,
//                        'member_duty'   => $user->member_duty,
//                    ]);
//                }
//            });
//        });

        $this->assertTrue(true);
    }

    public function test_training()
    {
        // json
        $data = [
            "eventType"      => "training",
            "startDate"      => "2021-06-15",
            "endDate"        => "2021-08-31",
            "maxParticipant" => 50,
            "stageCount"     => 3,
            "enableTrial"    => 1,
            "trialDeadline"  => "2021-07-31",
            "eventStage"     => [
                "registration" => [
                    "stageOrder" => 1,
                    "startDate"  => "2021-07-01",
                    "endDate"    => "2021-07-05"
                ],
                "reviewing"    => [
                    "stageOrder" => 0,
                    "startDate"  => 0,
                    "endDate"    => 0
                ],

                "submission"    => [
                    "stageOrder"  => 2,
                    "startDate"   => "2021-07-06",
                    "endDate"     => "2021-07-21",
                    "isMultiTask" => 1,
                    "requirement" => [
                        "isDoubleGreen" => 0,
                        "hasMaterial"   => 1,
                        "hasTPC"        => 1
                    ]
                ],
                "certification" => [
                    "stageOrder" => 3,
                    "startDate"  => "2021-07-22",
                    "endDate"    => "2021-07-31"
                ]
            ]
        ];
        // 預設進度狀態
        $stageTypes = collect(['registration', 'reviewing', 'submission', 'certification']);
        // 當前時間
        $current = Carbon::now()->format('Y-m-d');
        $stageTypes->each(function ($v) use ($current) {
            Group::query()->select('event_data->eventStage as eventStage', 'id')->with('channels')->whereNotNull('event_data')
                ->whereNotNull('event_data->eventStage->' . $v . '->stageOrder')
                ->whereNotNull('event_data->eventStage->' . $v . '->endDate')
                ->where('event_data->eventStage->' . $v . '->endDate', '=', '2021-07-05')
                ->get()->each(function ($q) use ($v) {
                    $q->channels()->update(['status' => json_decode($q->eventStage)->$v->stageOrder]);
                });
        });
    }

    public function testEvent()
    {
//        $stageTypes = collect(['registration', 'reviewing', 'submission', 'certification']);mechanism
        // 當前時間
        $current = Carbon::now()->format('Y-m-d');
//        $stageTypes->each(function ($v) use ($current) {
        Group::query()->select('event_data->eventStage as eventStage', 'id')->with('channels')
            ->whereNotNull('event_data')->get()->each(function ($q) use ($current) {
                $eventStage = collect(json_decode($q->eventStage))
                    ->where('endDate', '>=', $current)
                    ->where('stageOrder', '!=', 0)
                    ->sortBy('stageOrder');
                if ($eventStage->isNotEmpty()) {
                    return $q->channels()->update(['status' => $eventStage->first()->stageOrder]);
                }
                return $q->channels()->update(['status' => 3]);
            });

    }

    public function testSend()
    {
        $count = Redis::llen(getenv('REDIS_NOTIFICATION'));


        if ($count > 0) {
            for ($i = 0; $i <= $count; $i++) {
                $task = Redis::rpop(getenv('REDIS_NOTIFICATION'));
                if (!$task) {
                    break;
                }
                // 取出 Core Service 回傳資料
                $result = collect(json_decode($task, true));

                $channel_ids  = $result->get('channel_ids') ? explode(',', $result->get('channel_ids')) ?? null : null;
                $district_ids = $result->get('district_ids') ? explode(',', $result->get('district_ids')) ?? null : null;


                if ($channel_ids === null && $district_ids === null) {
                    // 發送全部
                    $this->userService->findWhere([['habook', '!=', 'null']])->each(function ($q) use ($result) {
                        $q->notify(new EventChannel($result->toArray()));
                    });
                } else {
                    // 發送頻道成員
                    if ($channel_ids) {
                        $user_ids = app(GroupChannelService::class)->getChannelForUser($channel_ids)->toArray();
                        app(UserService::class)->findWhereIn('id', $user_ids)->each(function ($q) use ($result) {
                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
                        });
                    }
                    // 發送學區成員
                    if ($district_ids) {
                        $user_ids = app(DistrictUserService::class)->findWhereIn('districts_id', $district_ids)->pluck('user_id')->toArray();
                        app(UserService::class)->findWhereIn('id', $user_ids)->each(function ($q) use ($result) {
                            if ($q->habook !== null) $q->notify(new EventChannel($result->toArray()));
                        });
                    }
                }
            }
        }
    }
}
