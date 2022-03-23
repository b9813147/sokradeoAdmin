<?php

namespace App\Http\Controllers\Api\V1\App\BB;

use App\Helpers\Custom\GlobalPlatform;
use App\Http\Controllers\Api\V1\Controller;
use App\Services\BbLicenseService;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    protected $bbLicenseService;

    /**
     * @param BbLicenseService $bbLicenseService
     */
    public function __construct(BbLicenseService $bbLicenseService)
    {
        $this->bbLicenseService = $bbLicenseService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'storage'          => 'required|integer',
            'start_time'       => 'required|date_format:Y-m-d',
            'deadline'         => 'required|date_format:Y-m-d',
            'school_shortcode' => 'required|string',
            'order_no'         => 'required|string'
        ]);
        // 轉換
        $request->merge(['group_id' => GlobalPlatform::convertAbbrToGroupId($request->input('school_shortcode'))]);

        $createData = $request->only('storage', 'start_time', 'deadline', 'status', 'group_id', 'order_no');
        $where      = $request->only('group_id', 'order_no');
        $code       = $request->only('code');

        if (!$this->bbLicenseService->createLicenseForGroup($code, $where, $createData)) {
            return $this->setStatus(404)->fail(['message' => 'Failed to create']);
        }

        return $this->setStatus(204)->success(null);
    }


    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param string $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $code): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'school_shortcode' => 'required|string',
            'order_no'         => 'required|string'
        ]);

        // 轉換
        $request->merge(['group_id' => GlobalPlatform::convertAbbrToGroupId($request->input('school_shortcode'))]);
        $where = $request->only('group_id', 'order_no');
        if (!$this->bbLicenseService->deleteLicenseForGroup($code, $where)) {
            return $this->setStatus(404)->fail(['message' => 'Failed to delete']);
        }

        return $this->setStatus(204)->success(null);
    }
}
