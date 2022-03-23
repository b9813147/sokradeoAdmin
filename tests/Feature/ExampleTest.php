<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\V1\Groups\QrCodeController;
use App\Models\BbLicense;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupUser;
use App\Models\User;
use App\Services\GroupChannelService;
use App\Services\UserService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\TestCase;

class ExampleTest extends TestCase
{    // 跑完測試復原這些動作
//    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
//        dd(strtoupper(uniqid('test')));
        dd(strtok('testuser'));
        $groupChannelService = app(GroupChannelService::class);
        $channelId           = $groupChannelService->getGroupIdByChannel(1)->id;
        dd($channelId);
        $g = Group::with([
            'users' => function ($q) {
                $q->select('habook', 'name');
            }
        ])->where('id', 7)->get();
//        dd($g->toArray());

        $t = Group::with([
            'users' => function ($q) {
                $q->select('habook', 'name');
            }
        ])->findOrFail(7);


        $u = User::query()->whereHas('groups', function ($q) {
            $q->select('name');
            $q->where('id', 7);
        })->get();
        dd($u->toArray());

        $select = 'users.name as name,
                   groups.name as group_name,
                   group_user.member_duty,
                   group_user.member_status,
                   groups.id as group_id,
                   users.id as user_id,
                   users.habook';

        $t = GroupUser::query()
            ->selectRaw($select)
            ->join('users', 'users.id', 'user_id')
            ->join('groups', 'groups.id', 'group_id')
            ->join('group_channels', 'group_channels.group_id', 'group_user.group_id')
            ->where('groups.id', 7)
            ->toSql();
        dd($t);

        $result = Group::with('channels')
            ->where('id', 7)->first();
        /*$result->update([
            "name"        => "test",
            "description" => "test"
        ]);*/
//        $result->channels->each(function ($item) {
//            dd($item);
//        });
//        dd();
//        $result->channels->update([
//            "name"        => "test",
//            "description" => "test"
//        ]);
        $g = GroupChannel::query()->selectRaw("group_id ,count(*)")->groupBy('group_id')->havingRaw("count(*)>1")->toSql();
        dd($g);
//        dd( Group::with('channels')
//            ->where('id', 7)->get()->toArray());


        if (!$userId) {
            dd(['status' => 404, 'message' => 'habook not found']);
        }


        if (!$exist) {
            dd(['status' => 200, 'message' => 'success']);
        }

//        $response = $this->get('/');
//        $response->assertStatus(200);

//        $api = new Client();
//       $response= $api->get('http://adminvuetify.com/api/group/1', [
//            'headers' => [
//                'Accept' => 'application/vnd.sokradeo.v1+json',
//            ]
//        ]);
//        dd($response->getBody()->getContents());

        $column = ['habook' => '041010276#6dasd853'];

        $teamModelId = '0303#3243123';
//       $u= User::query()->where('habook', $teamModelId)->get('id')->isEmpty();
        $u = User::query()->where($column)->pluck('id')->first();
        dd($u);

