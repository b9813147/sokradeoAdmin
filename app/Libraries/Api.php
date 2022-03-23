<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 2019-03-25
 * Time: 13:38
 */

namespace App\Libraries;


use App\Helpers\Api\JsonRpcII;
use App\Helpers\Code\AlphaNumeric;

class Api
{
    use JsonRpcII, AlphaNumeric;

    private $baseUrl = '';

    //
    public function __construct($params)
    {
        $this->baseUrl = $params['url'];
    }


    /**
     * service
     *
     * @param $params
     * @return array
     */
    public function getServiceRegister($params)
    {
        return $this->response('service', 'Regist', $params);
    }


    /**
     * account
     *
     * @param $params
     * @return array
     */
    public function getAccountUserInfoManage($params)
    {
        return $this->response('account', 'UserInfoManage', $params);
    }

    /**
     * @param $fun
     * @param $method
     * @param $params
     * @return array
     */

    private function response($fun, $method, $params)
    {
        $id   = $this->generateAlphaNum();
        $data = $this->toJsonRpcIIReq($id, $method, $params);
        return [
            'id'   => $id,
            'url'  => $this->baseUrl . $fun,
            'data' => $data,
        ];
    }
}
