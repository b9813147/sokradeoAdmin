<?php

namespace Tests\Feature;

use App\Models\Group;
use Tests\TestCase;

class PassportTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->post('/oauth/token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => '3',
            'client_secret' => '5Uzi8VcMq7n0XJVqi5JvX9u6dHcg4QeXY3YFbXug',
            'scope'         => '*',
        ]);
        $response->dump();
        $response->assertStatus(200);
    }

    public function test_url()
    {
        $group = Group::with([
//            'bbLicenses' => function ($q) {
//                $q->where('code', 'LY9AJ6NY');
//            }
        ])->find(143);
        dump($group->bbLicenses->where('code', 'LL9MJ6NY')->max('pivot.storage'));
        dd($group->bbLicenses->toArray());

        $url = 'https://www.google.com';
        $url = base64_encode($url);
        dump($url);
        $filter_var = filter_var($url, FILTER_VALIDATE_URL);
        dump($filter_var);
    }
}
