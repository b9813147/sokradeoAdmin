<?php

namespace App\Services\HaBook;

use App\Repositories\ConfigRepository;
use GuzzleHttp\Client;
use Yish\Generators\Foundation\Service\Service;

class ApiService extends Service
{
    protected $repository;

    private $configRepo = null;
    private $apiSrvConfig = null;
    private $habookConfig = null;

    //
    public function __construct(ConfigRepository $configRepo)
    {
        $this->apiSrvConfig = config('habook.api.url');
        $this->habookConfig = $configRepo->getParamsByCate('Habook');
        $this->configRepo   = $configRepo;
    }

    public function getUserInfo($ticket)
    {
        $token    = $this->habookConfig['ApiAuthorization']->val;
        $api      = new Client();
        $userInfo = [
            "jsonrpc" => "2.0",
            "method"  => "UserInfoManage",
            "params"  =>
                [
                    "idToken"   => $ticket,
                    "method"    => "get",
                    "option"    => "userInfo",
                    "extraInfo" => (object)[],
                ],
            "id"      => 1
        ];
        $data     = $api->request('get', $this->apiSrvConfig . 'account', [
            'headers' => [
                'Authorization' => $token,
                'Content-Type'  => 'application/json',
            ],
            'body'    => \GuzzleHttp\json_encode($userInfo)
        ])->getBody()->getContents();
        \Log::info('200',(array)\GuzzleHttp\json_decode($data));
        return \GuzzleHttp\json_decode($data)->result;


    }
}