        $d = GroupUser::query()
            ->join('users', 'users.id', 'user_id')
            ->join('groups', 'groups.id', 'group_id')
            ->whereNotNull('users.habook')
            ->where($column)->get();
        dd($d->toArray());
    }

    public function testTime()
    {

        $auth   = 'X-Auth-Hash';
        $qrCode = app(QrCodeController::class);
        $user   = app(UserService::class);
//        $this->userService->findBy();
        $result = $user->findBy('habook', '0934161322#');
//        $groupByUser = $group->getGroupByUser(1, 5);
//        $group->updateGroupByUserOrCreate(3, 948, 'Expert');
//        dd(url('api/qrcode/join?group_id=1&duty=admin&end_time=2019-03-13'));


//        GroupUser::query()->updateOrCreate(
//            ['group_id' => 1, 'user_id' => 5],
//            ['member_duty' => 'Admin']
//        );
//        dd($groupByUser->toArray());
//        $isMemberForGroupExist = $group->isMemberForGroupExist([
//            'group_id' => 1,
//            'user_id'  => 5,
//        ]);
//        dd($isMemberForGroupExist);


        dd($qrCode->index());
        //        1907400-1400000 = 507400 + 54180 +  15000 + 20000 +  36400 +8500
        //        637980
        //        644000
        // 台新 4000
        //

    }

    public function testTraining()
    {
        // json
        $data        = [
            "eventType"      => "training",
            "startDate"      => "2021-06-15",
            "endDate"        => "2021-08-31",
            "maxParticipant" => 50,
            "stageCount"     => 3,
            "enableTrial"    => 1,
            "trialDeadline"  => "2021-07-31",
            "eventStage"     => [
                "registration"  => [
                    "stageOrder" => "1",
                    "startDate"  => "2021-07-01",
                    "endDate"    => "2021-07-05"
                ],
                "submission"    => [
                    "stageOrder"  => "2",
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
                    "stageOrder" => "3",
                    "startDate"  => "2021-07-22",
                    "endDate"    => "2021-07-31"
                ]
            ]
        ];
        $json_encode = json_encode($data);
//        Group::query()->select('event_data')->where('public', 1)->update([
//            'event_data' => $json_encode,
//        ]);

//        $groups = Group::query()->select('event_data')->where('public', 1)->update(['event_data'=>null]);
        $groups = Group::query()->select('event_data')->where('public', 1)->take(1)->update(['event_data' => null]);
        dd($groups->toArray());

        $this->assertTrue(true);

    }

    public function testActivity()
    {
        // json
        $data = [
            "eventType"      => "activity",
            "startDate"      => "2021-06-15",
            "endDate"        => "2021-08-31",
            "maxParticipant" => 0,
            "stageCount"     => 3,
            "enableTrial"    => 0,
            "trialDeadline"  => "",
            "eventStage"     => [
                "submission"    => [
                    "stageOrder"  => "1",
                    "startDate"   => "2021-05-15",
                    "endDate"     => "2021-07-12",
                    "isMultiTask" => 0,
                    "requirement" => [
                        "isDoubleGreen" => 1,
                        "hasMaterial"   => 1,
                        "hasTPC"        => 1
                    ]
                ],
                "reviewing"     => [
                    "stageOrder" => "2",
                    "startDate"  => "2021-07-03",
                    "endDate"    => "2021-08-15"
                ],
                "certification" => [
                    "stageOrder" => "3",
                    "startDate"  => "2021-08-15",
                    "endDate"    => ""
                ]
            ]
        ];
        Group::query()->find(7)->update(['event_data' => json_encode($data)]);
    }

    public function testGroup()
    {
        $lang  = 'tw';
        $group = Group::query()->find(878);
        // 學科
        $groupSubjectFields = collect([
            'cn' => [
                ['subject' => '语文', 'alias' => '语文', 'subject_fields_id' => 1, 'order' => 1],
                ['subject' => '数学', 'alias' => '数学', 'subject_fields_id' => 2, 'order' => 2],
                ['subject' => '英语', 'alias' => '英语', 'subject_fields_id' => 7, 'order' => 3],
            ],
            'tw' => [
                ['subject' => '語文', 'alias' => '語文', 'subject_fields_id' => 1, 'order' => 1],
                ['subject' => '數學', 'alias' => '數學', 'subject_fields_id' => 2, 'order' => 2],
                ['subject' => '英語', 'alias' => '英語', 'subject_fields_id' => 1, 'order' => 3],
            ],
            'en' => [
                ['subject' => 'Language', 'alias' => 'Language', 'subject_fields_id' => 1, 'order' => 1],
                ['subject' => 'Math', 'alias' => 'Math', 'subject_fields_id' => 2, 'order' => 2],
                ['subject' => 'English', 'alias' => 'English', 'subject_fields_id' => 1, 'order' => 3],
            ]
        ]);
        // 語系
        $locales_ids = collect([37, 40, 65]);
        // 教研
        $ratings = collect([
            'cn' => [
                ['type' => '1', 'name' => '教研'],
                ['type' => '2', 'name' => '佳作'],
                ['type' => '3', 'name' => '二等'],
                ['type' => '4', 'name' => '一等'],
                ['type' => '5', 'name' => '优等'],
            ],
            'tw' => [
                ['type' => '1', 'name' => '教研'],
                ['type' => '2', 'name' => '佳作'],
                ['type' => '3', 'name' => '二等'],
                ['type' => '4', 'name' => '一等'],
                ['type' => '5', 'name' => '優等'],
            ],
            'en' => [
                ['type' => '1', 'name' => 'Study'],
                ['type' => '2', 'name' => 'Good'],
                ['type' => '3', 'name' => 'Great'],
                ['type' => '4', 'name' => 'Perfect'],
                ['type' => '5', 'name' => 'Excellent'],
            ]
        ]);
        // 學期
        $semesters = collect([
            ['year' => Carbon::now()->year, 'month' => '8', 'day' => '1'],
            ['year' => Carbon::now()->year + 1, 'month' => '2', 'day' => '1']
        ]);


//        $group->groupSubjectFields()->delete();
//        $group->ratings()->delete();
//        $group->groupLangs()->delete();
//        $group->semesters()->delete();
//        dd();
        $groupSubjectFields->each(function ($v, $k) use ($lang, $group) {
            if ($k === $lang) {
                collect($v)->each(function ($v) use ($group) {
                    $group->groupSubjectFields()->create($v);
                });


            }
        });
        $ratings->each(function ($v, $k) use ($lang, $group) {
            if ($k === $lang) {
                collect($v)->each(function ($v) use ($group) {
                    $group->ratings()->create($v);
                });
            }
        });

        $locales_ids->each(function ($v) use ($group) {
            $group->groupLangs()->create(['name' => $group->name, 'description' => $group->description, 'locales_id' => $v,]);
        });

        $semesters->each(function ($v) use ($group) {
            $group->semesters()->create($v);
        });


//        dd($group->groupSubjectFields()->get()->toArray());

//        foreach ($locales_ids as $locales_id) {
//            $group->groupLangs()->create(['name' => $group->name, 'description' => $group->description, 'locales_id' => $locales_id,]);
//        }

//        dd($group->groupLangs()->get()->toArray());
    }

    public function testLogin()
    {
        Passport::actingAs(
            User::first(),
            ['*']
        );
//        $response = $this->get('api/global/recommendedVideo');
        $response = $this->patch('api/group/7', [
            'description' => '網奕資訊研發部',
            'groupId'     => 'asdasda',
        ]);
//
        $response->dump();
        $response->assertStatus(200);
    }

    public function testUploadVideo()
    {
        $this->withHeaders([
            'Accept'        => 'application/vnd.sokradeo.v1+json',
            'application'   => 'c1e3deb8-9c20-49b3-9508-109911e5b984',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzczMDY3NjUsImlhdCI6MTYzNzIyMDM2NSwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLmNoaW5hY2xvdWRhcGkuY24vYWNjb3VudC9hdmF0YXIvMTUyNTY1ODM3NyIsImlzQWN0aXZhdGUiOiJ0cnVlIiwiYXJlYSI6Imdsb2JhbCIsIm5iZiI6MTYzNzIyMDM2NX0.65teilQTtNDrAuPTk6IsfSVioSPlkPBUJG9Dsb1_XC8'
        ]);

        $testResponse = $this->post('api/school/video',
            [
                'blobUri'   => 'https://teammodel.blob.core.windows.net/1525658377/records/256608031477993472',
                'zipFile'   => new \Illuminate\Http\UploadedFile(public_path('256608031477993472.zip'), '256608031477993472.zip', null, null, true),
                'onlyVideo' => true,
            ]
        );

        $testResponse->dump();
        $testResponse->assertStatus(200);
//        $api->post('/video', 'App\Video\LessonController@store');
    }

    public function testSql()
    {


        $n   = 10;
        $sum = 0;
        for ($i = 1; $i <= $n; $i++) {
            $sum += $i;
        }

        dump($n * ($n + 1) / 2);
        dump($sum);

        $a = "/[\d]/";
        preg_match($a, "1234", $b);
        dd($b);
        $pattern = "/(^886\-)\d{2,3}\-\d{7,8}$/";
        $string  = "886-09-12345678";
        preg_match($pattern, $string, $matches);
        dd($matches);
//        echo $matches[0]; // 886-09-123345678
//        echo $matches[1]; // 886-
        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                echo $i . 'x' . $j . '=' . $i * $j . PHP_EOL;
            }
        }


        dd();
        $t = [
            [
                'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4,

            ],
            [
                'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4
            ]
        ];
        $r = [];
//        for ($i = 0; $i < count($t); $i++) {
//            $r[] = $t[0]['d'];
//            array_push($r, $t[0]['d']);
//        }
        foreach ($t as $i) {
            $r[] = $i['d'];
        }
        dd($r);
        dd(array_column($t, 'd'));
//        $NowTime = date("Y-m-d H:i:s");
        dump(date('m-t', strtotime('+1 month')));
        dd(date('m-t', strtotime('-1 month')));
        $a        = "Original";
        $my_array = array("a" => "Cat", "b" => "Dog", "c" => "Horse");
        extract($my_array);
        echo "\$a = $a; \$b = $b; \$c = $c";

        dd();
        dd($this->has_string_keys(['is_string' => 1, '12312']));
//        BbLicense::query()->insert([
//            'name' => 'test',
//            'code' => 'test',
//        ]);
        $toArray = BbLicense::query()->selectRaw('id,count(bb_license_group.bb_license_id) as total')
            ->leftJoin('bb_license_group', 'bb_license_group.bb_license_id', 'bb_licenses.id')
            ->orderBy('id')
            ->groupBy('id')->get()->toArray();
        BbLicense::query()->find(10)->update(['name' => '1231232']);
    }

    public function has_string_keys(array $array)
    {

        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
