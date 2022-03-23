<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\Auth\UserService;
use App\Services\GroupChannelService;
use App\Services\GroupService;
use App\Types\Auth\AccountType;
use Carbon\Carbon;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MemberTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $groupService = app(GroupService::class);
        $user         = app(UserRepository::class);
        dd($user->findWhere(['habook' => '0934161322#4130', '']));
        $user      = '0934161322#4130';
        $channelId = 5;
        $userId    = User::query()->where('habook', $user)->pluck('id')->first();
        // 用channelID 去找 groupID
        $group_id = $groupService->getChannelByGroupId($channelId)->group_id;

        $data = GroupUser::query()
            ->where('group_id', $group_id)
            ->where('user_id', $userId)->get();

        dump($data, $group_id, $userId);

        $this->assertTrue(true);
    }

    public function testGetIdToken()
    {
        $api     = new Client();
        $token   = '';
        $idToken = '';
        $params  = [
            "jsonrpc" => "2.0",
            "method"  => "GetQuickLoginTicket",
            "params"  =>
                [
                    "idToken" => $idToken
                ],
            "id"      => 1
        ];

        $data = $api->request('post', 'https://api.habookaclass.biz/account', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ],
            'json'    => $params
        ])->getBody()->getContents();

        dump($data);

        $this->assertTrue(true);
    }

    public function testGetUserInfo()
    {
        $api    = new Client();
        $token  = '';
        $ticket = '';

        $userInfo = [
            "jsonrpc" => "2.0",
            "method"  => "UserInfoManage",
            "params"  =>
                [
                    "idToken"   => $ticket,
                    "method"    => "get",
                    "option"    => "userInfo",
                    "extraInfo" => (object)[]
                ],
            "id"      => 1
        ];

        $data = $api->get('https://api.habookaclass.biz/account', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json'
            ],
            'json'    => $userInfo
        ])->getBody()->getContents();

        dump($data);

        $this->assertTrue(true);
    }

    public function testGteChannelContent()
    {
        $api      = new Client();
        $contents = $api->get('http://adminvuetify.com/api/group/222222', [
            'headers' => [
                'Accept'       => 'application/vnd.sokradeo.v1+json',
                'Content-Type' => 'application/json'
            ]
        ])->getBody()->getContents();
        dd($contents);
    }

    public function testCreateUser()
    {
        $data = [
            'habook' => 'test#123',
            'name'   => 'test'
        ];
        $app  = app(UserService::class);
        $user = $app->loginAsHaBook('test#123', $data);
        $app->setUser($user->id, [], [6]);
        dd($user);
    }

    public function testGroup()
    {
        $model = Group::query()->select()->where('public', 2)->first();

        $carbon  = Carbon::create(json_decode($model->event_data)->trialDay);
        $carbon2 = Carbon::now();
        $d       = $carbon->diff($carbon2)->days;
        dd($d);
    }

    public function testGetUser()
    {
        $habook   = '1521599406';
        $response = collect(Http::post('https://api2.teammodel.net/oauth2/GetUserInfos', [
            $habook
        ])->json())->isEmpty();

        dd($response);

    }

}
