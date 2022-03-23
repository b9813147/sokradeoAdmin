<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    public function test_store()
    {
//        Passport::actingAs(\App\Models\User::query()->find(948), ['*']);
        $url = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->temporarySignedRoute('member.store', now()->addMinutes(5), ['groupId' => 143, 'sname' => 'test']);
//        dd($url);
        $this->withHeaders([
            'Accept'        => 'application/vnd.sokradeo.v1+json',
//            'application'   => 'c1e3deb8-9c20-49b3-9508-109911e5b984',
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJhY2NvdW50LnRlYW1tb2RlbCIsInN1YiI6IjE1MjU2NTgzNzciLCJhdWQiOiJkNzE5Mzg5Ni05YjEyLTQwNDYtYWQ0NC1jOTkxZGQ0OGNjMzkiLCJleHAiOjE2NDU2MDQ0NDgsImlhdCI6MTY0NTUxODA0OCwibm9uY2UiOiIwIiwibmFtZSI6IkFyZXMiLCJwaWN0dXJlIjoiaHR0cHM6Ly9jb3Jlc3RvcmFnZXNlcnZpY2UuYmxvYi5jb3JlLmNoaW5hY2xvdWRhcGkuY24vYWNjb3VudC9hdmF0YXIvMTUyNTY1ODM3NyIsImlzQWN0aXZhdGUiOiJ0cnVlIiwiYXJlYSI6Imdsb2JhbCIsIm5iZiI6MTY0NTUxODA0OH0.nhhlZ0mTdmzUmZ5aL2uj3uyN9qiPp1CYl6_Kyi0wtiY'
        ]);
        $response = $this->post($url);
        $response->dump();

//        $test = 'api/group/qrcode/10';
//        dd(explode( '/', $test));
//        dd(split('', $test));

//        $response->assertStatus(200);
    }

    public function test_data()
    {
        $data = collect([
            ['UID' => 10003217, 'read_content' => '戲劇', 'time' => '2022-02-12 09:15', 'user_agent' => 'Chrome', 'ip' => '1.23.54.231'],
            ['UID' => 10049213, 'read_content' => '綜藝', 'time' => '2022-02-12 09:16', 'user_agent' => 'Chrome', 'ip' => '47.199.48.111'],
            ['UID' => 10021531, 'read_content' => '戲劇', 'time' => '2022-02-12 09:25', 'user_agent' => 'Safari', 'ip' => '42.75.43.224'],
            ['UID' => 10085129, 'read_content' => '戲劇', 'time' => '2022-02-12 09:45', 'user_agent' => 'Chrome', 'ip' => '110.28.161.126'],
            ['UID' => 10100543, 'read_content' => '戲劇', 'time' => '2022-02-12 10:15', 'user_agent' => 'Chrome', 'ip' => '218.166.103.43'],
            ['UID' => 10400500, 'read_content' => '戲劇', 'time' => '2022-02-12 10:32', 'user_agent' => 'Chrome', 'ip' => '125.230.234.223'],
            ['UID' => 10021531, 'read_content' => '戲劇', 'time' => '2022-02-13 10:45', 'user_agent' => 'Safari', 'ip' => '101.12.18.199'],
            ['UID' => 10085129, 'read_content' => '戲劇', 'time' => '2022-02-13 11:07', 'user_agent' => 'Chrome', 'ip' => '1.169.133.223'],
            ['UID' => 10404999, 'read_content' => '戲劇', 'time' => '2022-02-13 11:25', 'user_agent' => 'Chrome', 'ip' => '111.253.231.1'],
            ['UID' => 10049213, 'read_content' => '戲劇', 'time' => '2022-02-13 11:34', 'user_agent' => 'Chrome', 'ip' => '47.199.48.111'],
            ['UID' => 10404999, 'read_content' => '戲劇', 'time' => '2022-02-13 11:45', 'user_agent' => 'Chrome', 'ip' => '111.253.231.1'],
            ['UID' => 10400502, 'read_content' => '綜藝', 'time' => '2022-02-13 12:13', 'user_agent' => 'Chrome', 'ip' => '125.230.234.223'],
            ['UID' => 10404999, 'read_content' => '戲劇', 'time' => '2022-02-13 12:25', 'user_agent' => 'Chrome', 'ip' => '111.253.231.1'],
            ['UID' => 10049213, 'read_content' => '綜藝', 'time' => '2022-02-14 10:02', 'user_agent' => 'Chrome', 'ip' => '47.199.48.111'],
            ['UID' => 10021531, 'read_content' => '戲劇', 'time' => '2022-02-14 10:13', 'user_agent' => 'Safari', 'ip' => '101.12.18.199'],
            ['UID' => 10404999, 'read_content' => '戲劇', 'time' => '2022-02-14 10:33', 'user_agent' => 'Chrome', 'ip' => '111.253.231.1'],
            ['UID' => 10085129, 'read_content' => '戲劇', 'time' => '2022-02-14 11:30', 'user_agent' => 'Chrome', 'ip' => '1.169.133.223'],
            ['UID' => 10404999, 'read_content' => '戲劇', 'time' => '2022-02-14 11:13', 'user_agent' => 'Chrome', 'ip' => '111.253.231.1'],
            ['UID' => 10404999, 'read_content' => '戲劇', 'time' => '2022-02-14 11:53', 'user_agent' => 'Chrome', 'ip' => '111.253.231.1'],
            ['UID' => 10021531, 'read_content' => '戲劇', 'time' => '2022-02-15 10:04', 'user_agent' => 'Safari', 'ip' => '101.12.18.199'],
            ['UID' => 10404999, 'read_content' => '戲劇', 'time' => '2022-02-15 10:15', 'user_agent' => 'Chrome', 'ip' => '111.253.231.1'],
            ['UID' => 10085129, 'read_content' => '戲劇', 'time' => '2022-02-15 10:23', 'user_agent' => 'Chrome', 'ip' => '1.169.133.223'],
            ['UID' => 10049213, 'read_content' => '戲劇', 'time' => '2022-02-15 10:37', 'user_agent' => 'Chrome', 'ip' => '47.199.48.111'],
        ]);
//        1
        /**
         *  10003217、 10049213、 10021531、 10085129、 10100543、 10400500、 10404999、 10400502
         *  判斷依據 先列出 2022-02-12 至 2022-02-15 ，並且過濾掉重複的 UID 就會得到 Unique User
         */
        $count = $data->whereBetween('time', ['2022-02-12', '2022-02-15'])->unique('UID')->map(function ($q) {
            return $q['UID'];
        })->toArray();

        dump($count);
//        2
        // 10049213 、  10021531 、  10085129 、  10404999
        /**
         * 10049213 、  10021531 、  10085129 、  10404999
         *  判斷依據 用UID做分組 並且列出他們的日期 再取出  四天只要使用者出現兩天以上就算是活躍
         *
         */
        $collection = $data->groupBy('UID')->map(function ($q, $k) {
            dump($q);
            if (count($q) > 2) {
                return $k;
            }
        })->toArray();
        dump(array_filter($collection));
//        3
        /**
         * 10404999
         * 判斷依據 一般來說看劇是至少要50分鐘，先用日期分組去找全部使用者
         * 並給予條件設定為每日出現2次以上，並且依照他們開啟的時間
         * 做相減 如果小於 50分鐘，就能斷定他是不合理的流量
         */
        $toArray = $data->groupBy(function ($q) {
            return Carbon::parse($q['time'])->format('Y-m-d');
        })->map(function ($q) {
            return array_filter(collect($q)->groupBy('UID')->map(function ($q) {
                if (count($q) > 1)
                    return [
                        'total' => $q->count()
                    ];
            })->toArray());
        })->toArray();
        dump(array_filter($toArray));

//        4
        /**
         * 10021531 、 10085129
         * 判斷依據 先過濾綜藝的使用者，及不合理的使用者10404999
         * 列出剩下的使用者並依照日期，來找使用者
         * 每日都上線即可列為重度使用者
         */
        $forth = $data->where('read_content', '!=', '綜藝')->groupBy('UID')->map(function ($q, $k) {
            if (count($q) > 2) {
                return $k;
            }
        })->toArray();

        dump(array_filter($forth));
    }
}
