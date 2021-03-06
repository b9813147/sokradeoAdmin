<?php

namespace Tests\Feature;

use App\Helpers\CoreService\CoreServiceApi;
use App\Models\Tba;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoreServiceApiTest extends TestCase
{
    use \App\Helpers\Path\Tba, CoreServiceApi;

    protected $coreServiceApi;
    protected $client_id = 'd7193896-9b12-4046-ad44-c991dd48cc39';
    protected $client_secret = 'l_reKlScUJHU1kM5S_j=BPW4:IXcWb24';


    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_login()
    {
        $url        = 'https://api2.teammodel.net/oauth2/login';
        $grant_type = 'account';
//        $id_token     = 'eyJpdiI6InlOTk54MFQyK0lyb0JGOVR3aWZaNEE9PSIsInZhbHVlIjoiQ0hUdGE4WktVdm02VzBXTEFTR3Fid0tjai9Ob0FXaHNiVTJ2YXA0M1hIa3oyTFViWmJmaFNpUzVnaStqcXJjTFFGR3lJc0JPWEtxd2xYR3FoeVRXN0hLODY3NzJFNHVvMStZM1p3QmduOStDS1A3S1QyZTFic2M3SjhIdm94cnIiLCJtYWMiOiJiOWU5NTJiNzMwNTNmNDk4MTdiNTcxNmJlZDhhZjFhZWI2NzI5MjYwNzIxYTVhNmE3MTE3YzhiNTU5M2U3NzUwIn0';
        $redirect_uri = '';
        $account      = '934161322';
        $password     = '830208';
        $name         = '';
        $nonce        = '0';
//        $code         = 'Codea3470dc4-cd32-4a59-8183-4000b164e611';
//        $open_id      = '';
        $open_code = '';
        $pin_code  = '';

        $response = Http::post($url, [
            'grant_type' => $grant_type,
            'client_id'  => $this->client_id,
            'account'    => $account,
            'password'   => $password,
            'nonce'      => $nonce,
//            'id_token'   => $id_token,
//            'redirect_uri' => $redirect_uri,

//            'name'         => $name,

//            'code'         => $code,
//            'open_id'      => $open_id,
//            'open_code'    => $open_code,
//            'pin_code'     => $pin_code,
        ]);

        dd($response->json());
        $this->assertTrue(true);
    }

    /**
     *  ????????????
     */
    public function test_getSsoCode()
    {
        $url        = getenv('HABOOK_2_API_URL') . 'oauth2/login';
        $grant_type = 'code';
        $id_token   = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzA2NTcxNzUsImlhdCI6MTYzMDU3MDc3NSwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLndpbmRvd3MubmV0L2FjY291bnQvYXZhdGFyLzE1MjU2NTgzNzciLCJpc0FjdGl2YXRlIjoiZmFsc2UiLCJuYmYiOjE2MzA1NzA3NzV9.QKxMgZzlZWWoJxKVHE3Sgpdk80yLRT83lTARulpU8mo';
//        $id_token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJjOGRlODIyYi0zZmNmLTQ4MzEtYWRiYS1jZGQxNDY1M2JmN2YiLCJleHAiOjE2MzUyMzYwNTMsImlhdCI6MTYzNTE0OTY1Mywibm9uY2UiOiIxZWM2ODUzYy00ZmY2LTQyMjEtOGQwNC03ZThiOTdkMTkwNTAiLCJuYW1lIjoiQXJlcyIsInBpY3R1cmUiOiJodHRwczovL2NvcmVzdG9yYWdlc2VydmljZS5ibG9iLmNvcmUud2luZG93cy5uZXQvYWNjb3VudC9hdmF0YXIvMTUyNTY1ODM3NyIsImlzQWN0aXZhdGUiOiJ0cnVlIiwiYXJlYSI6Imdsb2JhbCIsIm5iZiI6MTYzNTE0OTY1M30.4gCvt1P1lF3B480w01syV-YQ3vV24bwyo_vvUUMQazo';
        $nonce = '0';

        $response = Http::post($url, [
            'grant_type' => $grant_type,
            'client_id'  => $this->client_id,
            'nonce'      => $nonce,
            'id_token'   => $id_token,
        ])->json();
        dump($response['code']);
        $this->assertTrue(true);
    }

    /**
     * ???????????????
     * grant_type = get
     * ?????????????????????
     * grant_type = name
     * name = "{??????????????????}"
     *
     * ?????????????????????
     * grant_type = picture
     * picture = "{Base64????????????}"
     * ?????????????????????200*200???PNG???????????????Base64???????????????
     *
     * ??????????????????????????????????????????
     * grant_type = mobileOrMail
     * mobile_mail = "{mobile or mail}"
     * pin_code = "{pin_code}"
     * ????????????????????????(+??????-??????)?????????
     *
     * ???????????????????????????
     * grant_type = isSetPw
     * ???????????????true or false???
     *
     * ????????????
     * grant_type = password
     * old_pw = "{???????????????????????????}"
     * new_pw = "{???????????????????????????}"
     * ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
     * 1.??????????????????
     * ?????????????????????????????????????????????????????????????????????????????????????????????????????????
     * 2.?????????????????????
     * ???????????????????????????????????????
     *
     * ?????????????????????
     * grant_type = unbind
     * provider = "wechat","facebook","google","ding","apple"
     * ??????????????????????????????????????????????????????????????????????????????????????????error=4????????????
     *
     * ????????????
     * grant_type = extension
     * extension = {json object}
     *
     * ????????????
     * grant_type = delete
     * old_pw = "{????????????????????????}"
     * ???????????????????????????????????????????????????????????????????????????????????????????????????????????????
     */
    public function test_getProfile()
    {
        $url        = 'https://api2.teammodel.net/oauth2/profile';
        $grant_type = 'get';
        $id_token   = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzA3NDUwNzUsImlhdCI6MTYzMDY1ODY3NSwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLndpbmRvd3MubmV0L2FjY291bnQvYXZhdGFyLzE1MjU2NTgzNzciLCJpc0FjdGl2YXRlIjoiZmFsc2UiLCJuYmYiOjE2MzA2NTg2NzV9.xlJPh72xYHOo18zARAkcjGQgQbRbeTzMwSkOQAq5uzI';
        $attributes = [];

        switch ($grant_type) {
            case 'name':
            case 'picture':
            case 'mobileOrMail':
            case 'isSetPw':
            case 'password':
            case 'unbind':
            case 'delete':
            case 'extension':
                $baseData = [
                    'client_id' => $this->client_id,
                    'nonce'     => '0',
                    'id_token'  => $id_token,
                    $attributes
                ];
                break;
            default:
                $baseData = [
                    'client_id' => $this->client_id,
                    'nonce'     => '0',
                    'id_token'  => $id_token,
                ];
                break;
        }

        $result   = array_merge(['grant_type' => $grant_type], $baseData);
        $response = Http::post($url, $result)->json();
        dd($response);
        $this->assertTrue(true);

    }

    /**
     * ?????? Blob Sas
     */
    public function test_getBlobSas()
    {
        $url = 'https://www.teammodel.net/sokrate/get-blob-read';
//        $url      = 'https://test.teammodel.net/sokrate/get-blob-read';
        $idToken  = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzA3NDUwNzUsImlhdCI6MTYzMDY1ODY3NSwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLndpbmRvd3MubmV0L2FjY291bnQvYXZhdGFyLzE1MjU2NTgzNzciLCJpc0FjdGl2YXRlIjoiZmFsc2UiLCJuYmYiOjE2MzA2NTg2NzV9.xlJPh72xYHOo18zARAkcjGQgQbRbeTzMwSkOQAq5uzI';
        $response = Http::withHeaders([
            'X-Auth-IdToken' => $idToken,
        ])
            ->post($url, ['school_code' => 'hbgl'])->json();
        dd($response);
        $this->assertTrue(true);
    }

    /**
     *  ??????????????????
     */
    public function test_sendNotify()
    {
        $content = json_encode([
            "content" => "2021?????????????????????,??????????????????????????????\n??????110???7???31???????????????????????????, ???????????????",
            "action"  => [
                [
                    "type"          => "click",
                    "label"         => "????????????",
                    "url"           => "https://sokrates.teammodel.org/exhibition/tbavideo#/activity-channel/534",
                    "tokenbindtype" => 1,
                ], [
                    "type"          => "click",
                    "label"         => "??????????????????",
                    "url"           => "https://sokrates.teammodel.org/exhibition/tbavideo#/activity-channel/534/progress",
                    "tokenbindtype" => 1,
                ]
            ],
        ]);
        $var     = ["1595321354", "1525658377", "1525763138"];
        $data    = ["1529919145", "1525658323", "1525658377"];
        dd(collect(array_merge($var, $data))->unique()->toArray());
        dd(array_unique(array_merge($var, $data)));
        dd(array_values(array_unique($var)));
        $sendNotify = $this->sendNotify(array_unique($var), $content, 'test');
        $this->assertTrue($sendNotify);
    }

    public function test_apiAuth()
    {
        dd($this->isExist(1521599406));
    }

    public function test_commend_line()
    {
        $imag                  = 'CoverImage.jpg';
        $url_resource_sokrodeo = "'https://teammodel.blob.core.windows.net/1524738018/records/228726582682980352/Sokrates?sv=2020-08-04&st=2021-09-23T03%3A22%3A54Z&se=2021-09-24T03%3A52%3A54Z&sr=c&sp=rwdl&sig=T7OKERIYlS1v8SE9AVND6BOy1Z2AG9sUNrY1eUbjNmM%3D'";
        $url_image             = "'https://teammodel.blob.core.windows.net/1524738018/records/228726582682980352/Record/$imag?sv=2020-08-04&st=2021-09-23T03%3A22%3A54Z&se=2021-09-24T03%3A52%3A54Z&sr=c&sp=rwdl&sig=T7OKERIYlS1v8SE9AVND6BOy1Z2AG9sUNrY1eUbjNmM%3D'";

        $url_target = storage_path('temp/lesson/');
        system('~/web/azcopy copy ' . $url_resource_sokrodeo . ' ' . $url_target . ' --recursive=true', $out);
        system('~/web/azcopy copy ' . $url_image . ' ' . $url_target . ' --recursive=true', $out);

        $this->assertTrue(true);

    }

    public function test_lesson_upload()
    {
        // create tba
        $tbaInfo  = json_decode(file_get_contents(storage_path('temp/lesson/Sokrates/SokratesInfo.json')));
        $user_id  = User::query()->firstWhere('habook', $tbaInfo->teammodel_id)->id ?? null;
        $thumFile = $tbaInfo->cover_image ?? null;
        $thumName = is_null($thumFile) ? null : 'thum.' . pathinfo(storage_path('temp/lesson/' . $tbaInfo->cover_image), PATHINFO_EXTENSION);

        $tba = [
            'user_id'              => $user_id,
            'name'                 => $tbaInfo->name,
            'description'          => $tbaInfo->description,
            'thumbnail'            => $thumName,
            'teacher'              => $tbaInfo->teacher,
            'subject'              => $tbaInfo->subject,
            'educational_stage_id' => $tbaInfo->educational_stage_id,
            'grade'                => $tbaInfo->grade,
            'lecture_type'         => 0,
            'lecture_date'         => $tbaInfo->lecture_date,
            'locale_id'            => $tbaInfo->locale_id,
            'habook_id'            => $tbaInfo->teammodel_id,
            'student_count'        => $tbaInfo->student_count,
            'irs_count'            => $tbaInfo->irs_count,
            'binding_number'       => $tbaInfo->binding_number,
        ];

        $tbaModel = Tba::query()->create($tba);

        if (!is_null($thumFile)) {
            $file = new Filesystem();
            $file->move(storage_path('temp/lesson/CoverImage.jpg'), $this->pathPublicTba($tbaModel->id, null, true) . $thumName);
        }

        // Create tba_analysis_events


        // Create tba_comment


        // Create  tba_statistics


        // Create TbaAnnexes

        $commentInfo = json_decode(file_get_contents(public_path('lesson/Sokrates/SokratesAllComments.json')));
    }

    public function test_transform()
    {
        //886 ??????
        //86 ??????
        //1 ??????

        $userService  = app(UserService::class);
        $fileSystem   = new Filesystem();
        $country_code = 886;
        $recorder_id  = '233841654509998080';
        $json_path    = 'temp/lesson/' . $recorder_id;
        $info         = [];
        if ($fileSystem->exists(storage_path($json_path . '/Sokrates/SokratesInfo.json'))) {
            $tbaInfo            = json_decode(file_get_contents(storage_path($json_path . '/Sokrates/SokratesInfo.json')));
            $user_id            = User::query()->firstWhere('habook', $tbaInfo->teammodel_id)->id ?? null;
            $thumbnail          = $tbaInfo->cover_image ?? null;
            $thumbnail          = is_null($thumbnail) ? null : 'thum.' . pathinfo(storage_path('temp/lesson/' . $tbaInfo->cover_image), PATHINFO_EXTENSION);
            $tbaInfo->thumbnail = $thumbnail;
            $tbaInfo->user_id   = $user_id;
            $info               = (array)$tbaInfo;
        }


        // tba_analysis_events
        $anal = collect();
        if ($fileSystem->exists(storage_path($json_path . '/Sokrates/SokratesResults/imgname.json'))) {
            $eventJson = collect(json_decode(file_get_contents(storage_path($json_path . '/Sokrates/SokratesResults/imgname.json'))));

            $eventJson->groupBy('Event')->each(function ($q) use (&$anal) {
                $data = [];
                foreach ($q as $item) {
                    $data[] = [
                        $item->TimeStrt ?? 0,
                        $item->TimeEnd ?? 0,
                    ];
                }
                $anal->push([
                    'event' => $q->first()->Event ?? null,
                    'mode'  => $q->first()->Mode ?? null,
                    'data'  => $data
                ]);
            });
            $info['anal']['events'] = $anal->toArray();
        }


        $eval             = collect();
        $arrAutoPedaCount = ['LTK' => 1, 'SCD' => 1, 'WCT' => 1, 'WCI' => 1, 'DFI' => 2];
        if ($fileSystem->exists(storage_path($json_path . '/Sokrates/SokratesResults/Auto_Peda.json'))) {
            $AutoPedaJson = collect(json_decode(file_get_contents(storage_path($json_path . '/Sokrates/SokratesResults/Auto_Peda.json'))));
            $AutoPedaJson->each(function ($q) use (&$arrAutoPedaCount, $country_code, $eval) {
                if (empty($q->Type) || empty($q->TimePoint)) return;
                if ($arrAutoPedaCount[$q->Type] > 0) {
                    $arrAutoPedaCount[$q->Type]--;
                } else {
                    return;
                }
                $eval->push([
                    'time' => $q->TimePoint,
                    'text' => $this->convertEval($country_code, $q->Type),
                ]);
            });

            $info['eval']['users'] = [
                'events'  => [
                    'tag_id' => 'A0068',
                    'data'   => $eval->toArray()
                ],
                'accType' => 'Habook',
                'accUser' => 'AI001',
                'name'    => 'AI_Sokrates',
                'email'   => null
            ];
        }

        $stat = collect();

        if ($fileSystem->exists(storage_path($json_path . '/Sokrates/SokratesResults/Data_Freq.json'))) {
            $DataFreqJson = collect(json_decode(file_get_contents(storage_path($json_path . '/Sokrates/SokratesResults/Data_Freq.json'))));
            $DataFreqJson->each(function ($q) use ($stat) {
                $stat->push([
                    'type'     => $q->Event,
                    'freq'     => $q->Freq,
                    'duration' => 0,
                    'idx'      => 0,
                ]);
            });
        }

        //
        if ($fileSystem->exists(storage_path($json_path . '/Sokrates/SokratesResults/Data_PedaDex.json'))) {
            $DataPedaDexJson = collect(json_decode(file_get_contents(storage_path($json_path . '/Sokrates/SokratesResults/Data_PedaDex.json'))));
            $DataPedaDexJson->each(function ($q) use ($stat) {
                $stat->push([
                    'type'     => $q->Event,
                    'freq'     => 0,
                    'duration' => 0,
                    'idx'      => $q->Index,
                ]);
            });
        } else {
            $stat->push([
                'type'     => 47,
                'freq'     => 0,
                'duration' => 0,
                'idx'      => 0,
            ], [
                'type'     => 48,
                'freq'     => 0,
                'duration' => 0,
                'idx'      => 0,
            ]);
        }

        if ($fileSystem->exists(storage_path($json_path . '/Sokrates/SokratesResults/Data_Time.json'))) {
            $DataTimeJson = collect(json_decode(file_get_contents(storage_path($json_path . '/Sokrates/SokratesResults/Data_Time.json'))));
            $DataTimeJson->each(function ($q) use ($stat) {
                $stat->push([
                    'type'     => $q->Event,
                    'freq'     => 0,
                    'duration' => $q->Duration,
                    'idx'      => 0,
                ]);
            });
            $info['stat']['list'] = $stat->toArray();
        }
        if ($fileSystem->exists($SokratesAllComments = storage_path($json_path . '/Sokrates/SokratesAllComments.json'))) {
            $SokratesAllComments = collect(json_decode(file_get_contents($SokratesAllComments)));
            $commentData         = [];
            $SokratesAllComments->each(function ($q) use ($userService, $json_path, $fileSystem, &$commentData, &$info) {
                if ($fileSystem->exists($comments = storage_path($json_path . '/Sokrates/' . $q . '/comments.json'))) {
                    collect(json_decode(file_get_contents($comments)))->each(function ($q) use ($userService, &$commentData, &$info) {
                        $attachment = null;
                        if (!is_null($q->imageList)) {
                            $attachment = $q->imageList[0]->image;
                        }
                        if (!is_null($q->videoList)) {
                            $attachment = $q->videoList[0]->video;
                        }
                        if (!is_null($q->recList)) {
                            $attachment = $q->recList[0]->rec;
                        }
                        if ($q->expertType !== 'Guest') {
                            $user = $userService->loginAsHaBook($q->id, ['name' => $q->name]);
                        }

                        $commentData[] = [
                            'habook_id'    => $q->id,
                            'nick_name'    => $user->name ?? null,
                            'tag_id'       => $q->expPointTypeId,
                            'user_id'      => $user->id ?? null,
                            'comment_type' => 1,
                            'public'       => 1,
                            'time_point'   => $q->time,
                            'text'         => $q->desc,
                            'attachment'   => $attachment,
                        ];
                    });
                    $info['comments']['list'] = $commentData;
                }
            });
//            $info['stat']['list'] = $stat->toArray();
        }


        dd($info);


    }

    /**
     * @param $country_code
     * @param $type
     * @return array|\ArrayAccess|mixed|string
     */
    public function convertEval($country_code, $type)
    {
        switch ($type) {
            case 'LTK':
                $result = Arr::get(['886' => '????????????', '86' => '????????????', '1' => 'Learning assignment'], $country_code);
                break;
            case 'SCD':
                $result = Arr::get(['886' => '????????????', '86' => '????????????', '1' => 'Student - center decision'], $country_code);
                break;
            case 'WCT':
                $result = Arr::get(['886' => '????????????', '86' => '????????????', '1' => 'Whole -class assessment'], $country_code);
                break;
            case 'WCI':
                $result = Arr::get(['886' => '????????????', '86' => '????????????', '1' => 'Whole -class interaction'], $country_code);
                break;
            default:
                $result = Arr::get(['886' => '????????????', '86' => '????????????', '1' => 'Synchronous differentiated instruction'], $country_code);
        }
        return $result;
    }

    public function test_copy_video()
    {


        $myaccount = "iesnas";

        $mykey       = "kOHqeHSRFnggtIAYqwro4N+jgoSybtFgy0b/43eWu+aIs0KZgDCsPNCAjNCj2jzUo6T6SCmYv4OQaG9ThA1wpQ=="; //global
        $endpoints   = "core.windows.net"; //global
        $mycontainer = "twezcms";

        $connectionString = "DefaultEndpointsProtocol=https;AccountName=" . $myaccount . ";AccountKey=" . $mykey . ";EndpointSuffix=" . $endpoints;


        $sourceURL = "https://teammodel.blob.core.windows.net/1595321354/records/220463677474738176/Sokrates/9875m.mp4?sv=2020-04-08&st=2021-09-01T10%3A57%3A01Z&se=2021-09-30T10%3A57%3A00Z&sr=c&sp=rcwdl&sig=FDPi6Ldy3i3fS0a10CSzeKjCy3SjSZbD30517tE5Lic%3D";

        $blobClient = BlobRestProxy::createBlobService($connectionString);
        // tba_id / file name
        $target = "test/9875m3.mp4";
        echo "start" . time() . "\n\n";
        $result = $blobClient->copyBlobFromURL($mycontainer, $target, $sourceURL);
    }

    /**
     * @return array
     */
    public function example()
    {
        $func_get_args = func_get_args();

        $result = [];
        foreach ($func_get_args as $item) {
            if (gettype($item) === 'integer') array_push($result, $item);
            if (gettype($item) === 'array') array_push($result, $item);
            if (gettype($item) === 'object') array_push($result, $item);
            if (gettype($item) === 'string') array_push($result, $item);
        }

        return $result;
    }

    public function test_a()
    {
        $school  = ['name' => 'tset', 'abbr' => 'test'];
        $teacher = ['name' => 'tset', 'abbr' => 'test'];
        dd($this->example($school, $teacher));
    }

    public function test_user_setting()
    {
        $url          = getenv('HABOOK_2_API_URL') . 'oauth2/usersetting';
        $id_token     = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2MzUyMTI1OTQsImlhdCI6MTYzNTEyNjE5NCwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLndpbmRvd3MubmV0L2FjY291bnQvYXZhdGFyLzE1MjU2NTgzNzciLCJpc0FjdGl2YXRlIjoidHJ1ZSIsImFyZWEiOiJnbG9iYWwiLCJuYmYiOjE2MzUxMjYxOTR9.f1VGOXLGU7XbkYkIuLM5TcbSnpudP6q5z-qywP1lLmk';
        $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6Imwzc1EtNTBjQ0g0eEJWWkxIVEd3blNSNzY4MCJ9.eyJhdWQiOiI4NzY4YjA2Zi1jNWM1LTRiMGMtYWJmYi1kN2RlZDM1NDYyNmQiLCJpc3MiOiJodHRwczovL2xvZ2luLm1pY3Jvc29mdG9ubGluZS5jb20vNzNhMmJjYzUtZmU5OS00NTY2LWFhOGEtMDdlN2JiMjg3ZGYxL3YyLjAiLCJpYXQiOjE2MzUxMjU4OTQsIm5iZiI6MTYzNTEyNTg5NCwiZXhwIjoxNjM1MTI5Nzk0LCJhaW8iOiJFMlpnWUhBVW1aOWIzdWpqR0pleFJYeGxpNVVZQUE9PSIsImF6cCI6ImQ3MTkzODk2LTliMTItNDA0Ni1hZDQ0LWM5OTFkZDQ4Y2MzOSIsImF6cGFjciI6IjEiLCJvaWQiOiIxMGY0MGY2OC1hNzgyLTQ3YzgtYjQ4OC03MjljNGE2NDI5ODAiLCJyaCI6IjAuQVNzQXhieWljNW4tWmtXcWlnZm51eWg5OFpZNEdkY1NtMFpBclVUSmtkMUl6RGtyQUFBLiIsInJvbGVzIjpbIlNva3JhdGVzIl0sInN1YiI6IjEwZjQwZjY4LWE3ODItNDdjOC1iNDg4LTcyOWM0YTY0Mjk4MCIsInRpZCI6IjczYTJiY2M1LWZlOTktNDU2Ni1hYThhLTA3ZTdiYjI4N2RmMSIsInV0aSI6IkdLRXlsT2ZRa1U2WjZWMFc4aVo2QUEiLCJ2ZXIiOiIyLjAifQ.F4w7UHuU5nBevoI7ziQC_7muGWTu52zBVRaeh3cHMlBD3z481bnayyoPqhCMvGYQRTJj1bjnZONt-7j9y6DAyIqZ0sblOCRzw8tvQJe71bPyo0gLuzJ-NfK5EKD5O72v75kZkz67KYhMiV3c1-pOIk8aIT0mi5SFhKFM5wL-aGH6F5gwoMJHnbZSbxAII0mkh4pJuJjedrqaoDwaFmb_1TCnM50qG1BLQZrUqpowZuhmVxozdEhkXDsKMgHoK7yhENYUXPT1gf1dVkfSSN4-wHBhBADvnc8STuK9W6gHuaGvh-5pPGWZiSK6xeAZ9EHhnYpsBmv6c-yVnuYRgNFFeQ';
        $body         = json_encode([
            'grant_type' => 'get',
            'id_token'   => $id_token,
            'product'    => 'sokapp'
        ]);
        $json_data    = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer' . ' ' . $access_token
            ]
        )->post($url, [
            'body' => $body,
        ])->json();
        dd($json_data);

    }

    public function test_get_access_token()
    {
        $url  = getenv('HABOOK_2_API_URL') . 'oauth2/token';
        $code = 'device';
        $json = Http::post($url, [
            'grant_type' => 'device',
            'client_id'  => $this->client_id,
        ])->json();
        dd($json);
    }

    public function test_notify()
    {
        $test        = ['1595321354', null];
        $map         = app(UserService::class)->findWhereIn('id', [948, 1, 4])->map(function ($q) {
            return $q->habook;
        })->toArray();
        $array_merge = array_filter(array_merge($map, $test));
        $content     = json_encode([
            "content" => "2021?????????????????????,??????????????????????????????\n??????110???7???31???????????????????????????, ???????????????",
            "action"  => [
                [
                    "type"          => "click",
                    "label"         => __('video-upload-message.click'),
                    'url'           => getenv('SOKRADEO_URL') . '/exhibition/tbavideo/check-with-habook/?to=' . urlencode(getenv('SOKRADEO_URL') . '/exhibition/tbavideo#/content/1698?groupIds=4,120&channelId=4,109') . '&ticket=',
                    "tokenbindtype" => 1,
                ], [
                    "type"          => "click",
                    "label"         => __('video-upload-message.click'),
                    'url'           => getenv('SOKRADEO_URL') . '/exhibition/tbavideo/check-with-habook/?to=' . urlencode(getenv('SOKRADEO_URL') . '/exhibition/tbavideo#/content/1698?groupIds=4,120&channelId=4,109') . '&ticket=',
                    "tokenbindtype" => 1,

                ],
            ]
        ]);

        dd($content);
//        $class = app(::class);
//        dd($class->post(123, 123));
        dd($this->sendNotify($array_merge, $content, 'test'));
        dd(1);
//        dd(app(CoreServiceApi::class)->sendNotify($array_merge, $content, 'test'));
//        $this->coreServiceApi->sendNotify($array_merge, $content, 'test');

    }
}
