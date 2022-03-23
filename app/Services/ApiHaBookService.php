<?php

namespace App\Services;

use App\Exceptions\Service\ServiceException;
use App\Libraries\Api as HaBookApi;
use App\Repositories\ConfigRepository;
use Ixudra\Curl\Facades\Curl;

use Yish\Generators\Foundation\Service\Service;

class ApiHaBookService extends Service
{
    protected $repository;
    protected $haBookApi;
    protected $haBookConfig;

    public function __construct(ConfigRepository $configRepository)
    {
        $apiServerConfig    = \Config::get('server.haBook.api.url');
        $this->repository   = $configRepository;
        $this->haBookConfig = $this->repository->getParamsByCate('Habook');
        $this->haBookApi    = new HaBookApi(['url' => $apiServerConfig]);


    }

    /**
     * @return mixed
     * @throws ServiceException
     */
    public function getApiAuthorization()
    {
        $coreSrvConfig = \Config::get('server.haBook.core');


        $info = $this->haBookApi->getServiceRegister([
            'clientId'            => $coreSrvConfig['clientId'],
            'verificationCode'    => $coreSrvConfig['verificationCode'],
            'verificationCodeVer' => $coreSrvConfig['verificationCodeVer'],
            'productCode'         => $coreSrvConfig['productCode'],
        ]);

        $resp = Curl::to($info['url'])
            ->withContentType('application/json')
            ->withData($info['data'])
            ->asJson()
            ->returnResponseObject()
            ->post();


        if ($resp->status !== 200 || is_null($resp->content->result)) {
            throw new ServiceException('habook api is error');
        }

        $authorization = $resp->content->result->token;
        $this->repository->setParamVal('HaBook', 'ApiAuthorization', $authorization);
        $this->haBookConfig = $this->repository->getParamsByCate('Habook');

        return $authorization;
    }

    /**
     * @param $ticket
     * @return mixed
     * @throws ServiceException
     */
    public function getUserInfo($ticket)
    {
        $info = $this->haBookApi->getAccountUserInfoManage([
            'idToken' => $ticket,
            'method'  => 'get',
            'option'  => 'userInfo',
        ]);

        return $this->execute($info['url'], $info['data']);
    }

    /**
     * @param $url
     * @param $data
     * @return mixed
     * @throws ServiceException
     */
    public function execute($url, $data)
    {
        $authorization = $this->haBookConfig['ApiAuthorization']->val;

        if (is_null($authorization)) {
            $authorization = $this->getApiAuthorization();
        }


        try {
            $resp = Curl::to($url)
                ->withHeader('Authorization: ' . $authorization)
                ->withContentType('application/json')
                ->withData($data)
                ->asJson()
                ->returnResponseObject()
                ->post();

            if ($resp->status !== 200 || is_null($resp->content->result)) {
                throw new ServiceException('habook api is error');
            }

            return $resp->content->result;

        } catch (ServiceException $e) {

            $authorization = $this->getApiAuthorization();

            $resp = Curl::to($url)
                ->withHeader('Authorization: ' . $authorization)
                ->withContentType('application/json')
                ->withData($data)
                ->asJson()
                ->returnResponseObject()
                ->post();

            if ($resp->status !== 200 || is_null($resp->content->result)) {
                throw new ServiceException('habook api is error');
            }

            return $resp->content->result;
        }
    }
}
