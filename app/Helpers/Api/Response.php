<?php
/**
 * Created by PhpStorm.
 * User: steven
 * Date: 2019-03-25
 * Time: 13:11
 */

namespace App\Helpers\Api;

use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait Response
{
    /**
     * @var enum
     */
    protected $status = FoundationResponse::HTTP_OK;

    /**
     * @var enum
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param enum|int $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param mixed $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = []): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $this->getStatus(), $headers);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($data): \Illuminate\Http\JsonResponse
    {
        return $this->setStatus(FoundationResponse::HTTP_INTERNAL_SERVER_ERROR)->respond($data);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data): \Illuminate\Http\JsonResponse
    {
        return $this->respond($data);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail($data): \Illuminate\Http\JsonResponse
    {
        return $this->setStatus(FoundationResponse::HTTP_BAD_REQUEST)->respond($data);
    }

    /**
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFound($data): \Illuminate\Http\JsonResponse
    {
        return $this->setStatus(FoundationResponse::HTTP_NOT_FOUND)->respond($data);
    }
}
