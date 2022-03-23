<?php

namespace App\Http\Controllers\Api\V1\App\Video;

use App\Http\Controllers\Api\V1\Controller;
use App\Services\MobileService;
use Illuminate\Http\Request;

class MobileController extends Controller
{

    /**
     * @var MobileService
     */
    protected $mobileService;

    public function __construct(MobileService $mobileService)
    {
        $this->mobileService = $mobileService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): \Illuminate\Http\JsonResponse
    {
        return $this->success($this->mobileService->getUserVideoCount(auth()->id()));
    }
}
